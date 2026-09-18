@php
    $menus = [
        ['label' => 'Dashboard', 'icon' => 'home', 'route' => 'cashier.dashboard', 'href' => route('cashier.dashboard')],
        ['label' => 'POS / Kasir', 'icon' => 'calculator', 'route' => 'cashier.pos', 'href' => route('cashier.pos')],
        ['label' => 'Produk', 'icon' => 'cube', 'route' => 'cashier.products', 'href' => route('cashier.products')],
        ['label' => 'Customer', 'icon' => 'users', 'route' => 'cashier.customers', 'href' => route('cashier.customers')],
        ['label' => 'Riwayat Transaksi', 'icon' => 'receipt', 'route' => 'cashier.sales', 'href' => route('cashier.sales')],
    ];
@endphp

<div data-sidebar-overlay class="fixed inset-0 z-40 hidden bg-slate-900/40 lg:hidden"></div>

<aside data-sidebar-drawer class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-white/10 bg-[#1f2544] text-white shadow-[18px_0_45px_rgba(31,37,68,0.12)] transition duration-200 lg:translate-x-0">
    <div class="flex h-16 items-center justify-between gap-3 border-b border-white/10 px-6">
        <a href="{{ route('cashier.dashboard') }}" class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-[#98E4FF] to-[#687EFF] text-sm font-bold text-white shadow-sm">N</div>
            <div>
                <div class="text-lg font-semibold tracking-[-0.04em] text-white">NIVORA</div>
                <div class="text-[10px] uppercase tracking-[0.2em] text-[#dfe7ff]">Cashier Panel</div>
            </div>
        </a>
        <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 text-white lg:hidden" data-sidebar-toggle aria-label="Tutup sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18 18 6M6 6l12 12" /></svg>
        </button>
    </div>

    <div class="border-b border-white/10 px-5 py-5">
        <div class="rounded-2xl bg-white/10 p-3">
            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-[#98E4FF]">Outlet Aktif</p>
            <p class="mt-2 text-sm font-semibold text-white">Bandung Center</p>
            <p class="mt-1 text-xs text-[#dfe7ff]">Kasir operasional</p>
        </div>
    </div>

    <nav class="flex-1 space-y-1 px-4 py-5" aria-label="Navigasi kasir">
        @foreach ($menus as $menu)
            @php($active = request()->routeIs($menu['route']))
            <a href="{{ $menu['href'] }}" class="group flex min-h-[44px] items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ $active ? 'bg-[#dfe7ff] text-[#1f2544] shadow-sm' : 'text-[#dfe7ff] hover:bg-white/10 hover:text-white' }}">
                @switch($menu['icon'])
                    @case('home')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1v-9.5Z"/></svg>
                        @break
                    @case('calculator')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><rect width="16" height="18" x="4" y="3" rx="2" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M8 7h8M8 12h.01M12 12h.01M16 12h.01M8 16h.01M12 16h.01M16 16h.01"/></svg>
                        @break
                    @case('cube')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Zm0 0v9m8-4.5-8 4.5-8-4.5"/></svg>
                        @break
                    @case('users')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 19v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1m11-9a4 4 0 1 0-8 0 4 4 0 0 0 8 0Zm8 9v-1a4 4 0 0 0-3-3.87M20 5a4 4 0 0 1 0 8"/></svg>
                        @break
                    @case('receipt')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 4h14v17l-3-2-4 2-4-2-3 2V4Zm3 5h8m-8 4h8"/></svg>
                        @break
                @endswitch
                <span>{{ $menu['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="border-t border-white/10 p-4">
        <div class="rounded-2xl bg-white/5 p-3 ring-1 ring-white/10">
            <div class="mb-3 flex items-center gap-2">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-[#98E4FF] to-[#687EFF] text-xs font-semibold text-white">AR</div>
                <div>
                    <p class="text-sm font-semibold text-white">Ayu R.</p>
                    <p class="text-[10px] text-[#dfe7ff]">Kasir</p>
                </div>
            </div>
            <a href="{{ route('home') }}" class="inline-flex min-h-[44px] w-full items-center justify-center rounded-xl bg-[#dfe7ff] px-3 py-2 text-sm font-medium text-[#1f2544] transition hover:bg-white">Logout</a>
        </div>
    </div>
</aside>
