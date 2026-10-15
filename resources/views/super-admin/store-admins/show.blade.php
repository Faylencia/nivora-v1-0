@extends('layouts.super-admin')

@section('title', $admin->name.' - Admin Bisnis')

@section('content')
    <div class="space-y-6">
        <x-page-header eyebrow="SUPER ADMIN / AKUN OWNER" :title="$admin->name" description="Informasi akun dan bisnis yang terhubung.">
            <a href="{{ route('super-admin.store-admins') }}" data-go-back class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-[#e7ebff] bg-white px-4 text-sm font-medium text-[#5e647f]"><span aria-hidden="true">←</span> Kembali ke daftar</a>
            <a href="{{ route('super-admin.store-admins.edit', ['admin' => $admin->id]) }}" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-[#687EFF] px-4 text-sm font-semibold text-white">Edit</a>
        </x-page-header>
        <div class="grid gap-5 md:grid-cols-2">
            <section class="ni-panel p-5"><h2 class="font-semibold">Data akun</h2><dl class="mt-4 space-y-4 text-sm"><div><dt class="text-[#5e647f]">Email</dt><dd class="mt-1 font-medium">{{ $admin->email }}</dd></div><div><dt class="text-[#5e647f]">Telepon</dt><dd class="mt-1 font-medium">{{ $admin->phone ?: 'Belum dicantumkan' }}</dd></div><div><dt class="text-[#5e647f]">Status</dt><dd class="mt-1 font-medium">{{ $admin->status ?: '—' }}</dd></div><div><dt class="text-[#5e647f]">Role</dt><dd class="mt-1 font-medium">{{ $admin->roles->pluck('name')->join(', ') ?: '—' }}</dd></div></dl></section>
            <section class="ni-panel p-5"><h2 class="font-semibold">Bisnis terhubung</h2>
                @if ($admin->businesses->isEmpty())<p class="mt-4 text-sm text-[#5e647f]">Belum ada bisnis yang terhubung.</p>@else<div class="mt-4 space-y-2">@foreach ($admin->businesses as $business)<a href="{{ route('super-admin.stores.show', ['store' => $business->id]) }}" class="block rounded-xl border border-[#e7ebff] bg-[#f7f9ff] p-3"><span class="font-medium">{{ $business->name }}</span><span class="mt-1 block text-sm text-[#5e647f]">{{ $business->business_type }} · {{ $business->status }}</span></a>@endforeach</div>@endif
            </section>
        </div>
    </div>
@endsection
