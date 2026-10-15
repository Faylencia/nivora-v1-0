@extends('layouts.dashboard-shell', ['role' => 'member'])

@section('title', 'Kupon Saya - Nivora')

@section('content')
    <div class="space-y-6" data-member-list="coupons">
        <x-page-header eyebrow="MEMBER / LOYALITAS" title="Kupon saya" description="Kupon berlaku hanya untuk toko dan syarat yang tertera. Tunjukkan kodenya saat membayar." />
        <div data-member-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>
        <div data-member-loading class="ni-panel p-8 text-sm text-[#5e647f]">Memuat kupon…</div>
        <div data-member-empty class="hidden ni-panel p-8 text-center text-sm text-[#5e647f]">Belum ada kupon. Kupon loyalitas otomatis diberikan setiap 10 transaksi selesai di toko yang sama.</div>
        <div data-member-content class="hidden grid gap-4 sm:grid-cols-2 xl:grid-cols-3" data-member-rows></div>
    </div>
@endsection
