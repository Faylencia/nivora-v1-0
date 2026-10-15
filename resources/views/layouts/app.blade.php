<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Nivora')</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="preconnect" href="https://cdn.boxicons.com">
        <link href="https://cdn.boxicons.com/3.0.8/fonts/basic/boxicons.min.css" rel="stylesheet">
        <script>
            try {
                const savedThemePreference = localStorage.getItem('nivora.theme') || 'light';
                const themePreference = ['light', 'dark', 'system'].includes(savedThemePreference) ? savedThemePreference : 'light';
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.dataset.theme = themePreference === 'system' ? (prefersDark ? 'dark' : 'light') : themePreference;
                document.documentElement.dataset.themePreference = themePreference;
            } catch {
                document.documentElement.dataset.theme = 'light';
                document.documentElement.dataset.themePreference = 'light';
            }
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="ni-page text-[#1f2544] antialiased" data-public-layout>
        <div class="min-h-screen">
            <div data-member-public-sidebar class="hidden">
                @include('components.dashboard-sidebar', ['role' => 'member'])
            </div>

            <div data-public-content class="min-h-screen">
                @include('components.navbar')

                <main class="overflow-x-hidden">
                    @yield('content')
                </main>

                @include('components.footer')
            </div>
        </div>
    </body>
</html>
