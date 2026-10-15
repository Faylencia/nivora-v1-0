@extends('layouts.owner')

@section('title', 'Detail Produk - Nivora')

@section('content')
    <div class="product-crud-surface space-y-6" data-product-detail="{{ $product }}">
        <x-page-header eyebrow="OWNER / PRODUK" title="Detail produk" description="Rincian produk dan catatan stok yang tersimpan.">
            <a href="{{ route('owner.products.index') }}" data-go-back class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-[#e7ebff] bg-white px-4 text-sm font-medium text-[#5e647f]"><span aria-hidden="true">←</span> Kembali ke produk</a>
            <a href="{{ route('owner.products.index', ['edit' => $product]) }}" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-[#687EFF] px-4 text-sm font-semibold text-white">Edit produk</a>
        </x-page-header>
        <div data-entity-loading class="ni-panel p-8 text-center text-sm text-[#5e647f]">Memuat detail produk…</div>
        <div data-product-content class="hidden space-y-5">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article class="ni-stat-card"><p class="text-sm text-[#5e647f]">Harga modal</p><p data-product-purchase-price class="mt-2 text-xl font-semibold">—</p></article>
                <article class="ni-stat-card"><p class="text-sm text-[#5e647f]">Harga jual</p><p data-product-selling-price class="mt-2 text-xl font-semibold">—</p></article>
                <article class="ni-stat-card"><p class="text-sm text-[#5e647f]">Kategori</p><p data-product-category-name class="mt-2 text-xl font-semibold">—</p></article>
                <article class="ni-stat-card"><p class="text-sm text-[#5e647f]">Status</p><p data-product-status class="mt-2"></p></article>
            </div>
            <section class="ni-panel overflow-hidden">
                <div class="grid md:grid-cols-[240px_minmax(0,1fr)]">
                    <div class="relative flex min-h-56 items-center justify-center border-b border-[#e7ebff] bg-[#f7f9ff] p-5 md:border-b-0 md:border-r"><img data-product-photo alt="Foto produk" class="hidden h-48 w-full rounded-2xl object-contain"><span data-product-photo-empty class="product-photo-placeholder flex h-28 w-28 items-center justify-center rounded-3xl border border-[#dfe6ff] bg-[#eef1ff] text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-14 w-14" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m4 7 8-4 8 4v10l-8 4-8-4V7Z"/><path d="m4 7 8 4 8-4M12 11v10"/></svg></span></div>
                    <div class="p-5 sm:p-7">
                        <p class="ni-section-label">PRODUK</p><h1 data-product-name class="mt-2 text-2xl font-semibold text-[#1f2544]"></h1>
                        <p data-product-description class="mt-3 whitespace-pre-line text-sm leading-6 text-[#5e647f]"></p>
                        <dl class="mt-5 grid gap-4 border-t border-[#e7ebff] pt-5 sm:grid-cols-3"><div><dt class="text-xs text-[#5e647f]">SKU</dt><dd data-product-sku class="mt-1 font-medium"></dd></div><div><dt class="text-xs text-[#5e647f]">Barcode</dt><dd data-product-barcode class="mt-1 font-medium"></dd></div><div><dt class="text-xs text-[#5e647f]">Satuan</dt><dd data-product-unit class="mt-1 font-medium"></dd></div></dl>
                    </div>
                </div>
            </section>
            <section class="ni-panel p-5 sm:p-7"><h2 class="font-semibold text-[#1f2544]">Stok toko</h2><div data-product-inventories class="mt-4 space-y-2"></div></section>
        </div>
    </div>
@endsection
