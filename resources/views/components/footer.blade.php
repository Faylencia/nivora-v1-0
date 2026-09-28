<footer class="border-t border-[#e7ebff] bg-white/55">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-8 md:grid-cols-4">
            <div class="md:col-span-2">
                <div class="mb-4 flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-linear-to-br from-[#687EFF] to-[#98E4FF] text-lg font-bold text-white shadow-sm">N</div>
                    <div>
                        <div class="text-xl font-semibold tracking-[-0.04em] text-[#1f2544]">NIVORA</div>
                    </div>
                </div>
                <p class="max-w-md text-sm leading-6 text-[#5e647f]">
                    Platform untuk menemukan dan mengeksplorasi UMKM lokal dengan informasi yang jelas, cepat, dan akurat.
                </p>
            </div>

            <div>
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-[0.2em] text-[#5e647f]">Navigasi</h3>
                <ul class="space-y-3 text-sm text-[#5e647f]">
                    <li><a href="{{ route('home') }}" class="hover:text-[#1f2544]">Home</a></li>
                    <li><a href="{{ route('explore') }}" class="hover:text-[#1f2544]">Explore</a></li>
                    <li><a href="#categories" class="hover:text-[#1f2544]">Kategori</a></li>
                </ul>
            </div>

            <div>
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-[0.2em] text-[#5e647f]">Lainnya</h3>
                <ul class="space-y-3 text-sm text-[#5e647f]">
                    <li><a href="{{ route('login') }}" class="hover:text-[#1f2544]">Login</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-[#1f2544]">Register</a></li>
                    <li><a href="{{ route('owner.dashboard') }}" class="hover:text-[#1f2544]">Dashboard</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-10 flex flex-col gap-3 border-t border-[#e7ebff] pt-6 text-sm text-[#5e647f] sm:flex-row sm:items-center sm:justify-between">
            <p>© 2026 Nivora. All rights reserved.</p>
            <p>Made for local businesses.</p>
        </div>
    </div>
</footer>
