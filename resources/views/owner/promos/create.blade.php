@extends('layouts.owner')

@section('title', 'Tambah Promo - Owner Nivora')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <x-page-header eyebrow="OWNER / PROMO" title="Tambah promo" description="Pilih produk tertentu atau minimum transaksi sebagai target promo." />

        <div class="rounded-2xl border border-[#cbd7ff] bg-[#eef3ff] p-4 text-sm leading-6 text-[#4f5879]">
            Form ini mengikuti kolom tabel <code class="font-semibold text-[#1f2544]">promos</code> dan relasi <code class="font-semibold text-[#1f2544]">promo_products</code>. Tampilan belum menyimpan perubahan ke database.
        </div>

        <div class="rounded-[28px] border border-[#e7ebff] bg-white p-5 shadow-sm sm:p-7">
            <form class="space-y-6">
                <div class="grid gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <x-input label="Nama promo" name="name" placeholder="Contoh: Diskon Kopi Susu" required />
                    </div>

                    <div class="space-y-2 md:col-span-2">
                        <label for="promo-description" class="block text-sm font-medium text-[#1f2544]">Deskripsi</label>
                        <textarea id="promo-description" name="description" rows="3" placeholder="Jelaskan syarat dan ketentuan promo" class="w-full rounded-xl border border-[#e7ebff] bg-white px-3.5 py-2.5 text-sm text-[#1f2544] placeholder:text-[#9097b3] shadow-sm outline-none transition focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/10"></textarea>
                    </div>

                    <div class="space-y-2">
                        <label for="promo-type" class="block text-sm font-medium text-[#1f2544]">Jenis target promo</label>
                        <select id="promo-type" name="type" data-promo-type class="min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3.5 text-sm text-[#1f2544] outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/10">
                            <option value="product">Produk tertentu</option>
                            <option value="transaction">Minimum transaksi</option>
                        </select>
                        <p class="text-xs text-[#707895]">Nilai ini mengikuti kolom <code>promos.type</code>.</p>
                    </div>

                    <div class="space-y-2">
                        <label for="discount-value" class="block text-sm font-medium text-[#1f2544]">Nilai diskon</label>
                        <div class="flex min-h-11 overflow-hidden rounded-xl border border-[#e7ebff] focus-within:border-[#687EFF] focus-within:ring-4 focus-within:ring-[#687EFF]/10">
                            <input id="discount-value" name="discount_value" type="number" min="0" step="0.01" placeholder="10" class="w-full px-3.5 text-sm text-[#1f2544] outline-none" required />
                            <span class="flex items-center border-l border-[#e7ebff] bg-[#f7f9ff] px-3 text-sm text-[#5e647f]">%</span>
                        </div>
                        <p class="text-xs text-[#707895]">Disimpan sebagai nilai desimal di <code>promos.discount_value</code>.</p>
                    </div>

                    <div data-promo-target="product" class="space-y-2 md:col-span-2">
                        <label for="promo-products" class="block text-sm font-medium text-[#1f2544]">Produk yang mendapatkan promo</label>
                        <select id="promo-products" name="product_ids[]" multiple size="4" class="w-full rounded-xl border border-[#e7ebff] bg-white px-3.5 py-2.5 text-sm text-[#1f2544] outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/10">
                            <option value="1">Kopi Susu Signature — Rp 18.000</option>
                            <option value="2">Boba Hazelnut — Rp 22.000</option>
                            <option value="3">Croissant Matcha — Rp 26.000</option>
                            <option value="4">Batik Premium — Rp 180.000</option>
                        </select>
                        <p class="text-xs text-[#707895]">Produk terpilih dicatat melalui tabel relasi <code>promo_products</code>. Pilih satu atau beberapa produk.</p>
                    </div>

                    <div data-promo-target="transaction" class="hidden space-y-2 md:col-span-2">
                        <label for="min-transaction" class="block text-sm font-medium text-[#1f2544]">Minimum nilai transaksi</label>
                        <div class="flex min-h-11 overflow-hidden rounded-xl border border-[#e7ebff] focus-within:border-[#687EFF] focus-within:ring-4 focus-within:ring-[#687EFF]/10">
                            <span class="flex items-center border-r border-[#e7ebff] bg-[#f7f9ff] px-3 text-sm text-[#5e647f]">Rp</span>
                            <input id="min-transaction" name="min_transaction" type="number" min="0" step="1000" placeholder="100000" class="w-full px-3.5 text-sm text-[#1f2544] outline-none" disabled />
                        </div>
                        <p class="text-xs text-[#707895]">Ambang transaksi disimpan pada <code>promos.min_transaction</code>.</p>
                    </div>

                    <x-input label="Tanggal mulai" name="start_date" type="date" required />
                    <x-input label="Tanggal berakhir" name="end_date" type="date" required />

                    <div class="space-y-2 md:col-span-2">
                        <label for="promo-status" class="block text-sm font-medium text-[#1f2544]">Status</label>
                        <select id="promo-status" name="status" class="min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3.5 text-sm text-[#1f2544] outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/10">
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col gap-3 border-t border-[#eef3ff] pt-5 sm:flex-row">
                    <x-button type="primary" size="lg">Simpan promo</x-button>
                    <x-button href="{{ route('owner.promos.index') }}" type="ghost" size="lg">Batal</x-button>
                </div>
            </form>
        </div>
    </div>
@endsection
