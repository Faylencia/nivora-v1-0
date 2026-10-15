@extends('layouts.app')

@section('title', 'Nivora - Temukan Produk dan Bisnis Lokal')

@section('content')
    <div class="public-home" data-public-home>
        <section data-member-public-home class="px-4 pb-10 pt-6 sm:px-6 sm:pb-14 sm:pt-8 lg:px-8 lg:pb-16">
            <div class="public-home-hero mx-auto grid max-w-7xl items-center gap-5 px-5 py-6 sm:px-8 sm:py-8 lg:grid-cols-[1.05fr_.95fr] lg:gap-10 lg:px-12 lg:py-8">
                <div class="public-home-hero-copy" data-public-animate>
                    <span class="public-home-hero-eyebrow"><span></span>Platform UMKM Indonesia</span>
                    <h1 class="public-home-hero-title mt-5 max-w-2xl text-4xl font-semibold leading-[1.08] tracking-[-0.055em] sm:text-5xl lg:text-[3.1rem]"><span>Temukan UMKM menarik</span><span>di sekitarmu.</span></h1>
                    <p class="mt-4 max-w-xl text-sm leading-7 sm:text-base">Jelajahi toko lokal, kenali ceritanya, dan temukan produk pilihan dari bisnis di Indonesia.</p>

                    <form action="{{ route('explore') }}" method="GET" class="public-home-search mt-7 flex max-w-2xl flex-col gap-2 rounded-2xl border border-[#e4e9f5] bg-white p-2 shadow-[0_12px_30px_rgba(31,37,68,.08)] sm:flex-row">
                        <label class="flex min-h-12 min-w-0 flex-1 items-center gap-3 px-3"><x-public-icon name="search" class="public-home-search-icon text-xl" /><input name="search" type="search" value="{{ request('search') }}" placeholder="Temukan toko atau produk..." class="min-h-10 min-w-0 w-full border-0 bg-transparent text-sm text-[#20284a] outline-none ring-0 placeholder:text-[#929bb0] focus:ring-0" /></label>
                        <button type="submit" class="public-home-search-submit min-h-12 rounded-xl px-6 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-4">Jelajahi toko <span class="ml-1" aria-hidden="true">→</span></button>
                    </form>
                    <div class="public-home-hero-actions"><a href="{{ route('explore') }}" class="public-home-primary-link">Jelajahi bisnis <span aria-hidden="true">↗</span></a><a href="#about" class="public-home-secondary-link">Pelajari Nivora <span aria-hidden="true">↓</span></a></div>
                    @if ($categories->isNotEmpty())
                        <nav class="public-home-discovery-categories" aria-label="Jelajahi kategori populer">
                            <span>Jelajahi:</span>
                            @foreach ($categories->take(4) as $category)
                                <a href="#category-{{ $category->id }}">{{ $category->name }}</a>
                            @endforeach
                        </nav>
                    @endif
                </div>

                <div class="public-grocery-art" data-public-hero-carousel data-hero-images="{{ implode('|', [asset('images/visitor-hero-produce.jpg'), asset('images/visitor-hero-drinks.jpg'), asset('images/visitor-hero-food.jpg'), asset('images/visitor-hero-fashion.jpg')]) }}" data-hero-labels="Sayuran segar|Minuman pilihan|Makanan favorit|Busana lokal">
                    <img class="public-grocery-photo" data-public-hero-image src="{{ asset('images/visitor-hero-produce.jpg') }}" alt="Sayuran segar dalam tas belanja pakai ulang" fetchpriority="high" />
                    <span class="public-grocery-caption" data-public-hero-label>Sayuran segar</span>
                    <div class="public-hero-metrics" aria-label="Statistik Nivora">
                        <div><span>Bisnis aktif</span><strong>{{ number_format($businessCount, 0, ',', '.') }}</strong></div>
                        <span class="public-hero-metrics-divider" aria-hidden="true"></span>
                        <div><span>Produk lokal</span><strong>{{ number_format($productCount, 0, ',', '.') }}</strong></div>
                    </div>
                </div>
            </div>
        </section>

        <section id="recommendations" class="public-home-section public-gradient-section mx-auto max-w-7xl scroll-mt-28 px-4 pb-12 sm:px-6 lg:px-8 lg:pb-16" data-public-animate>
            <div class="public-section-heading"><div><p class="ni-section-label">PILIHAN UNTUKMU</p><h2 class="mt-2 text-2xl font-semibold tracking-tight text-[#1f2544] sm:text-3xl">Produk rekomendasi</h2><p class="mt-2 text-sm text-[#5e647f]">Pilihan populer dan produk terbaru dari bisnis aktif.</p></div><a href="{{ route('explore') }}" class="public-section-link">Jelajahi bisnis <span aria-hidden="true">→</span></a></div>

            @if ($featuredProducts->isNotEmpty())
                <div class="public-product-grid grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-4">
                    @foreach ($featuredProducts as $product)
                        <x-public-product-card :product="$product" />
                    @endforeach
                </div>
            @else
                <div class="public-home-empty"><span class="public-home-empty-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Zm0 0v9m8-4.5-8 4.5-8-4.5"/></svg></span><h3>Rekomendasi segera hadir</h3><p>Produk dari bisnis aktif akan muncul di bagian ini.</p></div>
            @endif
        </section>

        @if ($categories->isNotEmpty())
            <section id="categories" class="public-home-section mx-auto max-w-7xl scroll-mt-28 px-4 pb-6 sm:px-6 lg:px-8" data-public-animate>
                <div class="public-section-heading"><div><p class="ni-section-label">JELAJAHI BERDASARKAN KATEGORI</p><h2 class="mt-2 text-2xl font-semibold tracking-tight text-[#1f2544] sm:text-3xl">Temukan sesuai kebutuhanmu</h2><p class="mt-2 text-sm text-[#5e647f]">Setiap kategori menampilkan produk yang tersedia dari toko aktif.</p></div></div>
                <nav class="public-category-shortcuts" aria-label="Kategori produk">
                    @foreach ($categories as $category)
                        <a href="#category-{{ $category->id }}" class="public-category-chip"><x-public-category-icon :name="$category->name" /><span class="public-category-chip-name">{{ $category->name }}</span><span class="public-category-chip-count" aria-label="{{ $category->active_products_count }} produk tersedia">{{ $category->active_products_count }}</span></a>
                    @endforeach
                </nav>
            </section>

            @foreach ($categories as $category)
                <section id="category-{{ $category->id }}" class="public-category-section mx-auto max-w-7xl scroll-mt-28 px-4 pb-10 sm:px-6 lg:px-8 lg:pb-14" data-public-animate>
                    <div class="public-category-heading"><div class="public-category-heading-main"><x-public-category-icon :name="$category->name" /><div><span class="public-category-kicker">KATEGORI</span><h2>{{ $category->name }}</h2><p>{{ $category->active_products_count }} produk pilihan tersedia</p></div></div><a href="{{ route('explore') }}" aria-label="Jelajahi bisnis lokal">Lihat bisnis <span aria-hidden="true">→</span></a></div>
                    <div class="public-product-grid grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-4">
                        @foreach ($category->products as $product)
                            <x-public-product-card :product="$product" />
                        @endforeach
                    </div>
                </section>
            @endforeach
        @endif

        <section id="about" class="public-about-section" data-public-animate>
            <div class="public-about-copy">
                <p class="ni-section-label">TENTANG NIVORA</p>
                <h2>Bisnis lokal punya banyak hal menarik untuk ditemukan.</h2>
                <p>Nivora membantu pengunjung mengenal toko dan produk lokal melalui informasi yang lebih mudah dijelajahi dalam satu tempat.</p>
                <a href="{{ route('explore') }}" class="public-section-link">Mulai menjelajah <span aria-hidden="true">→</span></a>
            </div>
            <div class="public-about-values">
                <article class="public-about-value">
                    <span class="public-about-icon" aria-hidden="true"><x-public-icon name="store-alt" /></span>
                    <h3>Kenali toko</h3>
                    <p>Lihat profil, lokasi, dan informasi bisnis sebelum berkunjung.</p>
                </article>
                <article class="public-about-value">
                    <span class="public-about-icon" aria-hidden="true"><x-public-icon name="category" /></span>
                    <h3>Jelajahi pilihan</h3>
                    <p>Temukan produk lokal berdasarkan kategori dan toko yang tersedia.</p>
                </article>
                <article class="public-about-value">
                    <span class="public-about-icon" aria-hidden="true"><x-public-icon name="info-circle" /></span>
                    <h3>Dapatkan informasi</h3>
                    <p>Periksa detail produk, harga, dan ketersediaan yang ditampilkan toko.</p>
                </article>
            </div>
        </section>

        <section class="public-gradient-section public-home-join-section mx-auto max-w-7xl px-4 pb-14 sm:px-6 lg:px-8 lg:pb-20" data-public-animate>
            <div data-home-guest-callout class="public-home-join flex flex-col gap-5 rounded-[28px] border border-[#dfe7ff] bg-white p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                <div><p class="text-xs font-bold uppercase tracking-[0.16em] text-[#687EFF]">Punya bisnis lokal?</p><h2 class="mt-2 text-xl font-semibold text-[#1f2544]">Bawa tokomu lebih dekat ke pelanggan.</h2><p class="mt-2 max-w-2xl text-sm leading-6 text-[#5e647f]">Bergabung dengan Nivora untuk mengelola produk, stok, promo, dan transaksi dalam satu tempat.</p></div>
                <a href="{{ route('login') }}" class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-xl bg-[#687EFF] px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#596eea]">Masuk atau daftar <span class="ml-2" aria-hidden="true">→</span></a>
            </div>
            <div data-home-authenticated-callout class="public-home-join hidden rounded-[28px] border border-[#dfe7ff] bg-white p-6 sm:p-8"><p class="text-xs font-bold uppercase tracking-[0.16em] text-[#687EFF]">Selamat datang kembali</p><h2 class="mt-2 text-xl font-semibold text-[#1f2544]">Halo, <span data-session-user>Member</span>.</h2><p class="mt-2 text-sm leading-6 text-[#5e647f]">Jelajahi produk dan toko lokal. Klik <strong>Dashboard Member</strong> di navbar untuk membuka poin, kupon, dan riwayat belanja.</p></div>
        </section>
    </div>
@endsection
