@extends('layouts.dashboard')

@section('title', 'Create Outlet - Nivora')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#687EFF]">Outlet</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Add outlet</h1>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <form class="space-y-5">
                <x-input label="Nama outlet" name="name" placeholder="Masukkan nama outlet" required />
                <x-input label="Alamat" name="address" placeholder="Masukkan alamat lengkap" required />
                <x-input label="Nomor telepon" name="phone" type="tel" placeholder="0812-3456-7890" required />

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Status</label>
                    <select class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/10">
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                </div>

                <div class="flex flex-col gap-3 pt-2 sm:flex-row">
                    <x-button type="primary" size="lg">Save outlet</x-button>
                    <x-button href="{{ route('outlets.index') }}" type="ghost" size="lg">Cancel</x-button>
                </div>
            </form>
        </div>
    </div>
@endsection
