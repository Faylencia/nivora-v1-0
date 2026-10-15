import { apiMessage, apiRequest, escapeHtml, formatRupiah, getSession, pagedItems, requireSession } from './nivora-api';
import { bindPhotoDropzone } from './products';

function formatDate(value, withTime = false) {
    if (!value) {
        return '—';
    }

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return '—';
    }

    return new Intl.DateTimeFormat('id-ID', withTime
        ? { dateStyle: 'medium', timeStyle: 'short' }
        : { dateStyle: 'medium' }).format(date);
}

function statusBadge(value) {
    const labels = {
        active: 'Aktif',
        completed: 'Selesai',
        cancelled: 'Dibatalkan',
        returned: 'Dikembalikan',
        paid: 'Dibayar',
        pending: 'Menunggu',
        refunded: 'Dikembalikan',
        failed: 'Gagal',
        new: 'Baru',
        in_progress: 'Diproses',
        inactive: 'Nonaktif',
        high: 'Tinggi',
        medium: 'Sedang',
        low: 'Rendah',
    };
    const style = ['completed', 'paid', 'active'].includes(value)
        ? 'bg-emerald-50 text-emerald-700'
        : ['cancelled', 'failed', 'high'].includes(value)
            ? 'bg-rose-50 text-rose-700'
            : ['pending', 'new', 'medium'].includes(value)
                ? 'bg-amber-50 text-amber-700'
                : 'bg-slate-100 text-slate-600';

    return `<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ${style}">${escapeHtml(labels[value] || value || '—')}</span>`;
}

function tableCell(value, className = '') {
    return `<td class="py-3 pr-4 ${className}">${value}</td>`;
}

function renderSaleRow(sale, canReturnSale) {
    return `<tr class="border-b border-[#eef3ff]">
        ${tableCell(`<span class="font-semibold text-[#1f2544]">${escapeHtml(sale.invoice_number)}</span>`)}
        ${tableCell(escapeHtml(formatDate(sale.transaction_date, true)))}
        ${tableCell(escapeHtml(sale.customer?.name || 'Tanpa customer'))}
        ${tableCell(escapeHtml(sale.outlet?.name || '—'))}
        ${tableCell(escapeHtml(formatRupiah(sale.total)), 'font-medium text-[#1f2544]')}
        ${tableCell(statusBadge(sale.status))}
        ${canReturnSale ? tableCell(sale.status === 'completed' ? `<button type="button" data-sale-return="${Number(sale.id)}" class="rounded-lg border border-rose-200 px-2.5 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50">Batalkan</button>` : '—') : ''}
    </tr>`;
}

function renderCustomerRow(customer) {
    return `<tr class="border-b border-[#eef3ff]">
        ${tableCell(`<span class="font-semibold text-[#1f2544]">${escapeHtml(customer.name)}</span>`)}
        ${tableCell(escapeHtml(customer.phone || '—'))}
        ${tableCell(escapeHtml(customer.email || '—'))}
        ${tableCell(escapeHtml(String(customer.points ?? 0)))}
        ${tableCell(escapeHtml(formatDate(customer.created_at)))}
    </tr>`;
}

function renderInventoryRow(inventory) {
    const stock = Number(inventory.available_stock ?? inventory.stock ?? 0);
    const minimum = Number(inventory.minimum_stock || 0);
    const status = stock <= minimum ? 'Stok rendah' : 'Tersedia';
    const badge = stock <= minimum
        ? '<span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">Stok rendah</span>'
        : '<span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Tersedia</span>';

    return `<tr class="border-b border-[#eef3ff]">
        ${tableCell(`<span class="font-semibold text-[#1f2544]">${escapeHtml(inventory.product?.name || 'Produk tidak tersedia')}</span><span class="mt-0.5 block text-xs text-[#7b8299]">${escapeHtml(inventory.product?.sku || '—')}</span>`)}
        ${tableCell(escapeHtml(inventory.product?.category?.name || '—'))}
        ${tableCell(escapeHtml(inventory.outlet?.name || '—'))}
        ${tableCell(`${stock} ${escapeHtml(inventory.product?.unit || '')}`, 'font-medium text-[#1f2544]')}
        ${tableCell(escapeHtml(String(minimum)))}
        ${tableCell(badge)}
    </tr>`;
}

