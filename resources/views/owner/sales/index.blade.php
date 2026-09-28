@extends('layouts.owner')

@section('title', 'Sales - Nivora')

@section('content')
    @php
        $sales = [
            ['invoice' => '#INV-1024', 'customer' => 'Nadia Putri', 'outlet' => 'Bandung Center', 'cashier' => 'Ayu R.', 'total' => 'Rp 1.250.000', 'method' => 'QRIS', 'status' => 'Paid', 'date' => '2026-09-12'],
            ['invoice' => '#INV-1023', 'customer' => 'Fajar H.', 'outlet' => 'Jakarta Pop-up', 'cashier' => 'Dina', 'total' => 'Rp 890.000', 'method' => 'Transfer', 'status' => 'Pending', 'date' => '2026-09-11'],
            ['invoice' => '#INV-1022', 'customer' => 'Siti A.', 'outlet' => 'Cibadak Outlet', 'cashier' => 'Raka', 'total' => 'Rp 650.000', 'method' => 'Cash', 'status' => 'Paid', 'date' => '2026-09-10'],
        ];
    @endphp

    <div class="space-y-6">
        <x-page-header eyebrow="OWNER / TRANSAKSI" title="Transaksi" description="Pantau seluruh transaksi yang terjadi di toko Anda." />

        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="w-full max-w-md rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                    <input type="text" placeholder="Search invoice or customer" class="w-full bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400" />
                </div>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <input type="date" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none" />
                    <select class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none">
                        <option>All status</option>
                        <option>Paid</option>
                        <option>Pending</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-slate-700">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="pb-3 pr-4 font-medium">Invoice</th>
                            <th class="pb-3 pr-4 font-medium">Customer</th>
                            <th class="pb-3 pr-4 font-medium">Outlet</th>
                            <th class="pb-3 pr-4 font-medium">Cashier</th>
                            <th class="pb-3 pr-4 font-medium">Total</th>
                            <th class="pb-3 pr-4 font-medium">Payment</th>
                            <th class="pb-3 pr-4 font-medium">Status</th>
                            <th class="pb-3 font-medium">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales as $sale)
                            <tr class="border-b border-slate-100">
                                <td class="py-3 pr-4 font-medium text-slate-900">{{ $sale['invoice'] }}</td>
                                <td class="py-3 pr-4">{{ $sale['customer'] }}</td>
                                <td class="py-3 pr-4">{{ $sale['outlet'] }}</td>
                                <td class="py-3 pr-4">{{ $sale['cashier'] }}</td>
                                <td class="py-3 pr-4">{{ $sale['total'] }}</td>
                                <td class="py-3 pr-4">{{ $sale['method'] }}</td>
                                <td class="py-3 pr-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $sale['status'] === 'Paid' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">{{ $sale['status'] }}</span>
                                </td>
                                <td class="py-3">{{ $sale['date'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex items-center justify-between text-sm text-slate-500">
                <span>Showing 1-3 of 45</span>
                <div class="flex gap-2">
                    <button class="rounded-xl border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600">Prev</button>
                    <button class="rounded-xl bg-[#687EFF] px-3 py-2 font-medium text-white">1</button>
                    <button class="rounded-xl border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600">2</button>
                    <button class="rounded-xl border border-slate-200 bg-white px-3 py-2 font-medium text-slate-600">Next</button>
                </div>
            </div>
        </div>
    </div>
@endsection
