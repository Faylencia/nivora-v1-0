@extends('layouts.cashier')

@section('title', 'Dashboard Kasir - Nivora')

@section('content')
    <div class="cashier-dashboard space-y-6" data-live-dashboard>
        <x-page-header eyebrow="CASHIER" title="Dashboard kasir" description="Ringkasan transaksi dan produk dari data bisnis Anda.">
            <a href="{{ route('cashier.pos') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#687EFF] px-4 text-sm font-semibold text-white">Buka POS</a>
        </x-page-header>
        <div data-live-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>
        <section class="ni-panel relative overflow-hidden bg-[linear-gradient(115deg,#1f2544_0%,#414f9f_58%,#687eff_100%)] p-6 text-white sm:p-8">
            <div class="relative z-10 flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div><p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#b6fffa]">Aktivitas kasir</p><h2 class="mt-2 text-2xl font-semibold sm:text-3xl">Siap melayani transaksi hari ini?</h2><p class="mt-2 max-w-2xl text-sm leading-6 text-white/75">Pantau hasil penjualan, transaksi yang diproses, dan ketersediaan produk.</p></div>
                <a href="{{ route('cashier.pos') }}" class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-xl bg-white px-4 text-sm font-semibold text-[#414f9f]">Mulai transaksi <span class="ml-2" aria-hidden="true">→</span></a>
            </div>
            <div class="pointer-events-none absolute -right-8 -top-24 h-64 w-64 rounded-full border-[36px] border-white/10"></div>
        </section>
        <div data-dashboard-loading class="ni-panel p-4 text-center text-sm text-[#5e647f]"><span class="inline-flex items-center gap-2"><span class="h-2 w-2 animate-pulse rounded-full bg-[#687EFF]"></span>Memuat ringkasan…</span></div>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="ni-stat-card"><p class="text-sm text-[#5e647f]">Penjualan hari ini</p><p data-dashboard-stat="today_sales" class="mt-2 text-3xl font-semibold">—</p><p class="mt-2 text-xs text-[#7b8299]">Total transaksi selesai</p></article>
            <article class="ni-stat-card"><p class="text-sm text-[#5e647f]">Transaksi hari ini</p><p data-dashboard-stat="today_transactions" class="mt-2 text-3xl font-semibold">—</p><p class="mt-2 text-xs text-[#7b8299]">Transaksi yang Anda proses</p></article>
            <article class="ni-stat-card"><p class="text-sm text-[#5e647f]">Produk aktif</p><p data-dashboard-stat="active_products" class="mt-2 text-3xl font-semibold">—</p><p class="mt-2 text-xs text-[#7b8299]">Produk yang tersedia untuk dijual</p></article>
            <article class="ni-stat-card"><p class="text-sm text-[#5e647f]">Stok mencapai batas minimum</p><p data-dashboard-stat="low_stock_items" class="mt-2 text-3xl font-semibold">—</p><p class="mt-2 text-xs text-[#7b8299]">Perhatikan ketersediaan produk</p></article>
        </div>
        <section class="ni-panel overflow-hidden">
            <div class="flex items-center justify-between gap-3 border-b border-[#e7ebff] p-5"><div><h2 class="text-lg font-semibold">Transaksi terbaru</h2><p class="mt-1 text-sm text-[#5e647f]">Transaksi yang diproses oleh akun kasir ini.</p></div><a href="{{ route('cashier.sales') }}" class="text-sm font-semibold text-[#687EFF]">Riwayat transaksi</a></div>
            <div data-dashboard-empty class="hidden p-8 text-center"><p class="font-semibold">Belum ada transaksi.</p><p class="mt-1 text-sm text-[#5e647f]">Transaksi yang dibuat melalui POS akan muncul di sini.</p></div>
            <div data-dashboard-table-wrap class="hidden overflow-x-auto"><table class="ni-table"><thead><tr><th>Invoice</th><th>Customer</th><th>Tanggal</th><th>Total</th><th>Status</th></tr></thead><tbody data-dashboard-recent></tbody></table></div>
        </section>
    </div>
@endsection
