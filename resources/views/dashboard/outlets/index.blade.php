@extends('layouts.dashboard')

@section('title', 'Outlet Management - Nivora')

@section('content')
    @php
        $outlets = [
            ['name' => 'Bandung Center', 'address' => 'Jl. Citarum No. 24', 'phone' => '0812-1111-2222', 'status' => 'Active'],
            ['name' => 'Cibadak Outlet', 'address' => 'Jl. Raya Cibadak No. 18', 'phone' => '0812-3333-4444', 'status' => 'Inactive'],
            ['name' => 'Jakarta Pop-up', 'address' => 'Jl. Sudirman No. 4', 'phone' => '0812-5555-6666', 'status' => 'Active'],
        ];
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#687EFF]">Outlet</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-900">Outlet management</h1>
            </div>
            <x-button href="{{ route('outlets.create') }}" type="primary">Add outlet</x-button>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="w-full max-w-md rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                    <input type="text" placeholder="Search outlet" class="w-full bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400" />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-slate-700">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="pb-3 pr-4 font-medium">Name</th>
                            <th class="pb-3 pr-4 font-medium">Address</th>
                            <th class="pb-3 pr-4 font-medium">Phone</th>
                            <th class="pb-3 pr-4 font-medium">Status</th>
                            <th class="pb-3 text-right font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($outlets as $outlet)
                            <tr class="border-b border-slate-100">
                                <td class="py-3 pr-4 font-medium text-slate-900">{{ $outlet['name'] }}</td>
                                <td class="py-3 pr-4">{{ $outlet['address'] }}</td>
                                <td class="py-3 pr-4">{{ $outlet['phone'] }}</td>
                                <td class="py-3 pr-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $outlet['status'] === 'Active' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-600' }}">{{ $outlet['status'] }}</span>
                                </td>
                                <td class="py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('outlets.edit', ['outlet' => 1]) }}" class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:border-[#687EFF]/30 hover:text-[#687EFF]">Edit</a>
                                        <button type="button" class="rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-medium text-rose-600">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-[28px] border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
            <h3 class="text-xl font-semibold text-slate-900">Data outlet kosong</h3>
            <p class="mt-2 text-sm text-slate-500">Saat ini belum ada outlet yang ditambahkan.</p>
        </div>
    </div>
@endsection
