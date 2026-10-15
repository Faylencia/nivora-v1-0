@extends('layouts.cashier')

@section('title', 'Promo Aktif - Kasir Nivora')

@section('content')
    <div class="cashier-promos space-y-6" data-cashier-promos>
        <x-page-header eyebrow="KASIR / PROMO" title="Promo toko" description="Lihat promo dan ketentuannya untuk membantu pelanggan saat bertransaksi." />

        <section class="cashier-promos-panel overflow-hidden rounded-[28px] border border-[#e7ebff] bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-[#eef3ff] p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#687EFF]">Informasi promo</p>
                    <h2 class="mt-1 text-lg font-semibold text-[#1f2544]">Promo yang tersedia</h2>
                    <p class="mt-1 text-sm text-[#5e647f]">Promo dikelola oleh owner. Kasir dapat melihat detail tanpa mengubahnya.</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <label class="sr-only" for="cashier-promo-search">Cari promo</label>
                    <input id="cashier-promo-search" data-promo-search type="search" placeholder="Cari nama atau kode promo..." class="min-h-11 rounded-xl border border-[#dfe5f5] bg-white px-4 text-sm text-[#1f2544] outline-none transition placeholder:text-[#8a91aa] focus:border-[#687EFF] focus:ring-2 focus:ring-[#687EFF]/15">
                    <label class="sr-only" for="cashier-promo-filter">Filter promo</label>
                    <select id="cashier-promo-filter" data-promo-filter class="min-h-11 rounded-xl border border-[#dfe5f5] bg-white px-4 text-sm text-[#1f2544] outline-none focus:border-[#687EFF]">
                        <option value="all">Semua promo</option>
                        <option value="active">Sedang berlaku</option>
                        <option value="upcoming">Akan datang</option>
                        <option value="expired">Tidak berlaku</option>
                    </select>
                </div>
            </div>
            <div class="p-5 sm:p-6">
                <div data-promo-loading class="rounded-2xl border border-dashed border-[#dfe5f5] p-10 text-center text-sm text-[#5e647f]">Memuat promo toko...</div>
                <div data-promo-error class="hidden rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700" role="alert"></div>
                <div data-promo-empty class="hidden rounded-2xl border border-dashed border-[#dfe5f5] p-10 text-center">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-[#eef3ff] text-[#687EFF]" aria-hidden="true">％</span>
                    <h3 class="mt-3 font-semibold text-[#1f2544]">Belum ada promo</h3>
                    <p class="mt-1 text-sm text-[#5e647f]">Promo toko akan tampil di sini setelah dibuat oleh owner.</p>
                </div>
                <div data-promo-list class="grid gap-5 lg:grid-cols-2"></div>
            </div>
        </section>
    </div>
@endsection
