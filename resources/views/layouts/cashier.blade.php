<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Cashier - Nivora')</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="ni-page text-[#1f2544] antialiased">
        <div class="min-h-screen">
            @include('components.cashier-sidebar')

            <div class="lg:pl-72">
                <header class="sticky top-0 z-40 border-b border-[#e7ebff] bg-white/80 backdrop-blur-xl">
                    <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center gap-3">
                            <button type="button" data-sidebar-toggle class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[#e7ebff] bg-white text-[#1f2544] shadow-sm lg:hidden" aria-label="Buka menu kasir">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" /></svg>
                            </button>
                            <div>
                                <p class="text-sm font-semibold text-[#1f2544]">NIVORA</p>
                                <p class="text-[10px] uppercase tracking-[0.14em] text-[#5e647f]">Cashier Workspace</p>
                            </div>
                        </div>
                        <div class="hidden items-center gap-3 text-sm text-[#5e647f] sm:flex">
                            <span>Bandung Center</span>
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            <span>Kasir aktif</span>
                        </div>
                    </div>
                </header>

                <main class="px-4 pb-12 pt-6 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-[1500px]">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
