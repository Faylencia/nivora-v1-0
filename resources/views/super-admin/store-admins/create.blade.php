@extends('layouts.super-admin')

@section('title', 'Tambah Admin Toko - Nivora')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <x-page-header eyebrow="SUPER ADMIN / ADMIN TOKO" title="Tambah Admin Toko" description="Tambahkan admin yang bertanggung jawab atas operasional toko." />

        <form class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-sm sm:p-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <x-input label="Nama" name="name" placeholder="Nama lengkap" required />
                <x-input label="Email" name="email" type="email" placeholder="nama@email.com" required />
                <x-input label="Nomor Telepon" name="phone" type="tel" placeholder="08xx" required />
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-[#1f2544]">Toko</label>
                    <select name="store_id" class="min-h-11.5 w-full rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] px-4 text-sm">
                        <option>Kopi Tuku</option>
                        <option>Nusa Garden</option>
                    </select>
                </div>
                <x-input label="Password" name="password" type="password" placeholder="Password baru" required />
                <x-input label="Konfirmasi Password" name="password_confirmation" type="password" placeholder="Ulangi password" required />
            </div>

            <div class="mt-5 rounded-2xl bg-[#eef3ff] p-4 text-sm text-[#5e647f]">
                Role otomatis: <span class="font-semibold text-[#1f2544]">ADMIN TOKO</span>
            </div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <x-button type="primary" size="lg">Simpan Admin</x-button>
                <x-button href="{{ route('super-admin.store-admins') }}" type="ghost" size="lg">Batal</x-button>
            </div>
        </form>
    </div>
@endsection
