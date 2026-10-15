@extends('layouts.app')

@section('title', 'Profil Bisnis Lokal - Nivora')

@section('content')
    <section class="public-business-detail-page" data-member-business-detail="{{ $business->id }}" aria-label="Profil bisnis lokal">
        <a href="{{ route('explore') }}" class="public-detail-back"><span aria-hidden="true">←</span> Kembali ke Explore</a>

        <div data-member-detail-loading class="public-detail-loading" role="status" aria-live="polite">
            <span class="public-loading-mark" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 10.5 12 4l9 6.5M5 9.5V20h14V9.5M9 20v-6h6v6"/></svg></span>
            <div><strong>Menyiapkan profil bisnis</strong><p>Informasi toko dan produk sedang dimuat.</p></div>
            <div class="public-loading-lines" aria-hidden="true"><i></i><i></i><i></i></div>
        </div>

        <div data-member-detail-gate class="public-detail-gate hidden">
            <span class="public-gate-icon" aria-hidden="true"><x-public-icon name="lock-alt" /></span>
            <p class="ni-section-label">PROFIL BISNIS</p>
            <h1 id="business-access-title">Kenali toko lebih dekat.</h1>
            <p class="public-gate-copy">Profil lengkap, informasi produk, stok, dan promo tersedia setelah masuk sebagai member Nivora.</p>
            <div class="public-gate-actions">
                <a href="{{ route('login', ['redirect' => request()->getRequestUri()]) }}" class="public-primary-button">Masuk sebagai member <span aria-hidden="true">→</span></a>
                <a href="{{ route('register') }}" class="public-secondary-button">Buat akun</a>
            </div>
        </div>

        <div data-member-detail-content class="public-business-profile hidden">
            <header class="public-business-profile-hero">
                <div class="public-profile-ambient" aria-hidden="true"><span></span><span></span><span></span></div>
                <div class="public-business-profile-main">
                    <span data-store-logo class="public-profile-logo"><x-public-icon name="store-alt" /></span>
                    <div class="public-profile-copy">
                        <p data-store-type class="ni-section-label"></p>
                        <h1 data-store-name id="business-profile-title"></h1>
                        <p class="public-profile-address"><x-public-icon name="map-pin" /><span data-store-address></span></p>
                    </div>
                </div>
                <div class="public-profile-actions">
                    <a href="#business-products" class="public-primary-button">Lihat produk <span aria-hidden="true">↓</span></a>
                    <a data-store-phone class="public-secondary-button" aria-label="Hubungi toko">Hubungi toko</a>
                </div>
            </header>

            <section class="public-detail-section" aria-labelledby="business-location-title">
                <div class="public-detail-section-heading"><div><p class="ni-section-label">TEMUKAN KAMI</p><h2 id="business-location-title">Lokasi toko</h2><p>Informasi lokasi dan kontak yang ditampilkan oleh bisnis.</p></div></div>
                <div data-store-outlets class="public-outlet-grid"></div>
            </section>

            <section class="public-detail-section" aria-labelledby="business-promo-title">
                <div class="public-detail-section-heading"><div><p class="ni-section-label">PENAWARAN</p><h2 id="business-promo-title">Promo aktif</h2><p>Penawaran yang sedang tersedia dari toko ini.</p></div></div>
                <div data-store-promotions class="public-promotion-grid"></div>
            </section>

            <section id="business-products" class="public-detail-section" aria-labelledby="business-products-title">
                <div class="public-detail-section-heading"><div><p class="ni-section-label">KATALOG TOKO</p><h2 id="business-products-title">Produk pilihan</h2><p>Kenali produk dan ketersediaan yang dicatat oleh toko.</p></div></div>
                <div data-store-products class="public-store-product-grid"></div>
            </section>
        </div>

        <div data-member-detail-error class="public-detail-error hidden" role="alert"></div>
    </section>
@endsection