function renderOutletRow(outlet) {
    return `<tr class="border-b border-[#eef3ff]">
        ${tableCell(`<span class="font-semibold text-[#1f2544]">${escapeHtml(outlet.name)}</span>`)}
        ${tableCell(escapeHtml(outlet.address || '—'))}
        ${tableCell(escapeHtml(outlet.phone || '—'))}
        ${tableCell(statusBadge(outlet.status))}
        ${tableCell(`<a href="/owner/outlets/${encodeURIComponent(outlet.id)}/edit" class="font-semibold text-[#687EFF]">Lihat / edit</a>`)}
    </tr>`;
}

function renderPaymentRow(payment) {
    const sale = payment.sale;

    return `<tr class="border-b border-[#eef3ff]">
        ${tableCell(`<span class="font-semibold text-[#1f2544]">${escapeHtml(payment.payment_reference || sale?.invoice_number || '—')}</span>`)}
        ${tableCell(escapeHtml(sale?.invoice_number || '—'))}
        ${tableCell(escapeHtml(payment.method || '—'))}
        ${tableCell(escapeHtml(formatRupiah(payment.amount)), 'font-medium text-[#1f2544]')}
        ${tableCell(statusBadge(payment.status))}
        ${tableCell(escapeHtml(formatDate(payment.paid_at, true)))}
    </tr>`;
}

function renderRecommendationRow(recommendation) {
    return `<tr class="border-b border-[#eef3ff] align-top">
        ${tableCell(`<span class="font-semibold text-[#1f2544]">${escapeHtml(recommendation.title)}</span><span class="mt-1 block max-w-xl whitespace-normal text-sm leading-6 text-[#5e647f]">${escapeHtml(recommendation.insight || recommendation.recommendation || '')}</span>`)}
        ${tableCell(statusBadge(recommendation.priority))}
        ${tableCell(statusBadge(recommendation.status))}
        ${tableCell(escapeHtml(formatDate(recommendation.created_at, true)))}
    </tr>`;
}

function renderProductRow(product, inventory = null, cashier = false) {
    const stockValue = inventory ? Number(inventory.available_stock ?? inventory.stock ?? 0) : null;
    const stock = stockValue === null ? 'Belum diatur' : `${stockValue} ${product.unit || ''}`;
    const activeBadge = product.is_active
        ? '<span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Aktif</span>'
        : '<span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">Nonaktif</span>';
    const status = !product.is_active
        ? activeBadge
        : stockValue === null
            ? '<span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">Stok belum diatur</span>'
            : stockValue <= Number(inventory.minimum_stock || 0)
                ? '<span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">Stok rendah</span>'
                : activeBadge;
    const actions = cashier
        ? '<a href="/cashier/pos" class="inline-flex min-h-9 items-center gap-1.5 rounded-lg border border-[#e3e8f6] bg-white px-3 text-xs font-semibold text-[#536dfe] transition hover:border-[#b8c4ff] hover:bg-[#f7f8ff]"><svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 5h2l2.2 10.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 1.9-1.5L21 9H6"/><circle cx="10" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>Jual</a>'
        : `<a href="/owner/products/${encodeURIComponent(product.id)}" class="mr-3 font-semibold text-[#687EFF]">Detail</a><a href="/owner/products/${encodeURIComponent(product.id)}/edit" class="font-semibold text-[#5e647f]">Edit</a>`;
    const productIdentity = cashier
        ? `<span class="flex items-center gap-3"><span class="product-list-photo">${product.image_url ? `<img data-product-image src="${escapeHtml(product.image_url)}" alt="" loading="lazy" class="h-11 w-11 shrink-0 rounded-xl border border-[#e7ebff] bg-[#eef1ff] object-cover">` : '<span class="cashier-product-placeholder flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-[#e7ebff] bg-[#eef1ff] text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Zm0 0v9m8-4.5-8 4.5-8-4.5"/></svg></span>'}</span><span class="min-w-0"><span class="block truncate font-semibold text-[#20284a]">${escapeHtml(product.name)}</span><span class="mt-0.5 block max-w-[280px] truncate text-xs text-[#7b8299]">${escapeHtml(product.description || 'Produk katalog toko')}</span></span></span>`
        : `<span class="font-semibold text-[#1f2544]">${escapeHtml(product.name)}</span><span class="mt-0.5 block text-xs text-[#7b8299]">${escapeHtml(product.description || '')}</span>`;

    return `<tr class="border-b border-[#eef3ff]">
        ${tableCell(productIdentity)}
        ${tableCell(escapeHtml(product.sku || '—'))}
        ${tableCell(escapeHtml(product.category?.name || '—'))}
        ${tableCell(escapeHtml(formatRupiah(product.selling_price)), 'font-medium text-[#1f2544]')}
        ${tableCell(escapeHtml(stock))}
        ${tableCell(status)}
        ${tableCell(actions)}
    </tr>`;
}

