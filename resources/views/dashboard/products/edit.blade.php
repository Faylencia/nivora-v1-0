@extends('layouts.dashboard')

@section('title', 'Edit Product - Nivora')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#687EFF]">Products</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Edit product</h1>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <form class="space-y-6">
                <div class="grid gap-5 md:grid-cols-2">
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700">Product image</label>
                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                            <img src="https://images.unsplash.com/photo-1497636577773-f1231844b336?auto=format&fit=crop&w=1200&q=80" alt="Kopi Susu Signature" class="h-48 w-full object-cover" />
                        </div>
                    </div>

                    <x-input label="Product name" name="name" value="Kopi Susu Signature" required />
                    <x-input label="SKU" name="sku" value="KS-001" required />
                    <x-input label="Barcode" name="barcode" value="123456789012" required />
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700">Category</label>
                        <select class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/10">
                            <option selected>Minuman</option>
                            <option>Fashion</option>
                            <option>Kerajinan</option>
                        </select>
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700">Description</label>
                        <textarea rows="4" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-[#687EFF] focus:outline-none focus:ring-4 focus:ring-[#687EFF]/10">Kopi susu lembut dengan aroma khas, cita rasa yang seimbang, dan tekstur creamy yang tidak terlalu manis.</textarea>
                    </div>
                    <x-input label="Purchase price" name="purchase_price" type="number" value="15000" required />
                    <x-input label="Selling price" name="selling_price" type="number" value="18000" required />
                    <x-input label="Unit" name="unit" value="pcs" required />
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700">Active status</label>
                        <select class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/10">
                            <option selected>Active</option>
                            <option>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col gap-3 pt-2 sm:flex-row">
                    <x-button type="primary" size="lg">Update product</x-button>
                    <x-button href="{{ route('products.index') }}" type="ghost" size="lg">Cancel</x-button>
                </div>
            </form>
        </div>
    </div>
@endsection
