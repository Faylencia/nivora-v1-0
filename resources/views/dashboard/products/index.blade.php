@extends('layouts.dashboard')

@section('title', 'Product Management - Nivora')

@section('content')
    @php
        $products = [
            ['image' => 'https://images.unsplash.com/photo-1497636577773-f1231844b336?auto=format&fit=crop&w=900&q=80', 'name' => 'Kopi Susu Signature', 'sku' => 'KS-001', 'category' => 'Minuman', 'price' => 'Rp 18.000', 'stock' => 24, 'status' => 'Active'],
            ['image' => 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=900&q=80', 'name' => 'Boba Hazelnut', 'sku' => 'BH-205', 'category' => 'Minuman', 'price' => 'Rp 22.000', 'stock' => 12, 'status' => 'Active'],
            ['image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=900&q=80', 'name' => 'Batik Premium', 'sku' => 'BP-118', 'category' => 'Fashion', 'price' => 'Rp 180.000', 'stock' => 6, 'status' => 'Low'],
        ];
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#687EFF]">Products</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-900">Product management</h1>
            </div>
            <x-button href="{{ route('products.create') }}" type="primary">Add product</x-button>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="w-full max-w-md rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                    <input type="text" placeholder="Search product" class="w-full bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400" />
                </div>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <select class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none">
                        <option>All categories</option>
                        <option>Minuman</option>
                        <option>Fashion</option>
                    </select>
                    <select class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 outline-none">
                        <option>All status</option>
                        <option>Active</option>
                        <option>Low</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-slate-700">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="pb-3 pr-4 font-medium">Product</th>
                            <th class="pb-3 pr-4 font-medium">SKU</th>
                            <th class="pb-3 pr-4 font-medium">Category</th>
                            <th class="pb-3 pr-4 font-medium">Price</th>
                            <th class="pb-3 pr-4 font-medium">Stock</th>
                            <th class="pb-3 pr-4 font-medium">Status</th>
                            <th class="pb-3 text-right font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr class="border-b border-slate-100">
                                <td class="py-3 pr-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="h-11 w-11 rounded-xl object-cover" />
                                        <span class="font-medium text-slate-900">{{ $product['name'] }}</span>
                                    </div>
                                </td>
                                <td class="py-3 pr-4">{{ $product['sku'] }}</td>
                                <td class="py-3 pr-4">{{ $product['category'] }}</td>
                                <td class="py-3 pr-4">{{ $product['price'] }}</td>
                                <td class="py-3 pr-4">{{ $product['stock'] }}</td>
                                <td class="py-3 pr-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $product['status'] === 'Active' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">{{ $product['status'] }}</span>
                                </td>
                                <td class="py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('products.show', ['product' => 1]) }}" class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:border-[#687EFF]/30 hover:text-[#687EFF]">View</a>
                                        <a href="{{ route('products.edit', ['product' => 1]) }}" class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:border-[#687EFF]/30 hover:text-[#687EFF]">Edit</a>
                                        <button type="button" class="rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-medium text-rose-600">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex items-center justify-between text-sm text-slate-500">
                <span>Showing 1-3 of 24</span>
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
