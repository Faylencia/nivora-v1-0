@extends('layouts.super-admin')

@section('title', 'Kategori - Super Admin Nivora')

@section('content')
    @php
        $categories = [
            ['name' => 'Makanan', 'description' => 'Produk makanan dan camilan', 'products' => 184],
            ['name' => 'Minuman', 'description' => 'Minuman dingin dan hangat', 'products' => 96],
            ['name' => 'Fashion', 'description' => 'Pakaian dan aksesori', 'products' => 74],
        ];
    @endphp

    <div class="space-y-6">
        <x-page-header eyebrow="SUPER ADMIN / KATEGORI" title="Kategori" description="Kelola kategori yang tersedia di platform Nivora.">
            <x-button href="{{ route('super-admin.categories.create') }}" type="primary">Tambah kategori</x-button>
        </x-page-header>

        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="w-full max-w-md rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                    <input type="text" placeholder="Search category" class="w-full bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400" />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-slate-700">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="pb-3 pr-4 font-medium">Name</th>
                            <th class="pb-3 pr-4 font-medium">Description</th>
                            <th class="pb-3 pr-4 font-medium">Products</th>
                            <th class="pb-3 text-right font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr class="border-b border-slate-100">
                                <td class="py-3 pr-4 font-medium text-slate-900">{{ $category['name'] }}</td>
                                <td class="py-3 pr-4">{{ $category['description'] }}</td>
                                <td class="py-3 pr-4">{{ $category['products'] }}</td>
                                <td class="py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('super-admin.categories.edit', ['category' => 1]) }}" class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:border-[#687EFF]/30 hover:text-[#687EFF]">Edit</a>
                                        <button type="button" class="rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-medium text-rose-600">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
