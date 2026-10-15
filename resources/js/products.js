import { apiMessage, apiRequest, escapeHtml, formatRupiah, pagedItems, requireSession } from './nivora-api';

export function bindPhotoDropzone(root, initialPhoto = null) {
    const input = root.querySelector('[data-product-photo]');
    const preview = root.querySelector('[data-photo-preview]');
    const empty = root.querySelector('[data-photo-empty]');
    const changeHint = root.querySelector('[data-photo-change]');
    const removePhoto = root.querySelector('[data-remove-product-photo]');

    if (!input || !preview || !empty) {
        return { showPhoto: () => {} };
    }

    let objectUrl = null;
    const showPhoto = (source) => {
        preview.classList.toggle('hidden', !source);
        empty.classList.toggle('hidden', Boolean(source));
        changeHint?.classList.toggle('hidden', !source);
        if (source) {
            preview.src = source;
        } else {
            preview.removeAttribute('src');
        }
    };

    const updateFromFile = (file) => {
        if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
            objectUrl = null;
        }

        if (!file) {
            showPhoto(initialPhoto);
            return;
        }

        objectUrl = URL.createObjectURL(file);
        showPhoto(objectUrl);
        if (removePhoto) {
            removePhoto.checked = false;
        }
    };

    input.addEventListener('change', () => updateFromFile(input.files?.[0] || null));
    root.addEventListener('dragover', (event) => {
        event.preventDefault();
        root.classList.add('border-[#536dfe]', 'bg-[#eef1ff]');
    });
    root.addEventListener('dragleave', (event) => {
        if (!root.contains(event.relatedTarget)) {
            root.classList.remove('border-[#536dfe]', 'bg-[#eef1ff]');
        }
    });
    root.addEventListener('drop', (event) => {
        event.preventDefault();
        root.classList.remove('border-[#536dfe]', 'bg-[#eef1ff]');
        const file = event.dataTransfer?.files?.[0];
        if (!file || !file.type.startsWith('image/')) {
            return;
        }

        const transfer = new DataTransfer();
        transfer.items.add(file);
        input.files = transfer.files;
        updateFromFile(file);
    });

    showPhoto(initialPhoto);

    return { showPhoto };
}

