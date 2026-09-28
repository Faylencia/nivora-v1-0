@extends('layouts.owner')

@section('title', 'Store Analysis - Nivora')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <x-page-header eyebrow="OWNER / AI" title="Store analysis" description="Analisis tata letak toko dan temukan peluang untuk meningkatkan pengalaman pelanggan." />

        <div class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
            <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-semibold text-slate-900">Upload store image</h2>
                <div class="mt-5 flex h-72 cursor-pointer flex-col items-center justify-center rounded-[24px] border-2 border-dashed border-slate-300 bg-slate-50 p-6 text-center text-slate-500 transition hover:border-[#687EFF] hover:bg-[#F5FBFF]">
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-[#EAF6FF] text-3xl">⬆️</div>
                    <p class="text-base font-medium text-slate-700">Drag and drop image here</p>
                    <p class="mt-2 text-sm">or click to browse</p>
                </div>
                <div class="mt-5 rounded-[24px] border border-slate-200 bg-slate-50 p-4">
                    <img src="https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=1200&q=80" alt="Store preview" class="h-48 w-full rounded-2xl object-cover" />
                </div>
                <div class="mt-5">
                    <x-button type="primary" class="w-full" size="lg">Analyze</x-button>
                </div>
            </div>

            <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-semibold text-slate-900">Analysis result</h2>

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-sm text-slate-500">Overall score</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-900">88/100</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-sm text-slate-500">Recommendation</p>
                        <p class="mt-2 text-base font-semibold text-slate-900">Strong</p>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <h3 class="text-base font-semibold text-slate-900">Lighting recommendation</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Tambahkan pencahayaan hangat pada area produk utama untuk meningkatkan fokus dan daya tarik visual konsumen.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <h3 class="text-base font-semibold text-slate-900">Rack / layout recommendation</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Pindahkan produk unggulan ke area mata dan susun berdasarkan kategori agar alur pengunjung lebih natural.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
