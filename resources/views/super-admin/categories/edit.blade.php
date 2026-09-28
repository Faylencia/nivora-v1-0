@extends('layouts.super-admin')

@section('title', 'Edit Kategori - Super Admin Nivora')

@section('content')
    <div class="mx-auto max-w-3xl">
        <x-page-header eyebrow="SUPER ADMIN / KATEGORI" title="Edit kategori" description="Perbarui informasi kategori platform." />

        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <form class="space-y-5">
                <x-input label="Name" name="name" value="Minuman" required />
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Description</label>
                    <textarea rows="4" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-[#687EFF] focus:outline-none focus:ring-4 focus:ring-[#687EFF]/10">Minuman dingin dan hangat untuk kebutuhan pelanggan sehari-hari.</textarea>
                </div>

                <div class="flex flex-col gap-3 pt-2 sm:flex-row">
                    <x-button type="primary" size="lg">Update category</x-button>
                    <x-button href="{{ route('super-admin.categories.index') }}" type="ghost" size="lg">Batal</x-button>
                </div>
            </form>
        </div>
    </div>
@endsection
