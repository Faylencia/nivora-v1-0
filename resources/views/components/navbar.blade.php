@props(['dashboard' => false, 'role' => 'owner'])

@php
    $publicNav = [
        ['label' => 'Home', 'href' => route('home')],
        ['label' => 'Explore', 'href' => route('explore')],
        ['label' => 'Kategori', 'href' => route('explore') . '#categories'],
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
            'context' => 'Bandung Center',
            'roleLabel' => 'Kasir aktif',
            'menuLabel' => 'Kasir',
        ],
        default => [
            'workspace' => 'OWNER WORKSPACE',
            'context' => 'Operasional toko',
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
                <div>
                    <p class="text-sm font-semibold text-[#1f2544]">NIVORA</p>
                    <p class="text-[10px] uppercase tracking-[0.14em] text-[#5e647f]">{{ $dashboardHeader['workspace'] }}</p>
                </div>
            </div>

            <div class="hidden items-center gap-3 text-sm text-[#5e647f] sm:flex">
                <span>{{ $dashboardHeader['context'] }}</span>
                <span class="h-2 w-2 rounded-full bg-[#687EFF]"></span>
                <span>{{ $dashboardHeader['roleLabel'] }}</span>
            </div>
        </div>
    </header>
@else
    <header class="sticky top-0 z-50 border-b border-[#e7ebff]/80 bg-white/85 backdrop-blur-xl">
        <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-4" aria-label="Nivora home">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-linear-to-br from-[#687EFF] to-[#98E4FF] text-base font-bold text-white shadow-sm">N</div>
                    <div class="flex items-center gap-2">
                        <div class="text-2xl font-semibold tracking-[-0.06em] text-[#1f2544]">NIVORA</div>
                        <div class="text-[10px] font-medium uppercase tracking-[0.18em] text-[#5e647f]">Smart business</div>
                    </div>
                </a>

                <div class="hidden items-center gap-8 md:flex">
                    @foreach ($publicNav as $item)
                        <a href="{{ $item['href'] }}" class="text-sm font-medium text-[#5e647f] transition hover:text-[#1f2544]">{{ $item['label'] }}</a>
                    @endforeach
                </div>

                <div class="hidden items-center gap-3 md:flex">
                    <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-4 py-2.5 text-sm font-medium text-[#1f2544] transition hover:border-[#cad7ff] hover:bg-[#f7f9ff]">Sign in</a>
                    <a href="{{ route('register') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#687EFF] px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-[#596eea]">Create account</a>
                </div>

                <button type="button" data-mobile-nav-toggle class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-[#e7ebff] bg-white text-[#1f2544] md:hidden" aria-label="Buka menu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </nav>

        <div data-mobile-menu class="hidden border-t border-[#e7ebff] bg-[#f8faff] md:hidden">
            <div class="space-y-1 px-4 py-4">
                @foreach ($publicNav as $item)
                    <a href="{{ $item['href'] }}" class="block rounded-xl px-3 py-3 text-sm font-medium text-[#5e647f] transition hover:bg-white hover:text-[#1f2544]">{{ $item['label'] }}</a>
                @endforeach
                <div class="mt-3 grid grid-cols-2 gap-3 pt-2">
                    <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#e7ebff] bg-white text-sm font-medium text-[#1f2544]">Sign in</a>
                    <a href="{{ route('register') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#687EFF] text-sm font-medium text-white">Create account</a>
                </div>
            </div>
        </div>
    </header>
@endif