function setAlert(root, message, isError = true) {
    const alert = root.querySelector('[data-live-message]');
    if (!alert) {
        return;
    }

    alert.textContent = message;
    alert.className = `rounded-2xl border px-4 py-3 text-sm ${isError ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700'}`;
}

function filterItems(root, items) {
    const search = root.querySelector('[data-live-search]')?.value.trim().toLocaleLowerCase('id-ID') || '';
    const status = root.querySelector('[data-live-status]')?.value || '';

    return items.filter((item) => {
        const matchesSearch = !search || JSON.stringify(item).toLocaleLowerCase('id-ID').includes(search);
        const matchesStatus = !status || item.status === status;

        return matchesSearch && matchesStatus;
    });
}

async function loadList(root) {
    const page = root.dataset.liveList;
    const loading = root.querySelector('[data-live-loading]');
    const empty = root.querySelector('[data-live-empty]');
    const tableWrap = root.querySelector('[data-live-table-wrap]');
    const table = root.querySelector('[data-live-table]');
    const session = getSession();
    const roles = session?.user?.roles || [];
    const canReturnSales = roles.some((role) => role.name === 'super_admin')
        || roles.some((role) => role.name === 'owner' && Number(role.business_id) === Number(session?.businessId));
    const paths = {
        sales: '/sales?limit=100',
        customers: '/customers?limit=100',
        inventory: '/inventory?limit=100',
        outlets: '/outlets?limit=100',
        payments: '/payments?limit=100',
        recommendations: '/ai/recommendations',
        products: '/products?limit=100',
    };

    loading?.classList.remove('hidden');
    empty?.classList.add('hidden');
    tableWrap?.classList.add('hidden');

    try {
        const response = await apiRequest(paths[page]);
        let items = Array.isArray(response?.data) ? response.data : pagedItems(response);
        let inventory = [];

        if (page === 'products') {
            items = items.filter((product) => product.is_active);
            const outletQuery = session?.businessId ? `&business_id=${encodeURIComponent(session.businessId)}` : '';
            inventory = pagedItems(await apiRequest(`/inventory?limit=100${outletQuery}`));
        }

        if (page === 'inventory' && session?.businessId && root.dataset.scope === 'outlet') {
            items = items.filter((item) => Number(item.business_id) === Number(session.businessId));
        }

        const filtered = filterItems(root, items);
        const count = root.querySelector('[data-live-count]');
        if (count) {
            const countLabel = page === 'sales' ? 'transaksi' : page === 'payments' ? 'pembayaran' : page === 'products' ? 'produk' : 'customer';
            count.textContent = `${filtered.length} ${countLabel}`;
        }
        const rows = {
            sales: (item) => renderSaleRow(item, canReturnSales),
            customers: (item) => renderCustomerRow(item),
            inventory: (item) => renderInventoryRow(item),
            outlets: (item) => renderOutletRow(item),
            payments: (item) => renderPaymentRow(item),
            recommendations: (item) => renderRecommendationRow(item),
            products: (item) => {
                const stock = inventory.find((row) => Number(row.product_id) === Number(item.id));

                return renderProductRow(item, stock, true);
            },
        };

        table.innerHTML = filtered.map(rows[page]).join('');
        table.querySelectorAll('[data-product-image]').forEach((image) => {
            image.addEventListener('error', () => {
                image.outerHTML = '<span class="cashier-product-placeholder flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-[#e7ebff] bg-[#eef1ff] text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Zm0 0v9m8-4.5-8 4.5-8-4.5"/></svg></span>';
            }, { once: true });
        });
        loading?.classList.add('hidden');
        empty?.classList.toggle('hidden', filtered.length > 0);
        tableWrap?.classList.toggle('hidden', filtered.length === 0);
    } catch (error) {
        loading?.classList.add('hidden');
        setAlert(root, apiMessage(error));
    }
}

