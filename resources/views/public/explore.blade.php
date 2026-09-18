@extends('layouts.app')

@section('title', 'Explore UMKM - Nivora')

@section('content')
    @php
        $filters = ['Semua', 'Makanan', 'Minuman', 'Fashion', 'Kerajinan', 'Jasa'];
        $locations = ['Semua Lokasi', 'Bandung', 'Yogyakarta', 'Jakarta', 'Surabaya', 'Semarang'];
        $businesses = [
            ['name' => 'Kopi Tuku', 'category' => 'Minuman', 'location' => 'Bandung', 'rating' => 4.9, 'cover' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=900&q=80'],
            ['name' => 'Batik Nusantara', 'category' => 'Fashion', 'location' => 'Yogyakarta', 'rating' => 4.8, 'cover' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=900&q=80'],
            ['name' => 'Rasa Rakyat', 'category' => 'Makanan', 'location' => 'Surabaya', 'rating' => 4.7, 'cover' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=900&q=80'],
            ['name' => 'Toko Hati', 'category' => 'Kerajinan', 'location' => 'Semarang', 'rating' => 4.6, 'cover' => 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=900&q=80'],
            ['name' => 'Lumbung Jaya', 'category' => 'Rumah Tangga', 'location' => 'Jakarta', 'rating' => 4.8, 'cover' => 'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=900&q=80'],
            ['name' => 'Nusa Craft', 'category' => 'Kerajinan', 'location' => 'Bandung', 'rating' => 4.7, 'cover' => 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=900&q=80'],
        ];
    @endphp

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#687EFF]">Explore</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-[-0.05em] text-[#1f2544]">Jelajahi UMKM pilihan</h1>
            </div>
            <div class="w-full max-w-md rounded-2xl border border-[#e7ebff] bg-white p-2 shadow-sm">
                <div class="flex items-center gap-3 px-3 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#5e647f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35m1.6-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                    <input type="text" placeholder="Cari nama usaha atau produk" class="w-full border-0 bg-transparent text-sm text-[#1f2544] outline-none placeholder:text-[#7c859f]" />
                </div>
            </div>
        </div>

        <div class="mb-8 grid gap-4 rounded-[28px] border border-[#e7ebff] bg-white p-4 shadow-sm md:grid-cols-2 xl:grid-cols-4">
            <div>
                <label class="mb-2 block text-sm font-medium text-[#5e647f]">Kategori</label>
                <select class="w-full rounded-xl border border-[#e7ebff] bg-[#f7f9ff] px-3 py-2.5 text-sm text-[#1f2544] outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#dfe7ff]">
                    @foreach ($filters as $filter)
                        <option>{{ $filter }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-[#5e647f]">Lokasi</label>
                <select class="w-full rounded-xl border border-[#e7ebff] bg-[#f7f9ff] px-3 py-2.5 text-sm text-[#1f2544] outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#dfe7ff]">
                    @foreach ($locations as $location)
                        <option>{{ $location }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-[#5e647f]">Rating</label>
                <select class="w-full rounded-xl border border-[#e7ebff] bg-[#f7f9ff] px-3 py-2.5 text-sm text-[#1f2544] outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#dfe7ff]">
                    <option>Semua rating</option>
                    <option>4.5+</option>
                    <option>4.8+</option>
                </select>
            </div>

            <div class="flex items-end">
                <x-button type="primary" class="w-full" size="lg">Terapkan filter</x-button>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($businesses as $business)
                <article class="overflow-hidden rounded-[28px] border border-[#e7ebff] bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <img src="{{ $business['cover'] }}" alt="{{ $business['name'] }}" class="h-52 w-full object-cover" />
                    <div class="p-5">
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <span class="rounded-full bg-[#eef3ff] px-2.5 py-1 text-xs font-medium text-[#1f2544]">{{ $business['category'] }}</span>
                            <span class="inline-flex items-center gap-1 text-sm font-medium text-amber-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.539 1.118L10 18.32l-2.9 1.86c-.784.57-1.839-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L3.47 8.72c-.783-.57-.38-1.81.588-1.81h3.462a1 1 0 0 0 .95-.69l1.07-3.292Z"/></svg>
                                {{ $business['rating'] }}
                            </span>
                        </div>
                        <h3 class="text-xl font-semibold text-[#1f2544]">{{ $business['name'] }}</h3>
                        <div class="mt-3 flex items-center gap-2 text-sm text-[#5e647f]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s-6.5-4.35-9-8.2C.55 10.2 2.66 5 7.17 5A4.4 4.4 0 0 1 12 7.1 4.4 4.4 0 0 1 16.83 5c4.51 0 6.62 5.2 4.17 7.8-2.5 3.85-9 8.2-9 8.2Z"/></svg>
                            {{ $business['location'] }}
                        </div>
                        <div class="mt-5 flex items-center justify-between">
                            <span class="text-sm text-[#5e647f]">12 produk tersedia</span>
                            <a href="{{ route('business.show', ['business' => 1]) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#1f2544] hover:text-[#687EFF]">
                                Lihat detail
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14m-7-7 7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-10 flex items-center justify-center gap-2 text-sm text-[#5e647f]">
            <button class="rounded-xl border border-[#e7ebff] bg-white px-3 py-2 font-medium text-[#5e647f]">Prev</button>
            <button class="rounded-xl bg-[#687EFF] px-3 py-2 font-medium text-white">1</button>
            <button class="rounded-xl border border-[#e7ebff] bg-white px-3 py-2 font-medium text-[#5e647f]">2</button>
            <button class="rounded-xl border border-[#e7ebff] bg-white px-3 py-2 font-medium text-[#5e647f]">3</button>
            <button class="rounded-xl border border-[#e7ebff] bg-white px-3 py-2 font-medium text-[#5e647f]">Next</button>
        </div>

        <div class="mt-12 rounded-[28px] border border-dashed border-[#dfe7ff] bg-[#f7f9ff] p-8 text-center">
            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#eef3ff] text-2xl">🔎</div>
            <h3 class="text-xl font-semibold text-[#1f2544]">Tidak ada hasil yang cocok</h3>
            <p class="mt-2 text-sm text-[#5e647f]">Coba ubah filter lokasi atau kategori untuk menemukan usaha yang lebih sesuai.</p>
        </div>
    </section>
@endsection
