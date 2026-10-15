import { apiMessage, apiRequest, escapeHtml, formatRupiah, getSession } from './nivora-api';

function formatDate(value) {
    if (!value) {
        return '—';
    }

    const date = new Date(value);
    return Number.isNaN(date.getTime()) ? '—' : new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(date);
}

function setMessage(element, message, isError = false) {
    if (!element) {
        return;
    }

    element.textContent = message;
    element.className = `rounded-2xl border px-4 py-3 text-sm ${isError ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700'}`;
}

function hasMemberRole() {
    const session = getSession();
    const roles = session?.user?.roles?.map((role) => role.name) || [];
    return Boolean(session?.token && (roles.includes('member') || roles.includes('super_admin')));
}

function gateDetail(root, loadingSelector, gateSelector) {
    const loading = root.querySelector(loadingSelector);
    const gate = root.querySelector(gateSelector);

    if (!hasMemberRole()) {
        loading?.classList.add('hidden');
        gate?.classList.remove('hidden');
        return false;
    }

    return true;
}

function initializeBusinessDetail(root) {
    if (!gateDetail(root, '[data-member-detail-loading]', '[data-member-detail-gate]')) {
        return;
    }

    const error = root.querySelector('[data-member-detail-error]');
    apiRequest(`/directory/businesses/${encodeURIComponent(root.dataset.memberBusinessDetail)}`)
        .then(({ data }) => {
            const business = data.business;
            root.querySelector('[data-store-name]').textContent = business.name;
            root.querySelector('[data-store-type]').textContent = business.business_type || 'Bisnis lokal';
            root.querySelector('[data-store-address]').textContent = business.address || 'Alamat belum dicantumkan';
            const logo = root.querySelector('[data-store-logo]');
            if (business.logo && logo) {
                logo.innerHTML = `<img src="${escapeHtml(business.logo)}" alt="Logo ${escapeHtml(business.name)}" class="h-full w-full object-cover" onload="this.nextElementSibling.classList.add('hidden')" onerror="this.remove()"><svg viewBox="0 0 24 24" class="h-9 w-9" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M3 10.5 12 4l9 6.5M5 9.5V20h14V9.5M9 20v-6h6v6"/></svg>`;
            }
            const phone = root.querySelector('[data-store-phone]');
            phone.textContent = business.phone || 'Telepon belum tersedia';
            if (business.phone) {
                phone.href = `tel:${business.phone}`;
            } else {
                phone.removeAttribute('href');
            }

            const outletContainer = root.querySelector('[data-store-outlets]');
            outletContainer.innerHTML = data.outlets.length ? data.outlets.map((outlet) => `
                <article class="public-info-card"><span class="public-info-card-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 10.5 12 4l9 6.5M5 9.5V20h14V9.5M9 20v-6h6v6"/></svg></span><div><h3>${escapeHtml(outlet.name)}</h3><p>${escapeHtml(outlet.address || 'Alamat belum dicantumkan')}</p><small>${escapeHtml(outlet.phone || 'Telepon belum tersedia')}</small></div></article>
            `).join('') : '<div class="public-info-empty">Informasi lokasi toko belum tersedia.</div>';

            const promoContainer = root.querySelector('[data-store-promotions]');
            promoContainer.innerHTML = data.promotions.length ? data.promotions.map((promo) => `
                <article class="public-promotion-card"><span class="public-promotion-mark" aria-hidden="true">%</span><div><h3>${escapeHtml(promo.name)}</h3><p>Diskon ${escapeHtml(promo.discount_value)}%${Number(promo.min_transaction) > 0 ? ` · minimal ${escapeHtml(formatRupiah(promo.min_transaction))}` : ''}</p><small>Berakhir ${escapeHtml(formatDate(promo.end_date))}</small></div></article>
            `).join('') : '<div class="public-info-empty">Belum ada promo aktif.</div>';

            const productContainer = root.querySelector('[data-store-products]');
            productContainer.innerHTML = data.products.length ? data.products.map((product) => `
                <article class="public-detail-product"><div class="public-detail-product-photo">${product.image_url ? `<img src="${escapeHtml(product.image_url)}" alt="Foto ${escapeHtml(product.name)}" loading="lazy" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden')"><svg class="hidden" viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Zm0 0v9m8-4.5-8 4.5-8-4.5"/></svg>` : '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Zm0 0v9m8-4.5-8 4.5-8-4.5"/></svg>'}</div><div class="public-detail-product-body"><p class="public-detail-product-category">${escapeHtml(product.category || 'Tanpa kategori')}</p><h3>${escapeHtml(product.name)}</h3><p class="public-detail-product-description">${escapeHtml(product.description || 'Deskripsi belum tersedia.')}</p><strong>${escapeHtml(formatRupiah(product.selling_price))} <small>/ ${escapeHtml(product.unit || 'unit')}</small></strong><div class="public-detail-product-stock">${product.stock_by_outlet.length ? product.stock_by_outlet.map((stock) => `<p><span>${escapeHtml(stock.outlet_name || 'Toko')}</span><span>${Number(stock.stock).toLocaleString('id-ID')} tersedia</span></p>`).join('') : '<p><span>Stok</span><span>Belum dicatat</span></p>'}</div><a href="/product/${Number(product.id)}" class="public-detail-product-link">Lihat detail produk <span aria-hidden="true">→</span></a></div></article>
            `).join('') : '<div class="public-info-empty">Belum ada produk aktif.</div>';

            root.querySelector('[data-member-detail-loading]').classList.add('hidden');
            root.querySelector('[data-member-detail-content]').classList.remove('hidden');
        })
        .catch((requestError) => {
            root.querySelector('[data-member-detail-loading]').classList.add('hidden');
            error.textContent = apiMessage(requestError);
            error.classList.remove('hidden');
        });
}

function initializeProductDetail(root) {
    if (!gateDetail(root, '[data-product-detail-loading]', '[data-product-detail-gate]')) {
        return;
    }

    const error = root.querySelector('[data-product-detail-error]');
    apiRequest(`/directory/products/${encodeURIComponent(root.dataset.memberProductDetail)}`)
        .then(({ data }) => {
            const stockItems = data.stock_by_outlet || [];
            const totalStock = stockItems.reduce((total, item) => total + Number(item.stock || 0), 0);
            const stock = stockItems.length ? stockItems.map((item) => {
                const available = Number(item.stock || 0) > 0;

                return `
                    <div class="public-stock-row">
                        <span class="public-stock-pin" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M19 10c0 5-7 11-7 11S5 15 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2"/></svg></span>
                        <span class="public-stock-location"><strong>${escapeHtml(item.outlet_name || 'Outlet')}</strong><small>${escapeHtml(item.outlet_address || 'Alamat belum tersedia')}</small></span>
                        <span class="public-stock-availability ${available ? 'is-available' : 'is-empty'}"><i></i>${available ? `${Number(item.stock).toLocaleString('id-ID')} tersedia` : 'Habis'}</span>
                    </div>
                `;
            }).join('') : '<p class="public-stock-empty">Stok belum tersedia.</p>';
            const productPhoto = data.image_url
                ? `<img src="${escapeHtml(data.image_url)}" alt="Foto ${escapeHtml(data.name)}" fetchpriority="high" decoding="async" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden')"><svg class="hidden" viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Zm0 0v9m8-4.5-8 4.5-8-4.5"/></svg><a class="public-photo-zoom" href="${escapeHtml(data.image_url)}" target="_blank" rel="noopener">Lihat foto asli <span aria-hidden="true">↗</span></a>`
                : '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Zm0 0v9m8-4.5-8 4.5-8-4.5"/></svg>';

            root.querySelector('[data-product-detail-content]').innerHTML = `
                <a href="/business/${Number(data.business.id)}" class="public-product-back inline-flex items-center gap-2 text-sm font-semibold"><span aria-hidden="true">←</span> ${escapeHtml(data.business.name)}</a>
                <article class="public-product-detail-card mt-5 overflow-hidden rounded-[28px] border bg-white">
                    <div class="grid md:grid-cols-[minmax(0,1.02fr)_minmax(0,.98fr)]">
                        <div class="public-product-detail-photo">${productPhoto}<span class="public-photo-label">Foto produk</span></div>
                        <div class="flex flex-col p-5 sm:p-7 lg:p-9">
                            <div class="flex flex-wrap items-center gap-2"><span class="public-product-category">${escapeHtml(data.category || 'Produk lokal')}</span><span class="public-product-state ${totalStock > 0 ? 'is-available' : 'is-empty'}"><i></i>${totalStock > 0 ? 'Tersedia' : 'Stok habis'}</span></div>
                            <h1 class="mt-4 text-3xl font-semibold leading-tight tracking-tight text-[#1f2544] sm:text-4xl">${escapeHtml(data.name)}</h1>
                            <p class="mt-3 text-sm leading-6 text-[#68728e]">${escapeHtml(data.description || 'Produk pilihan dari bisnis lokal.')}</p>
                            <div class="public-product-price-block mt-6"><span>Harga produk</span><strong>${escapeHtml(formatRupiah(data.selling_price))}</strong><small>per ${escapeHtml(data.unit || 'unit')}</small></div>
                            <section class="mt-7"><div class="mb-2 flex items-center justify-between gap-3"><h2 class="text-sm font-semibold text-[#283052]">Ketersediaan produk</h2><span class="text-xs text-[#7b8299]">${stockItems.length} lokasi</span></div><div class="public-stock-list">${stock}</div></section>
                            <a href="/business/${Number(data.business.id)}" class="public-product-store-link mt-6"><span class="public-store-link-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 10.5 12 4l9 6.5M5 9.5V20h14V9.5M9 20v-6h6v6"/></svg></span><span><small>Dijual oleh</small><strong>${escapeHtml(data.business.name)}</strong><em>${escapeHtml(data.business.address || 'Lihat profil dan produk toko')}</em></span><span class="public-store-link-arrow" aria-hidden="true">→</span></a>
                        </div>
                    </div>
                </article>
            `;
            root.querySelector('[data-product-detail-loading]').classList.add('hidden');
            root.querySelector('[data-product-detail-content]').classList.remove('hidden');
        })
        .catch((requestError) => {
            root.querySelector('[data-product-detail-loading]').classList.add('hidden');
            error.textContent = apiMessage(requestError);
            error.classList.remove('hidden');
        });
}

function initializeMemberDashboard(root) {
    apiRequest('/member/dashboard').then(({ data }) => {
        root.querySelector('[data-member-points]').textContent = new Intl.NumberFormat('id-ID').format(data.points);
        root.querySelector('[data-member-purchase-count]').textContent = new Intl.NumberFormat('id-ID').format(data.purchase_count);
        root.querySelector('[data-member-coupon-count]').textContent = new Intl.NumberFormat('id-ID').format(data.active_coupons);
        root.querySelector('[data-member-unread-count]').textContent = new Intl.NumberFormat('id-ID').format(data.unread_notifications);
    }).catch((error) => setMessage(root.querySelector('[data-member-message]'), apiMessage(error), true));
}

function initializeMemberList(root) {
    const kind = root.dataset.memberList;
    const loading = root.querySelector('[data-member-loading]');
    const empty = root.querySelector('[data-member-empty]');
    const content = root.querySelector('[data-member-content]');
    const rows = root.querySelector('[data-member-rows]');
    const message = root.querySelector('[data-member-message]');
    const route = { sales: '/member/sales', coupons: '/member/coupons', notifications: '/member/notifications' }[kind];

    const load = async () => {
        loading?.classList.remove('hidden');
        content?.classList.add('hidden');
        empty?.classList.add('hidden');

        try {
            const { data } = await apiRequest(route);
            const items = data.items || [];
            loading?.classList.add('hidden');
            if (items.length === 0) {
                empty?.classList.remove('hidden');
                return;
            }

            rows.innerHTML = items.map((item) => {
                if (kind === 'sales') {
                    const pointsUsed = Number(item.points_redeemed || 0);
                    const pointsReceived = Number(item.points_to_member || 0);
                    const pointSummary = [
                        pointsUsed > 0 ? `${pointsUsed.toLocaleString('id-ID')} digunakan` : null,
                        pointsReceived > 0 ? `${pointsReceived.toLocaleString('id-ID')} diterima` : null,
                    ].filter(Boolean).join(' · ') || '—';
                    return `<tr class="border-b border-[#eef3ff]"><td>${escapeHtml(formatDate(item.transaction_date))}</td><td>${escapeHtml(item.business?.name || 'Toko')}</td><td>${escapeHtml(item.invoice_number)}</td><td>${escapeHtml(formatRupiah(item.total))}</td><td>${escapeHtml(pointSummary)}</td><td>${escapeHtml(item.status)}</td></tr>`;
                }
                if (kind === 'coupons') {
                    const active = item.status === 'active' && new Date(item.expires_at).getTime() > Date.now();
                    return `<article class="ni-panel p-5"><div class="flex items-start justify-between gap-3"><div><p class="text-xs font-semibold uppercase tracking-[0.15em] text-[#687EFF]">${escapeHtml(item.business?.name || 'Toko')}</p><h2 class="mt-2 text-xl font-semibold">${escapeHtml(item.discount_value)}% diskon</h2></div><span class="rounded-full px-3 py-1 text-xs font-semibold ${active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600'}">${active ? 'Aktif' : escapeHtml(item.status)}</span></div><p class="mt-3 text-sm text-[#5e647f]">Minimal belanja ${escapeHtml(formatRupiah(item.min_transaction))}</p><p class="mt-4 rounded-xl border border-dashed border-[#cfdcff] bg-[#f7f9ff] p-3 text-center font-mono text-lg font-bold tracking-widest">${escapeHtml(item.code)}</p><p class="mt-3 text-xs text-[#7b8299]">Berlaku sampai ${escapeHtml(formatDate(item.expires_at))}</p></article>`;
                }

                return `<article class="ni-panel flex flex-col gap-3 p-5 sm:flex-row sm:items-start sm:justify-between ${item.read_at ? 'opacity-70' : ''}"><div><div class="flex flex-wrap items-center gap-2"><h2 class="font-semibold">${escapeHtml(item.title)}</h2>${item.read_at ? '' : '<span class="rounded-full bg-[#eef3ff] px-2 py-1 text-[10px] font-semibold text-[#5265d8]">Baru</span>'}</div><p class="mt-2 text-sm leading-6 text-[#5e647f]">${escapeHtml(item.message)}</p><p class="mt-2 text-xs text-[#7b8299]">${escapeHtml(item.business?.name || 'Nivora')} · ${escapeHtml(formatDate(item.created_at))}</p></div>${item.read_at ? '' : `<button type="button" data-notification-read="${Number(item.id)}" class="shrink-0 text-sm font-semibold text-[#687EFF]">Tandai dibaca</button>`}</article>`;
            }).join('');
            content?.classList.remove('hidden');
        } catch (error) {
            loading?.classList.add('hidden');
            setMessage(message, apiMessage(error), true);
        }
    };

    root.addEventListener('click', async (event) => {
        const readButton = event.target.closest('[data-notification-read]');
        if (!readButton) {
            return;
        }

        try {
            await apiRequest(`/member/notifications/${Number(readButton.dataset.notificationRead)}/read`, { method: 'PATCH' });
            await load();
        } catch (error) {
            setMessage(message, apiMessage(error), true);
        }
    });

    load();
}

function initializeWallet(root) {
    apiRequest('/member/wallet').then(({ data }) => {
        root.querySelector('[data-member-wallet-balance]').textContent = Number(data.balance).toLocaleString('id-ID');
        const loading = root.querySelector('[data-member-loading]');
        const empty = root.querySelector('[data-member-empty]');
        const content = root.querySelector('[data-member-content]');
        const rows = root.querySelector('[data-member-rows]');
        loading.classList.add('hidden');
        if (data.transactions.length === 0) {
            empty.classList.remove('hidden');
            return;
        }

        rows.innerHTML = data.transactions.map((item) => {
            const incoming = item.to_wallet?.wallet_type === 'member';
            return `<tr class="border-b border-[#eef3ff]"><td>${escapeHtml(formatDate(item.created_at))}</td><td>${escapeHtml(item.description || item.type)}</td><td class="font-semibold ${incoming ? 'text-emerald-700' : 'text-rose-700'}">${incoming ? '+' : '−'}${Number(item.points).toLocaleString('id-ID')}</td><td>${Number(incoming ? item.to_balance_after : item.from_balance_after || 0).toLocaleString('id-ID')}</td></tr>`;
        }).join('');
        content.classList.remove('hidden');
    }).catch((error) => setMessage(root.querySelector('[data-member-message]'), apiMessage(error), true));
}

function initializeBusinessApplication(root) {
    const form = root.querySelector('[data-business-application-form]');
    const status = root.querySelector('[data-application-status]');
    const message = root.querySelector('[data-member-message]');
    const submit = form.querySelector('[type="submit"]');

    const load = async () => {
        try {
            const { data } = await apiRequest('/member/business-applications');
            status.innerHTML = data.length ? data.map((application) => `
                <article class="rounded-xl border border-[#e7ebff] bg-[#f7f9ff] p-3"><div class="flex flex-wrap items-center justify-between gap-2"><p class="font-semibold text-[#1f2544]">${escapeHtml(application.business_name)}</p><span>${escapeHtml(application.status)}</span></div><p class="mt-1">Dikirim ${escapeHtml(formatDate(application.created_at))}${application.business ? ` · ${escapeHtml(application.business.name)}` : ''}</p>${application.review_note ? `<p class="mt-2">Catatan: ${escapeHtml(application.review_note)}</p>` : ''}</article>
            `).join('') : '<p>Belum ada pengajuan.</p>';
            const pending = data.some((application) => application.status === 'pending');
            form.classList.toggle('hidden', pending || data.some((application) => application.status === 'approved'));
            if (pending) {
                setMessage(message, 'Pengajuan Anda sedang menunggu tinjauan Super Admin.');
            }

            const approvedApplication = data.find((application) => application.status === 'approved');
            const session = getSession();
            if (approvedApplication && session && !session.user?.roles?.some((role) => role.name === 'owner')) {
                const { data: user } = await apiRequest('/auth/me');
                session.user = user;
                session.businessId = approvedApplication.business_id;
                session.businessName = approvedApplication.business?.name || approvedApplication.business_name;
                const { data: outlets } = await apiRequest('/outlets?limit=100');
                session.businessId = outlets.items[0]?.business_id || null;
                window.localStorage.setItem('nivora.session', JSON.stringify(session));
                window.location.assign('/owner');
            }
        } catch (error) {
            status.textContent = apiMessage(error);
        }
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        submit.disabled = true;
        try {
            const body = Object.fromEntries(new FormData(form).entries());
            await apiRequest('/business-applications', { method: 'POST', body: JSON.stringify(body) });
            form.reset();
            setMessage(message, 'Pengajuan terkirim dan menunggu persetujuan Super Admin.');
            await load();
        } catch (error) {
            setMessage(message, apiMessage(error), true);
        } finally {
            submit.disabled = false;
        }
    });

    load();
}