function initializeList(root) {
    if (!requireSession()) {
        return;
    }

    root.querySelector('[data-live-search]')?.addEventListener('input', () => loadList(root));
    root.querySelector('[data-live-status]')?.addEventListener('change', () => loadList(root));
    root.addEventListener('click', async (event) => {
        const returnButton = event.target.closest('[data-sale-return]');
        if (!returnButton || !window.confirm('Batalkan transaksi ini dan pulihkan stok?')) {
            return;
        }

        returnButton.disabled = true;
        try {
            await apiRequest(`/sales/${Number(returnButton.dataset.saleReturn)}/return`, { method: 'POST' });
            setAlert(root, 'Transaksi berhasil dibatalkan dan stok dipulihkan.', false);
            await loadList(root);
        } catch (error) {
            setAlert(root, apiMessage(error));
        } finally {
            returnButton.disabled = false;
        }
    });
    root.querySelector('[data-ai-generate]')?.addEventListener('click', async (event) => {
        const button = event.currentTarget;
        button.disabled = true;
        button.textContent = 'Memproses…';

        try {
            const response = await apiRequest('/ai/recommendations/generate', { method: 'POST', body: JSON.stringify({}) });
            setAlert(root, response.message || 'Analisis stok selesai.', false);
            await loadList(root);
        } catch (error) {
            setAlert(root, apiMessage(error));
        } finally {
            button.disabled = false;
            button.textContent = 'Analisis stok sekarang';
        }
    });

    loadList(root);
}

function renderSalesChart(svg, chart) {
    const values = chart.values.map((value) => Number(value) || 0);
    const labels = chart.labels || [];
    const left = 54;
    const right = 744;
    const top = 24;
    const bottom = 218;
    const maximum = Math.max(...values, 1);
    const points = values.map((value, index) => {
        const x = left + ((right - left) * index) / Math.max(values.length - 1, 1);
        const y = bottom - (value / maximum) * (bottom - top);
        return { x, y, value, label: labels[index] || '' };
    });
    const gridLines = Array.from({ length: 4 }, (_, index) => {
        const y = top + ((bottom - top) * index) / 3;
        const amount = maximum * (1 - index / 3);
        const compactAmount = new Intl.NumberFormat('id-ID', { notation: 'compact', maximumFractionDigits: 1 }).format(amount);
        return `<g><line class="sales-chart-gridline" x1="${left}" y1="${y}" x2="${right}" y2="${y}"/><text x="${left - 10}" y="${y + 4}" text-anchor="end">${escapeHtml(compactAmount)}</text></g>`;
    }).join('');
    const linePath = points.map((point, index) => `${index === 0 ? 'M' : 'L'} ${point.x} ${point.y}`).join(' ');
    const firstPoint = points[0] || { x: left, y: bottom };
    const lastPoint = points.at(-1) || { x: left, y: bottom };
    const areaPath = `${linePath} L ${lastPoint.x} ${bottom} L ${firstPoint.x} ${bottom} Z`;
    const labelStep = chart.range === 'hour' ? 4 : 1;
    const xLabels = points.map((point, index) => {
        if (index % labelStep !== 0 && index !== points.length - 1) {
            return '';
        }
        return `<text x="${point.x}" y="252" text-anchor="middle">${escapeHtml(point.label)}</text>`;
    }).join('');
    const dots = points.map((point) => `<circle class="sales-chart-point" cx="${point.x}" cy="${point.y}" r="4"><title>${escapeHtml(point.label)}: ${escapeHtml(formatRupiah(point.value))}</title></circle>`).join('');

    svg.setAttribute('aria-label', `Grafik omzet ${chart.range} sebesar ${formatRupiah(chart.total)}`);
    svg.innerHTML = `${gridLines}<path class="sales-chart-area" d="${areaPath}"/><path class="sales-chart-line" d="${linePath}"/>${dots}${xLabels}`;
}

