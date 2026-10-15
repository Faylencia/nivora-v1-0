@extends('layouts.owner')

@section('title', 'Promo Toko - Nivora')

@section('content')
    <div class="owner-promotions space-y-6" data-promotions-root>
        <x-page-header eyebrow="OWNER / PEMASARAN" title="Promo toko" description="Rancang penawaran untuk produk atau transaksi dan kelola masa berlakunya.">
            <button type="button" data-promo-new class="group inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#536dfe] to-[#687eff] px-5 text-sm font-semibold text-white shadow-[0_8px_18px_rgba(83,109,254,0.24)] transition hover:-translate-y-0.5 hover:from-[#435ce7] hover:to-[#596ff5] hover:shadow-lg focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#536dfe]/20">
                <span class="text-lg leading-none transition group-hover:rotate-90">+</span> Buat promo
            </button>
        </x-page-header>

        <div data-promo-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>

        <div class="promo-summary-grid grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="promo-summary-card"><div class="promo-summary-heading"><span class="promo-summary-icon is-total"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4h14v16H5zM8 8h8m-8 4h8m-8 4h5"/></svg></span><span>Semua kampanye</span></div><p data-promo-stat="total">—</p><small>Tersimpan di toko</small></article>
            <article class="promo-summary-card"><div class="promo-summary-heading"><span class="promo-summary-icon is-active"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6"/><circle cx="12" cy="12" r="9"/></svg></span><span>Sedang berjalan</span></div><p data-promo-stat="active">—</p><small>Aktif dan dalam periode</small></article>
            <article class="promo-summary-card"><div class="promo-summary-heading"><span class="promo-summary-icon is-product"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Zm0 0v9m8-4.5-8 4.5-8-4.5"/></svg></span><span>Promo produk</span></div><p data-promo-stat="product">—</p><small>Diskon produk pilihan</small></article>
            <article class="promo-summary-card"><div class="promo-summary-heading"><span class="promo-summary-icon is-transaction"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5h2l2 10h11l3-7H6m3 12h.01M17 20h.01"/></svg></span><span>Promo transaksi</span></div><p data-promo-stat="transaction">—</p><small>Diskon pada keranjang</small></article>
        </div>

        <section class="overflow-hidden rounded-[26px] border border-[#e6eaff] bg-white shadow-[0_18px_50px_rgba(49,67,130,0.07)]">
            <div class="flex flex-col gap-4 border-b border-[#edf0fb] p-5 sm:p-6 xl:flex-row xl:items-center xl:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2"><h2 class="text-lg font-semibold tracking-tight text-[#20284a]">Daftar kampanye</h2><span data-promo-result-count class="rounded-full bg-[#eef1ff] px-2.5 py-1 text-xs font-semibold text-[#536dfe]">Memuat…</span></div>
                    <p class="mt-1 text-sm text-[#7b8299]">Atur target promo, diskon, kupon, dan periode penawaran.</p>
                </div>
                <div class="grid w-full gap-2 sm:grid-cols-2 xl:max-w-2xl xl:grid-cols-[minmax(220px,1fr)_auto_auto]">
                    <label class="relative sm:col-span-2 xl:col-span-1"><span class="sr-only">Cari promo</span><svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#8b93ad]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.2 4.2"/></svg><input type="search" data-promo-search placeholder="Cari nama atau kode promo" class="min-h-11 w-full rounded-xl border border-[#e5e9f7] bg-[#fafbff] pl-10 pr-3 text-sm text-[#20284a] outline-none transition placeholder:text-[#a1a8bc] focus:border-[#8697ff] focus:bg-white focus:ring-4 focus:ring-[#536dfe]/10"></label>
                    <select data-promo-type-filter aria-label="Filter jenis promo" class="min-h-11 rounded-xl border border-[#e5e9f7] bg-[#fafbff] px-3 text-sm font-medium text-[#4e5677] outline-none transition focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10">
                        <option value="">Semua jenis</option><option value="product">Produk</option><option value="transaction">Transaksi</option>
                    </select>
                    <select data-promo-status-filter aria-label="Filter status promo" class="min-h-11 rounded-xl border border-[#e5e9f7] bg-[#fafbff] px-3 text-sm font-medium text-[#4e5677] outline-none transition focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10">
                        <option value="">Semua status</option><option value="active">Aktif</option><option value="inactive">Nonaktif</option>
                    </select>
                </div>
            </div>
            <div data-promo-loading class="p-12 text-center text-sm text-[#7b8299]"><span class="inline-flex items-center gap-2"><span class="h-2 w-2 animate-pulse rounded-full bg-[#687EFF]"></span>Memuat kampanye promo…</span></div>
            <div data-promo-list class="hidden grid gap-3 p-4 sm:p-5"></div>
            <div data-promo-empty class="hidden p-10 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#f0f2ff] text-2xl text-[#687eff]" aria-hidden="true">%</div>
                <p data-promo-empty-title class="mt-3 font-semibold text-[#303858]">Belum ada promo</p>
                <p data-promo-empty-description class="mt-1 text-sm text-[#7b8299]">Buat promo pertama untuk mulai menarik pelanggan.</p>
            </div>
        </section>

        <div data-promo-modal class="fixed inset-0 z-[70] hidden items-center justify-center overflow-hidden bg-[#101633]/60 p-3 backdrop-blur-sm sm:p-6" role="dialog" aria-modal="true" aria-labelledby="promo-modal-title">
            <div class="relative my-auto flex max-h-[calc(100vh-1.5rem)] w-full max-w-3xl flex-col overflow-hidden rounded-[28px] border border-[#e6eaff] bg-white shadow-[0_30px_100px_rgba(15,23,60,0.34)] sm:max-h-[calc(100vh-3rem)]">
                <header class="relative flex shrink-0 items-center justify-between gap-4 border-b border-[#edf0fb] px-5 py-5 sm:px-7">
                    <span class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-[#536dfe] via-[#8293ff] to-[#54c8e8]"></span>
                    <div class="flex items-center gap-4"><span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#eef1ff] text-xl font-bold text-[#536dfe]">%</span><div><p class="text-xs font-bold uppercase tracking-[0.16em] text-[#687eff]">Pusat pemasaran</p><h2 data-promo-modal-title id="promo-modal-title" class="mt-1 text-xl font-semibold tracking-tight text-[#20284a]">Buat promo</h2><p class="mt-1 text-sm text-[#7b8299]">Siapkan penawaran yang menarik untuk customer.</p></div></div>
                    <button type="button" data-promo-close aria-label="Tutup" class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#f4f6fc] text-xl text-[#68708b] transition hover:rotate-90 hover:bg-[#e9edfa] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#536dfe]/15">×</button>
                </header>
                <div class="min-h-0 overflow-y-auto p-5 sm:p-7">
                <div data-promo-form-message class="mb-4 hidden rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700" role="alert"></div>
                <form data-promo-form class="space-y-6">
                    <section class="space-y-4"><div class="flex items-center gap-3"><span class="flex h-8 w-8 items-center justify-center rounded-xl bg-[#eef1ff] text-xs font-bold text-[#536dfe]">01</span><div><h3 class="text-sm font-semibold text-[#20284a]">Identitas promo</h3><p class="text-xs text-[#8b93ad]">Nama dan cara customer mengakses promo</p></div></div><div class="grid gap-4 sm:grid-cols-2">
                        <label class="space-y-1.5 text-sm font-semibold text-[#59617d] sm:col-span-2">Nama promo
                            <input name="name" required maxlength="150" placeholder="Contoh: Weekend Ceria" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:bg-white focus:ring-4 focus:ring-[#536dfe]/10">
                        </label>
                        <label class="space-y-1.5 text-sm font-semibold text-[#59617d] sm:col-span-2">Kode kupon <span class="font-normal text-[#8b93ad]">(opsional)</span>
                            <input name="code" maxlength="32" pattern="[A-Za-z0-9_-]+" autocomplete="off" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal uppercase tracking-wider text-[#20284a] outline-none transition focus:border-[#8697ff] focus:bg-white focus:ring-4 focus:ring-[#536dfe]/10" placeholder="Contoh: BELANJA10">
                            <span class="block text-xs font-normal text-[#8b93ad]">Kosongkan untuk menerapkan promo secara otomatis.</span>
                        </label>
                    </div></section>
                    <section class="space-y-4 border-t border-[#edf0fb] pt-5"><div class="flex items-center gap-3"><span class="flex h-8 w-8 items-center justify-center rounded-xl bg-violet-50 text-xs font-bold text-violet-600">02</span><div><h3 class="text-sm font-semibold text-[#20284a]">Penawaran</h3><p class="text-xs text-[#8b93ad]">Tentukan diskon dan targetnya</p></div></div><div class="grid gap-4 sm:grid-cols-2">
                        <label class="space-y-1.5 text-sm font-semibold text-[#59617d]">Jenis promo
                            <select name="type" data-promo-type required class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:bg-white focus:ring-4 focus:ring-[#536dfe]/10"><option value="product">Diskon produk</option><option value="transaction">Diskon transaksi</option></select>
                        </label>
                        <label class="space-y-1.5 text-sm font-semibold text-[#59617d]">Diskon (%)
                            <input name="discount_value" type="number" min="0.01" max="100" step="0.01" required placeholder="10" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:bg-white focus:ring-4 focus:ring-[#536dfe]/10">
                        </label>
                        <label data-promo-minimum-field class="hidden space-y-1.5 text-sm font-semibold text-[#59617d] sm:col-span-2">Minimum transaksi (Rp)
                            <input name="min_transaction" type="number" min="0" step="1" placeholder="50000" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:bg-white focus:ring-4 focus:ring-[#536dfe]/10">
                        </label>
                        <fieldset data-promo-products-field class="space-y-2 sm:col-span-2"><legend class="text-sm font-semibold text-[#59617d]">Produk yang mendapat promo</legend>
                            <div data-promo-product-options class="max-h-52 space-y-1 overflow-y-auto rounded-xl border border-[#e4e8f4] bg-[#fafbff] p-3 text-sm"></div>
                            <p class="text-xs text-[#8b93ad]">Pilih satu atau beberapa produk untuk penawaran ini.</p>
                        </fieldset>
                    </div></section>
                    <section class="space-y-4 border-t border-[#edf0fb] pt-5"><div class="flex items-center gap-3"><span class="flex h-8 w-8 items-center justify-center rounded-xl bg-sky-50 text-xs font-bold text-sky-600">03</span><div><h3 class="text-sm font-semibold text-[#20284a]">Jadwal dan status</h3><p class="text-xs text-[#8b93ad]">Atur periode tayang promo</p></div></div><div class="grid gap-4 sm:grid-cols-2">
                        <label class="space-y-1.5 text-sm font-semibold text-[#59617d]">Mulai
                            <input name="start_date" type="datetime-local" required class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:bg-white focus:ring-4 focus:ring-[#536dfe]/10">
                        </label>
                        <label class="space-y-1.5 text-sm font-semibold text-[#59617d]">Berakhir
                            <input name="end_date" type="datetime-local" required class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:bg-white focus:ring-4 focus:ring-[#536dfe]/10">
                        </label>
                        <label class="space-y-1.5 text-sm font-semibold text-[#59617d]">Status
                            <select name="status" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:bg-white focus:ring-4 focus:ring-[#536dfe]/10"><option value="active">Aktif</option><option value="inactive">Nonaktif</option></select>
                        </label>
                        <label class="space-y-1.5 text-sm font-semibold text-[#59617d] sm:col-span-2">Deskripsi <span class="font-normal text-[#8b93ad]">(opsional)</span>
                            <textarea name="description" rows="3" placeholder="Ceritakan detail singkat penawaran ini…" class="w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 py-3 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:bg-white focus:ring-4 focus:ring-[#536dfe]/10"></textarea>
                        </label>
                    </div></section>
                    <div class="flex flex-col-reverse gap-2 border-t border-[#edf0fb] pt-5 sm:flex-row sm:justify-end">
                        <button type="button" data-promo-close class="min-h-11 rounded-xl border border-[#e4e8f4] bg-white px-4 text-sm font-semibold text-[#59617d] transition hover:bg-[#f8f9fd]">Batal</button>
                        <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#536dfe] to-[#687eff] px-5 text-sm font-semibold text-white shadow-[0_8px_18px_rgba(83,109,254,0.2)] transition hover:-translate-y-0.5 hover:from-[#435ce7] hover:to-[#596ff5] disabled:cursor-wait disabled:opacity-60"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14m-7-7 7 7-7 7"/></svg><span data-promo-submit-label>Simpan promo</span></button>
                    </div>
                </form>
                </div>
            </div>
        </div>

        <div data-promo-delete-modal class="fixed inset-0 z-[80] hidden items-center justify-center bg-[#101633]/60 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="promo-delete-title">
            <section class="w-full max-w-md overflow-hidden rounded-[26px] border border-[#e6eaff] bg-white shadow-[0_30px_100px_rgba(15,23,60,0.34)]">
                <div class="p-6 sm:p-7"><span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600"><svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M10 11v6m4-6v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg></span><h2 id="promo-delete-title" data-promo-delete-title class="mt-4 text-lg font-semibold text-[#20284a]">Hapus promo?</h2><p data-promo-delete-copy class="mt-2 text-sm leading-6 text-[#737b96]"></p></div>
                <div class="flex flex-col-reverse gap-2 border-t border-[#edf0fb] bg-[#fcfcff] p-5 sm:flex-row sm:justify-end"><button type="button" data-promo-delete-cancel class="min-h-10 rounded-xl border border-[#e4e8f4] bg-white px-4 text-sm font-semibold text-[#59617d] hover:bg-[#f8f9fd]">Batal</button><button type="button" data-promo-delete-confirm class="min-h-10 rounded-xl bg-rose-600 px-4 text-sm font-semibold text-white transition hover:bg-rose-700 disabled:cursor-wait disabled:opacity-60">Hapus promo</button></div>
            </section>
        </div>
    </div>
@endsection
