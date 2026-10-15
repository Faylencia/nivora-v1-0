@extends('layouts.app')

@section('title', 'Explore Bisnis Lokal - Nivora')

@section('content')
    <section class="public-directory" aria-labelledby="explore-title">
        <header class="public-directory-hero" data-public-animate>
            <div class="public-directory-copy">
                <p class="ni-section-label">EXPLORE NIVORA</p>
                <h1 id="explore-title">Temukan UMKM favoritmu.</h1>
                <p class="public-directory-intro">Jelajahi profil bisnis lokal, lihat pilihan produknya, dan temukan toko yang ingin kamu kenal lebih dekat.</p>
                <div class="public-directory-highlights">
                    <span><strong>{{ number_format($businesses->total(), 0, ',', '.') }}</strong> bisnis aktif</span>
                    <a href="{{ route('home') }}#categories">Lihat kategori <span aria-hidden="true">↗</span></a>
                </div>
            </div>

            <form action="{{ route('explore') }}" method="GET" class="public-directory-search" role="search">
                <label for="business-search" class="sr-only">Cari bisnis lokal</label>
                <x-public-icon name="search" class="public-directory-search-icon" />
                <input id="business-search" name="search" type="search" value="{{ request('search') }}" placeholder="Cari nama toko, kategori, atau lokasi" />
                @if (request()->filled('search'))
                    <a href="{{ route('explore') }}" class="public-search-clear" aria-label="Hapus pencarian">×</a>
                @endif
                <button type="submit">Cari <span aria-hidden="true">→</span></button>
            </form>
        </header>

        <div class="public-directory-results">
            <div class="public-directory-results-heading">
                <div>
                    <p class="ni-section-label">BISNIS LOKAL</p>
                    <h2>{{ request()->filled('search') ? 'Hasil pencarian' : 'Jelajahi toko' }}</h2>
                </div>
                <span class="public-results-count">{{ number_format($businesses->total(), 0, ',', '.') }} hasil</span>
            </div>

            @if ($businesses->isEmpty())
                <div class="public-empty-state">
                    <span class="public-empty-icon" aria-hidden="true"><x-public-icon name="search-alt-2" /></span>
                    <h2>{{ request()->filled('search') ? 'Belum ada bisnis yang cocok.' : 'Belum ada bisnis untuk ditampilkan.' }}</h2>
                    <p>{{ request()->filled('search') ? 'Coba kata pencarian yang lebih umum atau periksa ejaannya.' : 'Bisnis lokal akan muncul di sini setelah tersedia.' }}</p>
                    @if (request()->filled('search'))
                        <a href="{{ route('explore') }}" class="public-secondary-button">Hapus pencarian</a>
                    @endif
                </div>
            @else
                <div class="public-business-grid">
                    @foreach ($businesses as $business)
                        @php($businessLogo = $business->logo ? (\Illuminate\Support\Str::startsWith($business->logo, ['http://', 'https://', '/']) ? $business->logo : '/storage/'.ltrim($business->logo, '/')) : null)
                        <article class="public-business-card" data-public-animate>
                            <div class="public-business-cover">
                                <span class="public-cover-orbit public-cover-orbit-one" aria-hidden="true"></span>
                                <span class="public-cover-orbit public-cover-orbit-two" aria-hidden="true"></span>
                                <span class="public-business-type">{{ $business->business_type ?: 'Bisnis lokal' }}</span>
                                <span class="public-business-logo-frame">
                                    @if ($businessLogo)
                                        <img src="{{ $businessLogo }}" alt="Logo {{ $business->name }}" loading="lazy" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden')">
                                        <x-public-icon name="store-alt" class="hidden" />
                                    @else
                                        <x-public-icon name="store-alt" />
                                    @endif
                                </span>
                            </div>
                            <div class="public-business-body">
                                <div class="public-business-title-row">
                                    <div class="min-w-0">
                                        <h2><a href="{{ route('business.show', ['business' => $business->id]) }}">{{ $business->name }}</a></h2>
                                        <p class="public-business-location"><x-public-icon name="map-pin" /><span>{{ $business->address ?: 'Alamat belum dicantumkan' }}</span></p>
                                    </div>
                                    <span class="public-business-arrow" aria-hidden="true">↗</span>
                                </div>
                                <div class="public-business-card-footer">
                                    <span><i aria-hidden="true"></i>{{ number_format($business->active_products_count, 0, ',', '.') }} produk aktif</span>
                                    <a href="{{ route('business.show', ['business' => $business->id]) }}">Lihat bisnis <span aria-hidden="true">→</span></a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="public-directory-pagination">{{ $businesses->links() }}</div>
            @endif
        </div>
    </section>
@endsection
