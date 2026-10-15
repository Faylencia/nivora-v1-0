@extends('layouts.cashier')

@section('title', 'Customer - Nivora')

@section('content')
    <div class="space-y-6" data-live-list="customers">
        <x-page-header eyebrow="CASHIER / CUSTOMER" title="Customer" description="Customer yang tercatat pada bisnis Anda." />
        <div data-live-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>
        <section class="ni-panel overflow-hidden">
            <div class="border-b border-[#e7ebff] p-5"><h2 class="text-lg font-semibold">Daftar customer</h2><input data-live-search type="search" placeholder="Cari nama, telepon, atau email" class="mt-4 min-h-10 w-full rounded-xl border border-[#e7ebff] bg-[#f7f9ff] px-3 text-sm outline-none focus:border-[#687EFF] sm:max-w-sm" /></div>
            <div data-live-loading class="p-10 text-center text-sm text-[#5e647f]">Memuat customer…</div>
            <div data-live-empty class="hidden p-10 text-center"><p class="font-semibold">Belum ada customer.</p><p class="mt-1 text-sm text-[#5e647f]">Customer baru akan muncul setelah tersimpan dalam bisnis ini.</p></div>
            <div data-live-table-wrap class="hidden overflow-x-auto"><table class="ni-table"><thead><tr><th>Nama</th><th>Telepon</th><th>Email</th><th>Poin</th><th>Terdaftar</th></tr></thead><tbody data-live-table></tbody></table></div>
        </section>
    </div>
@endsection
