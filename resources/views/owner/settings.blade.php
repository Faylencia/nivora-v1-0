@extends('layouts.owner')

@section('title', 'Pengaturan Owner - Nivora')

@section('content')
    <div class="owner-settings space-y-6">
        <x-page-header eyebrow="OWNER / PREFERENSI" title="Pengaturan workspace" description="Atur tampilan dan akses cepat untuk mengelola operasional toko." />

        <section class="relative isolate overflow-hidden rounded-[26px] bg-gradient-to-br from-[#20284a] via-[#394a91] to-[#687eff] p-6 text-white shadow-[0_20px_48px_rgba(49,67,130,0.18)] sm:p-8">
            <div class="pointer-events-none absolute -right-10 -top-24 -z-10 h-64 w-64 rounded-full border-[38px] border-white/[0.08]"></div>
            <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <span class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.18em] text-[#c8fffb]">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 3v2m0 14v2M3 12h2m14 0h2M5.6 5.6 7 7m10 10 1.4 1.4m0-12.8L17 7M7 17l-1.4 1.4"/><circle cx="12" cy="12" r="4"/></svg>
                        Preferensi workspace
                    </span>
                    <h2 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">Atur Nivora sesuai cara kerja Anda.</h2>
                    <p class="mt-2 max-w-xl text-sm leading-6 text-white/75">Tema disimpan di browser ini dan diterapkan ke dashboard owner, kasir, serta halaman Nivora lainnya.</p>
                </div>
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-white/15 bg-white/10 text-[#c8fffb]">
                    <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M12 3v2m0 14v2M3 12h2m14 0h2M5.6 5.6 7 7m10 10 1.4 1.4m0-12.8L17 7M7 17l-1.4 1.4"/><circle cx="12" cy="12" r="4"/></svg>
                </span>
            </div>
        </section>

        <section class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_320px]">
            <div class="ni-panel overflow-hidden">
                <div class="flex items-start gap-3 border-b border-[#e7ebff] p-5 sm:p-6">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#eef1ff] text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20.5 15.5A8.5 8.5 0 0 1 8.5 3.5 8.5 8.5 0 1 0 20.5 15.5Z"/></svg></span>
                    <div><h2 class="font-semibold text-[#20284a]">Tampilan dashboard</h2><p class="mt-1 text-sm text-[#7b8299]">Pilih tema yang paling nyaman untuk Anda.</p></div>
                </div>
                <div class="p-5 sm:p-6">
                    <div class="grid gap-3 sm:grid-cols-3" role="group" aria-label="Pilih tema dashboard">
                        <button type="button" data-theme-option="light" aria-pressed="false" class="theme-option group rounded-2xl border border-[#e7ebff] bg-white p-4 text-left transition hover:-translate-y-0.5 hover:border-[#b8c4ff] hover:shadow-md">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-500"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/></svg></span>
                            <span class="mt-3 block text-sm font-semibold text-[#303858]">Terang</span><span class="mt-1 block text-xs leading-5 text-[#7b8299]">Tema cerah untuk penggunaan siang hari.</span>
                        </button>
                        <button type="button" data-theme-option="dark" aria-pressed="false" class="theme-option group rounded-2xl border border-[#e7ebff] bg-white p-4 text-left transition hover:-translate-y-0.5 hover:border-[#b8c4ff] hover:shadow-md">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#eef1ff] text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20.5 15.5A8.5 8.5 0 0 1 8.5 3.5 8.5 8.5 0 1 0 20.5 15.5Z"/></svg></span>
                            <span class="mt-3 block text-sm font-semibold text-[#303858]">Gelap</span><span class="mt-1 block text-xs leading-5 text-[#7b8299]">Kurangi silau saat bekerja di ruangan minim cahaya.</span>
                        </button>
                        <button type="button" data-theme-option="system" aria-pressed="false" class="theme-option group rounded-2xl border border-[#e7ebff] bg-white p-4 text-left transition hover:-translate-y-0.5 hover:border-[#b8c4ff] hover:shadow-md">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#e9f8ff] text-[#2797bf]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="4" width="18" height="13" rx="2"/><path d="M8 21h8m-4-4v4"/></svg></span>
                            <span class="mt-3 block text-sm font-semibold text-[#303858]">Ikuti sistem</span><span class="mt-1 block text-xs leading-5 text-[#7b8299]">Sesuaikan otomatis dengan pengaturan perangkat.</span>
                        </button>
                    </div>
                    <p data-theme-description class="mt-4 flex items-center gap-2 text-xs text-[#7b8299]" role="status" aria-live="polite"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Memuat preferensi tema…</p>
                </div>
            </div>

            <aside class="ni-panel p-5 sm:p-6">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#e9f8ff] text-[#2797bf]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z"/></svg></span>
                <h2 class="mt-4 font-semibold text-[#20284a]">Area kerja</h2><p class="mt-1 text-sm leading-6 text-[#7b8299]">Akses cepat ke halaman operasional toko.</p>
                <div class="mt-4 space-y-2">
                    <a href="{{ route('owner.dashboard') }}" class="owner-settings-link">Dashboard owner <span>→</span></a>
                    <a href="{{ route('owner.business.index') }}" class="owner-settings-link">Profil bisnis <span>→</span></a>
                    <a href="{{ route('owner.products.index') }}" class="owner-settings-link">Produk <span>→</span></a>
                    <a href="{{ route('owner.sales.index') }}" class="owner-settings-link">Transaksi <span>→</span></a>
                </div>
            </aside>
        </section>
    </div>
@endsection
