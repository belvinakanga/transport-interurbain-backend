<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <script>
            (function () {
                try {
                    var t = localStorage.getItem('tk-theme');
                    if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                        document.documentElement.classList.add('dark');
                    }
                } catch (e) {}

                window.tkToggleTheme = function () {
                    var dark = document.documentElement.classList.toggle('dark');
                    try { localStorage.setItem('tk-theme', dark ? 'dark' : 'light'); } catch (e) {}
                    window.tkSyncTheme();
                };

                window.tkSyncTheme = function () {
                    var dark = document.documentElement.classList.contains('dark');
                    document.querySelectorAll('[data-theme-icon]').forEach(function (el) {
                        el.className = 'fa-solid ' + (dark ? 'fa-sun' : 'fa-moon');
                    });
                    document.querySelectorAll('[data-theme-toggle]').forEach(function (el) {
                        el.setAttribute('aria-label', dark ? 'Passer en mode clair' : 'Passer en mode sombre');
                        el.setAttribute('title', dark ? 'Mode clair' : 'Mode sombre');
                    });
                };

                document.addEventListener('DOMContentLoaded', window.tkSyncTheme);
            })();
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <x-confirm-modal />

<x-flash-modal />
    </body>
</html>
