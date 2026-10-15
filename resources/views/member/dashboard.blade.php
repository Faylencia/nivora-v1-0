@extends('layouts.dashboard-shell', ['role' => 'member'])

@section('title', 'Member Nivora')

@section('content')
    <div class="space-y-6" data-member-dashboard>
        <x-page-header eyebrow="MEMBER NIVORA" title="Ringkasan akun" description="Pantau poin, transaksi, kupon, dan kabar terbaru dari toko favorit Anda." />
        <div data-member-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>
        <section class="ni-panel relative overflow-hidden bg-[linear-gradient(115deg,#1f2544_0%,#414f9f_58%,#687eff_100%)] p-6 text-white sm:p-8">
            <div class="relative z-10 flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div><p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#b6fffa]">Nivora member</p><h2 class="mt-2 text-2xl font-semibold sm:text-3xl">Belanja lebih hemat, kumpulkan lebih banyak poin.</h2><p class="mt-2 max-w-2xl text-sm leading-6 text-white/75">Jelajahi toko lokal, temukan promo, dan gunakan poin Anda saat bertransaksi.</p></div>
                <a href="{{ route('explore') }}" class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-xl bg-white px-4 text-sm font-semibold text-[#414f9f]">Jelajahi toko <span class="ml-2" aria-hidden="true">→</span></a>
            </div>
            <div class="pointer-events-none absolute -right-8 -top-24 h-64 w-64 rounded-full border-[36px] border-white/10"></div>
        </section>
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="ni-stat-card"><p class="text-sm text-[#5e647f]">Saldo poin</p><p class="mt-2 text-3xl font-semibold"><span data-member-points>—</span> <span class="text-base font-medium text-[#5e647f]">poin</span></p><p class="mt-2 text-xs text-[#7b8299]">1 poin bernilai Rp1 saat digunakan di toko.</p><a href="{{ route('member.wallet') }}" class="mt-3 inline-flex text-sm font-semibold text-[#687EFF]">Lihat saldo →</a></article>
            <article class="ni-stat-card"><p class="text-sm text-[#5e647f]">Transaksi selesai</p><p data-member-purchase-count class="mt-2 text-3xl font-semibold">—</p><p class="mt-2 text-xs text-[#7b8299]">Total pembelian dengan akun member Anda.</p><a href="{{ route('member.sales') }}" class="mt-3 inline-flex text-sm font-semibold text-[#687EFF]">Riwayat transaksi →</a></article>
            <article class="ni-stat-card"><p class="text-sm text-[#5e647f]">Kupon aktif</p><p data-member-coupon-count class="mt-2 text-3xl font-semibold">—</p><p class="mt-2 text-xs text-[#7b8299]">Kupon yang siap digunakan saat belanja.</p><a href="{{ route('member.coupons') }}" class="mt-3 inline-flex text-sm font-semibold text-[#687EFF]">Lihat kupon →</a></article>
            <article class="ni-stat-card"><p class="text-sm text-[#5e647f]">Notifikasi belum dibaca</p><p data-member-unread-count class="mt-2 text-3xl font-semibold">—</p><p class="mt-2 text-xs text-[#7b8299]">Kabar baru dari toko dan Nivora.</p><a href="{{ route('member.notifications') }}" class="mt-3 inline-flex text-sm font-semibold text-[#687EFF]">Buka notifikasi →</a></article>
        </section>
        <section class="ni-panel overflow-hidden">
            <div class="border-b border-[#e7ebff] p-5"><p class="ni-section-label">MULAI DI SINI</p><h2 class="mt-1 text-lg font-semibold">Temukan lebih banyak di Nivora</h2><p class="mt-1 text-sm text-[#5e647f]">Akses cepat ke fitur yang paling sering Anda gunakan.</p></div>
            <div class="grid gap-px bg-[#e7ebff] sm:grid-cols-3">
                <a href="{{ route('explore') }}" class="bg-white p-5 transition hover:bg-[#f7f9ff]"><span class="ni-badge">JELAJAHI</span><h3 class="mt-3 font-semibold">Toko dan produk</h3><p class="mt-1 text-sm leading-6 text-[#5e647f]">Cari bisnis lokal, cek stok toko, dan lihat detail produk.</p><span class="mt-3 inline-flex text-sm font-semibold text-[#687EFF]">Cari toko →</span></a>
                <a href="{{ route('member.wallet') }}" class="bg-white p-5 transition hover:bg-[#f7f9ff]"><span class="ni-badge">POIN</span><h3 class="mt-3 font-semibold">Dompet poin</h3><p class="mt-1 text-sm leading-6 text-[#5e647f]">Periksa saldo dan aktivitas poin dari transaksi Anda.</p><span class="mt-3 inline-flex text-sm font-semibold text-[#687EFF]">Buka dompet →</span></a>
                <a href="{{ route('member.notifications') }}" class="bg-white p-5 transition hover:bg-[#f7f9ff]"><span class="ni-badge">KABAR TERBARU</span><h3 class="mt-3 font-semibold">Notifikasi toko</h3><p class="mt-1 text-sm leading-6 text-[#5e647f]">Jangan lewatkan informasi promo dan pembaruan terbaru.</p><span class="mt-3 inline-flex text-sm font-semibold text-[#687EFF]">Lihat kabar →</span></a>
            </div>
        </section>
    </div>
@endsection