function initializeCashierManagement(root) {
    const createForm = root.querySelector('[data-cashier-create]');
    const fundForm = root.querySelector('[data-cashier-fund]');
    const message = root.querySelector('[data-cashier-message]');
    const loading = root.querySelector('[data-cashier-loading]');
    const empty = root.querySelector('[data-cashier-empty]');
    const table = root.querySelector('[data-cashier-table-wrap]');
    const rows = root.querySelector('[data-cashier-rows]');
    const cashierSelect = root.querySelector('[data-cashier-select]');
    const count = root.querySelector('[data-cashier-count]');

    const load = async () => {
        try {
            const { data } = await apiRequest('/cashiers');
            loading.classList.add('hidden');
            cashierSelect.innerHTML = '<option value="">Pilih kasir</option>';
            if (count) {
                count.textContent = `${data.items.length} kasir aktif`;
            }
            if (data.items.length === 0) {
                empty.classList.remove('hidden');
                table.classList.add('hidden');
                return;
            }

            empty.classList.add('hidden');
            table.classList.remove('hidden');
            rows.innerHTML = data.items.map((cashier) => {
                cashierSelect.insertAdjacentHTML('beforeend', `<option value="${Number(cashier.id)}">${escapeHtml(cashier.name)} · ${escapeHtml(cashier.email)}</option>`);
                const role = cashier.roles?.find((item) => item.name === 'cashier');
                const isActive = cashier.status === 'active';
                const initials = cashier.name.split(/\s+/).slice(0, 2).map((part) => part.charAt(0)).join('').toLocaleUpperCase('id-ID');
                return `<tr class="cashier-roster-row"><td><div class="flex items-center gap-3"><span class="cashier-avatar flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#eef1ff] text-xs font-bold text-[#536dfe]">${escapeHtml(initials)}</span><span class="min-w-0"><span class="block truncate font-semibold text-[#303858]">${escapeHtml(cashier.name)}</span><span class="mt-0.5 block truncate text-xs text-[#858ca4]">${escapeHtml(cashier.email)}</span></span></div></td><td><span class="inline-flex items-center gap-2 text-sm font-medium text-[#58617f]"><svg viewBox="0 0 24 24" class="h-4 w-4 text-[#687eff]" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M3 21V8l9-5 9 5v13M3 10h18M8 21v-6h8v6"/></svg>${escapeHtml(cashier.outlet_name || (role?.business_id ? `Toko #${role.business_id}` : 'Toko'))}</span></td><td><span class="font-semibold text-[#303858]">${Number(cashier.point_balance || 0).toLocaleString('id-ID')}</span><span class="ml-1 text-xs text-[#858ca4]">poin</span></td><td><span class="inline-flex items-center gap-2 rounded-full ${isActive ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600'} px-2.5 py-1 text-xs font-semibold"><span class="h-1.5 w-1.5 rounded-full ${isActive ? 'bg-emerald-500' : 'bg-slate-400'}"></span>${isActive ? 'Aktif' : 'Nonaktif'}</span></td></tr>`;
            }).join('');
        } catch (error) {
            loading.textContent = apiMessage(error);
        }
    };

    createForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submit = createForm.querySelector('[type="submit"]');
        submit.disabled = true;
        try {
            const body = Object.fromEntries(new FormData(createForm).entries());
            await apiRequest('/cashiers', { method: 'POST', body: JSON.stringify(body) });
            createForm.reset();
            setMessage(message, 'Akun kasir dibuat. Bagikan email dan password sementara secara aman.');
            await load();
        } catch (error) {
            setMessage(message, apiMessage(error), true);
        } finally {
            submit.disabled = false;
        }
    });

    fundForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submit = fundForm.querySelector('[type="submit"]');
        submit.disabled = true;
        try {
            const data = new FormData(fundForm);
            const cashierId = Number(data.get('cashier_id'));
            await apiRequest(`/cashiers/${cashierId}/points`, { method: 'POST', body: JSON.stringify({ points: Number(data.get('points')) }) });
            fundForm.reset();
            setMessage(message, 'Saldo poin kasir berhasil ditambah.');
            await load();
        } catch (error) {
            setMessage(message, apiMessage(error), true);
        } finally {
            submit.disabled = false;
        }
    });

    load();
}

