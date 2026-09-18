@extends('layouts.guest')

@section('title', 'Forgot Password - Nivora')

@section('content')
    <div class="mx-auto flex min-h-screen max-w-md items-center px-4 py-12 sm:px-6">
        <div class="w-full rounded-[28px] border border-[#e7ebff] bg-white/90 p-6 shadow-[0_30px_80px_rgba(31,37,68,0.08)] backdrop-blur sm:p-8">
            <div class="mb-8 text-center">
                <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-[#687EFF] to-[#98E4FF] text-xl font-bold text-white shadow-sm">N</div>
                <h2 class="text-3xl font-semibold tracking-[-0.05em] text-[#1f2544]">Reset Password</h2>
                <p class="mt-2 text-sm text-[#5e647f]">Masukkan email Anda untuk menerima link reset password.</p>
            </div>

            <form class="space-y-5">
                <x-input label="Email" name="email" type="email" placeholder="nama@email.com" required />
                <x-button type="primary" class="w-full" size="lg">Send reset link</x-button>
            </form>

            <p class="mt-6 text-center text-sm text-[#5e647f]">
                <a href="{{ route('login') }}" class="font-semibold text-[#1f2544] hover:text-[#687EFF]">Kembali ke login</a>
            </p>
        </div>
    </div>
@endsection
