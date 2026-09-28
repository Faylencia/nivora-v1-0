@extends('layouts.owner')

@section('title', 'Edit Outlet - Nivora')

@section('content')
    <div class="mx-auto max-w-3xl">
        <x-page-header eyebrow="OWNER / OUTLET" title="Edit outlet" description="Perbarui informasi lokasi operasional toko." />

        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <form class="space-y-5">
                <x-input label="Nama outlet" name="name" value="Bandung Center" required />
                <x-input label="Alamat" name="address" value="Jl. Citarum No. 24, Bandung" required />
                <x-input label="Nomor telepon" name="phone" type="tel" value="0812-1111-2222" required />

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Status</label>
                    <select class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/10">
                        <option selected>Active</option>
                        <option>Inactive</option>
                    </select>
                </div>

                <div class="flex flex-col gap-3 pt-2 sm:flex-row">
                    <x-button type="primary" size="lg">Update outlet</x-button>
                    <x-button href="{{ route('owner.outlets.index') }}" type="ghost" size="lg">Cancel</x-button>
                </div>
            </form>
        </div>
    </div>
@endsection
