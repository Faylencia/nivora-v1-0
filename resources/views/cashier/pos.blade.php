@extends('layouts.cashier')

@section('title', 'Kasir POS - Nivora')

@section('content')
    <div class="cashier-pos space-y-6" data-pos-root>
        <x-page-header eyebrow="KASIR / TRANSAKSI" title="Kasir POS" description="Pilih produk, gunakan promo yang memenuhi syarat, lalu simpan transaksi ke backend.">
            <a href="{{ route('cashier.dashboard') }}" data-go-back class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-[#e7ebff] bg-white px-3 text-sm font-medium text-[#5e647f] transition hover:bg-[#f7f9ff]" aria-label="Kembali ke halaman sebelumnya">
                <span aria-hidden="true">←</span> Kembali
            </a>
            <label class="flex items-center gap-2 rounded-xl border border-[#e3e8f6] bg-white px-3 py-2 text-sm shadow-sm">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#eef1ff] text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M3 21V8l9-5 9 5v13M3 10h18M8 21v-6h8v6"/></svg></span>
                <span class="shrink-0 text-xs font-semibold text-[#7b8299]">Lokasi toko</span>
                <select data-pos-outlet class="min-h-8 min-w-28 bg-transparent text-sm font-semibold text-[#20284a] outline-none"></select>
            </label>
        </x-page-header>

        <div data-pos-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>
        <div data-pos-receipt class="hidden rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800" role="status" aria-live="polite"></div>

        <div class="pos-layout-toolbar flex flex-col gap-3 rounded-2xl border border-[#e6eaff] bg-white/90 p-3 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:px-4"><div class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#eef1ff] text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="4" width="8" height="16" rx="1.5"/><rect x="13" y="4" width="8" height="16" rx="1.5"/></svg></span><div><p class="text-sm font-semibold text-[#20284a]">Tata letak kasir</p><p class="text-xs text-[#7b8299]">Atur posisi katalog dan keranjang</p></div></div><div class="pos-layout-options flex w-full gap-1 rounded-xl bg-[#f3f5fc] p-1 sm:w-auto"><button type="button" data-pos-layout-option="products-left" aria-pressed="true" class="pos-layout-option inline-flex min-h-9 flex-1 items-center justify-center gap-2 rounded-lg bg-white px-3 text-xs font-semibold text-[#39436f] shadow-sm transition sm:flex-none"><svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="4" width="12" height="16" rx="1.5"/><rect x="17" y="4" width="4" height="16" rx="1"/></svg>Produk kiri · keranjang kanan</button><button type="button" data-pos-layout-option="cart-left" aria-pressed="false" class="pos-layout-option inline-flex min-h-9 flex-1 items-center justify-center gap-2 rounded-lg px-3 text-xs font-semibold text-[#858ca4] transition hover:text-[#39436f] sm:flex-none"><svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="4" width="4" height="16" rx="1"/><rect x="9" y="4" width="12" height="16" rx="1.5"/></svg>Keranjang kiri · produk kanan</button></div></div>

        <div class="pos-layout-grid items-start gap-5" data-pos-layout="products-left">
            <section class="pos-products-panel ni-panel overflow-hidden">
                <div class="flex flex-col gap-4 border-b border-[#e7ebff] p-5 sm:p-6">
                    <div class="flex items-start gap-3"><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#eef1ff] text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Zm0 0v9m8-4.5-8 4.5-8-4.5"/></svg></span><div><h2 class="text-lg font-semibold tracking-tight text-[#20284a]">Pilih produk</h2><p class="mt-1 text-sm text-[#7b8299]">Harga dan ketersediaan dibaca langsung dari katalog.</p></div></div>
                    <div class="grid gap-2 sm:grid-cols-[minmax(180px,1fr)_auto]">
                        <label class="pos-search relative block"><svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#8a91a9]" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4 4"/></svg><input data-pos-search type="search" placeholder="Cari nama atau SKU" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] py-2 pl-10 pr-3 text-sm outline-none transition focus:border-[#687EFF] focus:ring-4 focus:ring-[#536dfe]/10"></label>
                        <select data-pos-category class="min-h-11 rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3 text-sm text-[#20284a] outline-none transition focus:border-[#687EFF] focus:ring-4 focus:ring-[#536dfe]/10"><option value="">Semua kategori</option></select>
                    </div>
                </div>
                <div data-pos-products-loading class="p-12 text-center text-sm text-[#5e647f]"><span class="inline-flex items-center gap-3 rounded-2xl bg-[#f7f9ff] px-4 py-3"><span class="h-2 w-2 animate-pulse rounded-full bg-[#687EFF]"></span>Menghubungkan ke katalog…</span></div>
                <div data-pos-products class="hidden grid gap-3 p-5 sm:grid-cols-2 sm:p-6 2xl:grid-cols-3"></div>
                <div data-pos-products-empty class="hidden px-6 py-14 text-center">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#eef1ff] text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Zm0 0v9m8-4.5-8 4.5-8-4.5"/></svg></span>
                    <p class="font-semibold">Belum ada produk yang bisa dijual.</p>
                    <p class="mt-1 text-sm text-[#5e647f]">Tambahkan produk dan stok awal terlebih dahulu.</p>
                    <a href="{{ route('owner.products.index') }}" class="mt-4 inline-flex min-h-10 items-center gap-2 rounded-xl bg-gradient-to-r from-[#536dfe] to-[#687eff] px-4 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>Kelola produk</a>
                </div>
            </section>

            <section class="pos-cart ni-panel p-4 sm:p-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3"><span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#eef1ff] text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M3 5h2l2.2 10.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 1.9-1.5L21 9H6"/><circle cx="10" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg></span><div><p class="ni-section-label">TRANSAKSI BARU</p><h2 class="mt-0.5 text-xl font-semibold tracking-tight text-[#20284a]">Keranjang</h2></div></div>
                    <span data-pos-item-count class="rounded-full bg-[#eef3ff] px-3 py-1 text-xs font-semibold text-[#5265d8]">0 item</span>
                </div>

                <div data-pos-cart class="mt-4 max-h-[28rem] space-y-3 overflow-y-auto"></div>

                <div class="mt-5 rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] p-3">
                    <div class="flex items-center justify-between gap-2"><h3 class="text-sm font-semibold">Promo yang digunakan</h3><span class="text-[11px] text-[#7b8299]">Diskon akhir dihitung backend</span></div>
                    <div data-pos-promos class="mt-3 space-y-2"></div>
                </div>

                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <label class="space-y-1 text-xs font-semibold uppercase tracking-wide text-[#68718b] sm:col-span-2">Member <span class="font-normal normal-case tracking-normal">(opsional, pakai nomor akun)</span>
                        <div class="flex gap-2"><input data-pos-member-phone type="tel" inputmode="tel" autocomplete="off" placeholder="Nomor ponsel member" class="min-h-11 min-w-0 flex-1 rounded-xl border border-[#e7ebff] bg-white px-3 text-sm font-normal normal-case tracking-normal text-[#1f2544] outline-none"><button type="button" data-pos-member-lookup class="shrink-0 rounded-xl border border-[#dfe7ff] bg-[#eef3ff] px-3 text-xs font-semibold text-[#1f2544]">Verifikasi</button></div>
                        <span data-pos-member-status class="block text-[11px] font-normal normal-case tracking-normal text-[#7b8299]">Verifikasi nomor untuk memakai poin atau menerima poin kembalian.</span>
                    </label>
                    <label class="space-y-1 text-xs font-semibold uppercase tracking-wide text-[#68718b]">Kode kupon
                        <input data-pos-coupon-code type="text" maxlength="32" placeholder="Contoh: BELANJA10" class="min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm font-normal uppercase tracking-normal text-[#1f2544] outline-none focus:border-[#687EFF]">
                    </label>
                    <label class="space-y-1 text-xs font-semibold uppercase tracking-wide text-[#68718b]">Poin untuk pembayaran
                        <input data-pos-points-to-redeem type="number" min="0" step="1" value="0" disabled class="min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm font-normal normal-case tracking-normal text-[#1f2544] outline-none disabled:bg-slate-100">
                        <span class="block text-[11px] font-normal normal-case tracking-normal text-[#7b8299]">1 poin = Rp1; dibatasi saldo member dan total belanja.</span>
                    </label>
                    <label class="space-y-1 text-xs font-semibold uppercase tracking-wide text-[#68718b]">Poin untuk kembalian
                        <input data-pos-points-to-member type="number" min="0" step="1" value="0" disabled class="min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm font-normal normal-case tracking-normal text-[#1f2544] outline-none disabled:bg-slate-100">
                        <span class="block text-[11px] font-normal normal-case tracking-normal text-[#7b8299]">1 poin = Rp1; dibatasi oleh kembalian dan saldo kasir.</span>
                    </label>
                    <label class="space-y-1 text-xs font-semibold uppercase tracking-wide text-[#68718b]">Metode pembayaran
                        <select data-pos-payment-method class="min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm font-normal normal-case tracking-normal text-[#1f2544] outline-none"><option value="cash">Tunai</option><option value="debit">Kartu debit</option><option value="bank_transfer">Transfer bank</option><option value="ewallet">E-wallet</option></select>
                    </label>
                    <label class="space-y-1 text-xs font-semibold uppercase tracking-wide text-[#68718b]">Dibayar (Rp)
                        <input data-pos-paid type="number" min="0" step="1" value="0" class="min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm font-normal normal-case tracking-normal text-[#1f2544] outline-none focus:border-[#687EFF]">
                    </label>
                </div>

                <dl class="mt-5 space-y-2 border-t border-[#eef3ff] pt-4 text-sm">
                    <div class="flex justify-between text-[#5e647f]"><dt>Subtotal</dt><dd data-pos-subtotal class="font-medium text-[#1f2544]">Rp0</dd></div>
                    <div class="flex justify-between text-[#5e647f]"><dt>Estimasi diskon</dt><dd data-pos-discount class="font-medium text-emerald-700">−Rp0</dd></div>
                    <div class="flex justify-between border-t border-[#eef3ff] pt-3 text-base font-semibold"><dt>Total estimasi</dt><dd data-pos-total>Rp0</dd></div>
                    <div class="flex justify-between text-sm text-[#5e647f]"><dt>Sisa bayar setelah poin</dt><dd data-pos-amount-due class="font-medium text-[#1f2544]">Rp0</dd></div>
                    <div class="flex justify-between text-xs text-[#7b8299]"><dt>Estimasi kembalian</dt><dd data-pos-change>Rp0</dd></div>
                </dl>
                <button type="button" data-pos-checkout class="mt-5 min-h-12 w-full rounded-xl bg-[#687EFF] px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-[#596eea] disabled:cursor-not-allowed disabled:opacity-50">Simpan transaksi</button>
                <p class="mt-2 text-center text-xs text-[#7b8299]">Harga, stok, syarat promo, dan diskon diverifikasi lagi oleh server.</p>
            </section>
        </div>
    </div>
@endsection
