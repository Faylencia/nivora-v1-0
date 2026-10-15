import { apiMessage, apiRequest, escapeHtml, formatRupiah, pagedItems, requireSession } from './nivora-api';

function formatPromoDate(value) {
    if (!value) {
        return '—';
    }

    return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium' }).format(new Date(value));
}

function promoState(promo) {
    if (promo.is_currently_active) {
        return 'active';
    }

    const now = Date.now();
    const start = promo.start_date ? new Date(promo.start_date).getTime() : 0;
    const end = promo.end_date ? new Date(promo.end_date).getTime() : 0;

    if (promo.status === 'active' && start > now) {
        return 'upcoming';
    }

    return promo.status === 'active' && end > now ? 'upcoming' : 'expired';
}

function promoCard(promo) {
    const status = promoState(promo);
    const statusLabel = { active: 'Sedang berlaku', upcoming: 'Akan datang', expired: 'Tidak berlaku' }[status];
    const discount = `${Number(promo.discount_value || 0)}%`;
    const typeLabel = promo.type === 'product' ? 'Promo produk' : 'Promo transaksi';
    const productNames = (promo.products || []).map((product) => product.name).filter(Boolean);
    const terms = promo.type === 'product'
        ? (productNames.length
            ? `<div class="mt-2 flex flex-wrap gap-2">${productNames.map((name) => `<span class="cashier-promo-chip">${escapeHtml(name)}</span>`).join('')}</div>`
            : '<p class="mt-2 text-sm text-[#5e647f]">Berlaku untuk produk pilihan.</p>')
        : `<div class="cashier-promo-minimum"><span>Minimum transaksi</span><strong>${formatRupiah(promo.min_transaction)}</strong></div>`;
    const code = promo.code
        ? `<div class="cashier-promo-code"><span>Kode promo</span><strong>${escapeHtml(promo.code)}</strong></div>`
        : '';

    return `<article class="cashier-promo-card">
        <div class="cashier-promo-card-head">
            <div class="min-w-0">
                <span class="cashier-promo-type">${typeLabel}</span>
                <h3>${escapeHtml(promo.name)}</h3>
                <p>${escapeHtml(promo.description || 'Tidak ada deskripsi promo.')}</p>
            </div>
            <div class="cashier-promo-discount"><span>Diskon</span><strong>${discount}</strong></div>
        </div>
        <div class="cashier-promo-card-body">
            <div class="flex items-center justify-between gap-3">
                <span class="cashier-promo-status is-${status}"><i aria-hidden="true"></i>${statusLabel}</span>
                ${code}
            </div>
            <div>${promo.type === 'product' ? '<p class="cashier-promo-label">Produk yang berlaku</p>' : ''}${terms}</div>
            <div class="cashier-promo-validity"><span>Masa berlaku</span><strong>${formatPromoDate(promo.start_date)} – ${formatPromoDate(promo.end_date)}</strong></div>
        </div>
    </article>`;
}

export function bootCashierPromos() {
    const root = document.querySelector('[data-cashier-promos]');

    if (!root || !requireSession()) {
        return;
    }

    const elements = {
        list: root.querySelector('[data-promo-list]'),
        empty: root.querySelector('[data-promo-empty]'),
        loading: root.querySelector('[data-promo-loading]'),
        error: root.querySelector('[data-promo-error]'),
        search: root.querySelector('[data-promo-search]'),
        filter: root.querySelector('[data-promo-filter]'),
    };
    let promos = [];

    const render = () => {
        const query = elements.search.value.trim().toLocaleLowerCase('id-ID');
        const filter = elements.filter.value;
        const visiblePromos = promos.filter((promo) => {
            const searchable = `${promo.name || ''} ${promo.code || ''} ${promo.description || ''}`.toLocaleLowerCase('id-ID');

            return (!query || searchable.includes(query)) && (filter === 'all' || promoState(promo) === filter);
        });

        elements.list.innerHTML = visiblePromos.map(promoCard).join('');
        elements.empty.classList.toggle('hidden', visiblePromos.length > 0);
        elements.empty.querySelector('h3').textContent = promos.length === 0 ? 'Belum ada promo' : 'Promo tidak ditemukan';
        elements.empty.querySelector('p').textContent = promos.length === 0
            ? 'Promo toko akan tampil di sini setelah dibuat oleh owner.'
            : 'Coba ubah kata kunci atau filter status promo.';
    };

    elements.search.addEventListener('input', render);
    elements.filter.addEventListener('change', render);

    apiRequest('/promotions?limit=100')
        .then((response) => {
            promos = pagedItems(response);
            elements.loading.classList.add('hidden');
            render();
        })
        .catch((error) => {
            elements.loading.classList.add('hidden');
            elements.error.textContent = apiMessage(error);
            elements.error.classList.remove('hidden');
        });
}
