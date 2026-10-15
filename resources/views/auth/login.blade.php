@extends('layouts.guest')

@section('title', 'Login - Nivora')

@section('content')
    <div class="mx-auto flex min-h-screen max-w-6xl items-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid w-full overflow-hidden rounded-[32px] border border-[#e7ebff] bg-white/90 shadow-[0_30px_80px_rgba(31,37,68,0.08)] backdrop-blur md:grid-cols-2">
            <div class="hidden bg-[linear-gradient(180deg,_#eef3ff_0%,_#f5fbff_100%)] p-10 text-[#1f2544] md:flex md:flex-col md:justify-between">
                <div>
                    <div class="mb-8 flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-[#687EFF] to-[#98E4FF] text-lg font-bold text-white shadow-sm">N</div>
                        <div>
                            <div class="text-2xl font-semibold tracking-[-0.06em]">NIVORA</div>
                        </div>
                    </div>
                    <h1 class="max-w-sm text-3xl font-semibold leading-tight tracking-[-0.04em]">Temukan UMKM lokal yang tepat untuk bisnis Anda.</h1>
                </div>
                <div class="rounded-2xl border border-[#dfe7ff] bg-[#eef3ff] p-5 shadow-sm">
                    <p class="font-semibold text-[#1f2544]">Satu halaman masuk untuk semua peran</p>
                    <p class="mt-2 text-sm leading-6 text-[#5e647f]">Nivora mendeteksi akses Super Admin, Owner, Kasir, atau Member dari akun Anda dan membuka dashboard yang sesuai.</p>
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
                    <h2 class="text-3xl font-semibold tracking-[-0.05em] text-[#1f2544]">Masuk</h2>
                    <p class="mt-2 text-sm text-[#5e647f]">Selamat datang kembali di dashboard bisnis Anda.</p>
                </div>

                <form class="space-y-5" data-auth-form="login">
                    <div data-auth-message class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700" role="alert"></div>
                    <x-input label="Email" name="email" type="email" placeholder="nama@email.com" required autocomplete="email" />
                    <x-input label="Password" name="password" type="password" placeholder="Masukkan password" required autocomplete="current-password" />

                    <div class="flex items-center justify-between gap-3 text-sm">
                        <label class="inline-flex items-center gap-2 text-[#5e647f]">
                            <input type="checkbox" class="h-4 w-4 rounded border-[#dfe7ff] text-[#687EFF] focus:ring-[#dfe7ff]" />
                            <span>Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="font-medium text-[#1f2544] hover:text-[#687EFF]">Forgot password?</a>
                    </div>

                    <x-button type="primary" class="w-full" size="lg" button-type="submit">Masuk</x-button>
                </form>

                <p class="mt-6 text-center text-sm text-[#5e647f]">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="font-semibold text-[#1f2544] hover:text-[#687EFF]">Register</a>
                </p>
            </div>
        </div>
    </div>
@endsection