function initializeDashboard(root) {
    if (!requireSession()) {
        return;
    }

    const chartRange = root.querySelector('[data-sales-chart-range]');
    const chartSvg = root.querySelector('[data-sales-chart-svg]');
    const chartLoading = root.querySelector('[data-sales-chart-loading]');
    const chartContent = root.querySelector('[data-sales-chart-content]');
    const chartError = root.querySelector('[data-sales-chart-error]');
    const chartEmpty = root.querySelector('[data-sales-chart-empty]');
    const chartTotal = root.querySelector('[data-sales-chart-total]');
    const chartCaption = root.querySelector('[data-sales-chart-caption]');
    let chartRequest = 0;

    const loadSalesChart = async (range) => {
        const requestId = ++chartRequest;
        chartLoading?.classList.remove('hidden');
        chartContent?.classList.add('hidden');
        chartError?.classList.add('hidden');
        chartRange && (chartRange.disabled = true);

        try {
            const { data } = await apiRequest(`/dashboard/sales-chart?range=${encodeURIComponent(range)}`);
            if (requestId !== chartRequest) {
                return;
            }

            renderSalesChart(chartSvg, data);
            chartTotal.textContent = formatRupiah(data.total);
            chartCaption.textContent = {
                hour: 'Ringkasan omzet dalam 24 jam terakhir.',
                day: 'Ringkasan omzet dalam 7 hari terakhir.',
                month: 'Ringkasan omzet dalam 12 bulan terakhir.',
                year: 'Ringkasan omzet dalam 5 tahun terakhir.',
            }[range];
            chartEmpty?.classList.toggle('hidden', Number(data.total) > 0);
            chartLoading?.classList.add('hidden');
            chartContent?.classList.remove('hidden');
        } catch (error) {
            if (requestId !== chartRequest) {
                return;
            }
            chartLoading?.classList.add('hidden');
            chartError.textContent = apiMessage(error);
            chartError?.classList.remove('hidden');
        } finally {
            if (requestId === chartRequest && chartRange) {
                chartRange.disabled = false;
            }
        }
    };

    chartRange?.addEventListener('change', () => loadSalesChart(chartRange.value));

    const load = async () => {
        const loading = root.querySelector('[data-dashboard-loading]');
        const table = root.querySelector('[data-dashboard-recent]');
        const empty = root.querySelector('[data-dashboard-empty]');
        const tableWrap = root.querySelector('[data-dashboard-table-wrap]');

        try {
            const response = await apiRequest('/dashboard');
            const data = response.data;
            const kpi = data.kpi || {};
            root.querySelector('[data-dashboard-business]')?.replaceChildren(document.createTextNode(data.business?.name || 'Bisnis'));
            root.querySelector('[data-dashboard-stat="today_sales"]').textContent = formatRupiah(kpi.today_sales);
            root.querySelector('[data-dashboard-stat="today_transactions"]').textContent = String(kpi.today_transactions ?? 0);
            root.querySelector('[data-dashboard-stat="active_products"]').textContent = String(kpi.active_products ?? 0);
            root.querySelector('[data-dashboard-stat="low_stock_items"]').textContent = String(kpi.low_stock_items ?? 0);

            const sales = data.recent_sales || [];
            table.innerHTML = sales.map((sale) => `<tr class="border-b border-[#eef3ff]">
                ${tableCell(`<span class="font-semibold text-[#1f2544]">${escapeHtml(sale.invoice_number)}</span>`)}
                ${tableCell(escapeHtml(sale.customer || 'Tanpa customer'))}
                ${tableCell(escapeHtml(formatDate(sale.transaction_date, true)))}
                ${tableCell(escapeHtml(formatRupiah(sale.total)), 'font-medium text-[#1f2544]')}
                ${tableCell(statusBadge(sale.status))}
            </tr>`).join('');
            loading?.classList.add('hidden');
            empty?.classList.toggle('hidden', sales.length > 0);
            tableWrap?.classList.toggle('hidden', sales.length === 0);
        } catch (error) {
            loading?.classList.add('hidden');
            setAlert(root, apiMessage(error));
        }
    };

    load();
    if (chartRange && chartSvg) {
        loadSalesChart(chartRange.value);
    }
}

