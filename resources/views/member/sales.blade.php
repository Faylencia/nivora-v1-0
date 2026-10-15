@extends('layouts.dashboard-shell', ['role' => 'member'])

@section('title', 'Riwayat Belanja - Nivora')

@section('content')
    <div class="space-y-6" data-member-list="sales">
        <x-page-header eyebrow="MEMBER / RIWAYAT" title="Riwayat belanja" description="Transaksi yang terkait dengan akun dan nomor ponsel member Anda." />
        <div data-member-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>
        <div data-member-loading class="ni-panel p-8 text-sm text-[#5e647f]">Memuat riwayat belanja…</div>
        <div data-member-empty class="hidden ni-panel p-8 text-center text-sm text-[#5e647f]">Belum ada transaksi yang terhubung ke akun member ini.</div>
        <div data-member-content class="hidden ni-panel overflow-x-auto"><table class="ni-table"><thead><tr><th>Tanggal</th><th>Toko</th><th>Nomor transaksi</th><th>Total</th><th>Poin digunakan / diterima</th><th>Status</th></tr></thead><tbody data-member-rows></tbody></table></div>
    </div>
@endsection
