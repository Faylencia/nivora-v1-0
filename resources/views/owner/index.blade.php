@extends('layouts.owner')

@section('title', 'Dashboard Owner - Nivora')

@section('content')
    <div class="owner-dashboard space-y-6" data-live-dashboard>
        <x-page-header eyebrow="OWNER" title="Dashboard bisnis" description="Ringkasan operasional yang dimuat dari data bisnis Anda.">
            <span data-dashboard-business class="rounded-xl border border-[#e7ebff] bg-white px-3 py-2 text-sm font-medium text-[#5e647f]">Memuat bisnis…</span>
        </x-page-header>

        <div data-live-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>
        <section class="ni-panel relative overflow-hidden bg-[linear-gradient(115deg,#1f2544_0%,#414f9f_58%,#687eff_100%)] p-6 text-white sm:p-8">
            <div class="relative z-10 flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div><p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#b6fffa]">Ringkasan operasional</p><h2 class="mt-2 text-2xl font-semibold sm:text-3xl">Pantau bisnis Anda dalam satu tempat.</h2><p class="mt-2 max-w-2xl text-sm leading-6 text-white/75">Lihat performa penjualan, aktivitas transaksi, produk aktif, dan kondisi stok terbaru.</p></div>
                <a href="{{ route('owner.sales.index') }}" class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-xl bg-white px-4 text-sm font-semibold text-[#414f9f]">Lihat penjualan <span class="ml-2" aria-hidden="true">→</span></a>
            </div>
            <div class="pointer-events-none absolute -right-8 -top-24 h-64 w-64 rounded-full border-[36px] border-white/10"></div>
        </section>
        <div data-dashboard-loading class="ni-panel p-4 text-center text-sm text-[#5e647f]"><span class="inline-flex items-center gap-2"><span class="h-2 w-2 animate-pulse rounded-full bg-[#687EFF]"></span>Memuat ringkasan bisnis…</span></div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="ni-stat-card"><p class="text-sm text-[#5e647f]">Penjualan hari ini</p><p data-dashboard-stat="today_sales" class="mt-2 text-3xl font-semibold">—</p><p class="mt-2 text-xs text-[#7b8299]">Total dari transaksi selesai</p></article>
            <article class="ni-stat-card"><p class="text-sm text-[#5e647f]">Transaksi hari ini</p><p data-dashboard-stat="today_transactions" class="mt-2 text-3xl font-semibold">—</p><p class="mt-2 text-xs text-[#7b8299]">Jumlah transaksi bisnis Anda</p></article>
            <article class="ni-stat-card"><p class="text-sm text-[#5e647f]">Produk aktif</p><p data-dashboard-stat="active_products" class="mt-2 text-3xl font-semibold">—</p><p class="mt-2 text-xs text-[#7b8299]">Produk yang tersedia untuk dijual</p></article>
            <article class="ni-stat-card"><p class="text-sm text-[#5e647f]">Stok mencapai batas minimum</p><p data-dashboard-stat="low_stock_items" class="mt-2 text-3xl font-semibold">—</p><p class="mt-2 text-xs text-[#7b8299]">Produk yang perlu diperiksa</p></article>
        </div>

        <section class="ni-panel overflow-hidden" data-owner-sales-chart>
            <div class="flex flex-col gap-4 border-b border-[#e7ebff] p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#687eff]">Performa bisnis</p>
                    <h2 class="mt-1 text-lg font-semibold">Omzet penjualan</h2>
                    <p data-sales-chart-caption class="mt-1 text-sm text-[#5e647f]">Ringkasan omzet dalam 7 hari terakhir.</p>
                </div>
                <label class="flex items-center gap-3 text-sm font-medium text-[#5e647f]">
                    <span>Periode</span>
                    <select data-sales-chart-range class="min-h-10 rounded-xl border border-[#e7ebff] bg-white px-3 text-sm font-semibold text-[#1f2544] outline-none transition focus:border-[#687eff] focus:ring-4 focus:ring-[#687eff]/15">
                        <option value="hour">Per jam · 24 jam</option>
                        <option value="day" selected>Per hari · 7 hari</option>
                        <option value="month">Per bulan · 12 bulan</option>
                        <option value="year">Per tahun · 5 tahun</option>
                    </select>
                </label>
            </div>
            <div class="flex flex-col gap-1 px-5 pt-5 sm:px-6">
                <span class="text-xs font-medium text-[#7b8299]">Total omzet pada periode</span>
                <strong data-sales-chart-total class="text-2xl font-semibold tracking-tight">—</strong>
            </div>
            <div data-sales-chart-loading class="px-5 py-8 text-center text-sm text-[#7b8299] sm:px-6">Memuat grafik omzet…</div>
            <div data-sales-chart-content class="hidden px-2 pb-4 pt-2 sm:px-4">
                <svg data-sales-chart-svg class="sales-chart-svg h-[280px] w-full" viewBox="0 0 760 280" role="img" aria-label="Grafik omzet penjualan"></svg>
                <p data-sales-chart-empty class="hidden px-4 pb-3 text-center text-sm text-[#7b8299]">Belum ada omzet pada rentang waktu ini.</p>
            </div>
            <div data-sales-chart-error class="hidden px-5 pb-5 text-sm text-rose-600 sm:px-6" role="status" aria-live="polite"></div>
        </section>

        <section class="ni-panel overflow-hidden">
            <div class="flex items-center justify-between gap-3 border-b border-[#e7ebff] p-5">
                <div><h2 class="text-lg font-semibold">Transaksi terbaru</h2><p class="mt-1 text-sm text-[#5e647f]">Data transaksi terbaru dari bisnis Anda.</p></div>
                <a href="{{ route('owner.sales.index') }}" class="text-sm font-semibold text-[#687EFF]">Lihat semua</a>
            </div>
            <div data-dashboard-empty class="hidden p-8 text-center"><p class="font-semibold text-[#1f2544]">Belum ada transaksi.</p><p class="mt-1 text-sm text-[#5e647f]">Transaksi yang tersimpan akan muncul di sini.</p></div>
            <div data-dashboard-table-wrap class="hidden overflow-x-auto">
                <table class="ni-table"><thead><tr><th>Invoice</th><th>Customer</th><th>Tanggal</th><th>Total</th><th>Status</th></tr></thead><tbody data-dashboard-recent></tbody></table>
            </div>
        </section>
    </div>
@endsection
