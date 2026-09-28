@extends('layouts.super-admin')

@section('title', 'Admin Toko - Super Admin Nivora')

@section('content')
    <div class="space-y-6">
        <x-page-header eyebrow="SUPER ADMIN" title="Admin Toko" description="Kelola admin yang bertanggung jawab atas setiap toko.">
            <a href="{{ route('super-admin.store-admins.create') }}" class="inline-flex min-h-11.5 items-center justify-center rounded-2xl bg-[#687EFF] px-5 py-3 text-sm font-semibold text-white">+ Tambah Admin</a>
        </x-page-header>
        <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-[0_18px_40px_rgba(31,37,68,0.04)]">
            <div class="flex flex-col gap-3 sm:flex-row">
                <input type="search" placeholder="Cari nama atau email" class="min-h-[46px] w-full rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] px-4 text-sm outline-none focus:border-[#687EFF] sm:max-w-md" />
                <select class="min-h-[46px] rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] px-4 text-sm text-[#1f2544] outline-none">
                    <option>Semua toko</option>
                    <option>Kopi Tuku</option>
                    <option>Nusa Garden</option>
                </select>
            </div>
            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full text-left text-sm text-[#5e647f]">
                    <thead>
                        <tr class="border-b border-[#e7ebff]">
                            <th class="pb-3 pr-4 text-[#1f2544]">Nama</th>
                            <th class="pb-3 pr-4 text-[#1f2544]">Email</th>
                            <th class="pb-3 pr-4 text-[#1f2544]">Toko</th>
                            <th class="pb-3 pr-4 text-[#1f2544]">Status</th>
                            <th class="pb-3 text-[#1f2544]">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-[#eef3ff]">
                            <td class="py-4 pr-4 font-medium text-[#1f2544]">Ayu Sari</td>
                            <td class="py-4 pr-4">ayu@example.com</td>
                            <td class="py-4 pr-4">Kopi Tuku</td>
                            <td class="py-4 pr-4">
                                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs text-emerald-600">Aktif</span>
                            </td>
                            <td class="py-4">
                                <a href="{{ route('super-admin.store-admins.show', ['admin' => 1]) }}" class="mr-3 font-semibold text-[#687EFF]">Lihat</a>
                                <a href="{{ route('super-admin.store-admins.edit', ['admin' => 1]) }}" class="mr-3 font-semibold text-[#5e647f]">Edit</a>
                                <button type="button" data-open-modal="delete-admin" class="font-semibold text-rose-500">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <x-modal id="delete-admin" title="Hapus Admin Toko?">
            <p class="text-sm leading-6 text-[#5e647f]">Admin ini akan dihapus dari sistem setelah backend memvalidasi dependency toko.</p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" data-close-modal="delete-admin" class="rounded-2xl border border-[#e7ebff] px-4 py-2.5 text-sm font-semibold text-[#1f2544]">Batal</button>
                <button type="button" class="rounded-2xl bg-rose-500 px-4 py-2.5 text-sm font-semibold text-white">Hapus Admin</button>
            </div>
        </x-modal>
    </div>
@endsection
