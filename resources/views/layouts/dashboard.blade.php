<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Dashboard Nivora')</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="ni-page text-[#1f2544] antialiased">
        <div class="min-h-screen">
            @include('components.sidebar')

            <div class="lg:pl-72">
                @include('components.navbar', ['dashboard' => true])

                <main class="px-4 pb-12 pt-6 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-[1500px]">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
