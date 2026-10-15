import { apiMessage, apiRequest, escapeHtml, formatRupiah, pagedItems, requireSession } from './nivora-api';

function dateTimeForInput(value) {
    if (!value) {
        return '';
    }

    const date = new Date(value);
    const localDate = new Date(date.getTime() - date.getTimezoneOffset() * 60000);

    return localDate.toISOString().slice(0, 16);
}

function dateTimeForApi(value) {
    return new Date(value).toISOString();
}

function formatPeriod(value) {
    if (!value) {
        return '—';
    }

    return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value));
}

export function bootPromotions() {
    const root = document.querySelector('[data-promotions-root]');

    if (!root || !requireSession()) {
        return;
    }

    const elements = {
        alert: root.querySelector('[data-promo-message]'),
        empty: root.querySelector('[data-promo-empty]'),
        form: root.querySelector('[data-promo-form]'),
        formAlert: root.querySelector('[data-promo-form-message]'),
        list: root.querySelector('[data-promo-list]'),
        loading: root.querySelector('[data-promo-loading]'),
        modal: root.querySelector('[data-promo-modal]'),
        modalTitle: root.querySelector('[data-promo-modal-title]'),
        deleteModal: root.querySelector('[data-promo-delete-modal]'),
        deleteTitle: root.querySelector('[data-promo-delete-title]'),
        deleteCopy: root.querySelector('[data-promo-delete-copy]'),
        deleteConfirm: root.querySelector('[data-promo-delete-confirm]'),
        deleteCancel: root.querySelector('[data-promo-delete-cancel]'),
        products: root.querySelector('[data-promo-product-options]'),
        productField: root.querySelector('[data-promo-products-field]'),
        minimumField: root.querySelector('[data-promo-minimum-field]'),
    };
    const state = { promos: [], products: [], editingId: null, pendingDelete: null, scrollLock: null };

    // Portal fixed dialogs outside the dashboard's max-width and scroll containers.
    document.body.append(elements.modal, elements.deleteModal);

    const lockPageScroll = () => {
        if (!state.scrollLock) {
            state.scrollLock = {
                body: document.body.style.overflow,
                html: document.documentElement.style.overflow,
            };
        }
        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';
    };

    const unlockPageScroll = () => {
        if (!state.scrollLock) {
            return;
        }
        document.body.style.overflow = state.scrollLock.body;
        document.documentElement.style.overflow = state.scrollLock.html;
        state.scrollLock = null;
    };

    const showAlert = (message, type = 'success') => {
        elements.alert.textContent = message;
        elements.alert.className = `rounded-2xl border px-4 py-3 text-sm ${type === 'error' ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700'}`;
    };

    const showFormAlert = (message) => {
        elements.formAlert.textContent = message;
        elements.formAlert.classList.remove('hidden');
    };

    const selectedProducts = () => Array.from(elements.products.querySelectorAll('input:checked')).map((input) => input.value);

    const renderProductOptions = (selectedIds = []) => {
        if (state.products.length === 0) {
            elements.products.innerHTML = '<p class="text-[#5e647f]">Tambahkan produk terlebih dahulu. <a class="font-semibold text-[#687EFF] underline" href="/owner/products/create">Tambah produk</a></p>';
            return;
        }

        elements.products.innerHTML = state.products.map((product) => `
            <label class="flex cursor-pointer items-center justify-between gap-3 rounded-lg px-2 py-1.5 hover:bg-white">
                <span class="min-w-0"><span class="block truncate font-medium text-[#1f2544]">${escapeHtml(product.name)}</span><span class="text-xs text-[#5e647f]">${escapeHtml(product.sku)} · ${formatRupiah(product.selling_price)}</span></span>
                <input name="product_ids[]" type="checkbox" value="${product.id}" ${selectedIds.map(String).includes(String(product.id)) ? 'checked' : ''} class="h-4 w-4 rounded border-[#dfe7ff] text-[#687EFF] focus:ring-[#687EFF]">
            </label>
        `).join('');
    };

    const filteredPromos = () => {
        const search = root.querySelector('[data-promo-search]').value.trim().toLocaleLowerCase('id-ID');
        const type = root.querySelector('[data-promo-type-filter]').value;
        const status = root.querySelector('[data-promo-status-filter]').value;

        return state.promos.filter((promo) => {
            return (!search || `${promo.name} ${promo.code || ''}`.toLocaleLowerCase('id-ID').includes(search))
                && (!type || promo.type === type)
                && (!status || promo.status === status);
        });
    };

    const renderPromos = () => {
        const promos = filteredPromos();
        const activeCount = state.promos.filter((promo) => promo.is_currently_active).length;
        root.querySelector('[data-promo-stat="total"]').textContent = String(state.promos.length);
        root.querySelector('[data-promo-stat="active"]').textContent = String(activeCount);
        root.querySelector('[data-promo-stat="product"]').textContent = String(state.promos.filter((promo) => promo.type === 'product').length);
        root.querySelector('[data-promo-stat="transaction"]').textContent = String(state.promos.filter((promo) => promo.type === 'transaction').length);
        elements.loading.classList.add('hidden');
        elements.list.classList.toggle('hidden', promos.length === 0);
        elements.empty.classList.toggle('hidden', promos.length !== 0);
        root.querySelector('[data-promo-result-count]').textContent = `${promos.length} dari ${state.promos.length} promo`;
        root.querySelector('[data-promo-empty-title]').textContent = state.promos.length ? 'Promo tidak ditemukan' : 'Belum ada promo';
        root.querySelector('[data-promo-empty-description]').textContent = state.promos.length
            ? 'Coba ubah kata kunci atau filter untuk menemukan promo.'
            : 'Buat promo pertama untuk mulai menarik pelanggan.';

        elements.list.innerHTML = promos.map((promo) => {
            const statusLabel = promo.status !== 'active' ? 'Nonaktif' : (promo.is_currently_active ? 'Berjalan' : 'Di luar periode');
            const statusClass = promo.status !== 'active' ? 'is-inactive' : (promo.is_currently_active ? 'is-active' : 'is-scheduled');
            const targets = promo.type === 'product'
                ? (promo.products || []).map((product) => escapeHtml(product.name)).join(', ')
                : (promo.min_transaction ? `Minimum ${formatRupiah(promo.min_transaction)}` : 'Tanpa minimum transaksi');
            const typeLabel = promo.type === 'product' ? 'Promo produk' : 'Promo transaksi';
            const discount = Number(promo.discount_value).toLocaleString('id-ID');

            return `
                <article class="promo-card">
                    <div class="promo-card-heading">
                        <div class="promo-discount ${promo.type === 'product' ? 'is-product' : 'is-transaction'}"><span>Diskon</span><strong>${discount}<small>%</small></strong></div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2"><span class="promo-kind ${promo.type === 'product' ? 'is-product' : 'is-transaction'}">${typeLabel}</span><span class="promo-state ${statusClass}"><i aria-hidden="true"></i>${statusLabel}</span></div>
                            <h3>${escapeHtml(promo.name)}</h3>
                            <p>${escapeHtml(promo.description || 'Promo toko untuk pelanggan.')}</p>
                        </div>
                    </div>
                    <div class="promo-card-details">
                        <div class="promo-target"><span>${promo.type === 'product' ? 'Produk promo' : 'Syarat transaksi'}</span><strong>${targets || 'Belum ada produk dipilih'}</strong></div>
                        <div class="promo-meta"><span class="promo-period"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="5" width="17" height="16" rx="2"/><path d="M7.5 3v4M16.5 3v4M3.5 10h17"/></svg>${formatPeriod(promo.start_date)} – ${formatPeriod(promo.end_date)}</span>${promo.code ? `<span class="promo-code">${escapeHtml(promo.code)}</span>` : '<span class="promo-automatic">Otomatis</span>'}</div>
                    </div>
                    <div class="promo-card-actions"><button type="button" data-promo-edit="${promo.id}" class="promo-edit-button"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m14 5 5 5M4 20l4.5-1 11-11a2.1 2.1 0 0 0-3-3l-11 11L4 20Z"/><path d="M4 20h5"/></svg>Edit promo</button><button type="button" data-promo-delete="${promo.id}" class="promo-delete-button"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M10 11v6m4-6v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>Hapus</button></div>
                </article>
            `;
        }).join('');
    };

    const loadData = async () => {
        elements.loading.classList.remove('hidden');
        elements.list.classList.add('hidden');

        try {
            const [promosResponse, productsResponse] = await Promise.all([
                apiRequest('/promotions?limit=100'),
                apiRequest('/products?limit=100'),
            ]);
            state.promos = pagedItems(promosResponse);
            state.products = pagedItems(productsResponse).filter((product) => product.is_active);
            renderPromos();
        } catch (error) {
            elements.loading.textContent = apiMessage(error);
            showAlert(apiMessage(error), 'error');
        }
    };

    const updateFormFields = () => {
        const productPromo = elements.form.elements.type.value === 'product';
        elements.productField.classList.toggle('hidden', !productPromo);
        elements.minimumField.classList.toggle('hidden', productPromo);
        elements.products.querySelectorAll('input[type="checkbox"]').forEach((input) => {
            input.required = false;
        });
    };

    const openModal = (promo = null) => {
        state.editingId = promo?.id ?? null;
        elements.form.reset();
        elements.formAlert.classList.add('hidden');
        elements.modalTitle.textContent = promo ? 'Edit promo' : 'Buat promo';
        elements.form.elements.name.value = promo?.name || '';
        elements.form.elements.code.value = promo?.code || '';
        elements.form.elements.description.value = promo?.description || '';
        elements.form.elements.type.value = promo?.type || 'product';
        elements.form.elements.discount_value.value = promo?.discount_value ?? '';
        elements.form.elements.min_transaction.value = promo?.min_transaction ?? '';
        elements.form.elements.start_date.value = promo ? dateTimeForInput(promo.start_date) : dateTimeForInput(new Date());
        const endDefault = new Date();
        endDefault.setDate(endDefault.getDate() + 7);
        elements.form.elements.end_date.value = promo ? dateTimeForInput(promo.end_date) : dateTimeForInput(endDefault);
        elements.form.elements.status.value = promo?.status || 'active';
        renderProductOptions((promo?.products || []).map((product) => product.id));
        updateFormFields();
        elements.form.querySelector('[data-promo-submit-label]').textContent = promo ? 'Simpan perubahan' : 'Buat promo';
        elements.modal.classList.remove('hidden');
        elements.modal.classList.add('flex');
        lockPageScroll();
        elements.form.elements.name.focus({ preventScroll: true });
    };

    const closeModal = () => {
        elements.modal.classList.add('hidden');
        elements.modal.classList.remove('flex');
        unlockPageScroll();
        state.editingId = null;
    };

    const closeDeleteModal = () => {
        elements.deleteModal.classList.add('hidden');
        elements.deleteModal.classList.remove('flex');
        state.pendingDelete = null;
        unlockPageScroll();
    };

    const openDeleteModal = (promo) => {
        state.pendingDelete = promo;
        elements.deleteTitle.textContent = 'Hapus promo?';
        elements.deleteCopy.textContent = `Promo “${promo.name}” akan dihapus. Jika sudah tercatat pada transaksi, sistem akan menonaktifkannya agar riwayat transaksi tetap aman.`;
        elements.deleteConfirm.textContent = 'Ya, lanjutkan';
        elements.deleteModal.classList.remove('hidden');
        elements.deleteModal.classList.add('flex');
        lockPageScroll();
        elements.deleteConfirm.focus();
    };

    root.querySelector('[data-promo-new]').addEventListener('click', () => openModal());
    elements.modal.querySelectorAll('[data-promo-close]').forEach((button) => button.addEventListener('click', closeModal));
    elements.modal.addEventListener('click', (event) => {
        if (event.target === elements.modal) {
            closeModal();
        }
    });
    elements.deleteCancel.addEventListener('click', closeDeleteModal);
    elements.deleteModal.addEventListener('click', (event) => {
        if (event.target === event.currentTarget) {
            closeDeleteModal();
        }
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (state.editingId !== null || !elements.modal.classList.contains('hidden')) {
                closeModal();
            }
            if (state.pendingDelete) {
                closeDeleteModal();
            }
        }
    });
    elements.form.elements.type.addEventListener('change', updateFormFields);
    elements.products.addEventListener('change', updateFormFields);
    root.querySelector('[data-promo-search]').addEventListener('input', renderPromos);
    root.querySelector('[data-promo-type-filter]').addEventListener('change', renderPromos);
    root.querySelector('[data-promo-status-filter]').addEventListener('change', renderPromos);

    elements.list.addEventListener('click', async (event) => {
        const editButton = event.target.closest('[data-promo-edit]');
        const deleteButton = event.target.closest('[data-promo-delete]');

        if (editButton) {
            const promo = state.promos.find((item) => String(item.id) === editButton.dataset.promoEdit);
            if (promo) {
                openModal(promo);
            }
        }

        if (deleteButton) {
            const promo = state.promos.find((item) => String(item.id) === deleteButton.dataset.promoDelete);
            if (!promo) {
                return;
            }
            openDeleteModal(promo);
        }
    });

    elements.deleteConfirm.addEventListener('click', async (event) => {
        const button = event.currentTarget;
        const promo = state.pendingDelete;
        if (!promo) {
            return;
        }

        button.disabled = true;
        button.textContent = 'Memproses…';
        try {
            const response = await apiRequest(`/promotions/${promo.id}`, { method: 'DELETE' });
            closeDeleteModal();
            showAlert(response.message || 'Promo berhasil diperbarui.');
            await loadData();
        } catch (error) {
            closeDeleteModal();
            showAlert(apiMessage(error), 'error');
        } finally {
            button.disabled = false;
        }
    });

    elements.form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const values = Object.fromEntries(new FormData(elements.form).entries());
        const payload = {
            name: values.name.trim(),
            code: values.code.trim().toUpperCase() || null,
            description: values.description.trim() || null,
            type: values.type,
            discount_value: Number(values.discount_value),
            min_transaction: values.type === 'transaction' && values.min_transaction !== '' ? Number(values.min_transaction) : null,
            start_date: dateTimeForApi(values.start_date),
            end_date: dateTimeForApi(values.end_date),
            status: values.status,
        };

        if (values.type === 'product') {
            payload.product_ids = selectedProducts().map(Number);
            if (payload.product_ids.length === 0) {
                showFormAlert('Pilih minimal satu produk untuk promo produk.');
                return;
            }
        }

        const submitButton = elements.form.querySelector('[type="submit"]');
        const submitLabel = submitButton.querySelector('[data-promo-submit-label]');
        submitButton.disabled = true;
        submitLabel.textContent = 'Menyimpan…';

        try {
            const editingId = state.editingId;
            const response = await apiRequest(editingId ? `/promotions/${editingId}` : '/promotions', {
                method: editingId ? 'PUT' : 'POST',
                body: JSON.stringify(payload),
            });
            closeModal();
            showAlert(response.message || 'Promo berhasil disimpan.');
            await loadData();
        } catch (error) {
            showFormAlert(apiMessage(error));
        } finally {
            submitButton.disabled = false;
            submitLabel.textContent = state.editingId ? 'Simpan perubahan' : 'Buat promo';
        }
    });

    loadData();
}
