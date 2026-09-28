@extends('layouts.super-admin')

@section('title', 'Super Admin Dashboard - Nivora')

@section('content')
    @php
        $stats = [
            ['label' => 'Total Toko', 'value' => '248', 'detail' => '+18 bulan ini', 'tone' => 'bg-[#eef3ff] text-[#687EFF]'],
            ['label' => 'Toko Aktif', 'value' => '231', 'detail' => '93% dari total toko', 'tone' => 'bg-emerald-50 text-emerald-600'],
            ['label' => 'Toko Nonaktif', 'value' => '17', 'detail' => 'Perlu ditinjau', 'tone' => 'bg-amber-50 text-amber-600'],
            ['label' => 'Total Admin', 'value' => '312', 'detail' => '+24 bulan ini', 'tone' => 'bg-[#EAF6FF] text-[#687EFF]'],
        ];

        $stores = [
            ['name' => 'Kopi Tuku', 'location' => 'Bandung', 'admin' => 'Ayu Sari', 'status' => 'Aktif', 'updated' => '10 menit lalu'],
            ['name' => 'Nusa Garden', 'location' => 'Yogyakarta', 'admin' => 'Raka Pratama', 'status' => 'Aktif', 'updated' => '1 jam lalu'],
            ['name' => 'Rasa Rakyat', 'location' => 'Surabaya', 'admin' => 'Siti Aulia', 'status' => 'Review', 'updated' => 'Kemarin'],
        ];

        $activities = [
            'Admin baru ditambahkan ke Kopi Tuku',
            'Toko Nusa Garden memperbarui profil bisnis',
            'Toko Rasa Rakyat menunggu verifikasi',
        ];
    @endphp

    <div class="space-y-6">
        <x-page-header eyebrow="SUPER ADMIN" title="Kelola jaringan toko Nivora" description="Pantau pertumbuhan toko dan bantu setiap admin menjalankan bisnisnya.">
            <a href="{{ route('super-admin.stores') }}" class="inline-flex min-h-11.5 items-center justify-center rounded-2xl bg-[#687EFF] px-5 py-3 text-sm font-semibold text-white shadow-[0_10px_22px_rgba(104,126,255,0.22)]">+ Tambah Toko</a>
        </x-page-header>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($stats as $stat)
                <div class="rounded-[26px] border border-[#e7ebff] bg-white/90 p-5 shadow-[0_18px_40px_rgba(31,37,68,0.04)]">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-sm text-[#5e647f]">{{ $stat['label'] }}</p>
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $stat['tone'] }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 19h16M6 16V8m6 8V4m6 12v-5" /></svg>
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-semibold tracking-tighter text-[#1f2544]">{{ $stat['value'] }}</p>
                    <p class="mt-2 text-xs text-[#687EFF]">{{ $stat['detail'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.35fr_0.65fr]">
            <section class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-[0_18px_40px_rgba(31,37,68,0.04)]">
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-[#1f2544]">Toko terbaru</h2>
                        <p class="mt-1 text-sm text-[#5e647f]">Daftar toko yang baru aktif di platform.</p>
                    </div>
                    <a href="{{ route('super-admin.stores') }}" class="text-sm font-semibold text-[#687EFF]">Lihat semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-[#5e647f]">
                        <thead>
                            <tr class="border-b border-[#e7ebff]">
                                <th class="pb-3 pr-4 font-medium text-[#1f2544]">Toko</th>
                                <th class="pb-3 pr-4 font-medium text-[#1f2544]">Admin</th>
                                <th class="pb-3 pr-4 font-medium text-[#1f2544]">Status</th>
                                <th class="pb-3 font-medium text-[#1f2544]">Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stores as $store)
                                <tr class="border-b border-[#eef3ff]">
                                    <td class="py-4 pr-4"><p class="font-semibold text-[#1f2544]">{{ $store['name'] }}</p><p class="mt-1 text-xs">{{ $store['location'] }}</p></td>
                                    <td class="py-4 pr-4">{{ $store['admin'] }}</td>
                                    <td class="py-4 pr-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $store['status'] === 'Aktif' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">{{ $store['status'] }}</span></td>
                                    <td class="py-4">{{ $store['updated'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-[0_18px_40px_rgba(31,37,68,0.04)]">
                <h2 class="text-xl font-semibold text-[#1f2544]">Aktivitas terbaru</h2>
                <div class="mt-5 space-y-3">
                    @foreach ($activities as $activity)
                        <div class="flex gap-3 rounded-2xl border border-[#eef3ff] bg-[#f7f9ff] p-3 text-sm leading-6 text-[#5e647f]"><span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-[#687EFF]"></span>{{ $activity }}</div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
@endsection
