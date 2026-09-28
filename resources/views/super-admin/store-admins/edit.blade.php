@extends('layouts.super-admin')

@section('title', 'Edit Admin Toko - Nivora')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <x-page-header eyebrow="SUPER ADMIN / ADMIN TOKO" title="Edit Admin Toko" description="Perbarui informasi admin dan status aksesnya." />

        <form class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-sm sm:p-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <x-input label="Nama" name="name" value="Ahmad Rifai" required />
                <x-input label="Email" name="email" type="email" value="ahmad@example.com" required />
                <x-input label="Nomor Telepon" name="phone" value="0812-3456-7890" required />
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-[#1f2544]">Toko</label>
                    <select class="min-h-11.5 w-full rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] px-4 text-sm">
                        <option>Kopi Tuku</option>
                        <option>Nusa Garden</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-[#1f2544]">Status</label>
                    <select class="min-h-11.5 w-full rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] px-4 text-sm">
                        <option>Aktif</option>
                        <option>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="mt-5 border-t border-[#e7ebff] pt-5">
                <h2 class="font-semibold text-[#1f2544]">Ganti Password (opsional)</h2>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <x-input label="Password Baru" name="password" type="password" placeholder="Tidak diubah jika kosong" />
                    <x-input label="Konfirmasi Password" name="password_confirmation" type="password" />
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <x-button type="primary" size="lg">Simpan Perubahan</x-button>
                <x-button href="{{ route('super-admin.store-admins.show', ['admin' => $admin]) }}" type="ghost" size="lg">Batal</x-button>
            </div>
        </form>
    </div>
@endsection
