@extends('layouts.super-admin')

@section('title', 'Detail Admin Toko - Nivora')

@section('content')
    <div class="space-y-6">
        <x-page-header eyebrow="SUPER ADMIN / ADMIN TOKO" title="Ahmad Rifai" description="Detail admin dan toko yang dikelola.">
            <a href="{{ route('super-admin.store-admins.edit', ['admin' => $admin]) }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-[#e7ebff] bg-white px-4 text-sm font-semibold text-[#1f2544]">Edit Admin</a>
            <button type="button" class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-rose-500 px-4 text-sm font-semibold text-white">Hapus Admin</button>
        </x-page-header>
        <div class="grid gap-5 md:grid-cols-2">
            <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-sm">
                <h2 class="text-xl font-semibold text-[#1f2544]">Informasi Admin</h2>
                <dl class="mt-5 space-y-4 text-sm">
                    <div>
                        <dt class="text-[#5e647f]">Email</dt>
                        <dd class="mt-1 font-semibold text-[#1f2544]">ahmad@example.com</dd>
                    </div>
                    <div>
                        <dt class="text-[#5e647f]">Nomor Telepon</dt>
                        <dd class="mt-1 font-semibold text-[#1f2544]">0812-3456-7890</dd>
                    </div>
                    <div>
                        <dt class="text-[#5e647f]">Status</dt>
                        <dd class="mt-1 inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs text-emerald-600">Aktif</dd>
                    </div>
                </dl>
            </div>
            <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-sm">
                <h2 class="text-xl font-semibold text-[#1f2544]">Toko Dikelola</h2>
                <p class="mt-5 font-semibold text-[#1f2544]">Kopi Tuku</p>
                <p class="mt-1 text-sm text-[#5e647f]">Professional · Aktif</p>
                <a href="{{ route('super-admin.stores.show', ['store' => 1]) }}" class="mt-5 inline-flex text-sm font-semibold text-[#687EFF]">Lihat toko</a>
            </div>
        </div>
    </div>
@endsection
