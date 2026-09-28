@extends('layouts.cashier')

@section('title', 'Riwayat Transaksi - Nivora')

@section('content')
    @php
        $sales = [
            ['invoice' => '#NV-2048', 'date' => '2026-09-17', 'customer' => 'Budi', 'total' => 'Rp 160.000', 'payment' => 'Cash', 'status' => 'Completed'],
            ['invoice' => '#NV-2047', 'date' => '2026-09-17', 'customer' => 'Sinta', 'total' => 'Rp 82.000', 'payment' => 'QRIS', 'status' => 'Completed'],
            ['invoice' => '#NV-2046', 'date' => '2026-09-16', 'customer' => 'Rafi', 'total' => 'Rp 245.000', 'payment' => 'Cash', 'status' => 'Cancelled'],
            ['invoice' => '#NV-2045', 'date' => '2026-09-16', 'customer' => 'Nadia', 'total' => 'Rp 76.000', 'payment' => 'QRIS', 'status' => 'Returned'],
        ];
    @endphp

    <div class="space-y-6">
        <x-page-header eyebrow="CASHIER / RIWAYAT TRANSAKSI" title="Riwayat transaksi" description="Lihat dan pantau riwayat transaksi yang telah dilakukan.">
            <x-button href="{{ route('cashier.pos') }}" type="primary">Mulai Transaksi</x-button>
        </x-page-header>

        <div class="rounded-[28px] border border-[#e7ebff] bg-white p-5 shadow-sm">
            <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="w-full max-w-md rounded-xl border border-[#e7ebff] bg-[#f7f9ff] px-3 py-2">
                    <input type="text" placeholder="Search invoice atau customer" class="w-full bg-transparent text-sm text-[#1f2544] outline-none placeholder:text-[#6b7280]" />
                </div>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <input type="date" class="rounded-xl border border-[#e7ebff] bg-[#f7f9ff] px-3 py-2.5 text-sm text-[#1f2544] outline-none" />
                    <select class="rounded-xl border border-[#e7ebff] bg-[#f7f9ff] px-3 py-2.5 text-sm text-[#1f2544] outline-none">
                        <option>Semua status</option>
                        <option>Completed</option>
                        <option>Cancelled</option>
                        <option>Returned</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-[#5e647f]">
                    <thead>
                        <tr class="border-b border-[#e7ebff]">
                            <th class="pb-3 pr-4 font-medium text-[#1f2544]">Invoice</th>
                            <th class="pb-3 pr-4 font-medium text-[#1f2544]">Tanggal</th>
                            <th class="pb-3 pr-4 font-medium text-[#1f2544]">Customer</th>
                            <th class="pb-3 pr-4 font-medium text-[#1f2544]">Total</th>
                            <th class="pb-3 pr-4 font-medium text-[#1f2544]">Pembayaran</th>
                            <th class="pb-3 font-medium text-[#1f2544]">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales as $sale)
                            <tr class="border-b border-[#eef3ff]">
                                <td class="py-3 pr-4 font-medium text-[#1f2544]">{{ $sale['invoice'] }}</td>
                                <td class="py-3 pr-4">{{ $sale['date'] }}</td>
                                <td class="py-3 pr-4">{{ $sale['customer'] }}</td>
                                <td class="py-3 pr-4">{{ $sale['total'] }}</td>
                                <td class="py-3 pr-4">{{ $sale['payment'] }}</td>
                                <td class="py-3 pr-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium
                                        @if($sale['status'] === 'Completed') bg-emerald-50 text-emerald-600
                                        @elseif($sale['status'] === 'Cancelled') bg-rose-50 text-rose-600
                                        @else bg-amber-50 text-amber-600
                                        @endif">
                                        {{ $sale['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
