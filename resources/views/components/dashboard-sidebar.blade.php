@props(['role' => 'owner'])

@php
    $sidebar = match ($role) {
        'super-admin' => [
            'label' => 'SUPER ADMIN',
            'home' => route('super-admin.dashboard'),
            'workspaceLabel' => 'Workspace',
            'workspaceTitle' => 'Kelola jaringan toko',
            'workspaceDescription' => 'Akses seluruh platform',
            'initials' => 'SA',
            'name' => 'Super Admin',
            'profileLabel' => 'Platform Nivora',
            'navigationLabel' => 'Navigasi Super Admin',
            'menus' => [
                ['label' => 'Dashboard', 'icon' => 'home', 'href' => route('super-admin.dashboard'), 'active' => 'super-admin.dashboard'],
                ['label' => 'Pengajuan Toko', 'icon' => 'briefcase', 'href' => route('super-admin.business-applications'), 'active' => 'super-admin.business-applications'],
                ['label' => 'Akun Member & Owner', 'icon' => 'users', 'href' => route('super-admin.accounts'), 'active' => 'super-admin.accounts'],
                ['label' => 'Toko', 'icon' => 'store', 'href' => route('super-admin.stores'), 'active' => 'super-admin.stores*'],
                ['label' => 'Kategori', 'icon' => 'tag', 'href' => route('super-admin.categories.index'), 'active' => 'super-admin.categories.*'],
                ['label' => 'Aktivitas', 'icon' => 'activity', 'href' => route('super-admin.activity'), 'active' => 'super-admin.activity'],
                ['label' => 'Pengaturan', 'icon' => 'settings', 'href' => route('super-admin.settings'), 'active' => 'super-admin.settings'],
            ],
        ],
        'cashier' => [
            'label' => 'CASHIER',
            'home' => route('cashier.dashboard'),
            'workspaceLabel' => 'Toko Aktif',
            'workspaceTitle' => 'Toko akun aktif',
            'workspaceDescription' => 'Kasir operasional',
            'initials' => 'K',
            'name' => 'Akun aktif',
            'profileLabel' => 'Kasir',
            'navigationLabel' => 'Navigasi kasir',
            'menus' => [
                ['label' => 'Dashboard', 'icon' => 'home', 'href' => route('cashier.dashboard'), 'active' => 'cashier.dashboard'],
                ['label' => 'POS / Kasir', 'icon' => 'calculator', 'href' => route('cashier.pos'), 'active' => 'cashier.pos'],
                ['label' => 'Produk', 'icon' => 'cube', 'href' => route('cashier.products'), 'active' => 'cashier.products'],
                ['label' => 'Promo', 'icon' => 'tag', 'href' => route('cashier.promos'), 'active' => 'cashier.promos'],
                ['label' => 'Riwayat Transaksi', 'icon' => 'receipt', 'href' => route('cashier.sales'), 'active' => 'cashier.sales'],
                ['label' => 'Pengaturan', 'icon' => 'settings', 'href' => route('cashier.settings'), 'active' => 'cashier.settings'],
            ],
        ],
        'member' => [
            'label' => 'MEMBER',
            'home' => route('member.dashboard'),
            'workspaceLabel' => 'Akun member',
            'workspaceTitle' => 'Belanja lokal',
            'workspaceDescription' => 'Toko, promo, dan poin Anda',
            'initials' => 'M',
            'name' => 'Akun aktif',
            'profileLabel' => 'Member Nivora',
            'navigationLabel' => 'Navigasi member',
            'menus' => [
                ['label' => 'Ringkasan', 'icon' => 'home', 'href' => route('member.dashboard'), 'active' => 'member.dashboard'],
                ['label' => 'Home', 'icon' => 'home', 'href' => route('home'), 'active' => 'home'],
                ['label' => 'Jelajahi Toko', 'icon' => 'store', 'href' => route('explore'), 'active' => 'explore'],
                ['label' => 'Riwayat Belanja', 'icon' => 'receipt', 'href' => route('member.sales'), 'active' => 'member.sales'],
                ['label' => 'Kupon Saya', 'icon' => 'tag', 'href' => route('member.coupons'), 'active' => 'member.coupons'],
                ['label' => 'Notifikasi', 'icon' => 'activity', 'href' => route('member.notifications'), 'active' => 'member.notifications'],
                ['label' => 'Dompet Poin', 'icon' => 'credit-card', 'href' => route('member.wallet'), 'active' => 'member.wallet'],
                ['label' => 'Daftar Toko', 'icon' => 'briefcase', 'href' => route('member.business-application'), 'active' => 'member.business-application'],
            ],
        ],
        default => [
            'label' => 'OWNER',
            'home' => route('owner.dashboard'),
            'workspaceLabel' => 'Workspace',
            'workspaceTitle' => 'Operasional toko',
            'workspaceDescription' => 'Kelola toko dan aktivitas bisnis',
            'initials' => 'O',
            'name' => 'Akun aktif',
            'profileLabel' => 'Admin Toko',
            'navigationLabel' => 'Navigasi Owner',
            'menus' => [
                ['label' => 'Dashboard', 'icon' => 'home', 'href' => route('owner.dashboard'), 'active' => 'owner.dashboard'],
                ['label' => 'Toko Saya', 'icon' => 'briefcase', 'href' => route('owner.business.index'), 'active' => 'owner.business.*'],
                ['label' => 'Produk', 'icon' => 'cube', 'href' => route('owner.products.index'), 'active' => 'owner.products.*'],
                ['label' => 'Customer', 'icon' => 'users', 'href' => route('owner.customers.index'), 'active' => 'owner.customers.*'],
                ['label' => 'Promo', 'icon' => 'tag', 'href' => route('owner.promotions.index'), 'active' => 'owner.promotions.*'],
                ['label' => 'Transaksi', 'icon' => 'chart-bar', 'href' => route('owner.sales.index'), 'active' => 'owner.sales.*'],
                ['label' => 'Laporan', 'icon' => 'credit-card', 'href' => route('owner.payments.index'), 'active' => 'owner.payments.*'],
                ['label' => 'Kasir & Poin', 'icon' => 'users', 'href' => route('owner.cashiers.index'), 'active' => 'owner.cashiers.*'],
                ['label' => 'AI Insights', 'icon' => 'sparkles', 'href' => route('owner.ai.index'), 'active' => 'owner.ai.*'],
                ['label' => 'Kasir', 'icon' => 'users', 'href' => route('cashier.dashboard'), 'active' => 'cashier.*'],
                ['label' => 'Pengaturan', 'icon' => 'settings', 'href' => route('owner.settings'), 'active' => 'owner.settings'],
            ],
        ],
    };
