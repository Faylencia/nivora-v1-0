@extends('layouts.super-admin')

@section('title', 'Detail Toko - Super Admin Nivora')

@section('content')
    @php($storeName = $store == 2 ? 'Nusa Garden' : ($store == 3 ? 'Rasa Rakyat' : 'Kopi Tuku'))
    <div class="space-y-6">
        <x-page-header eyebrow="SUPER ADMIN / TOKO" :title="$storeName" description="Detail informasi toko, admin, dan membership.">
            <a href="{{ route('super-admin.stores.edit', ['store' => $store]) }}" class="inline-flex min-h-11 items-center justify-center rounded-2xl border border-[#e7ebff] bg-white px-4 text-sm font-semibold text-[#1f2544]">Edit Toko</a>
            <button type="button" class="inline-flex min-h-11 items-center justify-center rounded-2xl bg-rose-500 px-4 text-sm font-semibold text-white">Hapus Toko</button>
        </x-page-header>
        <div class="grid gap-5 lg:grid-cols-3">
            <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-sm lg:col-span-2">
                <h2 class="text-xl font-semibold text-[#1f2544]">Informasi Toko</h2>
                <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-[#5e647f]">Jenis Usaha</dt>
                        <dd class="mt-1 font-semibold text-[#1f2544]">Minuman & Kuliner</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-[#5e647f]">Telepon</dt>
                        <dd class="mt-1 font-semibold text-[#1f2544]">+62 812-3456-7890</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm text-[#5e647f]">Alamat</dt>
                        <dd class="mt-1 font-semibold text-[#1f2544]">Jl. Citarum No. 24, Bandung</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-[#5e647f]">Status</dt>
                        <dd class="mt-1 inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs text-emerald-600">Aktif</dd>
                    </div>
                </dl>
            </div>
            <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-sm">
                <h2 class="text-xl font-semibold text-[#1f2544]">Admin Toko</h2>
                <p class="mt-5 font-semibold text-[#1f2544]">Ahmad Rifai</p>
                <p class="mt-1 text-sm text-[#5e647f]">ahmad@example.com</p>
                <span class="mt-3 inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs text-emerald-600">Aktif</span>
                <button type="button" class="mt-5 block text-sm font-semibold text-[#687EFF]">Ganti Admin</button>
            </div>
            <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-sm">
                <h2 class="text-xl font-semibold text-[#1f2544]">Membership</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-3">
                    <div>
                        <p class="text-sm text-[#5e647f]">Paket</p>
                        <p class="mt-1 font-semibold text-[#1f2544]">Professional</p>
                    </div>
                    <div>
                        <p class="text-sm text-[#5e647f]">Status</p>
                        <p class="mt-1 font-semibold text-emerald-600">Aktif</p>
                    </div>
                    <div>
                        <p class="text-sm text-[#5e647f]">Berakhir</p>
                        <p class="mt-1 font-semibold text-[#1f2544]">01 Oktober 2026</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
