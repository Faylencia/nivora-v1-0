@extends('layouts.dashboard-shell', ['role' => 'member'])

@section('title', 'Notifikasi - Nivora')

@section('content')
    <div class="space-y-6" data-member-list="notifications">
        <x-page-header eyebrow="MEMBER / KABAR TOKO" title="Notifikasi" description="Promo baru, kupon loyalitas, poin kembalian, dan stok yang tersedia kembali." />
        <div data-member-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>
        <div data-member-loading class="ni-panel p-8 text-sm text-[#5e647f]">Memuat notifikasi…</div>
        <div data-member-empty class="hidden ni-panel p-8 text-center text-sm text-[#5e647f]">Belum ada notifikasi baru.</div>
        <div data-member-content class="hidden space-y-3" data-member-rows></div>
    </div>
@endsection
