@extends('layouts.owner')

@section('title', 'AI Dashboard - Nivora')

@section('content')
    @php
        $recommendations = [
            ['title' => 'Restock prioritas', 'detail' => 'Kopi Susu Signature dan Tas Jinjing sudah mendekati batas minimum stok.', 'priority' => 'High', 'status' => 'Action needed'],
            ['title' => 'Peningkatan penjualan', 'detail' => 'Produk peralatan rumah tangga naik 18% dalam 7 hari terakhir.', 'priority' => 'Medium', 'status' => 'Trending'],
            ['title' => 'Layout display', 'detail' => 'Penataan ulang produk di area depan dapat meningkatkan konversi.', 'priority' => 'Low', 'status' => 'Suggested'],
        ];
    @endphp

    <div class="space-y-6">
        <x-page-header eyebrow="OWNER / AI" title="AI insights" description="Tinjau rekomendasi dan analisis untuk membantu operasional toko.">
            <x-button href="{{ route('owner.ai.store-analysis') }}" type="primary">Store analysis</x-button>
        </x-page-header>

        <div class="grid gap-5 md:grid-cols-3">
            <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-[0_16px_38px_rgba(31,37,68,0.05)]">
                <p class="text-sm text-[#5e647f]">AI recommendations</p>
                <p class="mt-3 text-3xl font-semibold text-[#1f2544]">12</p>
            </div>
            <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-[0_16px_38px_rgba(31,37,68,0.05)]">
                <p class="text-sm text-[#5e647f]">AI insight</p>
                <p class="mt-3 text-3xl font-semibold text-[#1f2544]">8</p>
            </div>
            <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-[0_16px_38px_rgba(31,37,68,0.05)]">
                <p class="text-sm text-[#5e647f]">Priority</p>
                <p class="mt-3 text-3xl font-semibold text-[#1f2544]">3</p>
            </div>
        </div>

        <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-[0_16px_38px_rgba(31,37,68,0.05)]">
            <h2 class="text-xl font-semibold text-[#1f2544]">Recommendation cards</h2>
            <div class="mt-5 grid gap-5 lg:grid-cols-3">
                @foreach ($recommendations as $item)
                    <div class="rounded-[24px] border border-[#e7ebff] bg-[#f7f9ff] p-5">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <h3 class="text-lg font-semibold text-[#1f2544]">{{ $item['title'] }}</h3>
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $item['priority'] === 'High' ? 'bg-rose-50 text-rose-600' : ($item['priority'] === 'Medium' ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600') }}">{{ $item['priority'] }}</span>
                        </div>
                        <p class="text-sm leading-6 text-[#5e647f]">{{ $item['detail'] }}</p>
                        <div class="mt-5 inline-flex rounded-full bg-white px-2.5 py-1 text-xs font-medium text-[#1f2544]">{{ $item['status'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-[28px] border border-[#e7ebff] bg-white/90 p-5 shadow-[0_16px_38px_rgba(31,37,68,0.05)]">
            <h2 class="text-xl font-semibold text-[#1f2544]">AI actions</h2>
            <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                <x-button type="primary">Generate report</x-button>
                <x-button type="secondary">Suggest campaign</x-button>
                <x-button type="ghost">Review insights</x-button>
            </div>
        </div>
    </div>
@endsection
