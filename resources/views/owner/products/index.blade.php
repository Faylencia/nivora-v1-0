@extends('layouts.owner')

@section('title', 'Produk - Nivora')

@section('content')
    <div class="product-crud-surface space-y-6" data-products-root data-edit-on-load="{{ request('edit') }}">
        <x-page-header eyebrow="OWNER / PRODUK" title="Produk dan stok" description="Kelola katalog, harga, dan ketersediaan produk bisnis Anda.">
            <button type="button" data-open-product-create class="group inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#536dfe] to-[#687eff] px-4 text-sm font-semibold text-white shadow-[0_8px_18px_rgba(83,109,254,0.24)] transition duration-200 hover:-translate-y-0.5 hover:from-[#435ce7] hover:to-[#596ff5] hover:shadow-[0_12px_24px_rgba(83,109,254,0.3)] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#536dfe]/20">
                <svg viewBox="0 0 24 24" class="h-4 w-4 transition group-hover:rotate-90" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Tambah produk
            </button>
        </x-page-header>

        <div data-products-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>

        <section class="overflow-hidden rounded-[26px] border border-[#e6eaff] bg-white shadow-[0_18px_50px_rgba(49,67,130,0.07)]">
            <div class="flex flex-col gap-4 border-b border-[#edf0fb] p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-lg font-semibold tracking-tight text-[#20284a]">Katalog produk</h2>
                        <span data-products-count class="rounded-full bg-[#eef1ff] px-2.5 py-1 text-xs font-semibold text-[#536dfe]">Memuat…</span>
                    </div>
                    <p class="mt-1 text-sm text-[#7b8299]">Pantau foto, kategori, harga jual, dan stok lokasi toko.</p>
                </div>
                <div class="grid gap-2 sm:grid-cols-[minmax(220px,1fr)_minmax(170px,auto)]">
                    <label class="relative block">
                        <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#8a91a9]" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4 4"/></svg>
                        <input data-products-search type="search" placeholder="Cari nama, SKU, barcode" class="min-h-10 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] py-2 pl-9 pr-3 text-sm outline-none transition focus:border-[#687EFF] focus:ring-4 focus:ring-[#536dfe]/10">
                    </label>
                    <select data-products-outlet aria-label="Pilih lokasi toko" class="min-h-10 rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3 text-sm text-[#20284a] outline-none transition focus:border-[#687EFF] focus:ring-4 focus:ring-[#536dfe]/10"></select>
                </div>
            </div>

            <div data-products-loading class="p-10 text-center text-sm text-[#5e647f]"><span class="inline-flex items-center gap-2"><span class="h-2 w-2 animate-pulse rounded-full bg-[#687EFF]"></span>Memuat katalog produk…</span></div>

            <div data-products-empty class="hidden p-12 text-center">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#eef1ff] text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m4 7 8-4 8 4v10l-8 4-8-4V7Z"/><path d="m4 7 8 4 8-4M12 11v10M8 5l8 4"/></svg></span>
                <p data-products-empty-title class="mt-4 font-semibold text-[#303858]">Produk belum tersedia</p>
                <p data-products-empty-description class="mt-1 text-sm text-[#7b8299]">Mulai katalog bisnis Anda dengan menambahkan produk dan stok awal.</p>
                <button type="button" data-open-product-create class="mt-5 inline-flex min-h-10 items-center gap-2 rounded-xl bg-gradient-to-r from-[#536dfe] to-[#687eff] px-4 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>Tambah produk pertama</button>
            </div>

            <div data-products-table-wrap class="hidden overflow-x-auto">
                <table class="min-w-[980px] w-full text-left">
                    <thead class="bg-[#f8f9fe] text-[11px] font-bold uppercase tracking-[0.1em] text-[#8a91a9]"><tr><th class="px-6 py-3.5">Produk</th><th class="px-4 py-3.5">SKU</th><th class="px-4 py-3.5">Kategori</th><th class="px-4 py-3.5">Harga jual</th><th class="px-4 py-3.5">Stok</th><th class="px-4 py-3.5">Status</th><th class="px-6 py-3.5 text-right">Aksi</th></tr></thead>
                    <tbody data-products-table class="divide-y divide-[#edf0f8]"></tbody>
                </table>
            </div>
            <div data-products-footer class="hidden flex-wrap items-center justify-between gap-3 border-t border-[#edf0fb] bg-[#fcfcff] px-5 py-3.5 sm:px-6">
                <p data-products-footer-count class="text-xs font-medium text-[#737b96]"></p>
                <p class="text-xs text-[#8a91a9]">Stok dan status mengikuti lokasi operasional toko.</p>
            </div>
        </section>
    </div>

    @include('owner.products.create-modal')
    @include('owner.products.edit-modal')
@endsection
