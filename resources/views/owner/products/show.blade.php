@extends('layouts.owner')

@section('title', 'Product Detail - Nivora')

@section('content')
    @php
        $product = [
            'name' => 'Kopi Susu Signature',
            'sku' => 'KS-001',
            'category' => 'Minuman',
            'purchase_price' => 15000,
            'selling_price' => 18000,
            'description' => 'Kopi susu lembut dengan aroma khas dan tekstur creamy, cocok untuk pelanggan yang menyukai rasa kopi yang halus.',
            'stock' => 24,
            'status' => 'Active',
            'business' => 'Kopi Tuku',
        ];
    @endphp

    <div class="mx-auto max-w-5xl">
        <x-page-header eyebrow="OWNER / PRODUK" title="Detail produk" description="Tinjau informasi, harga, dan ketersediaan produk.">
            <x-button href="{{ route('owner.products.index') }}" type="ghost">Back</x-button>
        </x-page-header>

        <div class="overflow-hidden rounded-[30px] border border-[#e7ebff] bg-white/90 shadow-[0_22px_50px_rgba(31,37,68,0.05)]">
            <div class="grid gap-0 lg:grid-cols-[0.9fr_1.1fr]">
                <img src="https://images.unsplash.com/photo-1497636577773-f1231844b336?auto=format&fit=crop&w=1200&q=80" alt="{{ $product['name'] }}" class="h-full w-full object-cover" />

                <div class="p-6 sm:p-8">
                    <div class="mb-3 inline-flex rounded-full bg-[#eef3ff] px-2.5 py-1 text-xs font-medium text-[#687EFF]">{{ $product['category'] }}</div>
                    <h2 class="text-3xl font-semibold text-[#1f2544]">{{ $product['name'] }}</h2>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-[#f7f9ff] p-4">
                            <p class="text-sm text-[#5e647f]">SKU</p>
                            <p class="mt-2 text-base font-semibold text-[#1f2544]">{{ $product['sku'] }}</p>
                        </div>
                        <div class="rounded-2xl bg-[#f7f9ff] p-4">
                            <p class="text-sm text-[#5e647f]">Status</p>
                            <p class="mt-2 inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-600">{{ $product['status'] }}</p>
                        </div>
                        <div class="rounded-2xl bg-[#f7f9ff] p-4">
                            <p class="text-sm text-[#5e647f]">Purchase price</p>
                            <p class="mt-2 text-base font-semibold text-[#1f2544]">Rp {{ number_format($product['purchase_price'], 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-2xl bg-[#f7f9ff] p-4">
                            <p class="text-sm text-[#5e647f]">Selling price</p>
                            <p class="mt-2 text-base font-semibold text-[#1f2544]">Rp {{ number_format($product['selling_price'], 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="mt-6 rounded-2xl bg-[#f7f9ff] p-4">
                        <p class="text-sm text-[#5e647f]">Description</p>
                        <p class="mt-2 text-sm leading-6 text-[#5e647f]">{{ $product['description'] }}</p>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-[#f7f9ff] p-4">
                            <p class="text-sm text-[#5e647f]">Stock</p>
                            <p class="mt-2 text-base font-semibold text-[#1f2544]">{{ $product['stock'] }} pcs</p>
                        </div>
                        <div class="rounded-2xl bg-[#f7f9ff] p-4">
                            <p class="text-sm text-[#5e647f]">Business</p>
                            <p class="mt-2 text-base font-semibold text-[#1f2544]">{{ $product['business'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
