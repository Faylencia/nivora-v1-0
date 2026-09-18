@extends('layouts.guest')

@section('title', 'Register - Nivora')

@section('content')
    <div class="mx-auto flex min-h-screen max-w-6xl items-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid w-full overflow-hidden rounded-[32px] border border-[#e7ebff] bg-white/90 shadow-[0_30px_80px_rgba(31,37,68,0.08)] backdrop-blur md:grid-cols-2">
            <div class="hidden bg-[linear-gradient(180deg,_#eef3ff_0%,_#f5fbff_100%)] p-10 md:flex md:flex-col md:justify-between">
                <div>
                    <div class="mb-8 flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-[#687EFF] to-[#98E4FF] text-lg font-bold text-white shadow-sm">N</div>
                        <div>
                            <div class="text-2xl font-semibold tracking-[-0.06em] text-[#1f2544]">NIVORA</div>
                        </div>
                    </div>
                    <h1 class="max-w-sm text-3xl font-semibold leading-tight tracking-[-0.04em] text-[#1f2544]">Daftarkan bisnis Anda dan mulai hadir di hati pelanggan.</h1>
                </div>
                <div class="rounded-2xl border border-[#dfe7ff] bg-[#eef3ff] p-5 shadow-sm">
                    <div class="mb-3 flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-sm font-semibold text-[#1f2544]">NG</div>
                        <div>
                            <div class="font-semibold text-[#1f2544]">Nusa Garden</div>
                            <div class="text-xs text-[#5e647f]">UMKM Taman Bunga</div>
                        </div>
                    </div>
                    <p class="text-sm leading-6 text-[#5e647f]">“Dengan Nivora, kami lebih mudah menjangkau pelanggan setempat dan meningkatkan penjualan di area sekitar.”</p>
                </div>
            </div>

            <div class="p-6 sm:p-8 lg:p-12">
                <div class="mb-8 text-center md:text-left">
                    <div class="mb-5 flex items-center justify-center gap-3 md:justify-start">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-[#687EFF] to-[#98E4FF] text-lg font-bold text-white shadow-sm">N</div>
                        <div>
                            <div class="text-2xl font-semibold tracking-[-0.06em] text-[#1f2544]">NIVORA</div>
                        </div>
                    </div>
                    <h2 class="text-3xl font-semibold tracking-[-0.05em] text-[#1f2544]">Buat Akun</h2>
                    <p class="mt-2 text-sm text-[#5e647f]">Mari bangun bisnis lokal yang lebih berkembang.</p>
                </div>

                <form class="space-y-5">
                    <x-input label="Name" name="name" placeholder="Nama bisnis atau pemilik" required />
                    <x-input label="Email" name="email" type="email" placeholder="nama@email.com" required />
                    <x-input label="Phone" name="phone" type="tel" placeholder="0812-3456-7890" required />
                    <x-input label="Password" name="password" type="password" placeholder="Minimal 8 karakter" required />
                    <x-input label="Confirm password" name="password_confirmation" type="password" placeholder="Ulangi password" required />

                    <x-button type="primary" class="w-full" size="lg">Register</x-button>
                </form>

                <p class="mt-6 text-center text-sm text-[#5e647f]">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="font-semibold text-[#1f2544] hover:text-[#687EFF]">Login</a>
                </p>
            </div>
        </div>
    </div>
@endsection
