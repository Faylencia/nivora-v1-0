@props(['dashboard' => false, 'role' => 'owner'])

@php
    $publicNav = [
        ['label' => 'Explore', 'href' => route('explore'), 'active' => request()->routeIs('explore')],
        ['label' => 'Kategori', 'href' => route('home') . '#categories'],
        ['label' => 'Tentang', 'href' => route('home') . '#about'],
    ];

    $dashboardHeader = match ($role) {
        'super-admin' => [
            'workspace' => 'SUPER ADMIN WORKSPACE',
            'context' => 'Platform overview',
            'roleLabel' => 'Super Admin',
            'menuLabel' => 'Super Admin',
        ],
        'cashier' => [
            'workspace' => 'CASHIER WORKSPACE',
            'context' => 'Toko aktif',
            'roleLabel' => 'Kasir aktif',
            'menuLabel' => 'Kasir',
        ],
        'member' => [
            'workspace' => 'MEMBER WORKSPACE',
            'context' => 'Member Nivora',
            'roleLabel' => 'Member',
            'menuLabel' => 'Member',
        ],
        default => [
            'workspace' => 'OWNER WORKSPACE',
            'context' => 'Bisnis aktif',
            'roleLabel' => 'Admin Toko',
            'menuLabel' => 'Owner',
        ],
    };
@endphp

