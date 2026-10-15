@extends('layouts.super-admin')

@section('title', 'Aktivitas Platform - Super Admin Nivora')

@section('content')
    <div class="space-y-6">
        <x-page-header eyebrow="SUPER ADMIN · AUDIT TRAIL" title="Aktivitas platform" description="Riwayat perubahan penting di Nivora, tersusun dari yang paling baru.">
            <a href="{{ route('super-admin.dashboard') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-[#e3e8f6] bg-white px-4 text-sm font-semibold text-[#58617f] shadow-sm transition hover:-translate-y-0.5 hover:border-[#b8c4ff] hover:bg-[#f7f8ff] hover:text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m15 18-6-6 6-6M9 12h12"/></svg>Kembali ke dashboard</a>
        </x-page-header>

        <section class="relative isolate overflow-hidden rounded-[26px] bg-gradient-to-br from-[#20284a] via-[#394a91] to-[#687eff] p-6 text-white shadow-[0_20px_48px_rgba(49,67,130,0.18)] sm:p-8">
            <div class="pointer-events-none absolute -right-10 -top-24 -z-10 h-64 w-64 rounded-full border-[38px] border-white/[0.08]"></div>
            <div class="relative flex items-center gap-4"><span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-white/15 bg-white/10 text-[#c8fffb]"><svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2M5.6 5.6l1.8 1.8"/></svg></span><div><p class="text-xs font-bold uppercase tracking-[0.18em] text-[#c8fffb]">Riwayat aktivitas</p><p class="mt-1 text-sm leading-6 text-white/75">Menampilkan {{ number_format($activities->total(), 0, ',', '.') }} catatan yang tersimpan.</p></div></div>
        </section>

        <section class="overflow-hidden rounded-[26px] border border-[#e6eaff] bg-white shadow-[0_18px_50px_rgba(49,67,130,0.06)]">
            <div class="flex flex-col gap-4 border-b border-[#edf0fb] p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6"><div><h2 class="text-lg font-semibold tracking-tight text-[#20284a]">Jejak perubahan</h2><p class="mt-1 text-sm text-[#7b8299]">Pengajuan, bisnis, akun, dan kategori yang dibuat atau diperbarui.</p></div><nav aria-label="Filter aktivitas" class="activity-filter-tabs flex w-fit flex-wrap gap-1 rounded-xl bg-[#f3f5fc] p-1"><a href="{{ route('super-admin.activity') }}" aria-current="{{ $selectedType === '' ? 'page' : 'false' }}" @class(['rounded-lg px-3 py-2 text-xs font-semibold transition', 'bg-white text-[#39436f] shadow-sm' => $selectedType === '', 'text-[#858ca4] hover:text-[#39436f]' => $selectedType !== ''])>Semua</a>@foreach ($types as $type => $label)<a href="{{ route('super-admin.activity', ['type' => $type]) }}" aria-current="{{ $selectedType === $type ? 'page' : 'false' }}" @class(['rounded-lg px-3 py-2 text-xs font-semibold transition', 'bg-white text-[#39436f] shadow-sm' => $selectedType === $type, 'text-[#858ca4] hover:text-[#39436f]' => $selectedType !== $type])>{{ $label }}</a>@endforeach</nav></div>

            @if ($activities->isEmpty())
                <div class="px-6 py-14 text-center"><span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#f0f2ff] text-[#687eff]"><svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span><p class="mt-4 font-semibold text-[#303858]">Belum ada aktivitas tercatat</p><p class="mt-1 text-sm text-[#7b8299]">Aktivitas baru akan tercatat otomatis saat ada pengajuan atau perubahan data platform.</p></div>
            @else
                <div class="activity-list">
                    @foreach ($activities as $activity)
                        @php
                            $activityTone = str_contains($activity->event, 'rejected') || str_contains($activity->event, 'deleted')
                                ? 'bg-rose-50 text-rose-600 ring-rose-100'
                                : (str_contains($activity->event, 'approved') || str_contains($activity->event, 'created')
                                    ? 'bg-emerald-50 text-emerald-600 ring-emerald-100'
                                    : 'bg-[#eef1ff] text-[#536dfe] ring-[#e2e7ff]');
                        @endphp
                        <article class="flex items-start gap-4 p-5 transition hover:bg-[#fcfcff] sm:px-6"><span class="activity-icon mt-0.5 flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl ring-1 {{ $activityTone }}"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m5 12 4.5 4.5L19 7"/></svg></span><div class="min-w-0 flex-1"><div class="flex flex-wrap items-center gap-x-2 gap-y-1"><p class="text-sm leading-6 text-[#4e5677]"><span class="font-semibold text-[#303858]">{{ $activity->actor_name }}</span> {{ $activity->summary }}</p><span class="rounded-full bg-[#f4f6fc] px-2 py-0.5 text-[10px] font-semibold text-[#858ca4]">{{ $types[explode('.', $activity->event)[0]] ?? 'Platform' }}</span></div><p class="mt-1.5 flex flex-wrap items-center gap-x-2 text-xs text-[#969db2]"><span>{{ $activity->subject_name ?: 'Platform Nivora' }}</span><span aria-hidden="true">·</span><time datetime="{{ \Illuminate\Support\Carbon::parse($activity->created_at)->toIso8601String() }}">{{ \Illuminate\Support\Carbon::parse($activity->created_at)->format('d M Y · H:i') }}</time><span aria-hidden="true">·</span><span>{{ \Illuminate\Support\Carbon::parse($activity->created_at)->diffForHumans() }}</span></p></div></article>
                    @endforeach
                </div>
                <div class="border-t border-[#edf0fb] bg-[#fcfcff] px-5 py-4 sm:px-6">{{ $activities->links() }}</div>
            @endif
        </section>
    </div>
@endsection
