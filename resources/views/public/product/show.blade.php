@extends('layouts.app')

@section('title', 'Kopi Susu Signature - Nivora')

@section('content')
    @php
        $product = [
            'name' => 'Kopi Susu Signature',
            'category' => 'Minuman',
            'price' => 18000,
            'description' => 'Kopi susu lembut dengan aroma khas, cita rasa yang seimbang, dan tekstur creamy yang nikmat untuk menemani hari Anda.',
            'umkm' => 'Kopi Tuku',
            'outlet' => 'Bandung Center',
            'stock' => 24,
        ];
    @endphp

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('business.show', ['business' => 1]) }}" class="inline-flex items-center gap-2 text-sm font-medium text-[#1f2544] hover:text-[#687EFF]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke toko
            </a>
        </div>

        <div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr]">
            <div class="overflow-hidden rounded-[30px] border border-[#e7ebff] bg-white shadow-sm">
                <img src="https://images.unsplash.com/photo-1497636577773-f1231844b336?auto=format&fit=crop&w=1200&q=80" alt="{{ $product['name'] }}" class="h-full w-full object-cover" />
            </div>

            <div>
                <div class="mb-3 inline-flex rounded-full bg-[#eef3ff] px-2.5 py-1 text-xs font-medium text-[#1f2544]">{{ $product['category'] }}</div>
                <h1 class="text-3xl font-semibold tracking-[-0.05em] text-[#1f2544]">{{ $product['name'] }}</h1>

                <div class="mt-5 flex items-center gap-3">
                    <span class="text-3xl font-bold text-[#1f2544]">Rp {{ number_format($product['price'], 0, ',', '.') }}</span>
                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-600">Stok: {{ $product['stock'] }}</span>
                </div>

                <p class="mt-6 text-base leading-7 text-[#5e647f]">{{ $product['description'] }}</p>

                <div class="mt-8 rounded-[28px] border border-[#e7ebff] bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-semibold text-[#1f2544]">Informasi UMKM</h2>
                    <div class="mt-4 space-y-3 text-sm text-[#5e647f]">
                        <div class="flex items-center justify-between gap-3">
                            <span>UMKM</span>
                            <span class="font-medium text-slate-900">{{ $product['umkm'] }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span>Outlet</span>
                            <span class="font-medium text-slate-900">{{ $product['outlet'] }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span>Lokasi</span>
                            <span class="font-medium text-slate-900">Bandung</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <x-button href="{{ route('business.show', ['business' => 1]) }}" type="primary" size="lg">Lihat Toko</x-button>
                    <x-button href="{{ route('explore') }}" type="secondary" size="lg">Lihat Produk</x-button>
                </div>
            </div>
        </div>
    </section>
@endsection
