@extends('layouts.super-admin')

@section('title', 'Edit Toko - Super Admin Nivora')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <x-page-header eyebrow="SUPER ADMIN / TOKO" title="Edit Toko" description="Perbarui informasi toko, status, dan admin penanggung jawab." />
        <form class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-sm sm:p-6">
            <div class="grid gap-5 sm:grid-cols-2">
                <x-input label="Nama Toko" name="name" value="Kopi Tuku" required />
                <x-input label="Jenis Usaha" name="business_type" value="Minuman & Kuliner" required />
                <x-input label="Nomor Telepon" name="phone" value="+62 812-3456-7890" required />
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-[#1f2544]">Status</label>
                    <select class="min-h-[46px] w-full rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] px-4 text-sm">
                        <option>Aktif</option>
                        <option>Nonaktif</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <x-input label="Alamat" name="address" value="Jl. Citarum No. 24, Bandung" required />
                </div>
            </div>
            <div class="mt-6 rounded-2xl bg-[#f7f9ff] p-4">
                <p class="text-sm font-semibold text-[#1f2544]">Admin Toko Saat Ini</p>
                <p class="mt-2 text-sm text-[#5e647f]">Ahmad Rifai · ahmad@example.com · Aktif</p>
                <button type="button" class="mt-3 text-sm font-semibold text-[#687EFF]">Ganti Admin</button>
            </div>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <x-button type="primary" size="lg">Simpan Perubahan</x-button>
                <x-button href="{{ route('super-admin.stores.show', ['store' => $store]) }}" type="ghost" size="lg">Batal</x-button>
            </div>
        </form>
    </div>
@endsection
