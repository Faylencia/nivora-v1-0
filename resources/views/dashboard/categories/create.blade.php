@extends('layouts.dashboard')

@section('title', 'Create Category - Nivora')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#687EFF]">Category</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Add category</h1>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <form class="space-y-5">
                <x-input label="Name" name="name" placeholder="Masukkan nama kategori" required />
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-slate-700">Description</label>
                    <textarea rows="4" placeholder="Masukkan deskripsi kategori" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 shadow-sm transition focus:border-[#687EFF] focus:outline-none focus:ring-4 focus:ring-[#687EFF]/10"></textarea>
                </div>

                <div class="flex flex-col gap-3 pt-2 sm:flex-row">
                    <x-button type="primary" size="lg">Save category</x-button>
                    <x-button href="{{ route('categories.index') }}" type="ghost" size="lg">Cancel</x-button>
                </div>
            </form>
        </div>
    </div>
@endsection
