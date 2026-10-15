@extends('layouts.super-admin')

@section('title', $store->name.' - Super Admin Nivora')

@section('content')
    <div class="space-y-6">
        <x-page-header eyebrow="SUPER ADMIN / BISNIS" :title="$store->name" description="Detail record bisnis dan relasi yang tersimpan.">
            <a href="{{ route('super-admin.stores') }}" data-go-back class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-[#e7ebff] bg-white px-4 text-sm font-medium text-[#5e647f]"><span aria-hidden="true">←</span> Kembali ke daftar</a>
            <a href="{{ route('super-admin.stores.edit', ['store' => $store->id]) }}" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-[#687EFF] px-4 text-sm font-semibold text-white">Edit</a>
        </x-page-header>
        <section class="relative isolate h-52 overflow-hidden rounded-[26px] border border-[#e6eaff] bg-gradient-to-br from-[#e8edff] to-[#dff5ff] shadow-[0_18px_50px_rgba(49,67,130,0.1)] sm:h-64">
            <img src="{{ $store->logo ? (\Illuminate\Support\Str::startsWith($store->logo, '/') ? $store->logo : request()->getBaseUrl().'/storage/'.ltrim($store->logo, '/')) : asset('images/default-store.svg') }}" alt="Foto toko {{ $store->name }}" class="absolute inset-0 -z-10 h-full w-full object-cover">
            <div class="absolute inset-0 -z-0 bg-gradient-to-t from-[#101633]/65 via-[#101633]/10 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-4 p-5 sm:p-7"><div><span class="inline-flex rounded-full border border-white/25 bg-white/15 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.14em] text-white backdrop-blur">Foto toko</span><h2 class="mt-2 text-xl font-semibold text-white drop-shadow sm:text-2xl">{{ $store->name }}</h2></div><span class="rounded-full px-3 py-1.5 text-xs font-semibold backdrop-blur {{ $store->status === 'active' ? 'bg-emerald-400/20 text-white' : 'bg-white/20 text-white' }}">{{ $store->status === 'active' ? 'Aktif' : 'Nonaktif' }}</span></div>
        </section>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="ni-stat-card"><p class="text-sm text-[#5e647f]">Jenis usaha</p><p class="mt-2 font-semibold">{{ $store->business_type }}</p></article>
            <article class="ni-stat-card"><p class="text-sm text-[#5e647f]">Status</p><p class="mt-2 font-semibold">{{ $store->status }}</p></article>
            <article class="ni-stat-card"><p class="text-sm text-[#5e647f]">Lokasi toko</p><p class="mt-2 font-semibold">{{ $store->locationName() }}</p></article>
            <article class="ni-stat-card"><p class="text-sm text-[#5e647f]">Produk</p><p class="mt-2 font-semibold">{{ $store->products_count }}</p></article>
        </div>
        <section class="ni-panel p-5 sm:p-7"><h2 class="text-lg font-semibold">Kontak bisnis</h2><dl class="mt-4 grid gap-4 sm:grid-cols-2"><div><dt class="text-sm text-[#5e647f]">Telepon</dt><dd class="mt-1 font-medium">{{ $store->phone ?: 'Belum dicantumkan' }}</dd></div><div><dt class="text-sm text-[#5e647f]">Alamat</dt><dd class="mt-1 font-medium">{{ $store->address ?: 'Belum dicantumkan' }}</dd></div></dl></section>
        <section class="ni-panel p-5 sm:p-7"><h2 class="text-lg font-semibold">Akun owner terhubung</h2>
            @php($owners = $store->users->filter(fn ($user) => $user->roles->contains('name', 'owner')))
            @if ($owners->isEmpty())<p class="mt-3 text-sm text-[#5e647f]">Belum ada akun owner yang terhubung.</p>@else<div class="mt-4 space-y-2">@foreach ($owners as $owner)<div class="rounded-xl border border-[#e7ebff] bg-[#f7f9ff] p-3"><p class="font-medium">{{ $owner->name }}</p><p class="mt-1 text-sm text-[#5e647f]">{{ $owner->email }}</p></div>@endforeach</div>@endif
        </section>
        <p class="text-sm text-[#7b8299]">Informasi membership tidak ditampilkan karena belum ada data membership yang tersimpan.</p>
    </div>
@endsection