function initializeBusinessProfile(root) {
    if (!requireSession()) {
        return;
    }

    const form = root.querySelector('[data-business-form]');
    const submit = form.querySelector('[type="submit"]');

    apiRequest('/business').then(({ data }) => {
        for (const field of ['name', 'location_name', 'business_type', 'phone', 'address']) {
            const input = form.elements.namedItem(field);
            if (input) {
                input.value = data[field] || '';
            }
        }
        root.querySelector('[data-profile-business-name]').textContent = data.name || 'Nama bisnis belum diatur';
        root.querySelector('[data-profile-business-type]').textContent = data.business_type || 'Bidang usaha belum diatur';
        root.querySelector('[data-profile-location-name]').textContent = data.location_name || 'Lokasi belum diatur';
        root.querySelector('[data-business-loading]')?.classList.add('hidden');
        form.classList.remove('hidden');
    }).catch((error) => {
        root.querySelector('[data-business-loading]').textContent = apiMessage(error);
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        submit.disabled = true;
        const submitLabel = submit.querySelector('span');
        const originalLabel = submitLabel?.textContent;
        if (submitLabel) {
            submitLabel.textContent = 'Menyimpan…';
        }

        try {
            const body = Object.fromEntries(new FormData(form).entries());
            const response = await apiRequest('/business', { method: 'PUT', body: JSON.stringify(body) });
            root.querySelector('[data-profile-business-name]').textContent = body.name || 'Nama bisnis belum diatur';
            root.querySelector('[data-profile-business-type]').textContent = body.business_type || 'Bidang usaha belum diatur';
            root.querySelector('[data-profile-location-name]').textContent = body.location_name || 'Lokasi belum diatur';
            setAlert(root, response.message || 'Profil bisnis diperbarui.', false);
        } catch (error) {
            setAlert(root, apiMessage(error));
        } finally {
            submit.disabled = false;
            if (submitLabel) {
                submitLabel.textContent = originalLabel || 'Simpan perubahan';
            }
        }
    });
}

function setEntityMessage(root, message, isError = true) {
    const alert = root.querySelector('[data-entity-message]');
    alert.textContent = message;
    alert.className = `rounded-2xl border px-4 py-3 text-sm ${isError ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700'}`;
}

function initializeProductDetail(root) {
    if (!requireSession()) {
        return;
    }

    const productId = root.dataset.productDetail;
    const loading = root.querySelector('[data-entity-loading]');
    const content = root.querySelector('[data-product-content]');

    apiRequest(`/products/${encodeURIComponent(productId)}`).then(({ data }) => {
        const photo = root.querySelector('[data-product-photo]');
        const photoPlaceholder = root.querySelector('[data-product-photo-empty]');
        const showPhotoPlaceholder = () => {
            photo.classList.add('hidden');
            photo.removeAttribute('src');
            photoPlaceholder?.classList.remove('hidden');
        };
        if (data.image_url) {
            photo.onload = () => {
                photo.classList.remove('hidden');
                photoPlaceholder?.classList.add('hidden');
            };
            photo.onerror = showPhotoPlaceholder;
            photo.src = data.image_url;
            if (photo.complete && photo.naturalWidth > 0) {
                photo.onload();
            }
        } else {
            showPhotoPlaceholder();
        }
        root.querySelector('[data-product-name]').textContent = data.name;
        root.querySelector('[data-product-sku]').textContent = data.sku;
        root.querySelector('[data-product-barcode]').textContent = data.barcode || '—';
        root.querySelector('[data-product-category-name]').textContent = data.category?.name || '—';
        root.querySelector('[data-product-description]').textContent = data.description || 'Deskripsi belum tersedia.';
        root.querySelector('[data-product-purchase-price]').textContent = formatRupiah(data.purchase_price);
        root.querySelector('[data-product-selling-price]').textContent = formatRupiah(data.selling_price);
        root.querySelector('[data-product-unit]').textContent = data.unit || '—';
        root.querySelector('[data-product-status]').innerHTML = statusBadge(data.is_active ? 'active' : 'inactive');

        const inventories = data.inventories || [];
        const inventoryContainer = root.querySelector('[data-product-inventories]');
        inventoryContainer.innerHTML = inventories.length
            ? inventories.map((inventory) => `<div class="flex items-center justify-between gap-3 rounded-xl border border-[#e7ebff] bg-[#f7f9ff] p-3"><span><span class="block font-medium text-[#1f2544]">${escapeHtml(inventory.outlet?.name || 'Outlet')}</span><span class="text-xs text-[#5e647f]">Batas minimum ${Number(inventory.minimum_stock || 0)} ${escapeHtml(data.unit || '')}</span></span><span class="font-semibold text-[#1f2544]">${Number(inventory.available_stock ?? inventory.stock ?? 0)} ${escapeHtml(data.unit || '')}</span></div>`).join('')
            : '<p class="text-sm text-[#5e647f]">Belum ada catatan stok untuk produk ini.</p>';

        loading.classList.add('hidden');
        content.classList.remove('hidden');
    }).catch((error) => {
        loading.textContent = apiMessage(error);
    });
}