function initializeSuperAdminApplications(root) {
    const loading = root.querySelector('[data-application-loading]');
    const empty = root.querySelector('[data-application-empty]');
    const list = root.querySelector('[data-application-list]');
    const message = root.querySelector('[data-application-message]');
    let filter = 'pending';

    const load = async () => {
        loading.classList.remove('hidden');
        empty.classList.add('hidden');
        list.classList.add('hidden');
        try {
            const { data } = await apiRequest(`/super-admin/business-applications${filter === 'pending' ? '?status=pending' : ''}`);
            loading.classList.add('hidden');
            if (data.items.length === 0) {
                empty.classList.remove('hidden');
                return;
            }

            list.innerHTML = data.items.map((item) => {
                const pending = item.status === 'pending';
                const statusLabel = { pending: 'Menunggu review', approved: 'Disetujui', rejected: 'Ditolak' }[item.status] || item.status;
                const statusStyle = pending ? 'bg-amber-50 text-amber-700' : item.status === 'approved' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700';

                return `
                    <article class="application-card group overflow-hidden rounded-2xl border border-[#e7ebf7] bg-white shadow-[0_8px_24px_rgba(35,48,97,0.04)] transition duration-200 hover:-translate-y-0.5 hover:border-[#cbd4ff] hover:shadow-[0_16px_34px_rgba(35,48,97,0.08)]">
                        <div class="application-card-header flex flex-col gap-4 border-b border-[#edf0f8] p-5 sm:flex-row sm:items-start sm:justify-between sm:p-6">
                            <div class="flex min-w-0 gap-4"><span class="application-business-icon flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-[#eef1ff] to-[#e9f8ff] text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M3 21V8l9-5 9 5v13M3 10h18M8 21v-6h8v6"/></svg></span><div class="min-w-0"><p class="text-[10px] font-bold uppercase tracking-[0.16em] text-[#687eff]">${escapeHtml(item.business_type)}</p><h3 class="mt-1 truncate text-xl font-semibold tracking-tight text-[#20284a]">${escapeHtml(item.business_name)}</h3><p class="mt-1.5 flex flex-wrap items-center gap-x-1.5 text-sm text-[#737b96]"><svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="12" cy="8" r="3.5"/><path d="M5 20v-1a7 7 0 0 1 14 0v1"/></svg><span class="font-medium text-[#4f5879]">${escapeHtml(item.applicant?.name || 'Pemohon')}</span><span aria-hidden="true">·</span><span>${escapeHtml(item.applicant?.email || '—')}</span></p></div></div>
                            <span class="inline-flex w-fit shrink-0 items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold ${statusStyle}"><span class="application-status-dot h-1.5 w-1.5 rounded-full ${pending ? 'bg-amber-500' : item.status === 'approved' ? 'bg-emerald-500' : 'bg-rose-500'}"></span>${escapeHtml(statusLabel)}</span>
                        </div>
                        <div class="application-meta-grid grid gap-px bg-[#edf0f8] sm:grid-cols-2 lg:grid-cols-4">
                            <div class="application-meta-cell bg-[#fcfcff] p-4"><p class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.12em] text-[#969db2]"><svg viewBox="0 0 24 24" class="h-3.5 w-3.5 text-[#687eff]" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6.5 3.5h11A2.5 2.5 0 0 1 20 6v12a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 18V6a2.5 2.5 0 0 1 2.5-2.5Z"/><path d="M8 7h8m-8 4h8m-8 4h5"/></svg>Outlet pertama</p><p class="mt-2 text-sm font-semibold text-[#39415f]">${escapeHtml(item.outlet_name || item.business_name)}</p></div>
                            <div class="application-meta-cell bg-[#fcfcff] p-4"><p class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.12em] text-[#969db2]"><svg viewBox="0 0 24 24" class="h-3.5 w-3.5 text-[#687eff]" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M7 3v3m10-3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v13H4V6a1 1 0 0 1 1-1Z"/></svg>Tanggal pengajuan</p><p class="mt-2 text-sm font-semibold text-[#39415f]">${escapeHtml(formatDate(item.created_at))}</p></div>
                            <div class="application-meta-cell bg-[#fcfcff] p-4"><p class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.12em] text-[#969db2]"><svg viewBox="0 0 24 24" class="h-3.5 w-3.5 text-[#687eff]" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 3h14v18H5zM8 7h8m-8 4h8m-8 4h5"/></svg>Kontak pemohon</p><p class="mt-2 text-sm font-semibold text-[#39415f]">${escapeHtml(item.applicant?.phone || item.phone || 'Belum dicantumkan')}</p></div>
                            <div class="application-meta-cell bg-[#fcfcff] p-4"><p class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.12em] text-[#969db2]"><svg viewBox="0 0 24 24" class="h-3.5 w-3.5 text-[#687eff]" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11Z"/><circle cx="12" cy="10" r="2.2"/></svg>Alamat bisnis</p><p class="mt-2 line-clamp-2 text-sm font-semibold text-[#39415f]">${escapeHtml(item.address || 'Belum dicantumkan')}</p></div>
                        </div>
                        ${pending ? `<div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between sm:px-6"><p class="text-xs text-[#858ca4]">Tinjau detail sebelum memberikan akses kepada Owner.</p><div class="flex flex-wrap gap-2"><button type="button" data-application-reject="${Number(item.id)}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-rose-200 bg-white px-3.5 text-sm font-semibold text-rose-700 transition hover:border-rose-300 hover:bg-rose-50 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-rose-500/10"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>Tolak</button><button type="button" data-application-approve="${Number(item.id)}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 px-4 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-emerald-500/15"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m5 12 4.5 4.5L19 7"/></svg>Setujui & buat Owner</button></div></div>` : ''}
                    </article>`;
            }).join('');
            list.classList.remove('hidden');
        } catch (error) {
            loading.classList.add('hidden');
            setMessage(message, apiMessage(error), true);
        }
    };

    root.querySelectorAll('[data-application-filter]').forEach((button) => button.addEventListener('click', () => {
        filter = button.dataset.applicationFilter;
        root.querySelectorAll('[data-application-filter]').forEach((filterButton) => {
            const selected = filterButton === button;
            filterButton.setAttribute('aria-pressed', String(selected));
            filterButton.className = selected
                ? 'inline-flex min-h-10 items-center gap-2 rounded-lg bg-white px-3.5 text-sm font-semibold text-[#39436f] shadow-sm transition'
                : 'inline-flex min-h-10 items-center gap-2 rounded-lg px-3.5 text-sm font-semibold text-[#858ca4] transition hover:text-[#39436f]';
        });
        load();
    }));
    root.addEventListener('click', async (event) => {
        const approve = event.target.closest('[data-application-approve]');
        const reject = event.target.closest('[data-application-reject]');
        if (!approve && !reject) {
            return;
        }

        const id = Number((approve || reject).dataset.applicationApprove || (approve || reject).dataset.applicationReject);
        const note = reject ? window.prompt('Catatan penolakan (opsional):') : null;
        if (reject && note === null) {
            return;
        }

        try {
            const suffix = approve ? 'approve' : 'reject';
            await apiRequest(`/super-admin/business-applications/${id}/${suffix}`, {
                method: 'POST',
                body: JSON.stringify(reject ? { review_note: note } : {}),
            });
            setMessage(message, approve ? 'Pengajuan disetujui dan akses Owner dibuat.' : 'Pengajuan ditolak.');
            await load();
        } catch (error) {
            setMessage(message, apiMessage(error), true);
        }
    });

    load();
}

function initializeSuperAdminBusinessSelector(select) {
    apiRequest('/super-admin/business-options').then(({ data }) => {
        const session = getSession();
        if (!session) {
            return;
        }

        if (data.length === 0) {
            select.innerHTML = '<option value="">Belum ada bisnis</option>';
            select.disabled = true;
            return;
        }

        select.innerHTML = data.map((business) => `<option value="${Number(business.id)}">${escapeHtml(business.name)}</option>`).join('');
        let selected = data.find((business) => Number(business.id) === Number(session.businessId));
        if (!selected) {
            selected = data[0];
            session.businessId = selected.id;
            session.businessName = selected.name;
            window.localStorage.setItem('nivora.session', JSON.stringify(session));
        }
        select.value = String(selected.id);

        select.addEventListener('change', () => {
            const business = data.find((item) => Number(item.id) === Number(select.value));
            if (!business) {
                return;
            }

            const updatedSession = getSession();
            if (!updatedSession) {
                return;
            }
            updatedSession.businessId = business.id;
            updatedSession.businessName = business.name;
            window.localStorage.setItem('nivora.session', JSON.stringify(updatedSession));
            window.location.reload();
        });
    }).catch((error) => {
        select.innerHTML = `<option value="">${escapeHtml(apiMessage(error))}</option>`;
        select.disabled = true;
    });
}

function initializeSuperAdminStores(root) {
    const pageMessage = root.querySelector('[data-admin-stores-message]');
    const createModal = document.getElementById('store-create-modal');
    const editModal = document.getElementById('store-edit-modal');
    const createForm = createModal.querySelector('[data-store-form="create"]');
    const editForm = editModal.querySelector('[data-store-form="edit"]');

    const showFormMessage = (form, text) => {
        const element = form.querySelector('[data-store-form-message]');
        element.textContent = text;
        element.className = 'rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 sm:col-span-2';
    };

    const setPhotoPreview = (form, source) => {
        const preview = form.querySelector('[data-store-photo-preview]');
        const emptyHint = form.querySelector('[data-store-photo-empty]');
        preview.src = source || '';
        preview.classList.toggle('hidden', !source);
        emptyHint.classList.toggle('hidden', Boolean(source));
    };

    createForm.dataset.currentPhoto = createForm.dataset.defaultPhoto;
    setPhotoPreview(createForm, createForm.dataset.defaultPhoto);

    [createForm, editForm].forEach((form) => {
        const photo = form.elements.namedItem('photo');
        photo.addEventListener('change', () => {
            const file = photo.files?.[0];
            setPhotoPreview(form, file ? URL.createObjectURL(file) : form.dataset.currentPhoto || '');
        });
    });

    root.addEventListener('click', async (event) => {
        const editButton = event.target.closest('[data-store-edit]');
        if (editButton) {
            const business = JSON.parse(editButton.dataset.business);
            editForm.reset();
            editForm.querySelector('[data-store-form-message]').classList.add('hidden');
            ['name', 'location_name', 'business_type', 'phone', 'address', 'status'].forEach((key) => {
                editForm.elements.namedItem(key).value = business[key] || (key === 'status' ? 'active' : '');
            });
            editForm.dataset.businessId = String(business.id);
            editForm.dataset.currentPhoto = editButton.dataset.logoUrl || editForm.dataset.defaultPhoto;
            editForm.querySelector('[data-store-remove-photo-wrap]').classList.toggle('hidden', !business.logo);
            setPhotoPreview(editForm, editForm.dataset.currentPhoto);
            editModal.classList.remove('hidden');
            editModal.classList.add('flex');
            return;
        }

        const deleteButton = event.target.closest('[data-store-delete]');
        if (!deleteButton || !window.confirm(`Hapus toko ${deleteButton.dataset.storeName}? Penghapusan hanya dapat dilakukan jika toko belum memiliki data operasional.`)) {
            return;
        }

        deleteButton.disabled = true;
        try {
            const { message: responseMessage } = await apiRequest(`/super-admin/businesses/${Number(deleteButton.dataset.storeDelete)}`, { method: 'DELETE' });
            setMessage(pageMessage, responseMessage || 'Toko berhasil dihapus.');
            window.location.reload();
        } catch (error) {
            setMessage(pageMessage, apiMessage(error), true);
        } finally {
            deleteButton.disabled = false;
        }
    });

    [createForm, editForm].forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const submit = form.querySelector('[type="submit"]');
            const formMessage = form.querySelector('[data-store-form-message]');
            submit.disabled = true;
            formMessage.classList.add('hidden');

            const formData = new FormData(form);
            if (form.dataset.storeForm === 'edit') {
                formData.append('_method', 'PATCH');
                const photoFile = form.elements.namedItem('photo').files?.[0];
                if (!photoFile) {
                    formData.delete('photo');
                }
            }

            try {
                const isCreate = form.dataset.storeForm === 'create';
                await apiRequest(isCreate ? '/super-admin/businesses' : `/super-admin/businesses/${Number(form.dataset.businessId)}`, {
                    method: 'POST',
                    body: formData,
                });
                setMessage(pageMessage, isCreate ? 'Toko berhasil ditambahkan.' : 'Profil toko berhasil diperbarui.');
                window.location.reload();
            } catch (error) {
                showFormMessage(form, apiMessage(error));
            } finally {
                submit.disabled = false;
            }
        });
    });

    createForm.addEventListener('reset', () => {
        createForm.dataset.currentPhoto = createForm.dataset.defaultPhoto;
        setPhotoPreview(createForm, createForm.dataset.defaultPhoto);
        createForm.querySelector('[data-store-form-message]').classList.add('hidden');
    });
}

function initializeSuperAdminMembers(root) {
    const searchForm = root.querySelector('[data-admin-members-search]');
    const loading = root.querySelector('[data-admin-members-loading]');
    const empty = root.querySelector('[data-admin-members-empty]');
    const list = root.querySelector('[data-admin-members-list]');
    const message = root.querySelector('[data-admin-members-message]');
    const count = root.querySelector('[data-admin-members-count]');
    const previousButton = root.querySelector('[data-admin-members-prev]');
    const nextButton = root.querySelector('[data-admin-members-next]');
    const pageLabel = root.querySelector('[data-admin-members-page]');
    const roleFilter = root.querySelector('[data-admin-account-filter]');
    const contentContainer = root.parentElement;
    const createForm = contentContainer.querySelector('[data-admin-account-create]');
    const createMessage = createForm.querySelector('[data-admin-account-create-message]');
    const roleSelect = createForm.querySelector('[data-admin-account-role]');
    const businessWrap = createForm.querySelector('[data-admin-account-business-wrap]');
    const businessSelect = createForm.querySelector('[data-admin-account-business]');
    const createModal = document.getElementById('account-create-modal');
    const editModal = document.getElementById('account-edit-modal');
    const editForm = contentContainer.querySelector('[data-admin-account-edit]');
    const accountsById = new Map();
    let currentSearch = '';
    let currentPage = 1;
    let lastPage = 1;

    const updateRoleFields = () => {
        const isOwner = roleSelect.value === 'owner';
        businessWrap.classList.toggle('hidden', !isOwner);
        businessSelect.disabled = !isOwner || !businessSelect.value;
        businessSelect.required = isOwner;
    };

    apiRequest('/super-admin/business-options').then(({ data }) => {
        businessSelect.innerHTML = data.length
            ? data.map((business) => `<option value="${Number(business.id)}">${escapeHtml(business.name)}</option>`).join('')
            : '<option value="">Belum ada toko aktif</option>';
        if (data.length === 0) {
            roleSelect.querySelector('option[value="owner"]').disabled = true;
        }
        updateRoleFields();
    }).catch((error) => {
        businessSelect.innerHTML = `<option value="">${escapeHtml(apiMessage(error))}</option>`;
        roleSelect.querySelector('option[value="owner"]').disabled = true;
        updateRoleFields();
    });

    roleSelect.addEventListener('change', updateRoleFields);
    createForm.addEventListener('click', (event) => {
        const toggle = event.target.closest('[data-toggle-password]');
        if (!toggle) {
            return;
        }
        const password = document.getElementById(toggle.dataset.togglePassword);
        const isVisible = password.type === 'password';
        password.type = isVisible ? 'text' : 'password';
        toggle.setAttribute('aria-pressed', String(isVisible));
        toggle.setAttribute('aria-label', isVisible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        toggle.querySelector('[data-password-eye-slash]')?.classList.toggle('hidden', !isVisible);
    });
    createForm.addEventListener('submit', () => createMessage.classList.add('hidden'));

    const load = async (search = currentSearch, page = currentPage) => {
        currentSearch = search;
        currentPage = page;
        loading.classList.remove('hidden');
        empty.classList.add('hidden');
        list.classList.add('hidden');
        try {
            const query = new URLSearchParams({ limit: '20', page: String(page) });
            if (search) {
                query.set('search', search);
            }
            if (roleFilter.value) {
                query.set('role', roleFilter.value);
            }
            const { data } = await apiRequest(`/super-admin/accounts?${query.toString()}`);
            const members = data.items || [];
            const pagination = data.pagination || {};
            currentPage = Number(pagination.page || 1);
            lastPage = Math.max(1, Math.ceil(Number(pagination.total || 0) / Number(pagination.limit || 20)));
            loading.classList.add('hidden');
            const total = Number(pagination.total || 0);
            const firstRecord = total === 0 ? 0 : ((currentPage - 1) * Number(pagination.limit || 20)) + 1;
            const lastRecord = Math.min(currentPage * Number(pagination.limit || 20), total);
            count.textContent = `Menampilkan ${firstRecord}–${lastRecord} dari ${total.toLocaleString('id-ID')} akun`;
            pageLabel.textContent = `${currentPage} / ${lastPage}`;
            previousButton.disabled = currentPage <= 1;
            nextButton.disabled = currentPage >= lastPage;
            if (members.length === 0) {
                empty.classList.remove('hidden');
                return;
            }

            accountsById.clear();
            list.innerHTML = `
                <table class="min-w-[980px] w-full text-left">
                    <thead class="bg-[#f8f9fe] text-[11px] font-bold uppercase tracking-[0.1em] text-[#8a91a9]">
                        <tr><th class="px-6 py-3.5">Akun</th><th class="px-4 py-3.5">Peran</th><th class="px-4 py-3.5">Toko</th><th class="px-4 py-3.5">Telepon</th><th class="px-4 py-3.5">Status</th><th class="px-4 py-3.5">Bergabung</th><th class="px-6 py-3.5 text-right">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-[#edf0f8]">${members.map((member) => {
                        accountsById.set(Number(member.id), member);
                        const initials = member.name.split(/\s+/).filter(Boolean).slice(0, 2).map((part) => part[0]).join('').toUpperCase();
                        const roles = (member.roles || []).filter((role) => ['owner', 'member'].includes(role.name));
                        const roleBadges = roles.map((role) => `<span class="account-role-badge ${role.name === 'owner' ? 'account-role-owner' : 'account-role-member'} inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold ${role.name === 'owner' ? 'bg-violet-50 text-violet-700' : 'bg-sky-50 text-sky-700'}">${role.name === 'owner' ? 'Owner' : 'Member'}</span>`).join(' ');
                        const businessNames = member.businesses?.length ? member.businesses.join(', ') : '—';
                        const statusLabel = member.status === 'active' ? 'Aktif' : 'Nonaktif';
                        const statusClass = member.status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600';

                        return `<tr class="transition hover:bg-[#fafbff]">
                            <td class="px-6 py-4"><div class="flex min-w-[210px] items-center gap-3"><span class="member-avatar flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-[#e8edff] to-[#dff5ff] text-xs font-bold text-[#596ee6]">${escapeHtml(initials)}</span><span class="min-w-0"><span class="block truncate text-sm font-semibold text-[#283052]">${escapeHtml(member.name)}</span><span class="mt-0.5 block truncate text-xs text-[#8a91a9]">${escapeHtml(member.email)}</span></span></div></td>
                            <td class="px-4 py-4"><div class="flex min-w-[90px] flex-wrap gap-1.5">${roleBadges || '—'}</div></td>
                            <td class="max-w-[190px] px-4 py-4 text-sm text-[#66708d]">${escapeHtml(businessNames)}</td>
                            <td class="whitespace-nowrap px-4 py-4 text-sm text-[#66708d]">${escapeHtml(member.phone || '—')}</td>
                            <td class="px-4 py-4"><span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ${statusClass}"><span class="status-dot h-2 w-2 shrink-0 rounded-full ${member.status === 'active' ? 'bg-emerald-500' : 'bg-slate-400'}"></span>${statusLabel}</span></td>
                            <td class="whitespace-nowrap px-4 py-4 text-xs text-[#8a91a9]">${escapeHtml(formatDate(member.created_at))}</td>
                            <td class="px-6 py-4 text-right"><div class="flex justify-end gap-2"><button type="button" data-account-edit="${Number(member.id)}" aria-label="Edit akun ${escapeHtml(member.name)}" class="group inline-flex items-center gap-1.5 rounded-xl border border-[#e3e8f6] bg-white px-3 py-2 text-xs font-semibold text-[#58617f] shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-[#b8c4ff] hover:bg-[#f7f8ff] hover:text-[#536dfe] hover:shadow-md focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#536dfe]/15"><svg viewBox="0 0 24 24" class="h-3.5 w-3.5 transition group-hover:rotate-[-6deg]" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m14 5 5 5M4 20l4.5-1 11-11a2.1 2.1 0 0 0-3-3l-11 11L4 20Z"/></svg>Edit</button><button type="button" data-account-delete="${Number(member.id)}" aria-label="Hapus akun ${escapeHtml(member.name)}" class="group inline-flex items-center gap-1.5 rounded-xl border border-rose-100 bg-white px-3 py-2 text-xs font-semibold text-rose-600 transition duration-200 hover:-translate-y-0.5 hover:border-rose-300 hover:bg-rose-50 hover:shadow-sm focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-rose-500/15"><svg viewBox="0 0 24 24" class="h-3.5 w-3.5 transition group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M10 11v6m4-6v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>Hapus</button></div></td>
                        </tr>`;
                    }).join('')}</tbody>
                </table>`;
            list.classList.remove('hidden');
        } catch (error) {
            loading.classList.add('hidden');
            setMessage(message, apiMessage(error), true);
        }
    };

    searchForm.addEventListener('submit', (event) => {
        event.preventDefault();
        load(new FormData(searchForm).get('search')?.toString().trim() || '', 1);
    });
    roleFilter.addEventListener('change', () => load(currentSearch, 1));
    previousButton.addEventListener('click', () => load(currentSearch, currentPage - 1));
    nextButton.addEventListener('click', () => load(currentSearch, currentPage + 1));
    list.addEventListener('click', (event) => {
        const deleteButton = event.target.closest('[data-account-delete]');
        if (deleteButton) {
            const account = accountsById.get(Number(deleteButton.dataset.accountDelete));
            if (!account || !window.confirm(`Hapus akun ${account.name}? Tindakan ini tidak dapat dibatalkan.`)) {
                return;
            }

            deleteButton.disabled = true;
            apiRequest(`/super-admin/users/${Number(account.id)}`, { method: 'DELETE' }).then(async ({ message: responseMessage }) => {
                setMessage(message, responseMessage || 'Akun berhasil dihapus.');
                await load(currentSearch, currentPage);
            }).catch((error) => {
                setMessage(message, apiMessage(error), true);
            }).finally(() => {
                deleteButton.disabled = false;
            });
            return;
        }

        const editButton = event.target.closest('[data-account-edit]');
        if (!editButton) {
            return;
        }
        const account = accountsById.get(Number(editButton.dataset.accountEdit));
        if (!account) {
            return;
        }
        editForm.elements.namedItem('name').value = account.name || '';
        editForm.elements.namedItem('email').value = account.email || '';
        editForm.elements.namedItem('phone').value = account.phone || '';
        editForm.elements.namedItem('status').value = account.status || 'active';
        editForm.elements.namedItem('password').value = '';
        editForm.elements.namedItem('password_confirmation').value = '';
        const accountRoles = (account.roles || []).filter((role) => ['owner', 'member'].includes(role.name));
        const roleSummary = editForm.querySelector('[data-edit-account-role]');
        const businessSummary = editForm.querySelector('[data-edit-account-business]');
        roleSummary.textContent = accountRoles.map((role) => role.name === 'owner' ? 'Owner' : 'Member').join(' · ') || 'Akun';
        businessSummary.textContent = account.businesses?.length ? account.businesses.join(', ') : 'Belum terhubung ke toko';
        editForm.dataset.accountId = String(account.id);
        editModal.classList.remove('hidden');
        editModal.classList.add('flex');
    });

    editForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submit = editForm.querySelector('[type="submit"]');
        submit.disabled = true;
        const values = Object.fromEntries(new FormData(editForm).entries());
        if (!values.password) {
            delete values.password;
            delete values.password_confirmation;
        }
        try {
            await apiRequest(`/super-admin/users/${Number(editForm.dataset.accountId)}`, {
                method: 'PATCH',
                body: JSON.stringify(values),
            });
            setMessage(message, 'Profil dan status akun berhasil diperbarui.');
            editModal.classList.add('hidden');
            editModal.classList.remove('flex');
            await load(currentSearch, currentPage);
        } catch (error) {
            setMessage(message, apiMessage(error), true);
        } finally {
            submit.disabled = false;
        }
    });

    createForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submit = createForm.querySelector('[type="submit"]');
        submit.disabled = true;
        createMessage.classList.add('hidden');
        try {
            await apiRequest('/super-admin/accounts', {
                method: 'POST',
                body: JSON.stringify(Object.fromEntries(new FormData(createForm).entries())),
            });
            createForm.reset();
            createForm.querySelectorAll('[data-toggle-password]').forEach((toggle) => {
                toggle.setAttribute('aria-pressed', 'false');
                toggle.setAttribute('aria-label', 'Tampilkan kata sandi');
                toggle.querySelector('[data-password-eye-slash]')?.classList.add('hidden');
            });
            roleSelect.value = 'member';
            updateRoleFields();
            setMessage(message, 'Akun berhasil ditambahkan.');
            createModal.classList.add('hidden');
            createModal.classList.remove('flex');
            await load('', 1);
        } catch (error) {
            setMessage(createMessage, apiMessage(error), true);
        } finally {
            submit.disabled = false;
        }
    });

    load();
}

function initializeSuperAdminBusinessEdit(form) {
    const message = document.querySelector('[data-admin-business-message]');
    const photoInput = form.elements.namedItem('photo');
    const photoPreview = form.querySelector('[data-business-photo-preview]');
    photoInput?.addEventListener('change', () => {
        const file = photoInput.files?.[0];
        photoPreview.src = file ? URL.createObjectURL(file) : form.dataset.currentPhoto || form.dataset.defaultPhoto;
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submit = form.querySelector('[type="submit"]');
        submit.disabled = true;
        try {
            const formData = new FormData(form);
            formData.append('_method', 'PATCH');
            if (!photoInput?.files?.[0]) {
                formData.delete('photo');
            }
            await apiRequest(`/super-admin/businesses/${Number(form.dataset.superAdminBusinessEdit)}`, { method: 'POST', body: formData });
            setMessage(message, 'Profil dan status bisnis berhasil diperbarui.');
        } catch (error) {
            setMessage(message, apiMessage(error), true);
        } finally {
            submit.disabled = false;
        }
    });
}

function initializeSuperAdminUserEdit(form) {
    const message = document.querySelector('[data-admin-user-message]');
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submit = form.querySelector('[type="submit"]');
        submit.disabled = true;
        const values = Object.fromEntries(new FormData(form).entries());
        if (!values.password) {
            delete values.password;
            delete values.password_confirmation;
        }
        try {
            await apiRequest(`/super-admin/users/${Number(form.dataset.superAdminUserEdit)}`, {
                method: 'PATCH',
                body: JSON.stringify(values),
            });
            setMessage(message, 'Data akun berhasil diperbarui.');
        } catch (error) {
            setMessage(message, apiMessage(error), true);
        } finally {
            submit.disabled = false;
        }
    });
}

function initializeSuperAdminCategoryForm(form) {
    const message = form.querySelector('[data-admin-category-message]');
    const isCreate = form.hasAttribute('data-super-admin-category-create');
    const categoryId = Number(form.dataset.superAdminCategoryEdit || 0);

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submit = form.querySelector('[type="submit"]');
        submit.disabled = true;
        try {
            await apiRequest(isCreate ? '/super-admin/categories' : `/super-admin/categories/${categoryId}`, {
                method: isCreate ? 'POST' : 'PATCH',
                body: JSON.stringify(Object.fromEntries(new FormData(form).entries())),
            });
            const modal = form.closest('[role="dialog"]');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                window.location.reload();
            } else {
                window.location.assign('/super-admin/categories');
            }
        } catch (error) {
            setMessage(message, apiMessage(error), true);
        } finally {
            submit.disabled = false;
        }
    });
}

function initializeSuperAdminCategories(root) {
    const createForm = document.getElementById('category-create-modal')?.querySelector('[data-super-admin-category-create]');
    const editForm = root.querySelector('[data-super-admin-category-edit]');
    const editModal = document.getElementById('category-edit-modal');
    const pageMessage = root.querySelector('[data-admin-categories-message]');

    root.addEventListener('click', async (event) => {
        if (event.target.closest('[data-open-modal="category-create-modal"]')) {
            createForm?.reset();
            return;
        }

        const deleteButton = event.target.closest('[data-delete-category]');
        if (deleteButton) {
            const name = deleteButton.dataset.categoryName || 'kategori ini';
            if (!window.confirm(`Hapus ${name}? Kategori yang masih dipakai produk tidak dapat dihapus.`)) {
                return;
            }

            deleteButton.disabled = true;
            try {
                const { message } = await apiRequest(`/super-admin/categories/${Number(deleteButton.dataset.deleteCategory)}`, { method: 'DELETE' });
                setMessage(pageMessage, message || 'Kategori berhasil dihapus.');
                window.location.reload();
            } catch (error) {
                setMessage(pageMessage, apiMessage(error), true);
            } finally {
                deleteButton.disabled = false;
            }
            return;
        }

        const editButton = event.target.closest('[data-edit-category]');
        if (!editButton) {
            return;
        }

        editForm.elements.namedItem('name').value = editButton.dataset.categoryName || '';
        editForm.elements.namedItem('description').value = editButton.dataset.categoryDescription || '';
        editForm.dataset.superAdminCategoryEdit = editButton.dataset.editCategory;
        editForm.querySelector('[data-admin-category-message]').classList.add('hidden');
        editModal.classList.remove('hidden');
        editModal.classList.add('flex');
    });
}

export function bootMemberPages() {
    document.querySelectorAll('[data-member-business-detail]').forEach(initializeBusinessDetail);
    document.querySelectorAll('[data-member-product-detail]').forEach(initializeProductDetail);
    document.querySelectorAll('[data-member-dashboard]').forEach(initializeMemberDashboard);
    document.querySelectorAll('[data-member-list]').forEach(initializeMemberList);
    document.querySelectorAll('[data-member-wallet]').forEach(initializeWallet);
    document.querySelectorAll('[data-business-application]').forEach(initializeBusinessApplication);
    document.querySelectorAll('[data-cashier-management]').forEach(initializeCashierManagement);
    document.querySelectorAll('[data-super-admin-applications]').forEach(initializeSuperAdminApplications);
    document.querySelectorAll('[data-super-admin-business]').forEach(initializeSuperAdminBusinessSelector);
    document.querySelectorAll('[data-super-admin-stores]').forEach(initializeSuperAdminStores);
    document.querySelectorAll('[data-super-admin-members]').forEach(initializeSuperAdminMembers);
    document.querySelectorAll('[data-super-admin-business-edit]').forEach(initializeSuperAdminBusinessEdit);
    document.querySelectorAll('[data-super-admin-user-edit]').forEach(initializeSuperAdminUserEdit);
    document.querySelectorAll('[data-super-admin-category-create], [data-super-admin-category-edit]').forEach(initializeSuperAdminCategoryForm);
    document.querySelectorAll('[data-super-admin-categories]').forEach(initializeSuperAdminCategories);
}
