@extends('layouts.owner')

@section('title', 'Edit Produk - Nivora')

@section('content')
    <div class="product-crud-surface space-y-6" data-product-edit="{{ $product }}">
        <x-page-header eyebrow="OWNER / PRODUK" title="Edit produk" description="Perbarui foto, identitas, harga, dan status produk yang tersimpan.">
            <a href="{{ route('owner.products.index') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-[#e4e8f4] bg-white px-4 text-sm font-semibold text-[#59617d] transition hover:-translate-y-0.5 hover:border-[#cbd3e8] hover:bg-[#f8f9fd] hover:shadow-sm focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#536dfe]/15"><span aria-hidden="true">←</span> Kembali ke produk</a>
        </x-page-header>

        <div data-entity-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>
        <div data-entity-loading class="overflow-hidden rounded-[26px] border border-[#e6eaff] bg-white p-8 text-center text-sm text-[#5e647f] shadow-[0_18px_50px_rgba(49,67,130,0.07)]"><span class="inline-flex items-center gap-2"><span class="h-2 w-2 animate-pulse rounded-full bg-[#687EFF]"></span>Memuat informasi produk…</span></div>

        <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
            <form data-product-edit-form class="hidden overflow-hidden rounded-[26px] border border-[#e6eaff] bg-white shadow-[0_18px_50px_rgba(49,67,130,0.07)]" enctype="multipart/form-data">
                <div class="relative flex items-center gap-4 border-b border-[#edf0fb] px-5 py-5 sm:px-7"><span class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-[#536dfe] via-[#8293ff] to-[#54c8e8]"></span><span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#eef1ff] text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m14 5 5 5M4 20l4.5-1 11-11a2.1 2.1 0 0 0-3-3l-11 11L4 20Z"/><path d="M4 20h5"/></svg></span><div><p class="text-xs font-bold uppercase tracking-[0.16em] text-[#687eff]">Pembaruan katalog</p><h2 class="mt-1 text-xl font-semibold tracking-tight text-[#20284a]">Edit informasi produk</h2><p class="mt-1 text-sm text-[#7b8299]">Periksa kembali perubahan sebelum menyimpan.</p></div></div>

                <div class="space-y-6 p-5 sm:p-7">
                    <section class="space-y-4"><div><p class="text-xs font-bold uppercase tracking-[0.12em] text-[#687eff]">01 · Identitas</p><h3 class="mt-1 font-semibold text-[#20284a]">Nama dan pengelompokan</h3></div><div class="grid gap-4 sm:grid-cols-2">
                        <label class="space-y-1.5 text-sm font-semibold sm:col-span-2">Nama produk <span class="text-rose-600">*</span><input name="name" required maxlength="150" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"></label>
                        <label class="space-y-1.5 text-sm font-semibold">SKU <span class="text-rose-600">*</span><input name="sku" required maxlength="80" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal uppercase text-[#20284a] outline-none transition focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"></label>
                        <label class="space-y-1.5 text-sm font-semibold">Barcode<input name="barcode" maxlength="100" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"></label>
                        <label class="space-y-1.5 text-sm font-semibold sm:col-span-2">Kategori <span class="text-rose-600">*</span><select name="category_id" required class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm text-[#20284a] outline-none transition focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"></select></label>
                        <label class="space-y-1.5 text-sm font-semibold sm:col-span-2">Deskripsi<textarea name="description" rows="3" class="w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 py-3 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"></textarea></label>
                    </div></section>

                    <section class="space-y-4 border-t border-[#edf0fb] pt-6"><div><p class="text-xs font-bold uppercase tracking-[0.12em] text-[#687eff]">02 · Foto</p><h3 class="mt-1 font-semibold text-[#20284a]">Foto produk</h3></div>
                        <div data-photo-dropzone class="group relative flex min-h-40 cursor-pointer flex-col items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-[#cfd8f5] bg-[#fafbff] p-5 text-center transition hover:border-[#8293ff] hover:bg-[#f7f8ff] focus-within:border-[#687eff] focus-within:ring-4 focus-within:ring-[#536dfe]/10">
                            <input data-product-photo name="photo" type="file" accept="image/jpeg,image/png,image/webp" aria-label="Pilih atau seret foto produk" class="absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0">
                            <img data-photo-preview alt="Pratinjau foto produk" class="absolute inset-0 hidden h-full w-full object-contain p-3">
                            <div data-photo-empty class="pointer-events-none flex flex-col items-center"><span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#eef1ff] text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m4 7 8-4 8 4v10l-8 4-8-4V7Z"/><path d="m4 7 8 4 8-4M12 11v10"/></svg></span><p class="mt-2 text-sm font-semibold text-[#303858]">Seret foto ke sini atau <span class="text-[#536dfe]">pilih file</span></p><p data-photo-caption class="mt-1 text-xs text-[#8a91a9]">JPG, PNG, atau WebP · maksimal 3 MB</p></div>
                            <span data-photo-change class="pointer-events-none absolute bottom-3 z-20 hidden rounded-lg bg-[#20284a]/85 px-3 py-1.5 text-xs font-semibold text-white">Ganti foto</span>
                        </div>
                        <label class="hidden items-center gap-2 text-sm font-medium text-[#737b96]"><input data-remove-product-photo name="remove_photo" type="checkbox" value="1" class="rounded border-[#cbd3e8] text-[#536dfe] focus:ring-[#536dfe]/20">Hapus foto dan gunakan ikon produk</label>
                    </section>

                    <section class="space-y-4 border-t border-[#edf0fb] pt-6"><div><p class="text-xs font-bold uppercase tracking-[0.12em] text-[#687eff]">03 · Harga dan status</p><h3 class="mt-1 font-semibold text-[#20284a]">Pengaturan penjualan</h3></div><div class="grid gap-4 sm:grid-cols-2">
                        <label class="space-y-1.5 text-sm font-semibold">Harga modal (Rp) <span class="text-rose-600">*</span><input name="purchase_price" type="number" min="0" step="1" required class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"></label>
                        <label class="space-y-1.5 text-sm font-semibold">Harga jual (Rp) <span class="text-rose-600">*</span><input name="selling_price" type="number" min="0" step="1" required class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"></label>
                        <label class="space-y-1.5 text-sm font-semibold">Satuan<input name="unit" maxlength="30" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"></label>
                        <label class="space-y-1.5 text-sm font-semibold">Status<select name="is_active" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm text-[#20284a] outline-none transition focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"><option value="1">Aktif</option><option value="0">Nonaktif</option></select></label>
                    </div></section>

                    <div class="flex flex-col-reverse gap-2 border-t border-[#edf0fb] pt-5 sm:flex-row sm:justify-end"><a href="{{ route('owner.products.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#e4e8f4] bg-white px-4 text-sm font-semibold text-[#59617d] transition hover:bg-[#f8f9fd]">Batal</a><button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#536dfe] to-[#687eff] px-5 text-sm font-semibold text-white shadow-[0_8px_18px_rgba(83,109,254,0.2)] transition hover:-translate-y-0.5 hover:from-[#435ce7] hover:to-[#596ff5] hover:shadow-lg focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#536dfe]/20 disabled:cursor-wait disabled:opacity-60"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14m-7-7 7 7-7 7"/></svg><span>Simpan perubahan</span></button></div>
                </div>
            </form>

            <aside class="space-y-5">
                <section class="overflow-hidden rounded-[26px] border border-[#e6eaff] bg-white shadow-[0_18px_50px_rgba(49,67,130,0.07)]"><div class="border-b border-[#edf0fb] px-5 py-4"><p class="text-xs font-bold uppercase tracking-[0.14em] text-[#687eff]">Status produk</p><h2 class="mt-1 font-semibold text-[#20284a]">Kendalikan ketersediaan</h2></div><div class="p-5"><p class="text-sm leading-6 text-[#5e647f]">Produk nonaktif tidak tampil pada pilihan penjualan. Data historis dan foto tetap tersimpan.</p><a href="{{ route('owner.products.index') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-[#536dfe] hover:text-[#435ce7]">Lihat semua produk <span aria-hidden="true">→</span></a></div></section>
            </aside>
        </div>
    </div>
@endsection
