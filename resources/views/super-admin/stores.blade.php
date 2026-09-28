@extends('layouts.super-admin')

@section('title', 'Toko - Super Admin Nivora')

@section('content')
    <div class="space-y-6">
        <x-page-header eyebrow="SUPER ADMIN" title="Kelola Toko" description="Lihat, tambah, dan pantau toko yang terdaftar di Nivora.">
            <x-button href="{{ route('super-admin.stores.create') }}" type="primary">+ Tambah Toko</x-button>
        </x-page-header>
        <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-[0_18px_40px_rgba(31,37,68,0.04)]"><div class="flex flex-col gap-3 sm:flex-row"><input type="search" placeholder="Cari toko atau lokasi" class="min-h-[46px] w-full rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] px-4 text-sm outline-none focus:border-[#687EFF] sm:max-w-md" /><select class="min-h-[46px] rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] px-4 text-sm text-[#1f2544] outline-none"><option>Semua status</option><option>Aktif</option><option>Nonaktif</option></select></div><div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">@foreach (['Kopi Tuku|Bandung|Aktif|1','Nusa Garden|Yogyakarta|Aktif|2','Rasa Rakyat|Surabaya|Review|3'] as $store)<div class="rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] p-4"><div class="flex items-start justify-between gap-3"><div><h2 class="font-semibold text-[#1f2544]">{{ explode('|', $store)[0] }}</h2><p class="mt-1 text-sm text-[#5e647f]">{{ explode('|', $store)[1] }}</p></div><span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs text-emerald-600">{{ explode('|', $store)[2] }}</span></div><div class="mt-4 flex flex-wrap gap-3 text-sm"><a href="{{ route('super-admin.stores.show', ['store' => explode('|', $store)[3]]) }}" class="font-semibold text-[#687EFF]">Lihat</a><a href="{{ route('super-admin.stores.edit', ['store' => explode('|', $store)[3]]) }}" class="font-semibold text-[#5e647f]">Edit</a><button type="button" data-open-modal="delete-store" class="font-semibold text-rose-500">Hapus</button></div></div>@endforeach</div></div>

        <x-modal id="delete-store" title="Hapus Toko?">
            <p class="text-sm leading-6 text-[#5e647f]">Apakah Anda yakin ingin menghapus toko ini? Tindakan penghapusan harus diproses oleh backend sesuai dependency toko.</p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" data-close-modal="delete-store" class="rounded-2xl border border-[#e7ebff] px-4 py-2.5 text-sm font-semibold text-[#1f2544]">Batal</button>
                <button type="button" class="rounded-2xl bg-rose-500 px-4 py-2.5 text-sm font-semibold text-white">Hapus Toko</button>
            </div>
        </x-modal>
    </div>
@endsection
