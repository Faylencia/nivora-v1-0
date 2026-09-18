@extends('layouts.dashboard')

@section('title', 'Business Profile - Nivora')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#687EFF]">Business</p>
                <h1 class="mt-2 text-3xl font-semibold text-[#1f2544]">Business profile</h1>
            </div>
            <x-button href="#" type="primary">Edit profile</x-button>
        </div>

        <div class="grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
            <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-[0_16px_38px_rgba(31,37,68,0.05)]">
                <div class="flex items-center justify-center rounded-[24px] bg-[#eef3ff] p-6">
                    <div class="flex h-24 w-24 items-center justify-center rounded-3xl bg-gradient-to-br from-[#687EFF] to-[#98E4FF] text-3xl font-bold text-white shadow-md">N</div>
                </div>
                <div class="mt-5 text-center">
                    <h2 class="text-2xl font-semibold text-[#1f2544]">Nusa Bumi</h2>
                    <p class="mt-2 text-sm text-[#5e647f]">UMKM Produk Lokal</p>
                </div>
            </div>

            <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-[0_16px_38px_rgba(31,37,68,0.05)]">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="rounded-2xl bg-[#f7f9ff] p-4">
                        <p class="text-sm text-[#5e647f]">Business type</p>
                        <p class="mt-2 text-base font-semibold text-[#1f2544]">Fashion & Craft</p>
                    </div>
                    <div class="rounded-2xl bg-[#f7f9ff] p-4">
                        <p class="text-sm text-[#5e647f]">Phone</p>
                        <p class="mt-2 text-base font-semibold text-[#1f2544]">+62 812-3456-7890</p>
                    </div>
                    <div class="rounded-2xl bg-[#f7f9ff] p-4 sm:col-span-2">
                        <p class="text-sm text-[#5e647f]">Address</p>
                        <p class="mt-2 text-base font-semibold text-[#1f2544]">Jl. Pahlawan No. 20, Bandung, Jawa Barat</p>
                    </div>
                    <div class="rounded-2xl bg-[#f7f9ff] p-4">
                        <p class="text-sm text-[#5e647f]">Status</p>
                        <p class="mt-2 inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-600">Active</p>
                    </div>
                    <div class="rounded-2xl bg-[#f7f9ff] p-4">
                        <p class="text-sm text-[#5e647f]">Founded</p>
                        <p class="mt-2 text-base font-semibold text-[#1f2544]">2021</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
