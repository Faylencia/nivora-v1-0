@extends('layouts.owner')

@section('title', 'Profil Bisnis - Nivora')

@section('content')
    <div class="space-y-6" data-business-profile>
        <x-page-header eyebrow="OWNER / TOKO SAYA" title="Profil bisnis" description="Kelola identitas dan informasi resmi bisnis Anda dalam satu tempat." />

        <div data-live-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>

        <section class="ni-panel relative overflow-hidden bg-[linear-gradient(115deg,#1f2544_0%,#414f9f_58%,#687eff_100%)] p-6 text-white sm:p-8">
            <div class="relative z-10 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4 sm:gap-5">
                    <div data-business-avatar class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl border border-white/20 bg-white/10 text-2xl font-semibold text-white shadow-lg sm:h-[76px] sm:w-[76px]">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 10.5 12 4l9 6.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-9.5Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 10h8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#c8d0ff]">Profil perusahaan</p>
                        <h2 data-profile-business-name class="mt-1 truncate text-xl font-semibold sm:text-2xl">Memuat nama bisnis…</h2>
                        <p data-profile-business-type class="mt-1 text-sm text-white/75">Informasi usaha Anda</p>
                    </div>
                </div>
                <div class="flex shrink-0 items-center gap-2 self-start rounded-full border border-white/20 bg-white/10 px-3 py-2 text-xs font-medium text-white/90 sm:self-center">
                    <span class="h-2 w-2 rounded-full bg-[#71e0aa] shadow-[0_0_0_3px_rgba(113,224,170,0.16)]"></span>
                    Dikelola oleh Anda
                </div>
            </div>
            <div class="pointer-events-none absolute -right-8 -top-24 h-64 w-64 rounded-full border-[36px] border-white/10"></div>
            <div class="pointer-events-none absolute -bottom-32 right-1/4 h-56 w-56 rounded-full border-[28px] border-white/[0.06]"></div>
        </section>

        <div data-business-loading class="ni-panel p-8 text-center text-sm text-[#5e647f]">
            <span class="inline-flex items-center gap-2"><span class="h-2 w-2 animate-pulse rounded-full bg-[#687EFF]"></span>Memuat profil bisnis…</span>
        </div>

        <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
            <form data-business-form class="ni-panel hidden overflow-hidden">
                <div class="border-b border-[#e7ebff] px-5 py-5 sm:px-7">
                    <p class="ni-section-label">Informasi bisnis</p>
                    <h2 class="mt-1 text-lg font-semibold tracking-tight">Data perusahaan</h2>
                    <p class="mt-1 text-sm leading-6 text-[#5e647f]">Pastikan informasi bisnis selalu akurat agar mudah dikenali oleh pelanggan.</p>
                </div>

                <div class="grid gap-x-5 gap-y-5 p-5 sm:grid-cols-2 sm:p-7">
                    <label class="space-y-2 text-sm font-semibold sm:col-span-2">
                        <span>Nama bisnis <span class="text-rose-600">*</span></span>
                        <input name="name" required maxlength="150" autocomplete="organization" placeholder="Contoh: Nivora Market" class="min-h-11 w-full rounded-xl border border-[#e7ebff] bg-[#f7f9ff] px-3.5 text-sm font-normal outline-none transition focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/10" />
                    </label>

                    <label class="space-y-2 text-sm font-semibold sm:col-span-2">
                        <span>Nama lokasi operasional</span>
                        <input name="location_name" maxlength="150" autocomplete="address-level2" placeholder="Contoh: Cabang Utama" class="min-h-11 w-full rounded-xl border border-[#e7ebff] bg-[#f7f9ff] px-3.5 text-sm font-normal outline-none transition focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/10" />
                        <span class="block text-xs font-normal leading-5 text-[#7b8299]">Satu bisnis memiliki satu lokasi operasional yang terhubung dengan profil ini.</span>
                    </label>

                    <label class="space-y-2 text-sm font-semibold">
                        <span>Bidang usaha <span class="text-rose-600">*</span></span>
                        <input name="business_type" required maxlength="100" placeholder="Contoh: Retail dan kebutuhan harian" class="min-h-11 w-full rounded-xl border border-[#e7ebff] bg-[#f7f9ff] px-3.5 text-sm font-normal outline-none transition focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/10" />
                    </label>

                    <label class="space-y-2 text-sm font-semibold">
                        <span>Nomor telepon bisnis</span>
                        <input name="phone" type="tel" maxlength="30" autocomplete="tel" placeholder="Contoh: 08xxxxxxxxxx" class="min-h-11 w-full rounded-xl border border-[#e7ebff] bg-[#f7f9ff] px-3.5 text-sm font-normal outline-none transition focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/10" />
                    </label>

                    <label class="space-y-2 text-sm font-semibold sm:col-span-2">
                        <span>Alamat bisnis</span>
                        <textarea name="address" rows="4" autocomplete="street-address" placeholder="Masukkan alamat lengkap lokasi bisnis" class="w-full resize-y rounded-xl border border-[#e7ebff] bg-[#f7f9ff] px-3.5 py-3 text-sm font-normal outline-none transition focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/10"></textarea>
                    </label>
                </div>

                <div class="flex flex-col gap-3 border-t border-[#e7ebff] bg-[#fcfcff] px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-7">
                    <p class="text-xs leading-5 text-[#7b8299]"><span class="text-rose-600">*</span> Wajib diisi</p>
                    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#687EFF] px-5 text-sm font-semibold text-white shadow-[0_8px_18px_rgba(104,126,255,0.22)] transition hover:bg-[#596ee6] focus:outline-none focus:ring-4 focus:ring-[#687EFF]/20 disabled:cursor-wait disabled:opacity-60">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12.5 9.5 17 19 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Simpan perubahan</span>
                    </button>
                </div>
            </form>

            <aside class="space-y-5">
                <section class="ni-panel p-5 sm:p-6">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-[#dfe6ff] bg-[#eef3ff] text-[#536dfe]">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 8v4l2.5 1.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <h3 class="mt-4 font-semibold">Profil yang selalu terbaru</h3>
                    <p class="mt-2 text-sm leading-6 text-[#5e647f]">Gunakan nama dan kontak aktif agar pelanggan serta tim Anda mendapat informasi bisnis yang tepat.</p>
                </section>

                <section class="rounded-[26px] border border-[#dfe6ff] bg-[#f7f9ff] p-5 sm:p-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#687EFF]">Informasi lokasi</p>
                    <h3 data-profile-location-name class="mt-2 font-semibold text-[#1f2544]">Lokasi belum diatur</h3>
                    <p class="mt-1 text-sm leading-6 text-[#5e647f]">Lokasi operasional mengikuti data pada profil bisnis.</p>
                    <div class="mt-4 flex items-start gap-2 border-t border-[#e7ebff] pt-4 text-xs leading-5 text-[#7b8299]">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-[#687EFF]" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.7"/></svg>
                        <span>Alamat dan nomor telepon membantu pelanggan menemukan toko Anda.</span>
                    </div>
                </section>
            </aside>
        </div>
    </div>
@endsection
