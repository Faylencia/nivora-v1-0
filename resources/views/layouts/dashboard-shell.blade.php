@props(['role' => 'owner'])

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
    <body class="ni-page text-[#1f2544] antialiased" data-role-dashboard="{{ $role }}">
        <div class="min-h-screen">
            @include('components.dashboard-sidebar', ['role' => $role])

            <div class="min-h-screen lg:pl-72" data-dashboard-content>
                @include('components.navbar', ['dashboard' => true, 'role' => $role])

                <main class="px-4 pb-12 pt-6 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-375">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
