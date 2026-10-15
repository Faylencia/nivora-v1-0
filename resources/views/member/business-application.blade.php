@extends('layouts.dashboard-shell', ['role' => 'member'])

@section('title', 'Daftar Toko - Nivora')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6" data-business-application>
        <x-page-header eyebrow="MEMBER / PENGAJUAN" title="Ajukan pendaftaran toko" description="Pengajuan diperiksa Super Admin. Akun Anda baru mendapat akses Owner setelah persetujuan." />
        <div data-member-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>
        <section class="ni-panel p-5 sm:p-6"><h2 class="font-semibold">Status pengajuan</h2><div data-application-status class="mt-3 space-y-2 text-sm text-[#5e647f]">Memuat status…</div></section>
        <form data-business-application-form class="ni-panel grid gap-4 p-5 sm:grid-cols-2 sm:p-6">
            <h2 class="font-semibold sm:col-span-2">Informasi bisnis</h2>
            <label class="space-y-1 text-sm font-medium">Nama toko<input name="business_name" required maxlength="150" class="min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 font-normal" /></label>
            <label class="space-y-1 text-sm font-medium">Jenis usaha<input name="business_type" required maxlength="100" class="min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 font-normal" /></label>
            <label class="space-y-1 text-sm font-medium">Telepon toko<input name="phone" type="tel" class="min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 font-normal" /></label>
            <label class="space-y-1 text-sm font-medium">Nama lokasi toko<input name="outlet_name" maxlength="150" class="min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 font-normal" /></label>
            <label class="space-y-1 text-sm font-medium sm:col-span-2">Alamat bisnis<textarea name="address" rows="3" class="w-full rounded-xl border border-[#e7ebff] bg-white px-3 py-2 font-normal"></textarea></label>
            <label class="space-y-1 text-sm font-medium sm:col-span-2">Alamat tambahan jika berbeda<textarea name="outlet_address" rows="2" class="w-full rounded-xl border border-[#e7ebff] bg-white px-3 py-2 font-normal"></textarea></label>
            <button type="submit" class="min-h-11 rounded-xl bg-[#687EFF] px-4 text-sm font-semibold text-white sm:col-span-2">Kirim untuk ditinjau</button>
        </form>
    </div>
@endsection