function initializeProductEdit(root, modal = false) {
    if (!requireSession()) {
        return;
    }

    const productId = root.dataset.productEdit;
    const form = root.querySelector('[data-product-edit-form]');
    const categorySelect = form.elements.namedItem('category_id');
    const loading = root.querySelector('[data-entity-loading]');
    const submit = form.querySelector('[type="submit"]');
    const removePhoto = form.querySelector('[data-remove-product-photo]');

    Promise.all([apiRequest(`/products/${encodeURIComponent(productId)}`), apiRequest('/categories')]).then(([productResponse, categoriesResponse]) => {
        const product = productResponse.data;
        root.dataset.productBusinessId = String(product.business_id);
        const categories = categoriesResponse.data || [];
        const photoPreview = bindPhotoDropzone(root.querySelector('[data-photo-dropzone]'), product.image_url || null);
        root.querySelector('[data-product-edit-title]')?.replaceChildren(document.createTextNode(product.name));
        categorySelect.innerHTML = categories.map((category) => `<option value="${category.id}" ${Number(category.id) === Number(product.category_id) ? 'selected' : ''}>${escapeHtml(category.name)}</option>`).join('');

        for (const field of ['name', 'sku', 'barcode', 'description', 'purchase_price', 'selling_price', 'unit']) {
            form.elements.namedItem(field).value = product[field] ?? '';
        }
        form.elements.namedItem('is_active').value = product.is_active ? '1' : '0';
        const inventory = (product.inventories || []).find((item) => Number(item.business_id) === Number(product.business_id));
        const stockDisplay = root.querySelector('[data-edit-product-stock]');
        if (stockDisplay) {
            stockDisplay.textContent = `${Number(inventory?.available_stock || 0)} ${product.unit || ''}`.trim();
        }
        removePhoto?.closest('label')?.classList.toggle('hidden', !product.image);
        removePhoto?.addEventListener('change', () => {
            if (removePhoto.checked) {
                form.elements.namedItem('photo').value = '';
                photoPreview.showPhoto(null);
            } else {
                photoPreview.showPhoto(product.image_url || null);
            }
        });
        loading.classList.add('hidden');
        form.classList.remove('hidden');
    }).catch((error) => {
        loading.textContent = apiMessage(error);
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        submit.disabled = true;
        const submitLabel = submit.querySelector('span');
        const originalLabel = submitLabel?.textContent;
        if (submitLabel) {
            submitLabel.textContent = 'Menyimpan…';
        }

        try {
            const body = new FormData(form);
            body.set('_method', 'PUT');
            body.set('unit', form.elements.namedItem('unit').value.trim() || 'pcs');
            await apiRequest(`/products/${encodeURIComponent(productId)}`, {
                method: 'POST',
                body,
            });
            if (modal) {
                root.remove();
                document.body.classList.remove('overflow-hidden');
                document.dispatchEvent(new CustomEvent('nivora:products-refresh', { detail: { message: 'Perubahan produk berhasil disimpan.' } }));
            } else {
                window.location.assign(`/owner/products/${encodeURIComponent(productId)}`);
            }
        } catch (error) {
            setEntityMessage(root, apiMessage(error));
            submit.disabled = false;
            if (submitLabel) {
                submitLabel.textContent = originalLabel || 'Simpan perubahan';
            }
        }
    });

    const stockButton = root.querySelector('[data-adjust-stock]');
    stockButton?.addEventListener('click', async () => {
        const quantityInput = form.querySelector('[data-stock-quantity]');
        const notesInput = form.querySelector('[data-stock-notes]');
        const stockMessage = root.querySelector('[data-stock-message]');
        quantityInput.setCustomValidity(Number(quantityInput.value) >= 1 ? '' : 'Masukkan jumlah stok minimal 1.');
        if (!quantityInput.reportValidity()) {
            return;
        }

        stockButton.disabled = true;
        stockButton.textContent = 'Menyimpan stok…';
        try {
            const response = await apiRequest('/inventory/adjustment', {
                method: 'POST',
                body: JSON.stringify({
                    product_id: Number(productId),
                    business_id: Number(root.dataset.productBusinessId),
                    quantity: Number(quantityInput.value),
                    notes: notesInput.value.trim() || 'Penambahan stok dari edit produk',
                }),
            });
            const inventory = response.data;
            root.querySelector('[data-edit-product-stock]').textContent = `${Number(inventory.available_stock || 0)} ${form.elements.namedItem('unit').value || ''}`.trim();
            stockMessage.textContent = 'Stok berhasil ditambahkan.';
            stockMessage.className = 'rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700';
            quantityInput.value = '';
            notesInput.value = '';
            document.dispatchEvent(new CustomEvent('nivora:products-refresh'));
        } catch (error) {
            stockMessage.textContent = apiMessage(error);
            stockMessage.className = 'rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700';
        } finally {
            stockButton.disabled = false;
            stockButton.textContent = 'Tambah stok';
        }
    });
}

