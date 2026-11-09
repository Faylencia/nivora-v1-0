@extends('layouts.owner')

@section('title', 'AI Store Advisor - Nivora')

@section('content')
    <div class="space-y-6" data-store-advisor>
        <x-page-header eyebrow="OWNER / AI" title="AI Store Advisor" description="Masukkan ukuran toko, jenis toko, dan posisi pintu masuk. Gemini akan membuat denah 2D dan rekomendasi penataan toko.">
            <a href="{{ route('owner.ai.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-4 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]">Kembali ke AI insights</a>
        </x-page-header>

        <div data-advisor-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>

        {{-- ============ FORM INPUT ============ --}}
        <section class="ni-panel p-5 sm:p-6" aria-labelledby="advisor-form-title">
            <h2 id="advisor-form-title" class="text-lg font-semibold">Buat Rekomendasi Tata Letak Toko</h2>
            <p class="mt-1 text-sm text-[#5e647f]">Tidak perlu mengunggah foto atau mengisi koordinat teknis. Cukup isi informasi dasar toko Anda.</p>

            <form data-advisor-form class="mt-6 space-y-8">
                {{-- A. Ukuran & informasi toko --}}
                <fieldset>
                    <legend class="flex items-center gap-3 text-base font-semibold text-[#1f2544]">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#eef1ff] text-sm font-bold text-[#536dfe]">1</span>
                        Informasi Toko
                    </legend>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <label class="space-y-1 text-sm font-semibold text-[#1f2544]">
                            Panjang toko (meter) <span class="text-rose-600">*</span>
                            <input data-advisor-length type="number" min="0.5" max="50" step="0.1" required inputmode="decimal" placeholder="Contoh: 8" class="mt-2 min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/15" />
                        </label>
                        <label class="space-y-1 text-sm font-semibold text-[#1f2544]">
                            Lebar toko (meter) <span class="text-rose-600">*</span>
                            <input data-advisor-width type="number" min="0.5" max="50" step="0.1" required inputmode="decimal" placeholder="Contoh: 6" class="mt-2 min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/15" />
                        </label>
                        <div class="rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-[#5e647f]">Luas otomatis</p>
                            <p class="mt-1 text-2xl font-semibold text-[#1f2544]"><span data-advisor-area>0</span> <span class="text-base font-medium text-[#5e647f]">m²</span></p>
                        </div>
                    </div>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <label class="space-y-1 text-sm font-semibold text-[#1f2544]">
                            Jenis toko <span class="text-rose-600">*</span>
                            <select data-advisor-store-type required class="mt-2 min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/15">
                                <option value="">Pilih jenis toko</option>
                                <option value="kelontong">Kelontong</option>
                                <option value="minimarket">Minimarket</option>
                                <option value="sembako">Sembako</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </label>
                        <label data-advisor-store-type-other-wrap class="hidden space-y-1 text-sm font-semibold text-[#1f2544]">
                            Jenis toko lainnya <span class="text-rose-600">*</span>
                            <input data-advisor-store-type-other type="text" maxlength="100" placeholder="Contoh: Toko ATK, Apotek, dll." class="mt-2 min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/15" />
                        </label>
                        <label class="space-y-1 text-sm font-semibold text-[#1f2544]">
                            Jumlah rak yang dimiliki <span class="font-normal text-[#7b8299]">(opsional)</span>
                            <input data-advisor-rack-count type="number" min="0" max="100" step="1" inputmode="numeric" placeholder="Contoh: 6" class="mt-2 min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/15" />
                        </label>
                    </div>
                </fieldset>

                {{-- B. Posisi pintu & preferensi --}}
                <fieldset>
                    <legend class="flex items-center gap-3 text-base font-semibold text-[#1f2544]">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#eef1ff] text-sm font-bold text-[#536dfe]">2</span>
                        Pintu Masuk & Preferensi
                    </legend>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <label class="space-y-1 text-sm font-semibold text-[#1f2544]">
                            Posisi pintu masuk <span class="text-rose-600">*</span>
                            <select data-advisor-door-position required class="mt-2 min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/15">
                                <option value="">Pilih posisi pintu</option>
                                <option value="depan">Depan</option>
                                <option value="kiri">Kiri</option>
                                <option value="kanan">Kanan</option>
                                <option value="belakang">Belakang</option>
                            </select>
                        </label>
                        <label class="space-y-1 text-sm font-semibold text-[#1f2544]">
                            Konsep penataan <span class="font-normal text-[#7b8299]">(opsional)</span>
                            <select data-advisor-layout-concept class="mt-2 min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/15">
                                <option value="">Pilih otomatis</option>
                                <option value="modern">Modern</option>
                                <option value="minimalis">Minimalis</option>
                                <option value="tradisional">Tradisional</option>
                                <option value="efisien">Efisien</option>
                            </select>
                        </label>
                        <label class="space-y-1 text-sm font-semibold text-[#1f2544]">
                            Prioritas penataan <span class="font-normal text-[#7b8299]">(opsional)</span>
                            <select data-advisor-layout-priority class="mt-2 min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/15">
                                <option value="">Seimbang</option>
                                <option value="kapasitas_produk">Kapasitas produk</option>
                                <option value="kenyamanan_pelanggan">Kenyamanan pelanggan</option>
                                <option value="keseimbangan">Keseimbangan</option>
                            </select>
                        </label>
                    </div>
                </fieldset>

                <div class="flex flex-col gap-3 border-t border-[#e7ebff] pt-5 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-[#7b8299]">Analisis Gemini dapat memakan waktu beberapa saat. Jangan tutup halaman selama proses berjalan.</p>
                    <button type="submit" data-advisor-submit class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-[#1F2544] px-6 text-sm font-semibold text-white transition hover:bg-[#191f3c] disabled:cursor-wait disabled:opacity-60">
                        <svg data-advisor-submit-spinner viewBox="0 0 24 24" class="hidden h-5 w-5 animate-spin" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M12 3a9 9 0 1 0 9 9"/></svg>
                        <span data-advisor-submit-label>Analisis dengan Gemini</span>
                    </button>
                </div>
            </form>
        </section>

        {{-- ============ HASIL AI ============ --}}
        <section data-advisor-result class="hidden space-y-5" aria-labelledby="advisor-result-title">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 id="advisor-result-title" class="text-lg font-semibold">Hasil AI Store Advisor</h2>
                    <p class="mt-1 text-sm text-[#5e647f]"><span data-advisor-result-store class="font-semibold text-[#1f2544]"></span> · <span data-advisor-result-size></span> · <span data-advisor-result-date></span></p>
                </div>
            </div>

            <div data-advisor-failed class="hidden rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800" role="alert">
                <p class="font-semibold">Analisis AI gagal</p>
                <p data-advisor-failed-message class="mt-1"></p>
                <button type="button" data-advisor-retry class="mt-3 inline-flex min-h-10 items-center justify-center rounded-xl border border-rose-300 bg-white px-4 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">Coba Analisis Lagi</button>
            </div>

            <div class="flex flex-wrap items-center gap-2" role="tablist" aria-label="Hasil AI">
                <button type="button" role="tab" aria-selected="true" data-advisor-tab="recommendations" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#1F2544] bg-[#1F2544] px-5 text-sm font-semibold text-white transition">Saran & Rekomendasi</button>
                <button type="button" role="tab" aria-selected="false" data-advisor-tab="plan" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-5 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]">Denah 2D</button>
            </div>

            {{-- TAB 1: Rekomendasi --}}
            <div data-advisor-panel="recommendations" role="tabpanel" class="ni-panel p-5 sm:p-6">
                <h3 class="text-base font-semibold">Saran dan Rekomendasi Penataan</h3>
                <p class="mt-1 text-xs text-[#7b8299]">Rekomendasi dibuat berdasarkan ukuran, jenis toko, posisi pintu, dan preferensi yang Anda masukkan.</p>

                <div data-advisor-assumptions class="mt-4 hidden rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    <p class="font-semibold">Asumsi yang digunakan</p>
                    <ul data-advisor-assumptions-list class="mt-2 list-disc space-y-1 pl-5 text-xs leading-5"></ul>
                </div>

                <div class="mt-5 grid gap-5 lg:grid-cols-5">
                    <div class="lg:col-span-3">
                        <h4 class="text-sm font-semibold uppercase tracking-wide text-[#5e647f]">Rekomendasi</h4>
                        <div data-advisor-recommendations class="mt-3 space-y-3"></div>
                    </div>
                    <div class="lg:col-span-2 space-y-4">
                        <div>
                            <h4 class="text-sm font-semibold uppercase tracking-wide text-[#5e647f]">Ringkasan Toko</h4>
                            <dl data-advisor-summary class="mt-2 space-y-2 text-sm"></dl>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold uppercase tracking-wide text-[#5e647f]">Jalur & Area</h4>
                            <div data-advisor-paths-areas class="mt-2 space-y-2 text-sm text-[#1f2544]"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 flex flex-wrap items-center gap-2 border-t border-[#e7ebff] pt-4">
                    <button type="button" data-advisor-regenerate class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-4 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff] disabled:cursor-wait disabled:opacity-60"><span data-advisor-regenerate-label>Analisis Ulang</span></button>
                </div>
            </div>

            {{-- TAB 2: Denah 2D --}}
            <div data-advisor-panel="plan" role="tabpanel" class="hidden ni-panel p-5 sm:p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold">Denah 2D — Tampak Atas</h3>
                        <p class="mt-1 text-sm text-[#5e647f]"><span data-advisor-plan-size></span> · Skala sesuai ukuran toko · objek bergaris putus-putus = asumsi/perkiraan</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" data-advisor-relayout class="inline-flex min-h-11 items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-4 text-sm font-semibold text-emerald-800 transition hover:bg-emerald-100">Rapikan Otomatis</button>
                        <button type="button" data-advisor-edit class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-4 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]" aria-pressed="false">Edit Denah</button>
                        <button type="button" data-advisor-save class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#1F2544] px-4 text-sm font-semibold text-white transition hover:bg-[#191f3c] disabled:cursor-wait disabled:opacity-60">Simpan Denah</button>
                        <button type="button" data-advisor-download-png class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-4 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]">Download PNG</button>
                        <button type="button" data-advisor-download-pdf class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-4 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]">Download PDF</button>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <button type="button" data-advisor-zoom-out class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white text-lg font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]" aria-label="Perkecil">−</button>
                    <span data-advisor-zoom-value class="inline-flex h-11 min-w-16 items-center justify-center rounded-xl bg-[#eef3ff] px-3 text-sm font-semibold text-[#1f2544]">100%</span>
                    <button type="button" data-advisor-zoom-in class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white text-lg font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]" aria-label="Perbesar">+</button>
                    <button type="button" data-advisor-zoom-reset class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-4 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]">Reset</button>
                </div>

                <div class="mt-4 overflow-hidden rounded-[24px] border border-[#e7ebff] bg-[#f7f9ff] p-3 sm:p-4">
                    <svg data-advisor-svg class="h-[420px] w-full select-none" viewBox="0 0 10 10" role="img" aria-label="Denah 2D toko tampak atas"></svg>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs font-semibold text-[#1f2544]">
                    <span class="text-[#5e647f]">Legenda:</span>
                    <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm" style="background:#687EFF"></span>Rak Dinding</span>
                    <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm" style="background:#98E4FF"></span>Rak Tengah</span>
                    <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm" style="background:#1F2544"></span>Meja Kasir</span>
                    <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm" style="background:#F59E0B"></span>Pintu Masuk</span>
                    <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm" style="background:#64748B"></span>Gudang</span>
                    <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm" style="background:#B6FFFA"></span>Area Penyimpanan</span>
                    <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm" style="background:#D7E4FF"></span>Lorong Pelanggan</span>
                    <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm" style="background:#C7D2FE"></span>Area/Zona</span>
                </div>

                <div class="mt-5">
                    <h4 class="text-sm font-semibold uppercase tracking-wide text-[#5e647f]">Objek Denah</h4>
                    <p class="mt-1 text-xs text-[#7b8299]">Klik objek untuk memilih. Mode edit aktif untuk menggeser dengan mouse/sentuhan. Validasi mencegah objek keluar ruangan atau bertabrakan.</p>
                    <div data-advisor-objects class="mt-3 flex flex-wrap gap-2"></div>

                    <div data-advisor-selected class="mt-4 rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] p-4">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-sm font-semibold text-[#1f2544]"><span data-advisor-selected-name>Pilih objek…</span></span>
                            <span data-advisor-selected-estimated class="hidden rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-800">Asumsi</span>
                            <button type="button" data-advisor-rotate class="inline-flex min-h-10 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-3 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]">Putar 90°</button>
                            <button type="button" data-advisor-delete class="inline-flex min-h-10 items-center justify-center rounded-xl border border-rose-200 bg-white px-3 text-sm font-semibold text-rose-700 transition hover:bg-rose-50">Hapus</button>
                        </div>
                        <div class="mt-3 grid gap-3 sm:grid-cols-4">
                            <label class="space-y-1 text-xs font-semibold text-[#1f2544]">X (m)<input data-advisor-selected-x type="number" step="0.1" min="0" class="mt-1 min-h-10 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF]" /></label>
                            <label class="space-y-1 text-xs font-semibold text-[#1f2544]">Y (m)<input data-advisor-selected-y type="number" step="0.1" min="0" class="mt-1 min-h-10 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF]" /></label>
                            <label class="space-y-1 text-xs font-semibold text-[#1f2544]">Lebar (m)<input data-advisor-selected-width type="number" step="0.1" min="0.1" class="mt-1 min-h-10 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF]" /></label>
                            <label class="space-y-1 text-xs font-semibold text-[#1f2544]">Tinggi (m)<input data-advisor-selected-height type="number" step="0.1" min="0.1" class="mt-1 min-h-10 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF]" /></label>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <label class="text-sm font-semibold text-[#1f2544]" for="advisor-add-type">Tambah objek</label>
                        <select id="advisor-add-type" data-advisor-add-type class="min-h-11 rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF]">
                            <option value="wall_rack">Rak Dinding</option>
                            <option value="mid_rack">Rak Tengah</option>
                            <option value="cashier_desk">Meja Kasir</option>
                            <option value="door">Pintu Masuk</option>
                            <option value="stockroom">Gudang</option>
                            <option value="storage">Area Penyimpanan</option>
                            <option value="aisle">Lorong Pelanggan</option>
                            <option value="zone">Area/Zona</option>
                        </select>
                        <button type="button" data-advisor-add class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#687EFF] px-4 text-sm font-semibold text-white transition hover:bg-[#596eea]">Tambah ke Denah</button>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============ RIWAYAT ============ --}}
        <section class="ni-panel p-5 sm:p-6" aria-labelledby="advisor-history-title">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 id="advisor-history-title" class="text-lg font-semibold">Riwayat Desain</h2>
                    <p class="mt-1 text-sm text-[#5e647f]">Desain yang pernah dibuat untuk toko Anda. Klik "Buka" untuk melihat kembali.</p>
                </div>
                <span data-advisor-history-count class="rounded-full bg-[#eef1ff] px-3 py-1 text-xs font-semibold text-[#536dfe]"></span>
            </div>
            <p data-advisor-history-loading class="mt-6 text-center text-sm text-[#5e647f]">Memuat riwayat…</p>
            <div data-advisor-history-empty class="hidden p-8 text-center">
                <p class="font-semibold text-[#1f2544]">Belum ada desain tersimpan</p>
                <p class="mt-1 text-sm text-[#5e647f]">Isi form di atas dan klik "Analisis dengan Gemini" untuk membuat desain pertama Anda.</p>
            </div>
            <div data-advisor-history class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3"></div>
        </section>
    </div>
@endsection