function initializeProductList(root) {
    if (!requireSession()) {
        return;
    }

    const elements = {
        alert: root.querySelector('[data-products-message]'),
        empty: root.querySelector('[data-products-empty]'),
        loading: root.querySelector('[data-products-loading]'),
        outlet: root.querySelector('[data-products-outlet]'),
        search: root.querySelector('[data-products-search]'),
        table: root.querySelector('[data-products-table]'),
        tableWrap: root.querySelector('[data-products-table-wrap]'),
        count: root.querySelector('[data-products-count]'),
        footer: root.querySelector('[data-products-footer]'),
        footerCount: root.querySelector('[data-products-footer-count]'),
    };
    const state = { products: [], outlets: [], inventory: [] };

    const showAlert = (message, isError = false) => {
        elements.alert.textContent = message;
        elements.alert.className = `rounded-2xl border px-4 py-3 text-sm ${isError ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700'}`;
    };

    const renderProducts = () => {
        const query = elements.search.value.trim().toLocaleLowerCase('id-ID');
        const products = state.products.filter((product) => {
            return `${product.name} ${product.sku} ${product.barcode || ''}`.toLocaleLowerCase('id-ID').includes(query);
        });

        elements.loading.classList.add('hidden');
        elements.count.textContent = `${state.products.length} produk`;
        elements.tableWrap.classList.toggle('hidden', products.length === 0);
        elements.empty.classList.toggle('hidden', products.length !== 0);
        elements.footer.classList.toggle('hidden', products.length === 0);
        elements.footerCount.textContent = `Menampilkan ${products.length} dari ${state.products.length} produk`;
        const emptyTitle = root.querySelector('[data-products-empty-title]');
        const emptyDescription = root.querySelector('[data-products-empty-description]');
        if (emptyTitle && emptyDescription) {
            emptyTitle.textContent = state.products.length ? 'Produk tidak ditemukan' : 'Produk belum tersedia';
            emptyDescription.textContent = state.products.length
                ? 'Coba ubah kata pencarian atau pilih lokasi toko yang lain.'
                : 'Mulai katalog bisnis Anda dengan menambahkan produk dan stok awal.';
        }
        elements.empty.querySelector('a')?.classList.toggle('hidden', state.products.length > 0);
        elements.table.innerHTML = products.map((product) => {
            const inventory = state.inventory.find((item) => Number(item.product_id) === Number(product.id));
            const stock = Number(inventory?.available_stock || 0);
            const badge = !product.is_active
                ? '<span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600"><span class="status-dot h-2 w-2 rounded-full bg-slate-400"></span>Nonaktif</span>'
                : stock <= Number(inventory?.minimum_stock || 0)
                    ? '<span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700"><span class="status-dot h-2 w-2 rounded-full bg-amber-400"></span>Stok rendah</span>'
                    : '<span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700"><span class="status-dot h-2 w-2 rounded-full bg-emerald-500"></span>Aktif</span>';

            const photoMarkup = product.image_url
                ? `<img data-product-list-photo src="${escapeHtml(product.image_url)}" alt="Foto ${escapeHtml(product.name)}" loading="lazy" class="h-11 w-11 shrink-0 rounded-xl border border-[#e8ecfb] bg-[#eef1ff] object-cover">`
                : '<span class="product-photo-placeholder flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-[#e8ecfb] bg-[#eef1ff] text-[#536dfe]" aria-label="Foto default produk"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m4 7 8-4 8 4v10l-8 4-8-4V7Z"/><path d="m4 7 8 4 8-4M12 11v10"/></svg></span>';

            return `<tr class="transition hover:bg-[#fafbff]">
                <td class="px-6 py-4"><div class="flex items-center gap-3">${photoMarkup}<span class="min-w-0"><span class="block truncate text-sm font-semibold text-[#283052]">${escapeHtml(product.name)}</span><span class="mt-0.5 block max-w-[260px] truncate text-xs text-[#8a91a9]">${escapeHtml(product.description || 'Tidak ada deskripsi')}</span></span></div></td>
                <td class="px-4 py-4 text-sm font-medium text-[#58617f]">${escapeHtml(product.sku)}</td>
                <td class="px-4 py-4 text-sm text-[#66708d]">${escapeHtml(product.category?.name || '—')}</td>
                <td class="px-4 py-4 text-sm font-semibold text-[#283052]">${formatRupiah(product.selling_price)}</td>
                <td class="px-4 py-4 text-sm font-medium text-[#58617f]">${stock} ${escapeHtml(product.unit || '')}</td>
                <td class="px-4 py-4">${badge}</td>
                <td class="px-6 py-4 text-right"><div class="flex justify-end gap-2"><a href="/owner/products/${encodeURIComponent(product.id)}" class="inline-flex items-center gap-1.5 rounded-xl border border-[#e3e8f6] bg-white px-3 py-2 text-xs font-semibold text-[#58617f] shadow-sm transition hover:-translate-y-0.5 hover:border-[#b8c4ff] hover:bg-[#f7f8ff] hover:text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2.5 12s3.2-6 9.5-6 9.5 6 9.5 6-3.2 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>Detail</a><button type="button" data-product-edit-modal="${product.id}" class="inline-flex items-center gap-1.5 rounded-xl border border-[#e3e8f6] bg-white px-3 py-2 text-xs font-semibold text-[#58617f] shadow-sm transition hover:-translate-y-0.5 hover:border-[#b8c4ff] hover:bg-[#f7f8ff] hover:text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m14 5 5 5M4 20l4.5-1 11-11a2.1 2.1 0 0 0-3-3l-11 11L4 20Z"/></svg>Edit</button>${product.is_active ? `<button type="button" data-product-deactivate="${product.id}" class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-white px-3 py-2 text-xs font-semibold text-rose-600 transition hover:-translate-y-0.5 hover:border-rose-300 hover:bg-rose-50"><svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M10 11v6m4-6v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>Nonaktifkan</button>` : ''}</div></td>
            </tr>`;
        }).join('');
        elements.table.querySelectorAll('[data-product-list-photo]').forEach((photo) => {
            photo.addEventListener('error', () => {
                photo.outerHTML = '<span class="product-photo-placeholder flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-[#e8ecfb] bg-[#eef1ff] text-[#536dfe]" aria-label="Foto produk tidak tersedia"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m4 7 8-4 8 4v10l-8 4-8-4V7Z"/><path d="m4 7 8 4 8-4M12 11v10"/></svg></span>';
            }, { once: true });
        });
    };

    const loadInventory = async () => {
        const businessId = elements.outlet.value;
        if (!businessId) {
            state.inventory = [];
            renderProducts();
            return;
        }

        const response = await apiRequest(`/inventory?limit=100&business_id=${encodeURIComponent(businessId)}`);
        state.inventory = pagedItems(response);
        renderProducts();
    };

    const loadData = async () => {
        try {
            const [productsResponse, outletsResponse] = await Promise.all([
                apiRequest('/products?limit=100'),
                apiRequest('/outlets?limit=100'),
            ]);
            state.products = pagedItems(productsResponse);
            state.outlets = pagedItems(outletsResponse).filter((outlet) => outlet.status === 'active');
            elements.outlet.innerHTML = state.outlets.map((outlet) => `<option value="${outlet.id}">${escapeHtml(outlet.name)}</option>`).join('');

            if (state.outlets.length === 0) {
                elements.outlet.innerHTML = '<option value="">Belum ada outlet</option>';
            }

            await loadInventory();
        } catch (error) {
            elements.loading.textContent = apiMessage(error);
            showAlert(apiMessage(error), true);
        }
    };

    elements.search.addEventListener('input', renderProducts);
    elements.outlet.addEventListener('change', () => loadInventory().catch((error) => showAlert(apiMessage(error), true)));
    elements.table.addEventListener('click', async (event) => {
        const editButton = event.target.closest('[data-product-edit-modal]');
        if (editButton) {
            document.dispatchEvent(new CustomEvent('nivora:product-edit-modal-open', { detail: { productId: editButton.dataset.productEditModal } }));
            return;
        }

        const button = event.target.closest('[data-product-deactivate]');
        if (!button || !window.confirm('Nonaktifkan produk ini? Produk tetap tersimpan untuk riwayat transaksi.')) {
            return;
        }

        try {
            const response = await apiRequest(`/products/${button.dataset.productDeactivate}`, { method: 'DELETE' });
            showAlert(response.message || 'Produk berhasil dinonaktifkan.');
            await loadData();
        } catch (error) {
            showAlert(apiMessage(error), true);
        }
    });

    document.addEventListener('nivora:products-refresh', (event) => {
        loadData().then(() => {
            if (event.detail?.message) {
                showAlert(event.detail.message);
            }
        }).catch((error) => showAlert(apiMessage(error), true));
    });

    loadData();

    if (root.dataset.editOnLoad) {
        document.dispatchEvent(new CustomEvent('nivora:product-edit-modal-open', { detail: { productId: root.dataset.editOnLoad } }));
    }
}

function initializeProductCreate(root) {
    if (!requireSession()) {
        return;
    }

    const form = root.querySelector('[data-product-form]');
    const categoryForm = root.querySelector('[data-category-form]');
    const categorySelect = root.querySelector('[data-product-category]');
    const outletSelect = root.querySelector('[data-product-outlet]');
    const categoryPanel = root.querySelector('[data-category-panel]');
    const state = { outlets: [] };
    const photoDropzone = root.querySelector('[data-photo-dropzone]');
    const photoPreview = bindPhotoDropzone(photoDropzone);

    if (root.hasAttribute('data-product-modal')) {
        const close = () => {
            root.classList.add('hidden');
            root.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
            form.reset();
            form.querySelector('[data-product-photo]').value = '';
            photoPreview.showPhoto(null);
            root.querySelector('[data-product-message]')?.classList.add('hidden');
            categoryPanel.classList.add('hidden');
            categoryForm.reset();
        };

        document.querySelectorAll('[data-open-product-create]').forEach((button) => {
            button.addEventListener('click', () => {
                root.classList.remove('hidden');
                root.classList.add('flex');
                root.querySelector('.overflow-y-auto')?.scrollTo({ top: 0 });
                document.body.classList.add('overflow-hidden');
                root.querySelector('[data-product-message]')?.classList.add('hidden');
            });
        });
        root.querySelectorAll('[data-close-product-modal]').forEach((button) => button.addEventListener('click', close));
        root.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                close();
            }
        });
    }

    const showMessage = (message, isError = false) => {
        const box = root.querySelector('[data-product-message]');
        box.textContent = message;
        box.className = `rounded-2xl border px-4 py-3 text-sm ${isError ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700'}`;
    };

    const loadDependencies = async () => {
        try {
            const [categoriesResponse, outletsResponse] = await Promise.all([
                apiRequest('/categories'),
                apiRequest('/outlets?limit=100'),
            ]);
            const categories = categoriesResponse?.data || [];
            state.outlets = pagedItems(outletsResponse).filter((outlet) => outlet.status === 'active');
            categorySelect.innerHTML = categories.length
                ? `<option value="">Pilih kategori</option>${categories.map((category) => `<option value="${category.id}">${escapeHtml(category.name)}</option>`).join('')}`
                : '<option value="">Buat kategori terlebih dahulu</option>';
            outletSelect.innerHTML = state.outlets.map((outlet) => `<option value="${outlet.id}">${escapeHtml(outlet.name)}</option>`).join('');

            if (state.outlets.length === 0) {
                outletSelect.innerHTML = '<option value="">Belum ada outlet untuk bisnis ini</option>';
                showMessage('Akun ini belum memiliki outlet aktif. Tambahkan outlet sebelum membuat produk.', true);
            }
        } catch (error) {
            showMessage(apiMessage(error), true);
        }
    };

    root.querySelectorAll('[data-toggle-category]').forEach((button) => {
        button.addEventListener('click', () => categoryPanel.classList.toggle('hidden'));
    });

    categoryForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submitButton = categoryForm.querySelector('[type="submit"]');
        const values = Object.fromEntries(new FormData(categoryForm).entries());
        submitButton.disabled = true;

        try {
            const response = await apiRequest('/categories', {
                method: 'POST',
                body: JSON.stringify({ name: values.name.trim(), description: values.description.trim() || null }),
            });
            const category = response.data;
            const option = new Option(category.name, category.id, true, true);
            categorySelect.add(option);
            categoryForm.reset();
            categoryPanel.classList.add('hidden');
            showMessage('Kategori berhasil dibuat.');
        } catch (error) {
            const message = root.querySelector('[data-category-message]');
            message.textContent = apiMessage(error);
            message.classList.remove('hidden');
        } finally {
            submitButton.disabled = false;
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submitButton = form.querySelector('[type="submit"]');
        submitButton.disabled = true;
        const originalLabel = submitButton.innerHTML;
        submitButton.innerHTML = '<span class="inline-flex items-center gap-2"><span class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>Menyimpan…</span>';

        try {
            await apiRequest('/products', {
                method: 'POST',
                body: new FormData(form),
            });

            if (root.hasAttribute('data-product-modal')) {
                root.classList.add('hidden');
                root.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
                form.reset();
                form.querySelector('[data-product-photo]').value = '';
                photoPreview.showPhoto(null);
                root.querySelector('[data-product-message]')?.classList.add('hidden');
                document.dispatchEvent(new CustomEvent('nivora:products-refresh', { detail: { message: 'Produk berhasil ditambahkan.' } }));
            } else {
                window.location.assign('/owner/products');
            }
        } catch (error) {
            showMessage(apiMessage(error), true);
        } finally {
            if (document.contains(submitButton)) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalLabel;
            }
        }
    });

    loadDependencies();
}

export function bootProducts() {
    document.querySelectorAll('[data-products-root]').forEach(initializeProductList);
    document.querySelectorAll('[data-product-create-root]').forEach(initializeProductCreate);
}