@if ($dashboard)
    <header class="sticky top-0 z-40 border-b border-[#e7ebff]/80 bg-white/85 backdrop-blur-xl">
        <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <button type="button" data-sidebar-toggle class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[#e7ebff] bg-white text-[#1f2544] shadow-sm lg:hidden" aria-label="Buka menu {{ $dashboardHeader['menuLabel'] }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                @if ($role === 'cashier')
                    <button type="button" data-cashier-sidebar-toggle aria-expanded="true" class="hidden h-10 w-10 items-center justify-center rounded-xl border border-[#e7ebff] bg-white text-[#536dfe] shadow-sm transition hover:border-[#b8c4ff] hover:bg-[#f7f8ff] lg:inline-flex" aria-label="Tutup sidebar kasir" title="Tutup sidebar"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M9 4v16m3-11h5m-5 4h5"/></svg></button>
                @endif
                <div>
                    <p class="text-sm font-semibold text-[#1f2544]">NIVORA</p>
                    <p class="text-[10px] uppercase tracking-[0.14em] text-[#5e647f]">{{ $dashboardHeader['workspace'] }}</p>
                </div>
            </div>

            <div class="hidden items-center gap-3 text-sm text-[#5e647f] sm:flex">
                @if ($role === 'super-admin')
                    <label class="flex items-center gap-2 rounded-xl border border-[#e7ebff] bg-white px-3 py-2 text-xs"><span>Konteks bisnis</span><select data-super-admin-business class="max-w-48 bg-transparent font-semibold text-[#1f2544] outline-none"><option value="">Memuat…</option></select></label>
                @endif
                <span @if (in_array($role, ['owner', 'cashier'], true)) data-session-business @endif>{{ $dashboardHeader['context'] }}</span>
                <span class="h-2 w-2 rounded-full bg-[#687EFF]"></span>
                <span>{{ $dashboardHeader['roleLabel'] }}</span>
            </div>
        </div>
    </header>
@else
    <header class="public-site-header sticky top-0 z-50 border-b border-[#e7ebff]/80 bg-white/85 backdrop-blur-xl">
        <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="public-header-inner flex h-20 items-center justify-between">
                <a href="{{ route('home') }}" class="public-brand" aria-label="Nivora home">
                    <span class="public-brand-mark" aria-hidden="true">N</span>
                    <span class="public-brand-name">NIVORA</span>
                </a>

                <div class="public-site-desktop-nav hidden items-center gap-8 md:flex">
                    @foreach ($publicNav as $item)
                        <a href="{{ $item['href'] }}" aria-current="{{ ($item['active'] ?? false) ? 'page' : 'false' }}" class="public-site-nav-link text-sm font-medium text-[#5e647f] transition hover:text-[#1f2544]">{{ $item['label'] }}</a>
                    @endforeach
                </div>

                <div class="public-header-actions hidden items-center gap-3 md:flex">
                    <details class="public-theme-menu relative">
                        <summary class="public-theme-trigger" aria-label="Pengaturan tema warna" title="Pengaturan tema warna"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3.5"/><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/></svg><span>Tema</span></summary>
                        <div class="public-theme-popover" role="group" aria-label="Pilih tema warna">
                            <p>PREFERENSI TEMA</p>
                            <button type="button" data-theme-option="light" class="public-theme-choice"><span class="public-theme-choice-icon is-light"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/></svg></span>Terang<span class="public-theme-check">✓</span></button>
                            <button type="button" data-theme-option="dark" class="public-theme-choice"><span class="public-theme-choice-icon is-dark"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 15.5A8.5 8.5 0 0 1 8.5 3.5 8.5 8.5 0 1 0 20.5 15.5Z"/></svg></span>Gelap<span class="public-theme-check">✓</span></button>
                            <button type="button" data-theme-option="system" class="public-theme-choice"><span class="public-theme-choice-icon is-system"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="13" rx="2"/><path d="M8 21h8m-4-4v4"/></svg></span>Ikuti perangkat<span class="public-theme-check">✓</span></button>
                        </div>
                    </details>
                    <div data-auth-guest class="flex items-center gap-3">
                        <a href="{{ route('login') }}" class="public-login-link">Masuk</a>
                        <a href="{{ route('register') }}" class="public-register-link">Daftar <span aria-hidden="true">→</span></a>
                    </div>
                    <div data-authenticated-nav class="hidden flex items-center gap-3">
                        <a data-session-dashboard href="{{ route('member.dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-4 py-2.5 text-sm font-semibold text-[#1f2544]">Dashboard</a>
                        <button type="button" data-auth-logout class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#687EFF] px-4 py-2.5 text-sm font-semibold text-white">Keluar</button>
                    </div>
                </div>

                <button type="button" data-sidebar-toggle data-member-sidebar-trigger class="public-mobile-menu-button hidden h-11 w-11 items-center justify-center rounded-xl border border-[#e7ebff] bg-white text-[#1f2544] md:hidden" aria-label="Buka menu member">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                <button type="button" data-mobile-nav-toggle aria-controls="public-mobile-menu" aria-expanded="false" class="public-mobile-menu-button inline-flex h-11 w-11 items-center justify-center rounded-xl border border-[#e7ebff] bg-white text-[#1f2544] md:hidden" aria-label="Buka menu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </nav>

        <div id="public-mobile-menu" data-mobile-menu class="public-mobile-menu hidden border-t border-[#e7ebff] bg-[#f8faff] md:hidden">
            <div class="space-y-1 px-4 py-4">
                @foreach ($publicNav as $item)
                    <a href="{{ $item['href'] }}" aria-current="{{ ($item['active'] ?? false) ? 'page' : 'false' }}" class="public-site-mobile-link block rounded-xl px-3 py-3 text-sm font-medium text-[#5e647f] transition hover:bg-white hover:text-[#1f2544]">{{ $item['label'] }}</a>
                @endforeach
                <div class="public-mobile-theme mt-3 border-t border-[#e7ebff] pt-4">
                    <p class="mb-2 px-1 text-[10px] font-bold uppercase tracking-[.16em] text-[#7b8299]">Tampilan warna</p>
                    <div class="grid grid-cols-3 gap-2" role="group" aria-label="Pilih tema warna">
                        <button type="button" data-theme-option="light" class="public-mobile-theme-choice"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/></svg>Terang</button>
                        <button type="button" data-theme-option="dark" class="public-mobile-theme-choice"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 15.5A8.5 8.5 0 0 1 8.5 3.5 8.5 8.5 0 1 0 20.5 15.5Z"/></svg>Gelap</button>
                        <button type="button" data-theme-option="system" class="public-mobile-theme-choice"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="13" rx="2"/><path d="M8 21h8m-4-4v4"/></svg>Sistem</button>
                    </div>
                </div>
                <div data-auth-guest class="mt-3 pt-2">
                    <a href="{{ route('login') }}" class="public-login-link">Masuk</a>
                    <a href="{{ route('register') }}" class="public-register-link">Daftar akun <span aria-hidden="true">→</span></a>
                </div>
                <div data-authenticated-nav class="mt-3 hidden grid grid-cols-2 gap-3 pt-2">
                    <a data-session-dashboard href="{{ route('member.dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#e7ebff] bg-white text-sm font-semibold text-[#1f2544]">Dashboard</a>
                    <button type="button" data-auth-logout class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#687EFF] text-sm font-semibold text-white">Keluar</button>
                </div>
            </div>
        </div>
    </header>
@endif
