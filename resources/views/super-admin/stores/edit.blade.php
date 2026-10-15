@extends('layouts.super-admin')

@section('title', 'Edit Toko - Super Admin Nivora')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <x-page-header eyebrow="SUPER ADMIN / TOKO" title="Edit Toko" description="Perbarui profil dan status bisnis yang tersimpan." />
        <div data-admin-business-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>
        <form data-super-admin-business-edit="{{ $store->id }}" data-default-photo="{{ asset('images/default-store.svg') }}" data-current-photo="{{ $store->logo ? (\Illuminate\Support\Str::startsWith($store->logo, '/') ? $store->logo : request()->getBaseUrl().'/storage/'.ltrim($store->logo, '/')) : asset('images/default-store.svg') }}" class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-sm sm:p-6" enctype="multipart/form-data">
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2"><label class="block text-sm font-medium text-[#1f2544]">Foto toko</label><div class="mt-2 flex items-center gap-4"><img data-business-photo-preview src="{{ $store->logo ? (\Illuminate\Support\Str::startsWith($store->logo, '/') ? $store->logo : request()->getBaseUrl().'/storage/'.ltrim($store->logo, '/')) : asset('images/default-store.svg') }}" alt="Foto toko {{ $store->name }}" class="h-20 w-20 rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] object-cover"><div class="min-w-0 flex-1"><input name="photo" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-[#59617d] file:mr-3 file:rounded-lg file:border-0 file:bg-[#eef1ff] file:px-3 file:py-2 file:text-xs file:font-semibold file:text-[#536dfe]"><p class="mt-1 text-xs text-[#7b8299]">JPG, PNG, atau WebP · maks. 3 MB. Ikon toko default tampil jika foto belum diatur.</p>@if ($store->logo && $store->logo !== '/images/default-store.svg')<label class="mt-2 inline-flex items-center gap-2 text-xs font-medium text-[#737b96]"><input name="remove_photo" type="checkbox" value="1" class="rounded border-[#cbd3e8] text-[#536dfe] focus:ring-[#536dfe]/20">Gunakan ikon toko default</label>@endif</div></div></div>
                <x-input label="Nama Toko" name="name" :value="$store->name" required />
                <x-input label="Jenis Usaha" name="business_type" :value="$store->business_type" required />
                <x-input label="Nomor Telepon" name="phone" :value="$store->phone" />
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-[#1f2544]">Status</label>
                    <select name="status" required class="min-h-[46px] w-full rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] px-4 text-sm">
                        <option value="active" @selected($store->status === 'active')>Aktif</option>
                        <option value="inactive" @selected($store->status === 'inactive')>Nonaktif</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <x-input label="Alamat" name="address" :value="$store->address" />
                </div>
            </div>
            <div class="mt-6 rounded-2xl bg-[#f7f9ff] p-4">
                <p class="text-sm font-semibold text-[#1f2544]">Owner yang terhubung</p>
                @forelse ($owners as $owner)
                    <p class="mt-2 text-sm text-[#5e647f]">{{ $owner->name }} · {{ $owner->email }} · {{ $owner->status }}</p>
                @empty
                    <p class="mt-2 text-sm text-[#5e647f]">Belum ada akun owner aktif yang terhubung.</p>
                @endforelse
            </div>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#687EFF] px-5 text-sm font-semibold text-white">Simpan Perubahan</button>
                <x-button href="{{ route('super-admin.stores.show', ['store' => $store]) }}" type="ghost" size="lg">Batal</x-button>
            </div>
        </form>
    </div>
@endsection
