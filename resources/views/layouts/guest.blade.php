<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>TOKENDE - Connexion</title>

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

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600;700;800&display=swap" rel="stylesheet">
</head>

<body class="font-sans antialiased">

    <!-- Theme clair / sombre -->
    <button
        type="button"
        data-theme-toggle
        onclick="tkToggleTheme()"
        aria-label="Passer en mode sombre"
        title="Mode sombre"
        class="
            fixed right-5 top-5 z-50
            flex h-11 w-11 items-center justify-center
            rounded-full border border-white/30
            bg-black/30 text-white
            backdrop-blur-sm
            transition hover:bg-black/50
        "
    >
        <i data-theme-icon class="fa-solid fa-moon"></i>
    </button>

    {{ $slot }}

    <x-confirm-modal />

<x-flash-modal />

</body>

</html>