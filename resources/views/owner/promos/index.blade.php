@extends('layouts.owner')

@section('title', 'Promo - Owner Nivora')

@section('content')
    @php
        $promos = [
            [
                'name' => 'Diskon Kopi Susu Signature',
                'description' => 'Promo khusus untuk produk Kopi Susu Signature.',
                'type' => 'product',
                'discount_value' => '10%',
                'target' => 'Kopi Susu Signature',
                'period' => '20 Sep 2026 – 30 Sep 2026',
                'status' => 'Aktif',
            ],
            [
                'name' => 'Hemat Belanja 100 Ribu',
                'description' => 'Berlaku untuk transaksi dengan nilai minimum Rp 100.000.',
                'type' => 'transaction',
                'discount_value' => '5%',
                'target' => 'Min. transaksi Rp 100.000',
                'period' => '15 Sep 2026 – 15 Okt 2026',
                'status' => 'Aktif',
            ],
            [
                'name' => 'Promo Akhir Pekan',
                'description' => 'Potongan untuk produk pilihan di akhir pekan.',
                'type' => 'product',
                'discount_value' => 'Rp 15.000',
                'target' => 'Boba Hazelnut, Croissant Matcha',
                'period' => '01 Sep 2026 – 15 Sep 2026',
                'status' => 'Selesai',
            ],
        ];
    @endphp

    <div class="space-y-6">
        <x-page-header eyebrow="OWNER / PROMO" title="Promo" description="Buat dan kelola promo produk atau promo berdasarkan minimum transaksi.">
            <x-button href="{{ route('owner.promos.create') }}" type="primary">Tambah promo</x-button>
        </x-page-header>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-[#e7ebff] bg-white p-5 shadow-sm">
                <p class="text-sm text-[#5e647f]">Total promo</p>
                <p class="mt-2 text-2xl font-semibold text-[#1f2544]">{{ count($promos) }}</p>
            </div>
            <div class="rounded-2xl border border-[#e7ebff] bg-white p-5 shadow-sm">
                <p class="text-sm text-[#5e647f]">Promo aktif</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-600">{{ collect($promos)->where('status', 'Aktif')->count() }}</p>
            </div>
            <div class="rounded-2xl border border-[#e7ebff] bg-white p-5 shadow-sm">
                <p class="text-sm text-[#5e647f]">Jenis target</p>
                <p class="mt-2 text-sm font-semibold text-[#1f2544]">Produk dan transaksi</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-[28px] border border-[#e7ebff] bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-[#eef3ff] p-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-semibold text-[#1f2544]">Daftar promo</h2>
                    <p class="mt-1 text-sm text-[#5e647f]">Kelola masa berlaku dan target promo toko.</p>
                </div>
                <input type="search" placeholder="Cari promo" class="min-h-11 w-full rounded-xl border border-[#e7ebff] bg-[#f7f9ff] px-3 text-sm outline-none focus:border-[#687EFF] sm:max-w-xs" />
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-[#5e647f]">
                    <thead class="bg-[#f7f9ff] text-xs uppercase tracking-wide text-[#5e647f]">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Promo</th>
                            <th class="px-5 py-3 font-semibold">Target</th>
                            <th class="px-5 py-3 font-semibold">Diskon</th>
                            <th class="px-5 py-3 font-semibold">Periode</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($promos as $index => $promo)
                            <tr class="border-t border-[#eef3ff] align-top">
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-[#1f2544]">{{ $promo['name'] }}</p>
                                    <p class="mt-1 max-w-xs text-xs leading-5">{{ $promo['description'] }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="mb-1 inline-flex rounded-full bg-[#eef3ff] px-2 py-1 text-[10px] font-semibold uppercase tracking-wide text-[#687EFF]">{{ $promo['type'] === 'product' ? 'Produk' : 'Transaksi' }}</span>
                                    <p class="max-w-xs text-xs leading-5">{{ $promo['target'] }}</p>
                                </td>
                                <td class="px-5 py-4 font-semibold text-[#1f2544]">{{ $promo['discount_value'] }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-xs">{{ $promo['period'] }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $promo['status'] === 'Aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $promo['status'] }}</span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <a href="{{ route('owner.promos.edit', ['promo' => $index + 1]) }}" class="font-semibold text-[#687EFF] hover:text-[#4f63df]">Edit</a>
                                    <button type="button" data-open-modal="delete-promo" class="ml-3 font-semibold text-rose-500 hover:text-rose-600">Hapus</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <x-modal id="delete-promo" title="Hapus promo?">
            <p class="text-sm leading-6 text-[#5e647f]">Tampilan ini belum terhubung ke backend. Promo tidak akan dihapus dari database.</p>
            <div class="mt-6 flex justify-end">
                <button type="button" data-close-modal="delete-promo" class="rounded-xl bg-[#1f2544] px-4 py-2.5 text-sm font-semibold text-white">Tutup</button>
            </div>
        </x-modal>
    </div>
@endsection