@endphp

<div data-sidebar-overlay class="fixed inset-0 z-40 hidden bg-slate-900/40 lg:hidden"></div>

<aside data-sidebar-drawer class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-white/10 bg-[#1f2544] text-white shadow-[18px_0_45px_rgba(31,37,68,0.12)] transition duration-200 lg:translate-x-0" aria-label="{{ $sidebar['navigationLabel'] }}">
    <div class="flex h-16 shrink-0 items-center justify-between gap-3 border-b border-white/10 px-6">
        <a href="{{ $sidebar['home'] }}" class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-linear-to-br from-[#98E4FF] to-[#687EFF] text-sm font-bold text-white shadow-sm">N</div>
            <div>
                <div class="text-lg font-semibold tracking-[-0.04em] text-white">NIVORA</div>
                <div class="text-[10px] uppercase tracking-[0.2em] text-[#dfe7ff]">{{ $sidebar['label'] }}</div>
            </div>
        </a>
        <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 text-white lg:hidden" data-sidebar-toggle aria-label="Tutup sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18 18 6M6 6l12 12" /></svg>
        </button>
        @if ($role === 'cashier')
            <button type="button" data-cashier-sidebar-toggle aria-expanded="true" class="hidden h-9 w-9 items-center justify-center rounded-xl border border-white/10 text-white transition hover:bg-white/10 lg:inline-flex" aria-label="Tutup sidebar kasir" title="Tutup sidebar"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M9 4v16m3-11h5m-5 4h5"/></svg></button>
        @endif
    </div>

    <div class="shrink-0 border-b border-white/10 px-5 py-5">
        <div class="rounded-2xl bg-white/10 p-3">
            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-[#98E4FF]">{{ $sidebar['workspaceLabel'] }}</p>
            <p class="mt-2 text-sm font-semibold text-white" @if (in_array($role, ['owner', 'cashier'], true)) data-session-business @endif>{{ $sidebar['workspaceTitle'] }}</p>
            <p class="mt-1 text-xs text-[#dfe7ff]">{{ $sidebar['workspaceDescription'] }}</p>
        </div>
    </div>

    <nav class="min-h-0 flex-1 px-4 py-5 space-y-1 overflow-y-auto {{ $role === 'owner' ? 'scrollbar-hidden' : '' }}" aria-label="{{ $sidebar['navigationLabel'] }}">
        @foreach ($sidebar['menus'] as $menu)
            @php($active = request()->routeIs($menu['active']))
            <a href="{{ $menu['href'] }}" @if ($active) aria-current="page" @endif class="group flex min-h-11 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ $active ? 'bg-[#dfe7ff] text-[#1f2544] shadow-sm' : 'text-[#dfe7ff] hover:bg-white/10 hover:text-white' }}">
                @switch($menu['icon'])
                    @case('home')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1v-9.5Z" /></svg>
                        @break
                    @case('briefcase')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 8.5A2.5 2.5 0 0 1 6.5 6h11A2.5 2.5 0 0 1 20 8.5v7A2.5 2.5 0 0 1 17.5 18h-11A2.5 2.5 0 0 1 4 15.5v-7Zm0 0h16M9 6V4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5V6" /></svg>
                        @break
                    @case('store')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 10h14l-1.2-6.2A1 1 0 0 0 16.8 3H7.2a1 1 0 0 0-1 .8L5 10Zm0 0v8.5A1.5 1.5 0 0 0 6.5 20h11a1.5 1.5 0 0 0 1.5-1.5V10" /></svg>
                        @break
                    @case('tag')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 12V6.5A1.5 1.5 0 0 0 18.5 5H13l-8 8 7 7 8-8v-2Zm-7.5-1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z" /></svg>
                        @break
                    @case('cube')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Zm0 0v9m8-4.5-8 4.5-8-4.5" /></svg>
                        @break
                    @case('archive')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7.5A1.5 1.5 0 0 1 5.5 6h13A1.5 1.5 0 0 1 20 7.5v9A1.5 1.5 0 0 1 18.5 18h-13A1.5 1.5 0 0 1 4 16.5v-9ZM9 9h6m-7 6h8" /></svg>
                        @break
                    @case('users')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 19v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1m11-9a4 4 0 1 0-8 0 4 4 0 0 0 8 0Zm8 9v-1a4 4 0 0 0-3-3.87M20 5a4 4 0 0 1 0 8" /></svg>
                        @break
                    @case('chart-bar')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 18V8m7 10V4m7 14v-7" /></svg>
                        @break
                    @case('credit-card')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8.5A2.5 2.5 0 0 1 5.5 6h13A2.5 2.5 0 0 1 21 8.5v7A2.5 2.5 0 0 1 18.5 18h-13A2.5 2.5 0 0 1 3 15.5v-7Zm0 3h18" /></svg>
                        @break
                    @case('calculator')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><rect width="16" height="18" x="4" y="3" rx="2" stroke-width="1.8" /><path stroke-linecap="round" stroke-width="1.8" d="M8 7h8M8 12h.01M12 12h.01M16 12h.01M8 16h.01M12 16h.01M16 16h.01" /></svg>
                        @break
                    @case('receipt')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 4h14v17l-3-2-4 2-4-2-3 2V4Zm3 5h8m-8 4h8" /></svg>
                        @break
                    @case('activity')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 12h3l2-7 4 14 2-7h5" /></svg>
                        @break
                    @case('settings')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm7.4-3.5a7.6 7.6 0 0 0-.1-1l2-1.5-2-3.4-2.4 1a7.4 7.4 0 0 0-1.7-1L14.8 3h-4l-.4 3.1a7.4 7.4 0 0 0-1.7 1l-2.4-1-2 3.4 2 1.5a7.6 7.6 0 0 0-.1 1 7.6 7.6 0 0 0 .1 1l-2 1.5 2 3.4 2.4-1a7.4 7.4 0 0 0 1.7 1l.4 3.1h4l.4-3.1a7.4 7.4 0 0 0 1.7-1l2.4 1 2-3.4-2-1.5c.1-.3.1-.7.1-1Z" /></svg>
                        @break
                    @case('sparkles')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m12 3 1.2 5.8L19 10l-5.8 1.2L12 17l-1.2-5.8L5 10l5.8-1.2L12 3Zm7 11 .7 3.3L23 18l-3.3.7L19 22l-.7-3.3L15 18l3.3-.7L19 14ZM5 2l.6 2.4L8 5l-2.4.6L5 8l-.6-2.4L2 5l2.4-.6L5 2Z" /></svg>
                        @break
                @endswitch
                <span>{{ $menu['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="shrink-0 border-t border-white/10 p-4">
        <div class="rounded-2xl bg-white/5 p-3 ring-1 ring-white/10">
            <div class="mb-3 flex items-center gap-2">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-linear-to-br from-[#98E4FF] to-[#687EFF] text-xs font-semibold text-white">{{ $sidebar['initials'] }}</div>
                <div>
                    <p class="text-sm font-semibold text-white" data-session-user>{{ $sidebar['name'] }}</p>
                    <p class="text-[10px] text-[#dfe7ff]">{{ $sidebar['profileLabel'] }}</p>
                </div>
            </div>
            <a href="{{ route('login') }}" data-auth-logout class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-[#dfe7ff] px-3 py-2 text-sm font-medium text-[#1f2544] transition hover:bg-white">Keluar</a>
        </div>
    </div>
</aside>
