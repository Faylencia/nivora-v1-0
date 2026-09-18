@extends('layouts.cashier')

@section('title', 'Produk Kasir - Nivora')

@section('content')
    @php
        $products = [
            ['name' => 'Kopi Susu Signature', 'category' => 'Minuman', 'price' => 'Rp 18.000', 'stock' => 24, 'status' => 'Available'],
            ['name' => 'Boba Hazelnut', 'category' => 'Minuman', 'price' => 'Rp 22.000', 'stock' => 12, 'status' => 'Available'],
            ['name' => 'Croissant Matcha', 'category' => 'Cemilan', 'price' => 'Rp 26.000', 'stock' => 8, 'status' => 'Low Stock'],
            ['name' => 'Tas Jinjing', 'category' => 'Fashion', 'price' => 'Rp 125.000', 'stock' => 0, 'status' => 'Out of Stock'],
        ];
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#687EFF]">Produk</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-[-0.05em] text-[#1f2544]">Daftar Produk</h1>
            </div>
            <a href="{{ route('cashier.pos') }}" class="inline-flex min-h-[44px] items-center justify-center rounded-xl bg-[#687EFF] px-4 py-2.5 text-sm font-medium text-white">Mulai Transaksi</a>
        </div>

        <div class="rounded-[28px] border border-[#e7ebff] bg-white p-5 shadow-sm">
            <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="w-full max-w-md rounded-xl border border-[#e7ebff] bg-[#f7f9ff] px-3 py-2">
                    <input type="text" placeholder="Search produk" class="w-full bg-transparent text-sm text-[#1f2544] outline-none placeholder:text-[#6b7280]" />
                </div>
                <select class="rounded-xl border border-[#e7ebff] bg-[#f7f9ff] px-3 py-2.5 text-sm text-[#1f2544] outline-none">
                    <option>Semua kategori</option>
                    <option>Minuman</option>
                    <option>Cemilan</option>
                    <option>Fashion</option>
                </select>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($products as $product)
                    <div class="overflow-hidden rounded-[24px] border border-[#e7ebff] bg-[#f7f9ff] shadow-sm">
                        <div class="h-32 bg-gradient-to-br from-[#dfe7ff] to-[#98E4FF]"></div>
                        <div class="p-4">
                            <p class="font-semibold text-[#1f2544]">{{ $product['name'] }}</p>
                            <p class="mt-1 text-xs text-[#687EFF]">{{ $product['category'] }}</p>
                            <p class="mt-3 text-sm font-semibold text-[#1f2544]">{{ $product['price'] }}</p>
                            <div class="mt-3 flex items-center justify-between gap-3">
                                <span class="text-xs text-[#5e647f]">Stok: {{ $product['stock'] }}</span>
                                <span class="inline-flex rounded-full px-2 py-1 text-[10px] font-medium
                                    @if($product['status'] === 'Available') bg-emerald-50 text-emerald-600
                                    @elseif($product['status'] === 'Low Stock') bg-amber-50 text-amber-600
                                    @else bg-rose-50 text-rose-600
                                    @endif">
                                    {{ $product['status'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
