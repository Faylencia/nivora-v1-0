@extends('layouts.app')

@section('title', 'Kopi Tuku - Nivora')

@section('content')
    @php
        $business = [
            'name' => 'Kopi Tuku',
            'category' => 'Minuman',
            'address' => 'Jl. Citarum No. 24, Bandung',
            'phone' => '+62 812-3456-7890',
            'description' => 'Kopi Tuku menghadirkan pengalaman ngopi yang hangat dan nyaman dengan bahan kopi pilihan serta ruang santai untuk komunitas lokal.',
        ];

        $outlets = [
            ['name' => 'Bandung Center', 'address' => 'Jl. Citarum No. 24'],
            ['name' => 'Cibadak Outlet', 'address' => 'Jl. Raya Cibadak No. 18'],
        ];

        $products = [
            ['name' => 'Kopi Susu Signature', 'price' => 18000, 'image' => 'https://images.unsplash.com/photo-1497636577773-f1231844b336?auto=format&fit=crop&w=900&q=80'],
            ['name' => 'Boba Hazelnut', 'price' => 22000, 'image' => 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=900&q=80'],
            ['name' => 'Croissant Matcha', 'price' => 26000, 'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=900&q=80'],
        ];
    @endphp

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-[32px] border border-[#e7ebff] bg-white shadow-sm">
            <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=1600&q=80" alt="{{ $business['name'] }}" class="h-72 w-full object-cover sm:h-96" />
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-[1.4fr_0.6fr]">
            <div>
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-[#eef3ff] text-2xl">☕</div>
                    <div>
                        <div class="inline-flex rounded-full bg-[#eef3ff] px-2.5 py-1 text-xs font-medium text-[#1f2544]">{{ $business['category'] }}</div>
                        <h1 class="mt-2 text-3xl font-semibold tracking-[-0.05em] text-[#1f2544]">{{ $business['name'] }}</h1>
                    </div>
                </div>

                <div class="rounded-[28px] border border-[#e7ebff] bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center gap-3 text-[#5e647f]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s-6.5-4.35-9-8.2C.55 10.2 2.66 5 7.17 5A4.4 4.4 0 0 1 12 7.1 4.4 4.4 0 0 1 16.83 5c4.51 0 6.62 5.2 4.17 7.8-2.5 3.85-9 8.2-9 8.2Z"/></svg>
                        <span>{{ $business['address'] }}</span>
                    </div>
                    <div class="mb-5 flex items-center gap-3 text-[#5e647f]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5.5A2.5 2.5 0 0 1 5.5 3h.48a2 2 0 0 1 1.94 1.53l.72 3.23a2 2 0 0 1-1.24 2.32l-.94.38a15.52 15.52 0 0 0 7.1 7.1l.38-.94a2 2 0 0 1 2.32-1.24l3.23.72A2 2 0 0 1 21 17.02v.48A2.5 2.5 0 0 1 18.5 20h-1.5C10.95 20 4 13.05 4 7V5.5Z"/></svg>
                        <span>{{ $business['phone'] }}</span>
                    </div>
                    <p class="text-base leading-7 text-[#5e647f]">{{ $business['description'] }}</p>
                </div>

                <div class="mt-8">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-2xl font-semibold text-[#1f2544]">Outlet</h2>
                        <span class="text-sm text-[#5e647f]">2 lokasi tersedia</span>
                    </div>
                    <div class="space-y-4">
                        @foreach ($outlets as $outlet)
                            <div class="flex items-center justify-between rounded-2xl border border-[#e7ebff] bg-white p-4 shadow-sm">
                                <div>
                                    <h3 class="font-semibold text-[#1f2544]">{{ $outlet['name'] }}</h3>
                                    <p class="mt-1 text-sm text-[#5e647f]">{{ $outlet['address'] }}</p>
                                </div>
                                <a href="#" class="inline-flex items-center gap-2 text-sm font-semibold text-[#1f2544]">Lihat lokasi</a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-2xl font-semibold text-[#1f2544]">Daftar Produk</h2>
                        <a href="{{ route('product.show', ['product' => 1]) }}" class="text-sm font-semibold text-[#1f2544]">Lihat semua</a>
                    </div>
                    <div class="grid gap-5 md:grid-cols-3">
                        @foreach ($products as $product)
                            <article class="overflow-hidden rounded-[24px] border border-[#e7ebff] bg-white shadow-sm">
                                <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="h-40 w-full object-cover" />
                                <div class="p-4">
                                    <h3 class="text-base font-semibold text-[#1f2544]">{{ $product['name'] }}</h3>
                                    <p class="mt-2 text-base font-semibold text-[#1f2544]">Rp {{ number_format($product['price'], 0, ',', '.') }}</p>
                                    <a href="{{ route('product.show', ['product' => 1]) }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-[#5e647f]">Lihat Detail</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>

            <aside class="space-y-5">
                <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Informasi bisnis</h3>
                    <div class="mt-5 space-y-4 text-sm text-slate-600">
                        <div class="flex items-center justify-between gap-3">
                            <span>Kategori</span>
                            <span class="font-medium text-slate-900">{{ $business['category'] }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span>Jam operasional</span>
                            <span class="font-medium text-slate-900">08.00 - 21.00</span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span>Rating</span>
                            <span class="font-medium text-amber-500">4.9/5</span>
                        </div>
                    </div>
                    <div class="mt-5 space-y-3">
                        <x-button href="#" type="primary" class="w-full">Lihat lokasi</x-button>
                        <x-button href="#" type="secondary" class="w-full">Hubungi</x-button>
                    </div>
                </div>
            </aside>
        </div>
    </section>
@endsection
