@extends('layouts.owner')

@section('title', 'Customers - Nivora')

@section('content')
    @php
        $customers = [
            ['name' => 'Nadia Putri', 'phone' => '0812-1234-5678', 'email' => 'nadia@gmail.com', 'membership' => 'Gold', 'points' => 2450, 'status' => 'Aktif', 'joined' => '12 Jan 2026', 'transactions' => 18],
            ['name' => 'Fajar Hidayat', 'phone' => '0812-2345-6789', 'email' => 'fajar@gmail.com', 'membership' => 'Silver', 'points' => 1980, 'status' => 'Aktif', 'joined' => '04 Mar 2026', 'transactions' => 12],
            ['name' => 'Siti Aisyah', 'phone' => '0812-3456-7890', 'email' => 'siti@gmail.com', 'membership' => 'Platinum', 'points' => 3200, 'status' => 'Aktif', 'joined' => '21 Nov 2025', 'transactions' => 25],
        ];
    @endphp

    <div class="space-y-6">
        <x-page-header eyebrow="OWNER / CUSTOMER" title="Customer" description="Kelola informasi pelanggan dan pantau hubungan mereka dengan toko." />

        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="w-full max-w-md rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                    <input type="text" placeholder="Search customer" class="w-full bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400" />
                </div>
                <select class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none">
                    <option>All segments</option>
                    <option>VIP</option>
                    <option>Regular</option>
                </select>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-slate-700">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="pb-3 pr-4 font-medium">Name</th>
                            <th class="pb-3 pr-4 font-medium">Phone</th>
                            <th class="pb-3 pr-4 font-medium">Email</th>
                            <th class="pb-3 pr-4 font-medium">Membership</th>
                            <th class="pb-3 pr-4 font-medium">Points</th>
                            <th class="pb-3 pr-4 font-medium">Status</th>
                            <th class="pb-3 pr-4 font-medium">Joined</th>
                            <th class="pb-3 pr-4 font-medium">Total transaction</th>
                            <th class="pb-3 text-right font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr class="border-b border-slate-100">
                                <td class="py-3 pr-4 font-medium text-slate-900">{{ $customer['name'] }}</td>
                                <td class="py-3 pr-4">{{ $customer['phone'] }}</td>
                                <td class="py-3 pr-4">{{ $customer['email'] }}</td>
                                <td class="py-3 pr-4"><span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">{{ $customer['membership'] }}</span></td>
                                <td class="py-3 pr-4">{{ number_format($customer['points']) }} Points</td>
                                <td class="py-3 pr-4"><span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-600">{{ $customer['status'] }}</span></td>
                                <td class="py-3 pr-4">{{ $customer['joined'] }}</td>
                                <td class="py-3 pr-4">{{ $customer['transactions'] }}</td>
                                <td class="py-3 text-right">
                                    <button type="button" class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:border-[#687EFF]/30 hover:text-[#687EFF]">View detail</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
