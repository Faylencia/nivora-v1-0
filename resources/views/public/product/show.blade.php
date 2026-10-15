@extends('layouts.app')

@section('title', 'Detail Produk Lokal - Nivora')

@section('content')
    <section class="public-product-detail" data-member-product-detail="{{ $product->id }}" aria-labelledby="product-access-title">
        <a href="{{ route('explore') }}" class="public-detail-back"><span aria-hidden="true">←</span> Kembali ke Explore</a>

        <div data-product-detail-loading class="public-detail-loading public-product-loading" role="status" aria-live="polite">
            <div class="public-product-loading-image" aria-hidden="true"></div>
            <div class="public-product-loading-copy" aria-hidden="true"><i></i><i></i><i></i><i></i></div>
            <span class="sr-only">Memuat detail produk...</span>
        </div>

        <div data-product-detail-gate class="public-detail-gate hidden">
            <span class="public-gate-icon" aria-hidden="true"><x-public-icon name="lock-alt" /></span>
            <p class="ni-section-label">INFORMASI PRODUK</p>
            <h1 id="product-access-title">Kenali produk lokal lebih dekat.</h1>
            <p class="public-gate-copy">Masuk sebagai member untuk melihat harga, detail, dan ketersediaan produk dari toko.</p>
            <div class="public-gate-actions">
                <a href="{{ route('login', ['redirect' => request()->getRequestUri()]) }}" class="public-primary-button">Masuk sebagai member <span aria-hidden="true">→</span></a>
                <a href="{{ route('register') }}" class="public-secondary-button">Buat akun</a>
            </div>
        </div>

        <div data-product-detail-content class="public-product-detail-content hidden"></div>
        <div data-product-detail-error class="public-detail-error hidden" role="alert"></div>
    </section>
@endsection
