@php
    $menus = [
        ['label' => 'Dashboard', 'icon' => 'home', 'route' => 'dashboard', 'href' => route('dashboard')],
        ['label' => 'Business', 'icon' => 'briefcase', 'route' => 'business.index', 'href' => route('business.index')],
        ['label' => 'Outlet', 'icon' => 'store', 'route' => 'outlets.index', 'href' => route('outlets.index')],
        ['label' => 'Category', 'icon' => 'tag', 'route' => 'categories.index', 'href' => route('categories.index')],
        ['label' => 'Products', 'icon' => 'cube', 'route' => 'products.index', 'href' => route('products.index')],
        ['label' => 'Inventory', 'icon' => 'archive', 'route' => 'inventory.index', 'href' => route('inventory.index')],
        ['label' => 'Customers', 'icon' => 'users', 'route' => 'customers.index', 'href' => route('customers.index')],
        ['label' => 'Sales', 'icon' => 'chart-bar', 'route' => 'sales.index', 'href' => route('sales.index')],
        ['label' => 'Payments', 'icon' => 'credit-card', 'route' => 'payments.index', 'href' => route('payments.index')],
        ['label' => 'AI', 'icon' => 'sparkles', 'route' => 'ai.index', 'href' => route('ai.index')],
    ];
@endphp

<div data-sidebar-overlay class="fixed inset-0 z-40 hidden bg-slate-900/40 lg:hidden"></div>

<aside data-sidebar-drawer class="fixed inset-y-0 left-0 z-50 w-72 -translate-x-full flex-col border-r border-white/10 bg-[#1f2544] text-white shadow-[18px_0_45px_rgba(31,37,68,0.12)] transition duration-200 lg:translate-x-0 lg:flex">
    <div class="flex h-16 items-center justify-between gap-3 border-b border-white/10 px-6">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-[#98E4FF] to-[#687EFF] text-sm font-bold text-white shadow-sm">N</div>
            <div>
                <div class="text-lg font-semibold tracking-[-0.04em] text-white">NIVORA</div>
                <div class="text-[10px] uppercase tracking-[0.2em] text-[#dfe7ff]">Admin Panel</div>
            </div>
        </div>
        <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 text-white lg:hidden" data-sidebar-toggle aria-label="Tutup sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18 18 6M6 6l12 12" /></svg>
        </button>
    </div>

    <nav class="flex-1 space-y-1 px-4 py-5">
        @foreach ($menus as $menu)
            @php
                $active = request()->routeIs($menu['route']) || request()->routeIs($menu['route'] . '.*');
                if ($menu['route'] === 'dashboard') {
                    $active = request()->routeIs('dashboard');
                }
            @endphp
            <a href="{{ $menu['href'] }}" class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ $active ? 'bg-[#dfe7ff] text-[#1f2544] shadow-sm ring-1 ring-white/10' : 'text-[#dfe7ff] hover:bg-white/5 hover:text-white' }}">
                @switch($menu['icon'])
                    @case('home')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1v-9.5Z"/></svg>
                        @break
                    @case('briefcase')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 8.5A2.5 2.5 0 0 1 6.5 6h11A2.5 2.5 0 0 1 20 8.5v7A2.5 2.5 0 0 1 17.5 18h-11A2.5 2.5 0 0 1 4 15.5v-7Zm0 0h16M9 6V4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5V6"/></svg>
                        @break
                    @case('store')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 10h14l-1.2-6.2A1 1 0 0 0 16.8 3H7.2a1 1 0 0 0-1 .8L5 10Zm0 0v8.5A1.5 1.5 0 0 0 6.5 20h11a1.5 1.5 0 0 0 1.5-1.5V10"/></svg>
                        @break
                    @case('tag')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 12V6.5A1.5 1.5 0 0 0 18.5 5H13l-8 8 7 7 8-8v-2Zm-7.5-1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/></svg>
                        @break
                    @case('cube')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Zm0 0v9m8-4.5-8 4.5-8-4.5"/></svg>
                        @break
                    @case('archive')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7.5A1.5 1.5 0 0 1 5.5 6h13A1.5 1.5 0 0 1 20 7.5v9A1.5 1.5 0 0 1 18.5 18h-13A1.5 1.5 0 0 1 4 16.5v-9ZM9 9h6m-7 6h8"/></svg>
                        @break
                    @case('users')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 19v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1m11-9a4 4 0 1 0-8 0 4 4 0 0 0 8 0Zm8 9v-1a4 4 0 0 0-3-3.87M20 5a4 4 0 0 1 0 8"/></svg>
                        @break
                    @case('chart-bar')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 18V8m7 10V4m7 14v-7" /></svg>
                        @break
                    @case('credit-card')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8.5A2.5 2.5 0 0 1 5.5 6h13A2.5 2.5 0 0 1 21 8.5v7A2.5 2.5 0 0 1 18.5 18h-13A2.5 2.5 0 0 1 3 15.5v-7Zm0 3h18"/></svg>
                        @break
                    @case('sparkles')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m12 2 1.7 4.8L18.5 8l-4.8 1.2L12 14l-1.7-4.8L5.5 8l4.8-1.2L12 2Zm7 12 1 2.8 2.8 1-2.8 1-1 2.8-1-2.8-2.8-1 2.8-1 1-2.8ZM5 14l1 2.8L8.8 18l-2.8 1L5 21.8 4 19l-2.8-1L4 17l1-2.8Z"/></svg>
                        @break
                    @default
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M12 5v14"/></svg>
                @endswitch
                <span>{{ $menu['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="border-t border-white/10 p-4">
        <div class="rounded-2xl bg-white/5 p-3 ring-1 ring-white/10">
            <div class="mb-2 flex items-center gap-2">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-[#98E4FF] to-[#687EFF] text-xs font-semibold text-white">AR</div>
                <div>
                    <p class="text-sm font-semibold text-white">Ayu R.</p>
                    <p class="text-[10px] text-[#dfe7ff]">Pemilik Toko</p>
                </div>
            </div>
            <button type="button" class="w-full rounded-xl bg-[#dfe7ff] px-3 py-2 text-sm font-medium text-[#1f2544] transition hover:bg-[#d1dcff]">Logout</button>
        </div>
    </div>
</aside>
