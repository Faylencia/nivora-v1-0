@extends('layouts.super-admin')

@section('title', 'Kelola Akun - Super Admin Nivora')

@section('content')
    <div class="space-y-6" data-super-admin-members>
        <x-page-header eyebrow="SUPER ADMIN / PENGELOLAAN AKUN" title="Kelola akun" description="Kelola akses Member dan Owner dalam satu tempat.">
            <a href="{{ route('super-admin.business-applications') }}" class="group inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-[#dce4ff] bg-white px-4 text-sm font-semibold text-[#39436f] shadow-[0_5px_16px_rgba(49,67,130,0.05)] transition duration-200 hover:-translate-y-0.5 hover:border-[#b9c7ff] hover:bg-[#f8f9ff] hover:shadow-md focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#536dfe]/15">
                <svg viewBox="0 0 24 24" class="h-4 w-4 text-[#687eff] transition group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M7 3.75h7l4.25 4.5v11A1.75 1.75 0 0 1 16.5 21h-9A1.75 1.75 0 0 1 5.75 19.25v-13.75A1.75 1.75 0 0 1 7.5 3.75Z"/><path d="M14 4v5h4.5M8.5 13h7M8.5 16.5h7"/></svg> Lihat Pengajuan
            </a>
            <button type="button" data-open-modal="account-create-modal" class="group inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#536dfe] to-[#687eff] px-4 text-sm font-semibold text-white shadow-[0_8px_18px_rgba(83,109,254,0.24)] transition duration-200 hover:-translate-y-0.5 hover:from-[#435ce7] hover:to-[#596ff5] hover:shadow-[0_12px_24px_rgba(83,109,254,0.3)] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#536dfe]/20">
                <svg viewBox="0 0 24 24" class="h-4 w-4 transition group-hover:rotate-90" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg> Tambah Akun
            </button>
        </x-page-header>

        <div data-admin-members-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>

        <section class="overflow-hidden rounded-[26px] border border-[#e6eaff] bg-white shadow-[0_18px_50px_rgba(49,67,130,0.07)]">
            <div class="flex flex-col gap-4 border-b border-[#edf0fb] p-5 sm:p-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-semibold tracking-tight text-[#20284a]">Semua akun</h2>
                        <span class="rounded-full bg-[#eef1ff] px-2.5 py-1 text-xs font-semibold text-[#536dfe]">Member & Owner</span>
                    </div>
                    <p class="mt-1 text-sm text-[#7b8299]">Cari akun, lihat toko yang terhubung, dan perbarui statusnya.</p>
                </div>
                <form data-admin-members-search class="flex w-full flex-col gap-2 sm:flex-row lg:max-w-xl">
                    <label class="relative min-w-0 flex-1">
                        <span class="sr-only">Cari akun</span>
                        <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[#8b93ad]" aria-hidden="true">⌕</span>
                        <input id="admin-member-search" name="search" type="search" placeholder="Cari nama, email, atau telepon" class="min-h-11 w-full rounded-xl border border-[#e5e9f7] bg-[#fafbff] pl-10 pr-3 text-sm text-[#20284a] outline-none transition placeholder:text-[#a1a8bc] focus:border-[#8697ff] focus:bg-white focus:ring-4 focus:ring-[#536dfe]/10">
                    </label>
                    <label>
                        <span class="sr-only">Filter jenis akun</span>
                        <select id="admin-account-role-filter" name="role" data-admin-account-filter class="min-h-11 w-full rounded-xl border border-[#e5e9f7] bg-[#fafbff] px-3 text-sm font-medium text-[#4e5677] outline-none focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10 sm:w-36">
                            <option value="">Semua akun</option><option value="member">Member</option><option value="owner">Owner</option>
                        </select>
                    </label>
                    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-[#e5e9f7] bg-white px-4 text-sm font-semibold text-[#4e5677] shadow-sm transition hover:border-[#c8d1ee] hover:bg-[#f7f8ff] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#536dfe]/15"><svg viewBox="0 0 24 24" class="h-4 w-4 text-[#687eff]" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.2 4.2"/></svg>Cari</button>
                </form>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 bg-[#fcfcff] px-5 py-3.5 sm:px-6">
                <p data-admin-members-count class="text-xs font-medium text-[#737b96]"></p>
                <div class="flex items-center gap-2">
                    <button data-admin-members-prev type="button" aria-label="Halaman sebelumnya" class="inline-flex min-h-9 items-center gap-1 rounded-lg border border-[#e5e9f7] bg-white px-3 text-xs font-semibold text-[#4e5677] shadow-sm transition hover:border-[#c8d1ee] hover:bg-[#f7f8ff] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#536dfe]/15 disabled:cursor-not-allowed disabled:opacity-40"><svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m14.5 18-6-6 6-6"/></svg>Sebelumnya</button>
                    <span data-admin-members-page class="min-w-12 text-center text-xs font-semibold text-[#59617d]"></span>
                    <button data-admin-members-next type="button" aria-label="Halaman berikutnya" class="inline-flex min-h-9 items-center gap-1 rounded-lg border border-[#e5e9f7] bg-white px-3 text-xs font-semibold text-[#4e5677] shadow-sm transition hover:border-[#c8d1ee] hover:bg-[#f7f8ff] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#536dfe]/15 disabled:cursor-not-allowed disabled:opacity-40">Berikutnya<svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9.5 18 6-6-6-6"/></svg></button>
                </div>
            </div>

            <div data-admin-members-loading class="p-12 text-center text-sm text-[#7b8299]">Memuat akun…</div>
            <div data-admin-members-empty class="hidden p-12 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-[#f0f2ff] text-xl text-[#687eff]" aria-hidden="true">◎</div>
                <p class="mt-3 font-semibold text-[#303858]">Akun tidak ditemukan</p>
                <p class="mt-1 text-sm text-[#7b8299]">Ubah kata kunci atau filter untuk melihat akun lainnya.</p>
            </div>
            <div data-admin-members-list class="hidden overflow-x-auto"></div>
        </section>
    </div>

    <div id="account-create-modal" class="fixed inset-0 z-[100] hidden items-center justify-center overflow-y-auto bg-[#101633]/55 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="account-create-title">
        <button type="button" data-close-modal="account-create-modal" class="absolute inset-0 h-full w-full cursor-default" aria-label="Tutup dialog"></button>
        <section class="relative z-10 my-auto w-full max-w-2xl overflow-hidden rounded-[28px] bg-white shadow-[0_30px_100px_rgba(15,23,60,0.3)]">
            <div class="relative flex items-start justify-between gap-4 border-b border-[#edf0fb] px-6 py-5 sm:px-7">
                <span class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-[#536dfe] via-[#8293ff] to-[#54c8e8]"></span>
                <div class="flex items-center gap-4"><span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-[#536dfe] to-[#8190ff] text-white shadow-lg shadow-[#536dfe]/20"><svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M3 20v-1.5a6 6 0 0 1 6-6h1a6 6 0 0 1 5.8 4.5M19 13v7m-3.5-3.5h7"/></svg></span><div><p class="text-xs font-bold uppercase tracking-[0.16em] text-[#687eff]">Akun baru</p><h2 id="account-create-title" class="mt-1 text-xl font-semibold tracking-tight text-[#20284a]">Tambah akun</h2><p class="mt-1 text-sm text-[#7b8299]">Buat akses Member atau Owner untuk Nivora.</p></div></div>
                <button type="button" data-close-modal="account-create-modal" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#f4f6fc] text-lg text-[#68708b] transition hover:rotate-90 hover:bg-[#e9edfa] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#536dfe]/15" aria-label="Tutup">×</button>
            </div>
            <form data-admin-account-create class="grid gap-4 p-6 sm:grid-cols-2 sm:p-7">
                <div data-admin-account-create-message class="hidden rounded-xl border px-4 py-3 text-sm sm:col-span-2" role="alert" aria-live="assertive"></div>
                <label class="space-y-1.5 text-xs font-semibold text-[#59617d]">Nama lengkap<input name="name" required maxlength="100" placeholder="Nama pengguna" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"></label>
                <label class="space-y-1.5 text-xs font-semibold text-[#59617d]">Email<input name="email" type="email" required maxlength="150" placeholder="nama@email.com" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"></label>
                <label class="space-y-1.5 text-xs font-semibold text-[#59617d]">Nomor telepon<input name="phone" type="tel" required minlength="7" maxlength="30" placeholder="08xxxxxxxxxx" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"></label>
                <label class="space-y-1.5 text-xs font-semibold text-[#59617d]">Peran<select name="role" data-admin-account-role class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"><option value="member">Member</option><option value="owner">Owner</option></select></label>
                <label data-admin-account-business-wrap class="hidden space-y-1.5 text-xs font-semibold text-[#59617d] sm:col-span-2">Toko untuk Owner<select name="business_id" data-admin-account-business required disabled class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"><option value="">Memuat daftar toko…</option></select></label>
                <label class="space-y-1.5 text-xs font-semibold text-[#59617d]">Kata sandi<span class="relative block"><input id="account-password" name="password" type="password" required minlength="8" autocomplete="new-password" placeholder="Minimal 8 karakter" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 pr-12 text-sm font-normal text-[#20284a] outline-none focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"><button type="button" data-toggle-password="account-password" aria-label="Tampilkan kata sandi" aria-pressed="false" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center rounded-r-xl text-[#828aa4] transition hover:text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/><path data-password-eye-slash class="hidden" d="m4 4 16 16"/></svg></button></span></label>
                <label class="space-y-1.5 text-xs font-semibold text-[#59617d]">Ulangi kata sandi<span class="relative block"><input id="account-password-confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" placeholder="Ketik ulang kata sandi" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 pr-12 text-sm font-normal text-[#20284a] outline-none focus:border-[#8697ff] focus:ring-4 focus:ring-[#536dfe]/10"><button type="button" data-toggle-password="account-password-confirmation" aria-label="Tampilkan kata sandi" aria-pressed="false" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center rounded-r-xl text-[#828aa4] transition hover:text-[#536dfe]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/><path data-password-eye-slash class="hidden" d="m4 4 16 16"/></svg></button></span></label>
                <div class="mt-1 flex flex-col-reverse gap-2 border-t border-[#edf0fb] pt-5 sm:col-span-2 sm:flex-row sm:justify-end">
                    <button type="button" data-close-modal="account-create-modal" class="min-h-11 rounded-xl border border-[#e4e8f4] bg-white px-4 text-sm font-semibold text-[#59617d] transition hover:-translate-y-0.5 hover:border-[#cbd3e8] hover:bg-[#f8f9fd] hover:shadow-sm focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#536dfe]/15">Batal</button>
                    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#536dfe] to-[#687eff] px-5 text-sm font-semibold text-white shadow-[0_8px_18px_rgba(83,109,254,0.2)] transition hover:-translate-y-0.5 hover:from-[#435ce7] hover:to-[#596ff5] hover:shadow-lg focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#536dfe]/20 disabled:cursor-wait disabled:opacity-60"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>Simpan akun</button>
                </div>
            </form>
        </section>
    </div>

    <div id="account-edit-modal" class="fixed inset-0 z-[100] hidden items-center justify-center overflow-y-auto bg-[#101633]/55 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="account-edit-title">
        <button type="button" data-close-modal="account-edit-modal" class="absolute inset-0 h-full w-full cursor-default" aria-label="Tutup dialog"></button>
        <section class="relative z-10 my-auto w-full max-w-xl overflow-hidden rounded-[28px] bg-white shadow-[0_30px_100px_rgba(15,23,60,0.3)]">
            <div class="relative flex items-start justify-between gap-4 border-b border-[#edf0fb] px-6 py-5 sm:px-7">
                <span class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-[#536dfe] via-[#8293ff] to-[#54c8e8]"></span>
                <div class="flex items-center gap-4"><span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#eef1ff] text-[#596ee6]"><svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M3 20v-1.5a6 6 0 0 1 6-6h1a6 6 0 0 1 5.8 4.5M17 4h4m-2-2v4m-3 8 4 4m0-4-4 4"/></svg></span><div><p class="text-xs font-bold uppercase tracking-[0.16em] text-[#687eff]">Pengaturan pengguna</p><h2 id="account-edit-title" class="mt-1 text-xl font-semibold tracking-tight text-[#20284a]">Edit akun</h2><p class="mt-1 text-sm text-[#7b8299]">Perbarui profil, kontak, dan status akses.</p></div></div>
                <button type="button" data-close-modal="account-edit-modal" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#f4f6fc] text-lg text-[#68708b] transition hover:rotate-90 hover:bg-[#e9edfa] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#536dfe]/15" aria-label="Tutup">×</button>
            </div>
            <form data-admin-account-edit class="grid gap-4 p-6 sm:grid-cols-2 sm:p-7">
                <div class="account-edit-summary rounded-2xl border border-[#e8ecfb] bg-gradient-to-r from-[#f7f8ff] to-[#f4fbff] p-4 sm:col-span-2"><p class="text-[10px] font-bold uppercase tracking-[0.14em] text-[#8a91a9]">Ringkasan akun</p><div class="mt-2 flex flex-wrap items-center gap-2"><span data-edit-account-role class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-[#596ee6] shadow-sm">Peran</span><span data-edit-account-business class="text-xs font-medium text-[#737b96]"></span></div></div>
                <label class="space-y-1.5 text-xs font-semibold text-[#59617d]">Nama<input name="name" required maxlength="100" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:bg-white focus:ring-4 focus:ring-[#536dfe]/10"></label>
                <label class="space-y-1.5 text-xs font-semibold text-[#59617d]">Email<input name="email" type="email" required maxlength="150" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:bg-white focus:ring-4 focus:ring-[#536dfe]/10"></label>
                <label class="space-y-1.5 text-xs font-semibold text-[#59617d]">Nomor telepon<input name="phone" required maxlength="30" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:bg-white focus:ring-4 focus:ring-[#536dfe]/10"></label>
                <label class="space-y-1.5 text-xs font-semibold text-[#59617d]">Status<select name="status" class="min-h-11 w-full rounded-xl border border-[#e4e8f4] bg-[#fafbff] px-3.5 text-sm font-normal text-[#20284a] outline-none transition focus:border-[#8697ff] focus:bg-white focus:ring-4 focus:ring-[#536dfe]/10"><option value="active">Aktif</option><option value="inactive">Nonaktif</option></select></label>
                <input name="password" type="hidden"><input name="password_confirmation" type="hidden">
                <div class="mt-1 flex flex-col-reverse gap-2 border-t border-[#edf0fb] pt-5 sm:col-span-2 sm:flex-row sm:justify-end">
                    <button type="button" data-close-modal="account-edit-modal" class="min-h-11 rounded-xl border border-[#e4e8f4] bg-white px-4 text-sm font-semibold text-[#59617d] transition hover:-translate-y-0.5 hover:border-[#cbd3e8] hover:bg-[#f8f9fd] hover:shadow-sm focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#536dfe]/15">Batal</button>
                    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#536dfe] to-[#687eff] px-5 text-sm font-semibold text-white shadow-[0_8px_18px_rgba(83,109,254,0.2)] transition hover:-translate-y-0.5 hover:from-[#435ce7] hover:to-[#596ff5] hover:shadow-lg focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#536dfe]/20 disabled:cursor-wait disabled:opacity-60"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>Simpan perubahan</button>
                </div>
            </form>
        </section>
    </div>
@endsection
