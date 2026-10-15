@extends('layouts.owner')

@section('title', 'Edit Outlet - Nivora')

@section('content')
    <div class="space-y-6" data-outlet-edit="{{ $outlet }}">
        <x-page-header eyebrow="OWNER / OUTLET" title="Informasi outlet" description="Perbarui detail outlet yang tersimpan pada bisnis Anda.">
            <a href="{{ route('owner.outlets.index') }}" data-go-back class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-[#e7ebff] bg-white px-4 text-sm font-medium text-[#5e647f]"><span aria-hidden="true">←</span> Kembali ke outlet</a>
        </x-page-header>
        <div data-entity-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>
        <div data-entity-loading class="ni-panel p-8 text-center text-sm text-[#5e647f]">Memuat data outlet…</div>
        <form data-outlet-edit-form class="ni-panel hidden max-w-3xl space-y-5 p-5 sm:p-7">
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="space-y-1.5 text-sm font-medium sm:col-span-2">Nama outlet
                    <input name="name" required maxlength="150" class="min-h-11 w-full rounded-xl border border-[#e7ebff] bg-[#f7f9ff] px-3 text-sm font-normal outline-none focus:border-[#687EFF]" />
                </label>
                <label class="space-y-1.5 text-sm font-medium sm:col-span-2">Alamat
                    <textarea name="address" rows="3" class="w-full rounded-xl border border-[#e7ebff] bg-[#f7f9ff] px-3 py-2.5 text-sm font-normal outline-none focus:border-[#687EFF]"></textarea>
                </label>
                <label class="space-y-1.5 text-sm font-medium">Nomor telepon
                    <input name="phone" type="tel" maxlength="30" class="min-h-11 w-full rounded-xl border border-[#e7ebff] bg-[#f7f9ff] px-3 text-sm font-normal outline-none focus:border-[#687EFF]" />
                </label>
                <label class="space-y-1.5 text-sm font-medium">Status
                    <select name="status" class="min-h-11 w-full rounded-xl border border-[#e7ebff] bg-[#f7f9ff] px-3 text-sm outline-none focus:border-[#687EFF]"><option value="active">Aktif</option><option value="inactive">Nonaktif</option></select>
                </label>
            </div>
            <div class="flex justify-end border-t border-[#e7ebff] pt-5"><button type="submit" class="min-h-11 rounded-xl bg-[#687EFF] px-5 text-sm font-semibold text-white">Simpan perubahan</button></div>
        </form>
    </div>
@endsection
