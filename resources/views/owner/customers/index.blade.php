@extends('layouts.owner')

@section('title', 'Customer - Nivora')

@section('content')
    <div class="owner-customers space-y-6" data-live-list="customers">
        <x-page-header eyebrow="OWNER / CRM CUSTOMER" title="Kelola customer" description="Lihat informasi kontak dan poin loyalitas customer bisnis Anda." />
        <div data-live-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>
        <section class="overflow-hidden rounded-[26px] border border-[#e6eaff] bg-white shadow-[0_18px_50px_rgba(49,67,130,0.07)]">
            <div class="flex flex-col gap-4 border-b border-[#edf0fb] p-5 sm:p-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2"><h2 class="text-lg font-semibold tracking-tight text-[#20284a]">Daftar customer</h2><span class="rounded-full bg-[#eef1ff] px-2.5 py-1 text-xs font-semibold text-[#536dfe]">Loyalty</span></div>
                    <p class="mt-1 text-sm text-[#7b8299]">Cari profil customer, kontak, dan saldo poin mereka.</p>
                </div>
                <label class="relative w-full lg:max-w-md">
                    <span class="sr-only">Cari customer</span>
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#8b93ad]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.2 4.2"/></svg>
                    <input data-live-search type="search" placeholder="Cari nama, telepon, atau email" class="min-h-11 w-full rounded-xl border border-[#e5e9f7] bg-[#fafbff] pl-10 pr-3 text-sm text-[#20284a] outline-none transition placeholder:text-[#a1a8bc] focus:border-[#8697ff] focus:bg-white focus:ring-4 focus:ring-[#536dfe]/10" />
                </label>
            </div>
            <div class="flex flex-wrap items-center justify-between gap-3 bg-[#fcfcff] px-5 py-3.5 sm:px-6">
                <p data-live-count class="text-xs font-medium text-[#737b96]">Memuat daftar customer…</p>
                <span class="inline-flex items-center gap-2 text-xs font-medium text-[#737b96]"><span class="h-2 w-2 rounded-full bg-emerald-400"></span>Data bisnis aktif</span>
            </div>
            <div data-live-loading class="p-12 text-center text-sm text-[#7b8299]">Memuat customer…</div>
            <div data-live-empty class="hidden p-12 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-[#f0f2ff] text-xl text-[#687eff]" aria-hidden="true">◎</div>
                <p class="mt-3 font-semibold text-[#303858]">Customer tidak ditemukan</p>
                <p class="mt-1 text-sm text-[#7b8299]">Ubah kata kunci pencarian atau tambahkan customer melalui transaksi.</p>
            </div>
            <div data-live-table-wrap class="hidden overflow-x-auto"><table class="ni-table"><thead><tr><th>Customer</th><th>Nomor telepon</th><th>Email</th><th>Poin loyalitas</th><th>Tanggal bergabung</th></tr></thead><tbody data-live-table></tbody></table></div>
        </section>
    </div>
@endsection