function initializeOutletEdit(root) {
    if (!requireSession()) {
        return;
    }

    const outletId = root.dataset.outletEdit;
    const form = root.querySelector('[data-outlet-edit-form]');
    const loading = root.querySelector('[data-entity-loading]');
    const submit = form.querySelector('[type="submit"]');

    apiRequest(`/outlets/${encodeURIComponent(outletId)}`).then(({ data }) => {
        for (const field of ['name', 'address', 'phone', 'status']) {
            form.elements.namedItem(field).value = data[field] || '';
        }
        loading.classList.add('hidden');
        form.classList.remove('hidden');
    }).catch((error) => {
        loading.textContent = apiMessage(error);
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        submit.disabled = true;

        try {
            const body = Object.fromEntries(new FormData(form).entries());
            const response = await apiRequest(`/outlets/${encodeURIComponent(outletId)}`, { method: 'PUT', body: JSON.stringify(body) });
            setEntityMessage(root, response.message || 'Informasi outlet diperbarui.', false);
        } catch (error) {
            setEntityMessage(root, apiMessage(error));
        } finally {
            submit.disabled = false;
        }
    });
}

function hydrateAccountLabels() {
    const session = getSession();
    if (!session) {
        return;
    }

    document.querySelectorAll('[data-session-user]').forEach((element) => {
        element.textContent = session.user?.name || 'Akun aktif';
    });
    document.querySelectorAll('[data-session-business]').forEach((element) => {
        element.textContent = session.businessName || 'Bisnis';
    });
}

export function bootLivePages() {
    hydrateAccountLabels();
    document.querySelectorAll('[data-live-list]').forEach(initializeList);
    document.querySelectorAll('[data-live-dashboard]').forEach(initializeDashboard);
    document.querySelectorAll('[data-business-profile]').forEach(initializeBusinessProfile);
    document.querySelectorAll('[data-product-detail]').forEach(initializeProductDetail);
    document.querySelectorAll('[data-product-edit]').forEach(initializeProductEdit);
    document.querySelectorAll('[data-outlet-edit]').forEach(initializeOutletEdit);

    document.addEventListener('nivora:product-edit-modal-open', (event) => {
        const template = document.querySelector('template[data-product-edit-modal-template]');
        const productId = event.detail?.productId;
        if (!template || !productId) {
            return;
        }

        const root = template.content.firstElementChild.cloneNode(true);
        root.dataset.productEdit = String(productId);
        document.body.append(root);
        document.body.classList.add('overflow-hidden');
        const closeModal = () => {
            root.remove();
            document.body.classList.remove('overflow-hidden');
        };
        root.querySelectorAll('[data-close-product-modal]').forEach((button) => button.addEventListener('click', closeModal));
        root.addEventListener('keydown', (keyEvent) => {
            if (keyEvent.key === 'Escape') {
                closeModal();
            }
        });
        root.querySelector('[data-product-edit-form]')?.querySelector('[name="name"]')?.focus({ preventScroll: true });
        initializeProductEdit(root, true);
    });
}
