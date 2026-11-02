@extends('layouts.owner')

@section('title', 'AI Floor Plan - Nivora')

@section('content')
    <div class="space-y-6" data-floor-plan>
        <x-page-header eyebrow="OWNER / AI" title="AI 2D Floor Plan Generator" description="Buat denah toko 2D dari ukuran ruangan, posisi pintu, jendela, dan area permanen.">
            <button type="button" data-ai-generate class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#687EFF] px-4 text-sm font-semibold text-white transition hover:bg-[#596eea]">Analisis stok sekarang</button>
            <a href="{{ route('owner.ai.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-4 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]">Kembali ke AI insights</a>
        </x-page-header>

        <div data-floor-plan-message class="hidden rounded-2xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>

        <section class="ni-panel p-5">
            <h2 class="text-lg font-semibold">Input denah</h2>
            <p class="mt-1 text-sm text-[#5e647f]">Isi ukuran ruangan dan posisi awal fasilitas. Owner dapat mengonfirmasi atau mengisi ulang jika data belum diketahui.</p>

            <form data-floor-plan-generate-form class="mt-5 grid gap-4 sm:grid-cols-2">
                <label class="space-y-1 text-sm font-semibold text-[#1f2544]">
                    Panjang ruangan (meter)
                    <input data-floor-plan-room-length type="number" min="0.5" step="0.1" required class="mt-2 min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/15" />
                </label>
                <label class="space-y-1 text-sm font-semibold text-[#1f2544]">
                    Lebar ruangan (meter)
                    <input data-floor-plan-room-width type="number" min="0.5" step="0.1" required class="mt-2 min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/15" />
                </label>
                <label class="space-y-1 text-sm font-semibold text-[#1f2544]">
                    Posisi pintu X (meter)
                    <input data-floor-plan-door-x type="number" min="0" step="0.1" class="mt-2 min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/15" />
                </label>
                <label class="space-y-1 text-sm font-semibold text-[#1f2544]">
                    Posisi pintu Y (meter)
                    <input data-floor-plan-door-y type="number" min="0" step="0.1" class="mt-2 min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/15" />
                </label>
                <label class="space-y-1 text-sm font-semibold text-[#1f2544]">
                    Lebar pintu (meter)
                    <input data-floor-plan-door-width type="number" min="0.5" max="3" step="0.1" class="mt-2 min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/15" />
                </label>
                <label class="space-y-1 text-sm font-semibold text-[#1f2544]">
                    Tinggi pintu (meter)
                    <input data-floor-plan-door-height type="number" min="1" max="4" step="0.1" class="mt-2 min-h-11 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/15" />
                </label>
                <label class="space-y-1 text-sm font-semibold text-[#1f2544] sm:col-span-2">
                    Posisi jendela (baris: x,y,lebar,tinggi, semua dalam meter)
                    <textarea data-floor-plan-windows rows="2" placeholder="2,0,1.2,1.2" class="mt-2 min-h-24 w-full rounded-xl border border-[#e7ebff] bg-white px-3 py-2 text-sm outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/15"></textarea>
                    <p class="text-xs font-normal text-[#7b8299]">Format: x,y,lebar,tinggi. Contoh: 2,0,1.2,1.2 (posisi x=2 meter, y=0 meter, lebar=1.2 m, tinggi=1.2 m).</p>
                </label>
                <label class="space-y-1 text-sm font-semibold text-[#1f2544] sm:col-span-2">
                    Area permanen (baris: jenis,x,y,lebar,tinggi, semua dalam meter)
                    <textarea data-floor-plan-permanent-areas rows="2" placeholder="storage,3,2,2,2&#10;gudang,4,3,1.5,1.5" class="mt-2 min-h-24 w-full rounded-xl border border-[#e7ebff] bg-white px-3 py-2 text-sm outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/15"></textarea>
                    <p class="text-xs font-normal text-[#7b8299]">Format: jenis,x,y,lebar,tinggi. Contoh: storage,3,2,2,2 (area penyimpanan mulai x=3 m, y=2 m, lebar=2 m, tinggi=2 m).</p>
                </label>
                <div class="flex items-end sm:col-span-2">
                    <button type="submit" data-floor-plan-generate class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#687EFF] px-5 text-sm font-semibold text-white transition hover:bg-[#596eea]">Generate Denah</button>
                </div>
            </form>
        </section>

        <div class="flex flex-wrap items-center gap-2">
            <button type="button" data-floor-plan-tab="2d" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-4 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]">Denah 2D</button>
            <button type="button" data-floor-plan-tab="3d" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-4 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]">Visualisasi 3D / Before &amp; After</button>
        </div>

        <div data-floor-plan-panel="2d">
            <section class="ni-panel p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold">Denah 2D</h2>
                        <p data-floor-plan-room-size class="mt-1 text-sm text-[#5e647f]">Belum ada denah yang dihasilkan.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" data-floor-plan-save class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#1F2544] px-4 text-sm font-semibold text-white transition hover:bg-[#191f3c]">Simpan Denah</button>
                        <button type="button" data-floor-plan-download-png class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-4 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]">Download PNG</button>
                        <button type="button" data-floor-plan-download-pdf class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-4 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]">Download PDF</button>
                        <button type="button" data-floor-plan-generate-again class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-4 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]">Generate Ulang</button>
                        <button type="button" data-floor-plan-edit class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-4 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]" aria-pressed="false">Edit Denah</button>
                        <button type="button" data-floor-plan-zoom-out class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-4 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]">-</button>
                        <span class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#eef3ff] px-4 text-sm font-semibold text-[#1f2544]" data-floor-plan-zoom-value>50%</span>
                        <button type="button" data-floor-plan-zoom-in class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-4 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]">+</button>
                        <button type="button" data-floor-plan-zoom-reset class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-4 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]">Reset</button>
                    </div>
                </div>
                <div class="mt-5 overflow-hidden rounded-[24px] border border-[#e7ebff] bg-[#f7f9ff] p-4">
                    <svg data-floor-plan-svg class="h-[420px] w-full" viewBox="0 0 0 0" role="img" aria-label="Denah 2D toko">
                        <rect x="0" y="0" width="10" height="10" fill="transparent"></rect>
                    </svg>
                </div>
                <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2">
                    <span class="text-xs font-semibold text-[#5e647f]">Legenda:</span>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold text-[#1f2544]"><span class="h-3 w-3 rounded-full" style="background-color:#687EFF"></span>Rak Dinding</span>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold text-[#1f2544]"><span class="h-3 w-3 rounded-full" style="background-color:#98E4FF"></span>Rak Tengah</span>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold text-[#1f2544]"><span class="h-3 w-3 rounded-full" style="background-color:#1F2544"></span>Meja Kasir</span>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold text-[#1f2544]"><span class="h-3 w-3 rounded-full" style="background-color:#F59E0B"></span>Pintu Masuk</span>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold text-[#1f2544]"><span class="h-3 w-3 rounded-full" style="background-color:#38BDF8"></span>Jendela</span>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold text-[#1f2544]"><span class="h-3 w-3 rounded-full" style="background-color:#64748B"></span>Gudang</span>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold text-[#1f2544]"><span class="h-3 w-3 rounded-full" style="background-color:#B6FFFA"></span>Area Penyimpanan</span>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold text-[#1f2544]"><span class="h-3 w-3 rounded-full" style="background-color:#D7E4FF"></span>Lorong Pelanggan</span>
                </div>
            </section>
        </div>

        <div data-floor-plan-panel="3d" class="hidden">
            <section class="ni-panel p-5">
                <h2 class="text-lg font-semibold">Visualisasi 3D / Before &amp; After</h2>
                <p class="mt-1 text-sm text-[#5e647f]">Sebelum: foto toko. Sesudah: denah 2D yang direkomendasikan.</p>
                <div class="mt-5 grid gap-4 lg:grid-cols-2">
                    <div class="rounded-[24px] border border-[#e7ebff] bg-[#f7f9ff] p-4">
                        <p class="text-sm font-semibold text-[#1f2544]">Before</p>
                        <div class="mt-3 flex h-56 items-center justify-center rounded-2xl border border-dashed border-[#c7d4f5] bg-white text-sm text-[#7b8299]">Foto toko sebelum</div>
                    </div>
                    <div class="rounded-[24px] border border-[#e7ebff] bg-[#f7f9ff] p-4">
                        <p class="text-sm font-semibold text-[#1f2544]">After</p>
                        <div class="mt-3 rounded-2xl border border-[#e7ebff] bg-white p-2">
                            <svg data-floor-plan-3d class="h-56 w-full" viewBox="0 0 200 200" role="img" aria-label="Visualisasi 3D denah toko"></svg>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <section class="ni-panel p-5">
            <h2 class="text-lg font-semibold">Objek denah</h2>
            <p class="mt-1 text-sm text-[#5e647f]">Pilih objek untuk menggeser, memutar, atau menghapusnya. Validasi akan mencegah objek di luar ruangan atau bertabrakan.</p>
            <div data-floor-plan-objects class="mt-5 flex flex-wrap gap-2"></div>
            <div data-floor-plan-selected class="mt-5 rounded-2xl border border-[#e7ebff] bg-[#f7f9ff] p-4">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="text-sm font-semibold text-[#1f2544]">Nama:</span>
                    <span data-floor-plan-selected-name class="text-sm text-[#1f2544]"></span>
                    <span class="text-sm font-semibold text-[#1f2544]">Jenis:</span>
                    <span data-floor-plan-selected-type class="text-sm text-[#1f2544]"></span>
                    <button type="button" data-object-rotate class="inline-flex min-h-10 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-3 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]">Putar 90°</button>
                    <button type="button" data-object-delete class="inline-flex min-h-10 items-center justify-center rounded-xl border border-rose-200 bg-white px-3 text-sm font-semibold text-rose-700 transition hover:bg-rose-50">Hapus</button>
                </div>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <label class="space-y-1 text-sm font-semibold text-[#1f2544]">X (meter)<input data-floor-plan-selected-x type="number" step="0.1" class="mt-2 min-h-10 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/15" /></label>
                    <label class="space-y-1 text-sm font-semibold text-[#1f2544]">Y (meter)<input data-floor-plan-selected-y type="number" step="0.1" class="mt-2 min-h-10 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/15" /></label>
                    <label class="space-y-1 text-sm font-semibold text-[#1f2544]">Lebar (meter)<input data-floor-plan-selected-width type="number" step="0.1" class="mt-2 min-h-10 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/15" /></label>
                    <label class="space-y-1 text-sm font-semibold text-[#1f2544]">Tinggi (meter)<input data-floor-plan-selected-height type="number" step="0.1" class="mt-2 min-h-10 w-full rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/15" /></label>
                </div>
            </div>
            <div class="mt-5 flex flex-wrap items-center gap-3">
                <label class="text-sm font-semibold text-[#1f2544]" for="data-floor-plan-add-type">Tambah objek</label>
                <select id="data-floor-plan-add-type" data-floor-plan-add-type class="min-h-11 rounded-xl border border-[#e7ebff] bg-white px-3 text-sm outline-none focus:border-[#687EFF] focus:ring-4 focus:ring-[#687EFF]/15">
                    <option value="wall_rack">Rak Dinding</option>
                    <option value="mid_rack">Rak Tengah</option>
                    <option value="cashier_desk">Meja Kasir</option>
                    <option value="stockroom">Gudang</option>
                    <option value="storage">Area Penyimpanan</option>
                    <option value="aisle">Lorong Pelanggan</option>
                </select>
                <button type="button" data-floor-plan-add class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#687EFF] px-4 text-sm font-semibold text-white transition hover:bg-[#596eea]">Tambah ke denah</button>
            </div>
        </section>
    </div>
@endsection
