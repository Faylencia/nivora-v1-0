@extends('layouts.owner')

@section('title', 'Payments - Nivora')

@section('content')
    @php
        $payments = [
            ['reference' => 'PAY-2048', 'invoice' => '#INV-1024', 'method' => 'QRIS', 'amount' => 'Rp 1.250.000', 'status' => 'Paid', 'paid_at' => '2026-09-12 14:30'],
            ['reference' => 'PAY-2047', 'invoice' => '#INV-1023', 'method' => 'Transfer', 'amount' => 'Rp 890.000', 'status' => 'Pending', 'paid_at' => '2026-09-11 11:15'],
            ['reference' => 'PAY-2046', 'invoice' => '#INV-1022', 'method' => 'Cash', 'amount' => 'Rp 650.000', 'status' => 'Refunded', 'paid_at' => '2026-09-10 09:20'],
        ];
    @endphp

    <div class="space-y-6">
        <x-page-header eyebrow="OWNER / LAPORAN" title="Laporan pembayaran" description="Pantau status dan metode pembayaran transaksi toko." />

        <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-[0_16px_38px_rgba(31,37,68,0.05)]">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-[#5e647f]">
                    <thead>
                        <tr class="border-b border-[#e7ebff]">
                            <th class="pb-3 pr-4 font-medium text-[#1f2544]">Payment reference</th>
                            <th class="pb-3 pr-4 font-medium text-[#1f2544]">Invoice</th>
                            <th class="pb-3 pr-4 font-medium text-[#1f2544]">Method</th>
                            <th class="pb-3 pr-4 font-medium text-[#1f2544]">Amount</th>
                            <th class="pb-3 pr-4 font-medium text-[#1f2544]">Status</th>
                            <th class="pb-3 font-medium text-[#1f2544]">Paid at</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr class="border-b border-[#eef3ff]">
                                <td class="py-3 pr-4 font-medium text-[#1f2544]">{{ $payment['reference'] }}</td>
                                <td class="py-3 pr-4">{{ $payment['invoice'] }}</td>
                                <td class="py-3 pr-4">{{ $payment['method'] }}</td>
                                <td class="py-3 pr-4">{{ $payment['amount'] }}</td>
                                <td class="py-3 pr-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $payment['status'] === 'Paid' ? 'bg-emerald-50 text-emerald-600' : ($payment['status'] === 'Pending' ? 'bg-amber-50 text-amber-600' : ($payment['status'] === 'Failed' ? 'bg-rose-50 text-rose-600' : 'bg-sky-50 text-sky-600')) }}">{{ $payment['status'] }}</span>
                                </td>
                                <td class="py-3">{{ $payment['paid_at'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
