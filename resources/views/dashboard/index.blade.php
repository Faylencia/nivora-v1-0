@extends('layouts.dashboard')

@section('title', 'Dashboard - Nivora')

@section('content')
    @php
        $stats = [
            ['label' => 'Total Produk', 'value' => '1,284', 'change' => '+12.4%', 'icon' => 'cube', 'color' => 'bg-[#eef3ff] text-[#687EFF]'],
            ['label' => 'Total Outlet', 'value' => '24', 'change' => '+2.1%', 'icon' => 'store', 'color' => 'bg-[#EAF6FF] text-[#687EFF]'],
            ['label' => 'Total Customer', 'value' => '3,842', 'change' => '+8.3%', 'icon' => 'users', 'color' => 'bg-[#edf3ff] text-[#687EFF]'],
            ['label' => 'Total Transaksi', 'value' => '8,904', 'change' => '+15.7%', 'icon' => 'chart', 'color' => 'bg-[#EEF9FF] text-[#687EFF]'],
        ];

        $transactions = [
            ['invoice' => '#INV-1024', 'customer' => 'Nadia Putri', 'amount' => 'Rp 1.250.000', 'status' => 'Paid', 'date' => '12 Sep 2026'],
            ['invoice' => '#INV-1023', 'customer' => 'Fajar H.', 'amount' => 'Rp 890.000', 'status' => 'Pending', 'date' => '11 Sep 2026'],
            ['invoice' => '#INV-1022', 'customer' => 'Siti A.', 'amount' => 'Rp 650.000', 'status' => 'Paid', 'date' => '10 Sep 2026'],
        ];

        $lowStock = [
            ['product' => 'Kopi Susu Signature', 'stock' => 8],
            ['product' => 'Tas Jinjing', 'stock' => 4],
            ['product' => 'Sambal Tempoe', 'stock' => 2],
        ];

        $insights = [
            'Stok beberapa produk berada di bawah batas minimum.',
            'Produk tertentu memiliki penjualan yang meningkat.',
            'Pertimbangkan penataan ulang produk pada area display.',
        ];
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#687EFF]">Overview</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-[-0.05em] text-[#1f2544]">Dashboard</h1>
            </div>
            <div class="rounded-xl border border-[#e7ebff] bg-white px-3 py-2 text-sm text-[#5e647f] shadow-sm">
                Periode: <span class="font-semibold text-[#1f2544]">Sep 2026</span>
            </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($stats as $stat)
                <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-[0_18px_40px_rgba(31,37,68,0.04)]">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm text-[#5e647f]">{{ $stat['label'] }}</p>
                            <p class="mt-3 text-3xl font-semibold text-[#1f2544]">{{ $stat['value'] }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $stat['color'] }}">
                            @switch($stat['icon'])
                                @case('cube')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Zm0 0v9m8-4.5-8 4.5-8-4.5" /></svg>
                                    @break
                                @case('store')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 10h14l-1.2-6.2A1 1 0 0 0 16.8 3H7.2a1 1 0 0 0-1 .8L5 10Zm0 0v8.5A1.5 1.5 0 0 0 6.5 20h11a1.5 1.5 0 0 0 1.5-1.5V10" /></svg>
                                    @break
                                @case('users')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 19v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1m11-9a4 4 0 1 0-8 0 4 4 0 0 0 8 0Zm8 9v-1a4 4 0 0 0-3-3.87M20 5a4 4 0 0 1 0 8" /></svg>
                                    @break
                                @case('chart')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 18V8m7 10V4m7 14v-7" /></svg>
                                    @break
                            @endswitch
                        </div>
                    </div>
                    <div class="mt-4 inline-flex items-center gap-2 rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-600">
                        {{ $stat['change'] }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">
            <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-[0_18px_40px_rgba(31,37,68,0.04)]">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <h2 class="text-xl font-semibold text-[#1f2544]">Grafik penjualan</h2>
                    <span class="rounded-full bg-[#eef3ff] px-2.5 py-1 text-xs font-medium text-[#687EFF]">This month</span>
                </div>
                <div class="flex h-64 items-end gap-3">
                    @foreach ([45, 62, 58, 80, 72, 96, 110] as $bar)
                        <div class="flex flex-1 flex-col items-center gap-3">
                            <div class="w-full rounded-t-2xl bg-gradient-to-t from-[#1F2544] to-[#98E4FF]" style="height: {{ $bar }}%;"></div>
                            <span class="text-xs text-[#5e647f]">{{ ['M','S','S','R','K','J','S'][array_search($bar, [45, 62, 58, 80, 72, 96, 110])] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-[0_18px_40px_rgba(31,37,68,0.04)]">
                <h2 class="text-xl font-semibold text-[#1f2544]">AI insight</h2>
                <div class="mt-5 space-y-3">
                    @foreach ($insights as $insight)
                        <div class="rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] p-3 text-sm leading-6 text-[#5e647f]">
                            {{ $insight }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-[0_18px_40px_rgba(31,37,68,0.04)]">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <h2 class="text-xl font-semibold text-[#1f2544]">Transaksi terbaru</h2>
                    <a href="{{ route('sales.index') }}" class="text-sm font-medium text-[#687EFF]">Lihat semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-[#5e647f]">
                        <thead>
                            <tr class="border-b border-[#e7ebff]">
                                <th class="pb-3 pr-4 font-medium text-[#1f2544]">Invoice</th>
                                <th class="pb-3 pr-4 font-medium text-[#1f2544]">Customer</th>
                                <th class="pb-3 pr-4 font-medium text-[#1f2544]">Amount</th>
                                <th class="pb-3 font-medium text-[#1f2544]">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $transaction)
                                <tr class="border-b border-[#eef3ff]">
                                    <td class="py-3 pr-4 text-[#1f2544]">{{ $transaction['invoice'] }}</td>
                                    <td class="py-3 pr-4 text-[#1f2544]">{{ $transaction['customer'] }}</td>
                                    <td class="py-3 pr-4 text-[#1f2544]">{{ $transaction['amount'] }}</td>
                                    <td class="py-3">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $transaction['status'] === 'Paid' ? 'bg-emerald-50 text-emerald-600' : ($transaction['status'] === 'Pending' ? 'bg-amber-50 text-amber-600' : 'bg-[#eef3ff] text-[#5e647f]') }}">
                                            {{ $transaction['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-[0_18px_40px_rgba(31,37,68,0.04)]">
                <h2 class="text-xl font-semibold text-[#1f2544]">Stok rendah</h2>
                <div class="mt-5 space-y-4">
                    @foreach ($lowStock as $item)
                        <div class="flex items-center justify-between gap-3 rounded-2xl bg-[#f7f9ff] p-3">
                            <div>
                                <p class="font-medium text-[#1f2544]">{{ $item['product'] }}</p>
                                <p class="mt-1 text-xs text-[#5e647f]">Tersisa {{ $item['stock'] }} unit</p>
                            </div>
                            <span class="rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-600">Low</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
