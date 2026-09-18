@props(['dashboard' => false])

@php
    $publicNav = [
        ['label' => 'Home', 'href' => route('home')],
        ['label' => 'Explore', 'href' => route('explore')],
        ['label' => 'Kategori', 'href' => route('explore') . '#categories'],
    ];

    $dashboardNav = [
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'Business', 'href' => route('business.index')],
        ['label' => 'Products', 'href' => route('products.index')],
    ];
@endphp

@if ($dashboard)
    <header class="sticky top-0 z-40 border-b border-[#e7ebff]/80 bg-white/85 backdrop-blur-xl">
        <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <button type="button" data-sidebar-toggle class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[#e7ebff] bg-white text-[#1f2544] shadow-sm lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="flex items-center gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-[#687EFF] to-[#98E4FF] text-sm font-bold text-white shadow-sm">N</div>
                    <div>
                        <div class="text-sm font-semibold text-[#1f2544]">NIVORA</div>
                        <div class="text-[10px] uppercase tracking-[0.14em] text-[#5e647f]">Business</div>
                    </div>
                </div>
            </div>

            <div class="hidden items-center gap-6 md:flex">
                @foreach ($dashboardNav as $item)
                    <a href="{{ $item['href'] }}" class="text-sm font-medium text-[#5e647f] transition hover:text-[#1f2544]">{{ $item['label'] }}</a>
                @endforeach
            </div>

            <div class="flex items-center gap-3">
                <button type="button" class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[#e7ebff] bg-white text-[#1f2544] shadow-sm transition hover:border-[#cfdcff] hover:bg-[#f7f9ff]" aria-label="Notifikasi">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0m6 0H9" />
                    </svg>
                    <span class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-[#687EFF] px-1 text-[9px] font-semibold text-white">3</span>
                </button>

                <div class="flex items-center gap-3 rounded-xl border border-[#e7ebff] bg-white px-2 py-1.5 shadow-sm">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#dfe7ff] text-xs font-semibold text-[#1f2544]">AR</div>
                    <div class="hidden sm:block">
                        <p class="text-sm font-semibold text-[#1f2544]">Ayu R.</p>
                        <p class="text-[10px] text-[#5e647f]">Pemilik</p>
                    </div>
                </div>
            </div>
        </div>
    </header>
@else
    <header class="sticky top-0 z-50 border-b border-[#e7ebff]/80 bg-white/85 backdrop-blur-xl">
        <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-4" aria-label="Nivora home">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-[#687EFF] to-[#98E4FF] text-base font-bold text-white shadow-sm">N</div>
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
                    <a href="{{ route('login') }}" class="inline-flex min-h-[44px] items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-4 py-2.5 text-sm font-medium text-[#1f2544] transition hover:border-[#cad7ff] hover:bg-[#f7f9ff]">Sign in</a>
                    <a href="{{ route('register') }}" class="inline-flex min-h-[44px] items-center justify-center rounded-xl bg-[#687EFF] px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-[#596eea]">Create account</a>
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
                    <a href="{{ route('login') }}" class="inline-flex min-h-[44px] items-center justify-center rounded-xl border border-[#e7ebff] bg-white text-sm font-medium text-[#1f2544]">Sign in</a>
                    <a href="{{ route('register') }}" class="inline-flex min-h-[44px] items-center justify-center rounded-xl bg-[#687EFF] text-sm font-medium text-white">Create account</a>
                </div>
            </div>
        </div>
    </header>
@endif
