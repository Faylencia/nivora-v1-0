@extends('layouts.cashier')

@section('title', 'Cashier Dashboard - Nivora')

@section('content')
    @php
        $stats = [
            ['label' => 'Transaksi Hari Ini', 'value' => '128', 'detail' => '+12% vs kemarin'],
            ['label' => 'Total Penjualan Hari Ini', 'value' => 'Rp 8.450.000', 'detail' => 'Target hari ini'],
            ['label' => 'Produk Terjual', 'value' => '342', 'detail' => 'Unit'],
            ['label' => 'Transaksi Selesai', 'value' => '119', 'detail' => '90% selesai'],
        ];

        $recentSales = [
            ['invoice' => '#NV-2048', 'customer' => 'Budi', 'total' => 'Rp 160.000', 'method' => 'Cash', 'status' => 'Completed'],
            ['invoice' => '#NV-2047', 'customer' => 'Sinta', 'total' => 'Rp 82.000', 'method' => 'QRIS', 'status' => 'Completed'],
            ['invoice' => '#NV-2046', 'customer' => 'Rafi', 'total' => 'Rp 245.000', 'method' => 'Cash', 'status' => 'Completed'],
        ];
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-4 rounded-[30px] border border-[#e7ebff] bg-gradient-to-r from-[#687EFF] to-[#98E4FF] p-5 text-white shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/80">Cashier</p>
                <h1 class="mt-2 text-2xl font-semibold tracking-[-0.05em]">Selamat datang, Ayu R.</h1>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-sm">
                <div class="rounded-2xl bg-white/10 px-3 py-2">Outlet aktif: Bandung Center</div>
                <div class="rounded-2xl bg-white/10 px-3 py-2">{{ now()->translatedFormat('d M Y') }}</div>
                <a href="{{ route('home') }}" class="rounded-2xl bg-white px-3 py-2 font-medium text-[#1f2544]">Logout</a>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($stats as $stat)
                <div class="rounded-[26px] border border-[#e7ebff] bg-white p-5 shadow-sm">
                    <p class="text-sm text-[#5e647f]">{{ $stat['label'] }}</p>
                    <p class="mt-4 text-2xl font-semibold tracking-[-0.05em] text-[#1f2544]">{{ $stat['value'] }}</p>
                    <p class="mt-2 text-xs text-[#687EFF]">{{ $stat['detail'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
            <div class="rounded-[28px] border border-[#e7ebff] bg-white p-5 shadow-sm">
                <h2 class="text-xl font-semibold text-[#1f2544]">Quick Action</h2>
                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <a href="{{ route('cashier.pos') }}" class="rounded-2xl bg-[#687EFF] p-4 text-left text-white shadow-sm">Mulai Transaksi</a>
                    <a href="{{ route('cashier.sales') }}" class="rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] p-4 text-left text-[#1f2544]">Lihat Transaksi</a>
                    <a href="{{ route('cashier.products') }}" class="rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] p-4 text-left text-[#1f2544]">Produk</a>
                    <a href="{{ route('cashier.customers') }}" class="rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] p-4 text-left text-[#1f2544]">Customer</a>
                </div>
            </div>

            <div class="rounded-[28px] border border-[#e7ebff] bg-white p-5 shadow-sm">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-[#1f2544]">Transaksi terbaru</h2>
                    <a href="{{ route('cashier.sales') }}" class="text-sm font-medium text-[#687EFF]">Lihat semua</a>
                </div>
                <div class="space-y-3">
                    @foreach ($recentSales as $sale)
                        <div class="flex items-center justify-between gap-3 rounded-2xl border border-[#eef3ff] bg-[#f7f9ff] p-3">
                            <div>
                                <p class="font-medium text-[#1f2544]">{{ $sale['invoice'] }}</p>
                                <p class="text-xs text-[#5e647f]">{{ $sale['customer'] }} · {{ $sale['method'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-[#1f2544]">{{ $sale['total'] }}</p>
                                <span class="inline-flex rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-medium text-emerald-600">{{ $sale['status'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
