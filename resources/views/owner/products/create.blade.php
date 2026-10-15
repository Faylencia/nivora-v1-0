@extends('layouts.owner')

@section('title', 'Tambah Produk - Nivora')

@section('content')
    <div class="product-crud-surface space-y-6" data-product-create-root>
        <x-page-header eyebrow="OWNER / PRODUK" title="Tambah produk" description="Lengkapi informasi katalog, foto, harga, dan stok awal produk.">
            <a href="{{ route('owner.products.index') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-[#e4e8f4] bg-white px-4 text-sm font-semibold text-[#59617d] transition hover:-translate-y-0.5 hover:border-[#cbd3e8] hover:bg-[#f8f9fd] hover:shadow-sm focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#536dfe]/15"><span aria-hidden="true">←</span> Kembali ke produk</a>
        </x-page-header>

        <div data-product-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>

        <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
            <section class="overflow-hidden rounded-[26px] border border-[#e6eaff] bg-white shadow-[0_18px_50px_rgba(49,67,130,0.07)]">
                <div class="relative flex items-center gap-4 border-b border-[#edf0fb] px-5 py-5 sm:px-7"><span class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-[#536dfe] via-[#8293ff] to-[#54c8e8]"></span><span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-[#536dfe] to-[#8190ff] text-white shadow-lg shadow-[#536dfe]/20"><svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m4 7 8-4 8 4v10l-8 4-8-4V7Z"/><path d="m4 7 8 4 8-4M12 11v10M8 5l8 4"/></svg></span><div><p class="text-xs font-bold uppercase tracking-[0.16em] text-[#687eff]">Produk baru</p><h2 class="mt-1 text-xl font-semibold tracking-tight text-[#20284a]">Informasi produk</h2><p class="mt-1 text-sm text-[#7b8299]">Isi detail produk agar katalog toko tetap rapi dan akurat.</p></div></div>

                <form data-product-form class="space-y-6 p-5 sm:p-7" enctype="multipart/form-data">
                    <section class="space-y-4">
                        <div><p class="text-xs font-bold uppercase tracking-[0.12em] text-[#687eff]">01 · Identitas</p><h3 class="mt-1 font-semibold text-[#20284a]">Nama dan pengelompokan</h3></div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="space-y-1.5 text-sm font-semibold sm:col-span-2">Nama produk <span class="text-rose-600">*</span><input name="name" type="text" required maxlength="150" autocomplete="off" placeholder="Contoh: Kopi susu gula aren" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition placeholder:text-[#a1a8bc] focus:border-[#8697ff] focus:bg-white focus:ring-4 focus:ring-[#536dfe]/10"></label>
                            <label class="space-y-1.5 text-sm font-semibold">SKU <span class="text-rose-600">*</span><input name="sku" type="text" required maxlength="80" autocomplete="off" placeholder="Contoh: KOPI-001" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal uppercase text-[#20284a] outline-none transition placeholder:normal-case placeholder:text-[#a1a8bc] focus:border-[#8697ff] focus:bg-white focus:ring-4 focus:ring-[#536dfe]/10"></label>
                            <label class="space-y-1.5 text-sm font-semibold">Barcode <span class="font-normal text-[#8a91a9]">(opsional)</span><input name="barcode" type="text" maxlength="100" autocomplete="off" placeholder="Masukkan nomor barcode" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition placeholder:text-[#a1a8bc] focus:border-[#8697ff] focus:bg-white focus:ring-4 focus:ring-[#536dfe]/10"></label>
                            <div class="space-y-1.5 sm:col-span-2"><div class="flex items-center justify-between gap-3"><label for="product-category" class="text-sm font-semibold">Kategori <span class="text-rose-600">*</span></label><button type="button" data-toggle-category class="text-sm font-semibold text-[#536dfe] transition hover:text-[#435ce7]">+ Buat kategori</button></div><select id="product-category" name="category_id" data-product-category required class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm text-[#20284a] outline-none transition focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"><option value="">Memuat kategori…</option></select></div>
                            <label class="space-y-1.5 text-sm font-semibold sm:col-span-2">Deskripsi <span class="font-normal text-[#8a91a9]">(opsional)</span><textarea name="description" rows="3" placeholder="Jelaskan fitur atau informasi penting produk" class="w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 py-3 text-sm font-normal text-[#20284a] outline-none transition placeholder:text-[#a1a8bc] focus:border-[#8697ff] focus:bg-white focus:ring-4 focus:ring-[#536dfe]/10"></textarea></label>
                        </div>
                    </section>

                    <section class="space-y-4 border-t border-[#edf0fb] pt-6">
                        <div><p class="text-xs font-bold uppercase tracking-[0.12em] text-[#687eff]">02 · Foto</p><h3 class="mt-1 font-semibold text-[#20284a]">Foto produk</h3></div>
                        <div data-photo-dropzone class="group relative flex min-h-40 cursor-pointer flex-col items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-[#cfd8f5] bg-[#fafbff] p-5 text-center transition hover:border-[#8293ff] hover:bg-[#f7f8ff] focus-within:border-[#687eff] focus-within:ring-4 focus-within:ring-[#536dfe]/10">
                            <input data-product-photo name="photo" type="file" accept="image/jpeg,image/png,image/webp" aria-label="Pilih atau seret foto produk" class="absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0">
                            <img data-photo-preview alt="Pratinjau foto produk" class="absolute inset-0 hidden h-full w-full object-contain p-3">
                            <div data-photo-empty class="pointer-events-none flex flex-col items-center">
                                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#eef1ff] text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m4 7 8-4 8 4v10l-8 4-8-4V7Z"/><path d="m4 7 8 4 8-4M12 11v10"/></svg></span>
                                <p class="mt-3 text-sm font-semibold text-[#303858]">Seret foto ke sini atau <span class="text-[#536dfe]">pilih file</span></p>
                                <p class="mt-1 text-xs text-[#8a91a9]">JPG, PNG, atau WebP · maksimal 3 MB</p>
                            </div>
                            <span data-photo-change class="pointer-events-none absolute bottom-3 z-20 hidden rounded-lg bg-[#20284a]/85 px-3 py-1.5 text-xs font-semibold text-white">Ganti foto</span>
                        </div>
                    </section>

                    <section class="space-y-4 border-t border-[#edf0fb] pt-6">
                        <div><p class="text-xs font-bold uppercase tracking-[0.12em] text-[#687eff]">03 · Harga</p><h3 class="mt-1 font-semibold text-[#20284a]">Harga dan satuan</h3></div>
                        <div class="grid gap-4 sm:grid-cols-3">
                            <label class="space-y-1.5 text-sm font-semibold">Harga modal (Rp) <span class="text-rose-600">*</span><input name="purchase_price" type="number" min="0" step="1" required inputmode="numeric" placeholder="0" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition placeholder:text-[#a1a8bc] focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"></label>
                            <label class="space-y-1.5 text-sm font-semibold">Harga jual (Rp) <span class="text-rose-600">*</span><input name="selling_price" type="number" min="0" step="1" required inputmode="numeric" placeholder="0" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition placeholder:text-[#a1a8bc] focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"></label>
                            <label class="space-y-1.5 text-sm font-semibold">Satuan<input name="unit" type="text" maxlength="30" value="pcs" placeholder="pcs" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition placeholder:text-[#a1a8bc] focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"></label>
                        </div>
                    </section>

                    <section class="space-y-4 border-t border-[#edf0fb] pt-6">
                        <div><p class="text-xs font-bold uppercase tracking-[0.12em] text-[#687eff]">04 · Persediaan</p><h3 class="mt-1 font-semibold text-[#20284a]">Lokasi dan stok awal</h3><p class="mt-1 text-sm text-[#7b8299]">Stok dan batas minimum dicatat pada lokasi operasional toko.</p></div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="space-y-1.5 text-sm font-semibold sm:col-span-2">Lokasi toko <span class="text-rose-600">*</span><select name="business_id" data-product-outlet required class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm text-[#20284a] outline-none transition focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"><option value="">Memuat lokasi toko…</option></select></label>
                            <label class="space-y-1.5 text-sm font-semibold">Jumlah stok <span class="text-rose-600">*</span><input name="initial_stock" type="number" min="0" step="1" required value="0" inputmode="numeric" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"></label>
                            <label class="space-y-1.5 text-sm font-semibold">Batas minimum<input name="minimum_stock" type="number" min="0" step="1" value="0" inputmode="numeric" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"></label>
                        </div>
                    </section>

                    <div class="flex flex-col-reverse gap-2 border-t border-[#edf0fb] pt-5 sm:flex-row sm:justify-end"><a href="{{ route('owner.products.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#e4e8f4] bg-white px-4 text-sm font-semibold text-[#59617d] transition hover:bg-[#f8f9fd]">Batal</a><button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#536dfe] to-[#687eff] px-5 text-sm font-semibold text-white shadow-[0_8px_18px_rgba(83,109,254,0.2)] transition hover:-translate-y-0.5 hover:from-[#435ce7] hover:to-[#596ff5] hover:shadow-lg focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#536dfe]/20 disabled:cursor-wait disabled:opacity-60"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>Simpan produk &amp; stok</button></div>
                </form>
            </section>

            <aside class="space-y-5">
                <section class="overflow-hidden rounded-[26px] border border-[#e6eaff] bg-white shadow-[0_18px_50px_rgba(49,67,130,0.07)]">
                    <div class="border-b border-[#edf0fb] px-5 py-4"><p class="text-xs font-bold uppercase tracking-[0.14em] text-[#687eff]">Kiat katalog</p><h2 class="mt-1 font-semibold text-[#20284a]">Informasi produk yang baik</h2></div>
                    <div class="space-y-4 p-5"><p class="flex gap-3 text-sm leading-6 text-[#5e647f]"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-[#eef1ff] text-xs font-bold text-[#536dfe]">1</span>Gunakan nama yang mudah dicari pelanggan.</p><p class="flex gap-3 text-sm leading-6 text-[#5e647f]"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-[#eef1ff] text-xs font-bold text-[#536dfe]">2</span>Foto produk membantu kasir memilih barang yang tepat.</p><p class="flex gap-3 text-sm leading-6 text-[#5e647f]"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-[#eef1ff] text-xs font-bold text-[#536dfe]">3</span>Pastikan SKU unik untuk setiap produk di toko Anda.</p></div>
                </section>
                <aside data-category-panel class="hidden overflow-hidden rounded-[26px] border border-[#e6eaff] bg-white shadow-[0_18px_50px_rgba(49,67,130,0.07)]">
                    <div class="border-b border-[#edf0fb] px-5 py-4"><p class="text-xs font-bold uppercase tracking-[0.14em] text-[#687eff]">Pengelolaan kategori</p><h2 class="mt-1 font-semibold text-[#20284a]">Kategori baru</h2><p class="mt-1 text-sm text-[#7b8299]">Kategori langsung tersedia setelah disimpan.</p></div>
                    <form data-category-form class="space-y-4 p-5"><div data-category-message class="hidden rounded-xl border border-rose-200 bg-rose-50 px-3 py-2.5 text-sm text-rose-700" role="alert"></div><label class="block space-y-1.5 text-sm font-semibold">Nama kategori <span class="text-rose-600">*</span><input name="name" type="text" required maxlength="100" placeholder="Contoh: Minuman" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3 text-sm font-normal text-[#20284a] outline-none focus:border-[#8697ff]"></label><label class="block space-y-1.5 text-sm font-semibold">Deskripsi<textarea name="description" rows="2" placeholder="Opsional" class="w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3 py-2 text-sm font-normal text-[#20284a] outline-none focus:border-[#8697ff]"></textarea></label><button type="submit" class="min-h-10 w-full rounded-xl bg-gradient-to-r from-[#536dfe] to-[#687eff] px-4 text-sm font-semibold text-white transition hover:brightness-105 disabled:opacity-60">Simpan kategori</button></form>
                </aside>
            </aside>
        </div>
    </div>
@endsection
