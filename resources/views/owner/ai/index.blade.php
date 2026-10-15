@extends('layouts.owner')

@section('title', 'AI Insights - Nivora')

@section('content')
    <div class="space-y-6" data-live-list="recommendations">
        <x-page-header eyebrow="OWNER / AI" title="AI insights" description="Rekomendasi yang sudah tersimpan untuk bisnis Anda.">
            <button type="button" data-ai-generate class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#687EFF] px-4 text-sm font-semibold text-white transition hover:bg-[#596eea]">Analisis stok sekarang</button>
        </x-page-header>
        <div data-live-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>
        <section class="ni-panel overflow-hidden">
            <div class="border-b border-[#e7ebff] p-5"><h2 class="text-lg font-semibold">Rekomendasi tersimpan</h2><p class="mt-1 text-sm text-[#5e647f]">Analisis hanya dibuat dari catatan stok yang memenuhi batas minimum.</p></div>
            <div data-live-loading class="p-10 text-center text-sm text-[#5e647f]">Memuat rekomendasi…</div>
            <div data-live-empty class="hidden p-10 text-center"><p class="font-semibold">Belum ada rekomendasi tersimpan.</p><p class="mt-1 text-sm text-[#5e647f]">Jalankan analisis stok untuk memeriksa inventori yang sudah ada.</p></div>
            <div data-live-table-wrap class="hidden overflow-x-auto"><table class="ni-table"><thead><tr><th>Rekomendasi</th><th>Prioritas</th><th>Status</th><th>Dibuat</th></tr></thead><tbody data-live-table></tbody></table></div>
        </section>
    </div>
@endsection
