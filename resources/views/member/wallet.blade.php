@extends('layouts.dashboard-shell', ['role' => 'member'])

@section('title', 'Poin Member - Nivora')

@section('content')
    <div class="space-y-6" data-member-wallet>
        <x-page-header eyebrow="MEMBER / POIN" title="Dompet poin" description="Poin masuk ketika kasir mengirimkan poin sebagai pengganti uang kembalian." />
        <div data-member-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>
        <article class="ni-stat-card"><p class="text-sm text-[#5e647f]">Saldo tersedia</p><p class="mt-2 text-4xl font-semibold"><span data-member-wallet-balance>—</span> poin</p><p class="mt-2 text-sm text-[#5e647f]">Nilai penukaran: 1 poin = Rp1. Minta kasir memeriksa saldo sebelum transaksi diselesaikan.</p></article>
        <section class="ni-panel overflow-x-auto"><h2 class="border-b border-[#e7ebff] p-5 font-semibold">Riwayat poin</h2><div data-member-loading class="p-8 text-sm text-[#5e647f]">Memuat riwayat…</div><div data-member-empty class="hidden p-8 text-sm text-[#5e647f]">Belum ada perubahan saldo poin.</div><table data-member-content class="hidden ni-table"><thead><tr><th>Waktu</th><th>Aktivitas</th><th>Poin</th><th>Saldo setelah</th></tr></thead><tbody data-member-rows></tbody></table></section>
    </div>
@endsection
