import { apiMessage, apiRequest, escapeHtml, formatRupiah, getSession, pagedItems, requireSession } from './nivora-api';

function initializePos(root) {
    if (!requireSession()) {
        return;
    }

    const elements = {
        alert: root.querySelector('[data-pos-message]'),
        amountDue: root.querySelector('[data-pos-amount-due]'),
        cart: root.querySelector('[data-pos-cart]'),
        confirmDetails: root.querySelector('[data-pos-confirm-details]'),
        confirmModal: root.querySelector('[data-pos-confirm-modal]'),
        confirmSubmit: root.querySelector('[data-pos-confirm-submit]'),
        category: root.querySelector('[data-pos-category]'),
        change: root.querySelector('[data-pos-change]'),
        checkout: root.querySelector('[data-pos-checkout]'),
        couponCode: root.querySelector('[data-pos-coupon-code]'),
        discount: root.querySelector('[data-pos-discount]'),
        empty: root.querySelector('[data-pos-products-empty]'),
        itemCount: root.querySelector('[data-pos-item-count]'),
        loading: root.querySelector('[data-pos-products-loading]'),
        memberLookup: root.querySelector('[data-pos-member-lookup]'),
        memberPhone: root.querySelector('[data-pos-member-phone]'),
        memberStatus: root.querySelector('[data-pos-member-status]'),
        outlet: root.querySelector('[data-pos-outlet]'),
        paid: root.querySelector('[data-pos-paid]'),
        products: root.querySelector('[data-pos-products]'),
        pointsToMember: root.querySelector('[data-pos-points-to-member]'),
        pointsToRedeem: root.querySelector('[data-pos-points-to-redeem]'),
        promos: root.querySelector('[data-pos-promos]'),
        receipt: root.querySelector('[data-pos-receipt]'),
        search: root.querySelector('[data-pos-search]'),
        subtotal: root.querySelector('[data-pos-subtotal]'),
        total: root.querySelector('[data-pos-total]'),
    };
    const state = {
        cart: new Map(),
        categories: [],
        inventory: [],
        outlets: [],
        products: [],
        promos: [],
        selectedPromoIds: new Set(),
        paidTouched: false,
        verifiedMember: null,
        submittingSale: false,
    };
    const layoutGrid = root.querySelector('[data-pos-layout]');
    const layoutOptions = root.querySelectorAll('[data-pos-layout-option]');
    let savedLayout = 'products-left';
    try {
        savedLayout = window.localStorage.getItem('nivora.pos-layout') || savedLayout;
    } catch {
        savedLayout = 'products-left';
    }
    const applyLayout = (layout) => {
        const selectedLayout = layout === 'cart-left' ? 'cart-left' : 'products-left';
        layoutGrid.dataset.posLayout = selectedLayout;
        layoutOptions.forEach((option) => {
            const isSelected = option.dataset.posLayoutOption === selectedLayout;
            option.setAttribute('aria-pressed', String(isSelected));
            option.classList.toggle('is-selected', isSelected);
        });
        try {
            window.localStorage.setItem('nivora.pos-layout', selectedLayout);
        } catch {
            // Keep the selected layout for the current page when storage is unavailable.
        }
    };

    layoutOptions.forEach((option) => {
        option.addEventListener('click', () => applyLayout(option.dataset.posLayoutOption));
    });
    applyLayout(savedLayout || 'products-left');

    const subtotalValue = () => Array.from(state.cart.values()).reduce((total, line) => total + Number(line.product.selling_price) * line.quantity, 0);
    const linePromoDiscount = () => Array.from(state.cart.values()).reduce((total, line) => total + Number(line.discount || 0), 0);

    const showMessage = (message, isError = true) => {
        elements.alert.textContent = message;
        elements.alert.className = `rounded-2xl border px-4 py-3 text-sm ${isError ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700'}`;
    };

    const getStock = (productId) => {
        const inventory = state.inventory.find((item) => Number(item.product_id) === Number(productId));

        return Number(inventory?.available_stock || 0);
    };

    const eligiblePromos = () => {
        const cartProductIds = new Set(Array.from(state.cart.keys()).map(Number));
        const subtotal = subtotalValue();

        return state.promos.map((promo) => {
            let eligible = false;
            let reason = '';

            if (promo.type === 'product') {
                eligible = (promo.products || []).some((product) => cartProductIds.has(Number(product.id)));
                reason = state.cart.size === 0 ? 'Tambahkan produk ke keranjang' : 'Tidak ada produk promo di keranjang';
            } else {
                const minimum = Number(promo.min_transaction || 0);
                eligible = subtotal >= minimum;
                reason = minimum > 0 ? `Belanja minimal ${formatRupiah(minimum)}` : 'Tambahkan produk ke keranjang';
            }

            return { ...promo, eligible, reason };
        });
    };

    const estimateDiscount = () => {
        const lines = Array.from(state.cart.values()).map((line) => ({
            product: line.product,
            subtotal: Number(line.product.selling_price) * line.quantity,
            discount: 0,
        }));
        const subtotal = lines.reduce((total, line) => total + line.subtotal, 0);
        let transactionDiscount = 0;
        const promos = eligiblePromos()
            .filter((promo) => promo.eligible && state.selectedPromoIds.has(Number(promo.id)))
            .sort((first, second) => Number(first.id) - Number(second.id));

        promos.forEach((promo) => {
            if (promo.type === 'product') {
                const eligibleProductIds = new Set((promo.products || []).map((product) => Number(product.id)));
                lines.forEach((line) => {
                    if (!eligibleProductIds.has(Number(line.product.id))) {
                        return;
                    }

                    const remaining = Math.max(0, line.subtotal - line.discount);
                    line.discount += Math.min(remaining, Math.round(remaining * Number(promo.discount_value)) / 100);
                });
                return;
            }

            const minimum = Number(promo.min_transaction || 0);
            if (subtotal < minimum) {
                return;
            }

            const remaining = Math.max(0, subtotal - lines.reduce((total, line) => total + line.discount, 0) - transactionDiscount);
            transactionDiscount += Math.min(remaining, Math.round(remaining * Number(promo.discount_value)) / 100);
        });

        return lines.reduce((total, line) => total + line.discount, 0) + transactionDiscount;
    };

    const renderProducts = () => {
        const query = elements.search.value.trim().toLocaleLowerCase('id-ID');
        const categoryId = elements.category.value;
        const products = state.products.filter((product) => {
            return product.is_active
                && (!categoryId || String(product.category_id) === categoryId)
                && `${product.name} ${product.sku}`.toLocaleLowerCase('id-ID').includes(query);
        });

        elements.loading.classList.add('hidden');
        elements.products.classList.toggle('hidden', products.length === 0);
        elements.empty.classList.toggle('hidden', products.length !== 0);
        elements.products.innerHTML = products.map((product) => {
            const stock = getStock(product.id);
            const disabled = stock === 0;

            const photo = product.image_url
                ? `<img data-pos-product-image src="${escapeHtml(product.image_url)}" alt="" class="h-12 w-12 shrink-0 rounded-xl border border-[#e7ebff] bg-[#eef1ff] object-cover">`
                : '<span class="pos-product-placeholder flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-[#e7ebff] bg-[#eef1ff] text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Zm0 0v9m8-4.5-8 4.5-8-4.5"/></svg></span>';
            return `<button type="button" data-pos-add-product="${product.id}" ${disabled ? 'disabled' : ''} class="pos-product-card group rounded-2xl border border-[#e7ebff] bg-white p-3.5 text-left transition duration-200 hover:-translate-y-0.5 hover:border-[#b8c5ff] hover:shadow-[0_12px_26px_rgba(49,67,130,0.08)] disabled:cursor-not-allowed disabled:opacity-55">
                <span class="flex items-start gap-3">${photo}<span class="min-w-0 flex-1"><span class="block truncate text-sm font-semibold text-[#20284a]">${escapeHtml(product.name)}</span><span class="mt-1 block truncate text-xs text-[#7b8299]">${escapeHtml(product.category?.name || 'Tanpa kategori')}</span></span><span class="shrink-0 rounded-full bg-[#eef3ff] px-2 py-1 text-[10px] font-semibold text-[#5265d8]">${escapeHtml(product.sku)}</span></span>
                <span class="mt-4 flex items-end justify-between gap-2 border-t border-[#f0f2fa] pt-3"><span><span class="block text-[10px] font-semibold uppercase tracking-wide text-[#8a91a9]">Harga jual</span><span class="mt-0.5 block font-semibold text-[#20284a]">${formatRupiah(product.selling_price)}</span></span><span class="rounded-lg ${stock ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'} px-2 py-1 text-[11px] font-semibold">Stok ${stock}</span></span>
            </button>`;
        }).join('');
        elements.products.querySelectorAll('[data-pos-product-image]').forEach((image) => {
            image.addEventListener('error', () => {
                image.outerHTML = '<span class="pos-product-placeholder flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-[#e7ebff] bg-[#eef1ff] text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Zm0 0v9m8-4.5-8 4.5-8-4.5"/></svg></span>';
            }, { once: true });
        });
    };

    const renderCart = () => {
        const lines = Array.from(state.cart.values());
        const totalQuantity = lines.reduce((total, line) => total + line.quantity, 0);
        elements.itemCount.textContent = `${totalQuantity} item`;

        if (lines.length === 0) {
            elements.cart.innerHTML = '<div class="rounded-xl border border-dashed border-[#dfe7ff] p-5 text-center text-sm text-[#7b8299]">Keranjang masih kosong. Pilih produk untuk memulai.</div>';
        } else {
            elements.cart.innerHTML = lines.map((line) => {
                const stock = getStock(line.product.id);

                return `<article class="rounded-xl border border-[#eef3ff] bg-[#f7f9ff] p-3">
                    <div class="flex items-start justify-between gap-2"><div class="min-w-0"><p class="truncate text-sm font-semibold text-[#1f2544]">${escapeHtml(line.product.name)}</p><p class="mt-1 text-xs text-[#5e647f]">${formatRupiah(line.product.selling_price)} / ${escapeHtml(line.product.unit || 'pcs')}</p></div><button type="button" data-pos-remove="${line.product.id}" class="text-xs font-medium text-rose-600">Hapus</button></div>
                    <div class="mt-3 flex items-center justify-between"><div class="flex items-center gap-2"><button type="button" data-pos-quantity="${line.product.id}" data-delta="-1" class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-lg" aria-label="Kurangi jumlah">−</button><span class="min-w-6 text-center text-sm font-semibold">${line.quantity}</span><button type="button" data-pos-quantity="${line.product.id}" data-delta="1" ${line.quantity >= stock ? 'disabled' : ''} class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-lg disabled:opacity-40" aria-label="Tambah jumlah">+</button></div><span class="text-sm font-semibold">${formatRupiah(Number(line.product.selling_price) * line.quantity)}</span></div>
                </article>`;
            }).join('');
        }

        renderPromos();
        renderSummary();
    };

    const renderPromos = () => {
        const promos = eligiblePromos();
        const validPromoIds = new Set(promos.filter((promo) => promo.eligible).map((promo) => Number(promo.id)));
        state.selectedPromoIds = new Set(Array.from(state.selectedPromoIds).filter((id) => validPromoIds.has(id)));

        if (promos.length === 0) {
            elements.promos.innerHTML = '<p class="text-xs text-[#7b8299]">Belum ada promo aktif untuk bisnis ini.</p>';
            return;
        }

        elements.promos.innerHTML = promos.map((promo) => `
            <label class="flex gap-2 rounded-xl border border-[#e7ebff] bg-white p-2.5 ${promo.eligible ? 'cursor-pointer' : 'cursor-not-allowed opacity-50'}">
                <input type="checkbox" data-pos-promo="${promo.id}" ${state.selectedPromoIds.has(Number(promo.id)) ? 'checked' : ''} ${promo.eligible ? '' : 'disabled'} class="mt-0.5 h-4 w-4 rounded border-[#dfe7ff] text-[#687EFF] focus:ring-[#687EFF]">
                <span class="min-w-0"><span class="block text-xs font-semibold text-[#1f2544]">${escapeHtml(promo.name)} · ${Number(promo.discount_value)}%</span><span class="mt-0.5 block text-[11px] text-[#68718b]">${promo.type === 'product' ? 'Promo produk' : 'Promo transaksi'}${promo.eligible ? '' : ` · ${escapeHtml(promo.reason)}`}</span></span>
            </label>
        `).join('');
    };

    const renderSummary = () => {
        const subtotal = subtotalValue();
        const discount = estimateDiscount();
        const total = Math.max(0, subtotal - discount);
        const pointsToRedeem = Number(elements.pointsToRedeem.value || 0);
        const pointsBalance = Number(state.verifiedMember?.points || 0);
        const amountDue = Math.max(0, total - pointsToRedeem);
        elements.pointsToRedeem.max = String(Math.floor(Math.min(total, pointsBalance)));
        if (!state.paidTouched) {
            elements.paid.value = String(Math.ceil(amountDue));
        }
        const paid = Number(elements.paid.value || 0);
        const cashChange = Math.max(0, paid - amountDue);
        const pointsToMember = Number(elements.pointsToMember.value || 0);
        elements.subtotal.textContent = formatRupiah(subtotal);
        elements.discount.textContent = `−${formatRupiah(discount)}`;
        elements.total.textContent = formatRupiah(total);
        elements.amountDue.textContent = formatRupiah(amountDue);
        elements.pointsToMember.max = String(Math.floor(cashChange));

        let paymentError = '';
        if (pointsToRedeem > total) {
            paymentError = 'Poin melebihi total transaksi';
        } else if (pointsToRedeem > pointsBalance) {
            paymentError = 'Poin melebihi saldo member';
        } else if (paid < amountDue) {
            paymentError = `Pembayaran kurang ${formatRupiah(amountDue - paid)}`;
        } else if (pointsToMember > cashChange) {
            paymentError = 'Poin kembalian melebihi uang tunai';
        } else if ((pointsToRedeem > 0 || pointsToMember > 0) && !state.verifiedMember) {
            paymentError = 'Verifikasi nomor member terlebih dahulu';
        }

        elements.change.textContent = paymentError || `${formatRupiah(cashChange - pointsToMember)} tunai${pointsToMember > 0 ? ` + ${pointsToMember.toLocaleString('id-ID')} poin` : ''}`;
        elements.checkout.disabled = state.cart.size === 0 || !elements.outlet.value || Boolean(paymentError);
    };

    const loadInventory = async () => {
        if (!elements.outlet.value) {
            state.inventory = [];
            renderProducts();
            renderCart();
            return;
        }

        const response = await apiRequest(`/inventory?limit=100&business_id=${encodeURIComponent(elements.outlet.value)}`);
        state.inventory = pagedItems(response);
        renderProducts();
        renderCart();
    };

    const loadData = async () => {
        try {
            const [productsResponse, categoriesResponse, outletsResponse, promosResponse] = await Promise.all([
                apiRequest('/products?limit=100'),
                apiRequest('/categories'),
                apiRequest('/outlets?limit=100'),
                apiRequest('/promotions?limit=100&current_only=1'),
            ]);
            state.products = pagedItems(productsResponse);
            state.categories = categoriesResponse?.data || [];
            state.outlets = pagedItems(outletsResponse).filter((outlet) => outlet.status === 'active');
            state.promos = pagedItems(promosResponse);
            elements.outlet.innerHTML = state.outlets.map((outlet) => `<option value="${outlet.id}">${escapeHtml(outlet.name)}</option>`).join('');
            elements.category.innerHTML = `<option value="">Semua kategori</option>${state.categories.map((category) => `<option value="${category.id}">${escapeHtml(category.name)}</option>`).join('')}`;

            if (state.outlets.length === 0) {
                elements.outlet.innerHTML = '<option value="">Belum ada outlet</option>';
                showMessage('Bisnis ini belum memiliki outlet aktif. Tambahkan outlet sebelum memakai POS.');
            }

            await loadInventory();
        } catch (error) {
            elements.loading.textContent = apiMessage(error);
            showMessage(apiMessage(error));
        }
    };

    elements.search.addEventListener('input', renderProducts);
    elements.category.addEventListener('change', renderProducts);
    elements.outlet.addEventListener('change', () => loadInventory().catch((error) => showMessage(apiMessage(error))));
    elements.paid.addEventListener('input', () => {
        state.paidTouched = true;
        renderSummary();
    });
    elements.memberPhone.addEventListener('input', () => {
        state.verifiedMember = null;
        elements.pointsToRedeem.value = '0';
        elements.pointsToRedeem.disabled = true;
        elements.pointsToMember.value = '0';
        elements.pointsToMember.disabled = true;
        elements.memberStatus.textContent = 'Verifikasi nomor untuk memakai poin atau menerima poin kembalian.';
        elements.memberStatus.className = 'block text-[11px] font-normal normal-case tracking-normal text-[#7b8299]';
        renderSummary();
    });
    elements.pointsToRedeem.addEventListener('input', renderSummary);
    elements.pointsToMember.addEventListener('input', renderSummary);
    elements.memberLookup.addEventListener('click', async () => {
        const phone = elements.memberPhone.value.trim();
        if (!phone) {
            elements.memberStatus.textContent = 'Masukkan nomor ponsel member terlebih dahulu.';
            elements.memberStatus.className = 'block text-[11px] font-normal normal-case tracking-normal text-rose-600';
            return;
        }

        elements.memberLookup.disabled = true;
        elements.memberLookup.textContent = 'Memeriksa…';
        try {
            const response = await apiRequest(`/cashiers/member-lookup?phone=${encodeURIComponent(phone)}`);
            if (!response.data.found) {
                state.verifiedMember = null;
                elements.pointsToRedeem.value = '0';
                elements.pointsToRedeem.disabled = true;
                elements.pointsToMember.value = '0';
                elements.pointsToMember.disabled = true;
                elements.memberStatus.textContent = 'Nomor tidak terdaftar sebagai member. Transaksi tetap dapat dilanjutkan tanpa mengirim poin.';
                elements.memberStatus.className = 'block text-[11px] font-normal normal-case tracking-normal text-amber-700';
                renderSummary();
                return;
            }

            state.verifiedMember = response.data.member;
            elements.pointsToRedeem.disabled = false;
            elements.pointsToMember.disabled = false;
            elements.memberStatus.textContent = `${response.data.member.name} terverifikasi · saldo ${Number(response.data.member.points).toLocaleString('id-ID')} poin untuk pembayaran atau kembalian.`;
            elements.memberStatus.className = 'block text-[11px] font-normal normal-case tracking-normal text-emerald-700';
            renderSummary();
        } catch (error) {
            state.verifiedMember = null;
            elements.pointsToRedeem.value = '0';
            elements.pointsToRedeem.disabled = true;
            elements.pointsToMember.value = '0';
            elements.pointsToMember.disabled = true;
            elements.memberStatus.textContent = apiMessage(error);
            elements.memberStatus.className = 'block text-[11px] font-normal normal-case tracking-normal text-rose-600';
            renderSummary();
        } finally {
            elements.memberLookup.disabled = false;
            elements.memberLookup.textContent = 'Verifikasi';
        }
    });

    elements.products.addEventListener('click', (event) => {
        const button = event.target.closest('[data-pos-add-product]');
        if (!button) {
            return;
        }

        const product = state.products.find((item) => String(item.id) === button.dataset.posAddProduct);
        if (!product) {
            return;
        }

        const existing = state.cart.get(Number(product.id));
        if ((existing?.quantity || 0) >= getStock(product.id)) {
            return;
        }

        state.cart.set(Number(product.id), { product, quantity: (existing?.quantity || 0) + 1 });
        elements.receipt.classList.add('hidden');
        renderCart();
    });

    elements.cart.addEventListener('click', (event) => {
        const removeButton = event.target.closest('[data-pos-remove]');
        const quantityButton = event.target.closest('[data-pos-quantity]');

        if (removeButton) {
            state.cart.delete(Number(removeButton.dataset.posRemove));
        }

        if (quantityButton) {
            const productId = Number(quantityButton.dataset.posQuantity);
            const line = state.cart.get(productId);
            const nextQuantity = line.quantity + Number(quantityButton.dataset.delta);

            if (nextQuantity <= 0) {
                state.cart.delete(productId);
            } else if (nextQuantity <= getStock(productId)) {
                state.cart.set(productId, { ...line, quantity: nextQuantity });
            }
        }

        renderProducts();
        renderCart();
    });

    elements.promos.addEventListener('change', (event) => {
        const checkbox = event.target.closest('[data-pos-promo]');
        if (!checkbox) {
            return;
        }

        const promoId = Number(checkbox.dataset.posPromo);
        if (checkbox.checked) {
            state.selectedPromoIds.add(promoId);
        } else {
            state.selectedPromoIds.delete(promoId);
        }

        renderPromos();
        renderSummary();
    });

    const buildConfirmationHtml = () => {
        const lines = Array.from(state.cart.values());
        const subtotal = subtotalValue();
        const discount = estimateDiscount();
        const total = Math.max(0, subtotal - discount);
        const pointsToRedeem = Number(elements.pointsToRedeem.value || 0);
        const amountDue = Math.max(0, total - pointsToRedeem);
        const paid = Number(elements.paid.value || 0);
        const paymentMethod = root.querySelector('[data-pos-payment-method]')?.value || 'cash';
        const methodLabels = { cash: 'Tunai', debit: 'Kartu debit', bank_transfer: 'Transfer bank', ewallet: 'E-wallet' };
        const cashierName = getSession()?.user?.name || 'Kasir';
        const outletName = elements.outlet.selectedOptions?.[0]?.textContent?.trim() || 'Toko';
        const memberName = state.verifiedMember?.name || (elements.memberPhone.value.trim() ? 'Nomor belum terverifikasi' : 'Tanpa member');
        const couponCode = elements.couponCode.value.trim();
        const cashChange = Math.max(0, paid - amountDue);
        const pointsToMember = Number(elements.pointsToMember.value || 0);
        const appliedPromoIds = Array.from(state.selectedPromoIds);
        const appliedPromoNames = appliedPromoIds.map((id) => state.promos.find((promo) => Number(promo.id) === Number(id))?.name).filter(Boolean);

        return `
            <div class="space-y-4">
                <div class="rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] p-4">
                    <dl class="grid gap-2 text-xs sm:grid-cols-2">
                        <div><dt class="text-[#7b8299]">Toko</dt><dd class="font-semibold text-[#1f2544]">${escapeHtml(outletName)}</dd></div>
                        <div><dt class="text-[#7b8299]">Kasir</dt><dd class="font-semibold text-[#1f2544]">${escapeHtml(cashierName)}</dd></div>
                        <div><dt class="text-[#7b8299]">Member</dt><dd class="font-semibold text-[#1f2544]">${escapeHtml(memberName)}</dd></div>
                        <div><dt class="text-[#7b8299]">Metode bayar</dt><dd class="font-semibold text-[#1f2544]">${escapeHtml(methodLabels[paymentMethod] || paymentMethod)}</dd></div>
                    </dl>
                </div>

                <div class="overflow-hidden rounded-2xl border border-[#e7ebff]">
                    <table class="min-w-full text-xs">
                        <thead class="border-b border-[#e7ebff] bg-[#f7f9ff] text-[#5e647f]">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold">Produk</th>
                                <th class="px-3 py-2 text-right font-semibold">Qty</th>
                                <th class="px-3 py-2 text-right font-semibold">Harga</th>
                                <th class="px-3 py-2 text-right font-semibold">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#eef3ff]">
                            ${lines.map((line) => `
                                <tr>
                                    <td class="px-3 py-2 font-medium text-[#1f2544]">${escapeHtml(line.product.name)}</td>
                                    <td class="px-3 py-2 text-right text-[#5e647f]">${line.quantity}</td>
                                    <td class="px-3 py-2 text-right text-[#5e647f]">${formatRupiah(line.product.selling_price)}</td>
                                    <td class="px-3 py-2 text-right font-semibold text-[#1f2544]">${formatRupiah(Number(line.product.selling_price) * line.quantity)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>

                <dl class="space-y-2 rounded-2xl border border-[#e7ebff] p-4 text-sm">
                    <div class="flex justify-between text-[#5e647f]"><dt>Subtotal</dt><dd class="font-medium text-[#1f2544]">${formatRupiah(subtotal)}</dd></div>
                    <div class="flex justify-between text-[#5e647f]"><dt>Estimasi diskon</dt><dd class="font-medium text-emerald-700">−${formatRupiah(discount)}</dd></div>
                    <div class="flex justify-between border-t border-[#eef3ff] pt-2 text-base font-semibold"><dt>Total</dt><dd>${formatRupiah(total)}</dd></div>
                    <div class="flex justify-between text-[#5e647f]"><dt>Poin pembayaran</dt><dd class="font-medium text-[#1f2544]">${pointsToRedeem.toLocaleString('id-ID')}</dd></div>
                    <div class="flex justify-between text-[#5e647f]"><dt>Sisa bayar</dt><dd class="font-medium text-[#1f2544]">${formatRupiah(amountDue)}</dd></div>
                    <div class="flex justify-between text-[#5e647f]"><dt>Dibayar</dt><dd class="font-medium text-[#1f2544]">${formatRupiah(paid)}</dd></div>
                    <div class="flex justify-between text-[#5e647f]"><dt>Kembalian</dt><dd class="font-medium text-[#1f2544]">${formatRupiah(cashChange - pointsToMember)} tunai${pointsToMember > 0 ? ` + ${pointsToMember.toLocaleString('id-ID')} poin` : ''}</dd></div>
                    ${couponCode ? `<div class="flex justify-between text-[#5e647f]"><dt>Kupon</dt><dd class="font-medium text-[#1f2544]">${escapeHtml(couponCode)}</dd></div>` : ''}
                    ${appliedPromoNames.length ? `<div class="flex justify-between text-[#5e647f]"><dt>Promo</dt><dd class="font-medium text-[#1f2544]">${escapeHtml(appliedPromoNames.join(', '))}</dd></div>` : ''}
                </dl>
            </div>
        `;
    };

    const openConfirmModal = () => {
        elements.confirmDetails.innerHTML = buildConfirmationHtml();
        elements.confirmModal.classList.remove('hidden');
        elements.confirmModal.classList.add('flex');
    };

    const closeConfirmModal = () => {
        elements.confirmModal.classList.add('hidden');
        elements.confirmModal.classList.remove('flex');
    };

    const renderReceiptCard = (sale, payload) => {
        const outletName = elements.outlet.selectedOptions?.[0]?.textContent?.trim() || 'Toko';
        const cashierName = getSession()?.user?.name || 'Kasir';
        const transactionDate = sale.transaction_date ? new Date(sale.transaction_date).toLocaleString('id-ID') : new Date().toLocaleString('id-ID');
        const appliedPromos = (sale.sale_promos || []).map((salePromo) => {
            const promo = state.promos.find((item) => Number(item.id) === Number(salePromo.promo_id));
            return promo?.name || salePromo?.promo?.name;
        }).filter(Boolean);
        const customerName = sale.customer?.name || state.verifiedMember?.name || '-';
        const paymentLabels = { cash: 'Tunai', debit: 'Kartu debit', bank_transfer: 'Transfer bank', ewallet: 'E-wallet' };
        const items = sale.items || [];

        return `
            <div class="space-y-4">
                <div class="text-center">
                    <p class="text-lg font-bold text-[#1f2544]">${escapeHtml(outletName)}</p>
                    <p class="text-xs uppercase tracking-[0.2em] text-[#8a91a9]">Struk pembayaran</p>
                </div>
                <dl class="grid gap-2 rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] p-4 text-xs">
                    <div class="flex justify-between gap-4"><dt class="text-[#7b8299]">Invoice</dt><dd class="font-semibold text-[#1f2544]">${escapeHtml(sale.invoice_number)}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-[#7b8299]">Tanggal</dt><dd class="font-semibold text-[#1f2544]">${escapeHtml(transactionDate)}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-[#7b8299]">Kasir</dt><dd class="font-semibold text-[#1f2544]">${escapeHtml(cashierName)}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-[#7b8299]">Pelanggan</dt><dd class="font-semibold text-[#1f2544]">${escapeHtml(customerName)}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-[#7b8299]">Pembayaran</dt><dd class="font-semibold text-[#1f2544]">${escapeHtml(paymentLabels[payload.payment_method] || payload.payment_method)}</dd></div>
                </dl>

                <div class="overflow-hidden rounded-2xl border border-[#e7ebff]">
                    <table class="min-w-full text-xs">
                        <thead class="border-b border-[#e7ebff] bg-[#f7f9ff] text-[#5e647f]">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold">Produk</th>
                                <th class="px-3 py-2 text-right font-semibold">Qty</th>
                                <th class="px-3 py-2 text-right font-semibold">Harga</th>
                                <th class="px-3 py-2 text-right font-semibold">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#eef3ff]">
                            ${items.map((item) => `
                                <tr>
                                    <td class="px-3 py-2 font-medium text-[#1f2544]">${escapeHtml(item.product?.name || `Produk #${item.product_id}`)}</td>
                                    <td class="px-3 py-2 text-right text-[#5e647f]">${item.quantity}</td>
                                    <td class="px-3 py-2 text-right text-[#5e647f]">${formatRupiah(item.price)}</td>
                                    <td class="px-3 py-2 text-right font-semibold text-[#1f2544]">${formatRupiah(item.subtotal)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>

                <dl class="space-y-2 rounded-2xl border border-[#e7ebff] p-4 text-sm">
                    <div class="flex justify-between text-[#5e647f]"><dt>Subtotal</dt><dd class="font-medium text-[#1f2544]">${formatRupiah(sale.subtotal)}</dd></div>
                    <div class="flex justify-between text-[#5e647f]"><dt>Diskon</dt><dd class="font-medium text-emerald-700">−${formatRupiah(sale.discount)}</dd></div>
                    <div class="flex justify-between border-t border-[#eef3ff] pt-2 text-base font-semibold"><dt>Total</dt><dd>${formatRupiah(sale.total)}</dd></div>
                    ${Number(sale.points_redeemed) > 0 ? `<div class="flex justify-between text-[#5e647f]"><dt>Poin digunakan</dt><dd class="font-medium text-[#1f2544]">${Number(sale.points_redeemed).toLocaleString('id-ID')}</dd></div>` : ''}
                    ${Number(sale.points_to_member) > 0 ? `<div class="flex justify-between text-[#5e647f]"><dt>Poin kembalian</dt><dd class="font-medium text-[#1f2544]">${Number(sale.points_to_member).toLocaleString('id-ID')}</dd></div>` : ''}
                    <div class="flex justify-between text-[#5e647f]"><dt>Dibayar</dt><dd class="font-medium text-[#1f2544]">${formatRupiah(sale.paid)}</dd></div>
                    <div class="flex justify-between text-[#5e647f]"><dt>Kembalian tunai</dt><dd class="font-medium text-[#1f2544]">${formatRupiah(sale.change)}</dd></div>
                    ${payload.coupon_code ? `<div class="flex justify-between text-[#5e647f]"><dt>Kupon</dt><dd class="font-medium text-[#1f2544]">${escapeHtml(payload.coupon_code)}</dd></div>` : ''}
                    ${appliedPromos.length ? `<div class="flex justify-between text-[#5e647f]"><dt>Promo diterapkan</dt><dd class="font-medium text-[#1f2544]">${escapeHtml(appliedPromos.join(', '))}</dd></div>` : ''}
                </dl>

                <p class="text-center text-xs text-[#7b8299]">Terima kasih atas kunjungan Anda.</p>
                <button type="button" data-pos-print class="no-print min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-4 text-sm font-semibold text-[#5e647f] transition hover:bg-[#f7f9ff]">Cetak struk</button>
            </div>
        `;
    };

    const submitSale = async () => {
        if (state.submittingSale || state.cart.size === 0 || !elements.outlet.value) {
            return;
        }

        const payload = {
            business_id: Number(elements.outlet.value),
            member_phone: elements.memberPhone.value.trim() || null,
            coupon_code: elements.couponCode.value.trim() || null,
            points_to_member: Number(elements.pointsToMember.value || 0),
            points_to_redeem: Number(elements.pointsToRedeem.value || 0),
            items: Array.from(state.cart.values()).map((line) => ({ product_id: Number(line.product.id), quantity: line.quantity })),
            promo_ids: Array.from(state.selectedPromoIds),
            discount: 0,
            tax: 0,
            payment_method: root.querySelector('[data-pos-payment-method]').value,
            paid: Number(elements.paid.value || 0),
        };
        if (payload.points_to_member > 0 && !state.verifiedMember) {
            showMessage('Verifikasi nomor member terlebih dahulu sebelum mengirim poin kembalian.');
            return;
        }
        if (payload.points_to_redeem > 0 && !state.verifiedMember) {
            showMessage('Verifikasi nomor member terlebih dahulu sebelum menggunakan poin pembayaran.');
            return;
        }

        state.submittingSale = true;
        const submitButton = elements.confirmSubmit;
        submitButton.disabled = true;
        submitButton.textContent = 'Menyimpan transaksi…';
        elements.checkout.disabled = true;

        try {
            const response = await apiRequest('/sales', { method: 'POST', body: JSON.stringify(payload) });
            const sale = response.data;
            elements.receipt.innerHTML = renderReceiptCard(sale, payload);
            elements.receipt.classList.remove('hidden');
            elements.alert.classList.add('hidden');
            closeConfirmModal();
            state.cart.clear();
            state.selectedPromoIds.clear();
            state.paidTouched = false;
            elements.memberPhone.value = '';
            elements.couponCode.value = '';
            elements.pointsToRedeem.value = '0';
            elements.pointsToRedeem.disabled = true;
            elements.pointsToMember.value = '0';
            elements.pointsToMember.disabled = true;
            state.verifiedMember = null;
            elements.memberStatus.textContent = 'Verifikasi nomor untuk memakai poin atau menerima poin kembalian.';
            await loadInventory();
        } catch (error) {
            showMessage(apiMessage(error));
        } finally {
            state.submittingSale = false;
            submitButton.disabled = false;
            submitButton.textContent = 'Yakin, simpan transaksi';
            elements.checkout.disabled = state.cart.size === 0 || !elements.outlet.value;
            renderCart();
        }
    };

    elements.checkout.addEventListener('click', () => {
        if (state.cart.size === 0 || !elements.outlet.value) {
            return;
        }

        // Basic payment validation is also enforced by disabled button, but re-check here.
        openConfirmModal();
    });

    elements.confirmSubmit.addEventListener('click', () => {
        submitSale();
    });

    elements.confirmModal.addEventListener('click', (event) => {
        const cancelButton = event.target.closest('[data-pos-confirm-cancel]');
        if (cancelButton || event.target === elements.confirmModal) {
            closeConfirmModal();
        }
    });

    elements.receipt.addEventListener('click', (event) => {
        const printButton = event.target.closest('[data-pos-print]');
        if (printButton) {
            window.print();
        }
    });

    loadData();
}

export function bootPos() {
    document.querySelectorAll('[data-pos-root]').forEach(initializePos);
}
