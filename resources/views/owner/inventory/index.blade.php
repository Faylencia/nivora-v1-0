@extends('layouts.owner')

@section('title', 'Inventory - Nivora')

@section('content')
    @php
        $stats = [
            ['label' => 'Total stock', 'value' => '12,430', 'type' => 'normal'],
            ['label' => 'Low stock', 'value' => '24', 'type' => 'low'],
            ['label' => 'Out of stock', 'value' => '8', 'type' => 'out'],
        ];

        $inventory = [
            ['product' => 'Kopi Susu Signature', 'outlet' => 'Bandung Center', 'stock' => 24, 'min_stock' => 10, 'status' => 'Normal'],
            ['product' => 'Tas Jinjing', 'outlet' => 'Jakarta Pop-up', 'stock' => 7, 'min_stock' => 10, 'status' => 'Low Stock'],
            ['product' => 'Sambal Tempoe', 'outlet' => 'Cibadak Outlet', 'stock' => 0, 'min_stock' => 8, 'status' => 'Out of Stock'],
        ];
    @endphp

    <div class="space-y-6">
        <x-page-header eyebrow="OWNER / INVENTORI" title="Inventori" description="Pantau stok, kondisi, dan ketersediaan barang toko." />

        <div class="grid gap-5 md:grid-cols-3">
            @foreach ($stats as $stat)
                <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-[0_16px_38px_rgba(31,37,68,0.05)]">
                    <p class="text-sm text-[#5e647f]">{{ $stat['label'] }}</p>
                    <p class="mt-3 text-3xl font-semibold text-[#1f2544]">{{ $stat['value'] }}</p>
                    <div class="mt-4 inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $stat['type'] === 'normal' ? 'bg-emerald-50 text-emerald-600' : ($stat['type'] === 'low' ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600') }}">{{ $stat['type'] === 'normal' ? 'Healthy' : ($stat['type'] === 'low' ? 'Watch' : 'Critical') }}</div>
                </div>
            @endforeach
        </div>

        <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-[0_16px_38px_rgba(31,37,68,0.05)]">
            <div class="mb-5 flex items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-[#1f2544]">Inventory table</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-[#5e647f]">
                    <thead>
                        <tr class="border-b border-[#e7ebff]">
                            <th class="pb-3 pr-4 font-medium text-[#1f2544]">Product</th>
                            <th class="pb-3 pr-4 font-medium text-[#1f2544]">Outlet</th>
                            <th class="pb-3 pr-4 font-medium text-[#1f2544]">Stock</th>
                            <th class="pb-3 pr-4 font-medium text-[#1f2544]">Minimum</th>
                            <th class="pb-3 font-medium text-[#1f2544]">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventory as $item)
                            <tr class="border-b border-[#eef3ff]">
                                <td class="py-3 pr-4 font-medium text-[#1f2544]">{{ $item['product'] }}</td>
                                <td class="py-3 pr-4">{{ $item['outlet'] }}</td>
                                <td class="py-3 pr-4">{{ $item['stock'] }}</td>
                                <td class="py-3 pr-4">{{ $item['min_stock'] }}</td>
                                <td class="py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $item['status'] === 'Normal' ? 'bg-emerald-50 text-emerald-600' : ($item['status'] === 'Low Stock' ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600') }}">{{ $item['status'] }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
