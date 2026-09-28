@extends('layouts.app')

@section('title', 'Nivora - Temukan UMKM Lokal di Sekitarmu')

@section('content')
    @php
        $stats = [
            ['label' => 'usaha terdaftar', 'value' => '12k+'],
            ['label' => 'rating pengguna', 'value' => '4.9/5'],
            ['label' => 'kota terjangkau', 'value' => '320+'],
        ];

        $modules = [
            ['index' => '01', 'title' => 'Sales', 'description' => 'Record transactions and see your daily activity.', 'icon' => '↗'],
            ['index' => '02', 'title' => 'Catalog', 'description' => 'Manage products, categories and featured listings.', 'icon' => '◌'],
            ['index' => '03', 'title' => 'Customers', 'description' => 'Track buyers, loyalty and repeat business.', 'icon' => '◎'],
        ];
    @endphp

    <section class="mx-auto max-w-7xl px-4 pb-12 pt-10 sm:px-6 lg:px-8 lg:pt-14">
        <div class="border-b border-[#e7ebff] pb-10">
            <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-[#dfe7ff] bg-[#f7f9ff] px-3 py-1.5 text-[11px] font-medium uppercase tracking-[0.18em] text-[#1f2544]">
                <span class="h-2 w-2 rounded-full bg-[#687EFF]"></span>
                Platform UMKM terdepan
            </div>

            <div class="grid items-start gap-8 lg:grid-cols-[1.4fr_0.6fr]">
                <div>
                    <h1 class="max-w-[720px] text-5xl font-semibold leading-[0.92] tracking-[-0.06em] text-[#1f2544] sm:text-6xl xl:text-[7rem]">
                        Everything your business<br>
                        needs,<br>
                        <span class="bg-gradient-to-r from-[#687EFF] via-[#98E4FF] to-[#B6FFFA] bg-clip-text text-transparent">within reach.</span>
                    </h1>

                    <p class="mt-7 max-w-[620px] text-lg leading-8 text-[#5e647f]">
                        Choose a workspace to get started. Keep your daily operations clear, practical, and moving forward.
                    </p>
                </div>

                <div class="flex justify-end pt-2">
                    <div class="w-full max-w-[360px] rounded-[26px] border border-[#dfe7ff] bg-[linear-gradient(180deg,_#eef3ff_0%,_#f7f9ff_100%)] p-5 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/80 text-base text-[#687EFF]">✦</div>
                            <div class="text-xl font-medium text-[#1f2544]">Smart Business for Everyone</div>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-[#5e647f]">
                            Your business, one calm workspace.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-10 rounded-[26px] border border-[#e7ebff] bg-white/40 p-2 shadow-sm">
                <div class="flex items-center gap-3 px-3 py-2 sm:px-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#5e647f]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35m1.6-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/>
                    </svg>
                    <input type="text" placeholder="Cari UMKM atau produk" class="w-full border-0 bg-transparent text-base text-[#1f2544] placeholder:text-[#7c859f] outline-none" />
                </div>
            </div>

            <div class="mt-8 flex flex-wrap items-center gap-6 text-base text-[#5e647f]">
                @foreach ($stats as $stat)
                    <div>
                        <span class="font-semibold text-[#1f2544]">{{ $stat['value'] }}</span>
                        <span class="ml-2">{{ $stat['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <p class="text-[11px] font-medium uppercase tracking-[0.2em] text-[#687EFF]">Workspace modules</p>
                <h2 class="mt-3 text-3xl font-semibold tracking-[-0.05em] text-[#1f2544] sm:text-4xl">Where would you like to go?</h2>
            </div>
            <a href="{{ route('explore') }}" class="text-sm font-medium text-[#1f2544] hover:text-[#687EFF]">Open overview →</a>
        </div>

        <div class="space-y-4">
            @foreach ($modules as $module)
                <a href="{{ route('owner.dashboard') }}" class="group block rounded-[26px] border border-[#e7ebff] bg-[linear-gradient(180deg,_#eef3ff_0%,_#f7f9ff_100%)] p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="text-[11px] font-medium uppercase tracking-[0.2em] text-[#687EFF]">{{ $module['index'] }}</div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-xl text-[#1f2544] shadow-sm">
                                {{ $module['icon'] }}
                            </div>
                            <div>
                                <div class="text-2xl font-semibold tracking-[-0.04em] text-[#1f2544]">{{ $module['title'] }}</div>
                                <p class="mt-1 text-sm text-[#5e647f]">{{ $module['description'] }}</p>
                            </div>
                        </div>
                        <div class="text-2xl text-[#687EFF] transition group-hover:translate-x-1">→</div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endsection
