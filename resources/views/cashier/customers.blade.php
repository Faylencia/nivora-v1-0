@extends('layouts.cashier')

@section('title', 'Customer Kasir - Nivora')

@section('content')
    @php
        $customers = [
            ['name' => 'Budi', 'phone' => '0812-3456-7890', 'points' => 120],
            ['name' => 'Sinta', 'phone' => '0812-1111-2222', 'points' => 240],
            ['name' => 'Rafi', 'phone' => '0812-3333-4444', 'points' => 80],
        ];
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#687EFF]">Customer</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-[-0.05em] text-[#1f2544]">Customer</h1>
            </div>
        </div>

        <div class="rounded-[28px] border border-[#e7ebff] bg-white p-5 shadow-sm">
            <div class="mb-5 max-w-md rounded-xl border border-[#e7ebff] bg-[#f7f9ff] px-3 py-2">
                <input type="text" placeholder="Search customer" class="w-full bg-transparent text-sm text-[#1f2544] outline-none placeholder:text-[#6b7280]" />
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($customers as $customer)
                    <div class="rounded-[24px] border border-[#e7ebff] bg-[#f7f9ff] p-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#dfe7ff] font-semibold text-[#1f2544]">{{ strtoupper(substr($customer['name'], 0, 1)) }}</div>
                            <div>
                                <p class="font-semibold text-[#1f2544]">{{ $customer['name'] }}</p>
                                <p class="text-xs text-[#5e647f]">{{ $customer['phone'] }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between text-sm text-[#5e647f]">
                            <span>Points</span>
                            <span class="font-semibold text-[#1f2544]">{{ $customer['points'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
