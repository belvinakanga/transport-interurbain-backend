<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>InterGO Congo | Administration</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        crossorigin="anonymous"
        referrerpolicy="no-referrer" />
</head>

<body class="bg-gray-100">

<div class="flex h-screen overflow-hidden">

    <!-- ================= SIDEBAR ================= -->

    <aside class="w-72 bg-gray-900 text-white flex flex-col shadow-2xl">

        <!-- Logo -->

        <div class="h-24 flex items-center justify-center border-b border-gray-800">

            <div class="flex items-center gap-4">

                <div class="w-14 h-14 rounded-2xl bg-amber-400 flex items-center justify-center">

                    <i class="fa-solid fa-bus text-2xl text-gray-900"></i>

                </div>

                <div>

                    <h1 class="text-2xl font-extrabold tracking-wide">
                        INTERGO
                    </h1>

                    <p class="text-sm text-gray-400">
                        Congo Administration
                    </p>

                </div>

            </div>

        </div>

        <!-- Navigation -->

        <nav class="flex-1 px-5 py-8 overflow-y-auto">

            <p class="text-xs uppercase tracking-widest text-gray-500 mb-4">
                Navigation
            </p>

            <!-- Dashboard -->

            <a href="{{ url('/admin') }}"
                class="flex items-center gap-4 px-5 py-4 rounded-2xl mb-2 transition-all duration-300
                {{ request()->is('admin') ? 'bg-amber-400 text-gray-900 font-semibold shadow-lg' : 'hover:bg-gray-800 text-gray-300' }}">

                <i class="fa-solid fa-chart-line text-lg"></i>

                <span>Dashboard</span>

            </a>

            <!-- Agences -->

            <a href="{{ url('/admin/agences') }}"
                class="flex items-center gap-4 px-5 py-4 rounded-2xl mb-2 transition-all duration-300
                {{ request()->is('admin/agences*') ? 'bg-amber-400 text-gray-900 font-semibold shadow-lg' : 'hover:bg-gray-800 text-gray-300' }}">

                <i class="fa-solid fa-building"></i>

                <span>Agences</span>

            </a>

            <!-- Utilisateurs -->

            <a href="{{ url('/admin/voyageurs') }}"
                class="flex items-center gap-4 px-5 py-4 rounded-2xl mb-2 transition-all duration-300
                {{ request()->is('admin/voyageurs*') ? 'bg-amber-400 text-gray-900 font-semibold shadow-lg' : 'hover:bg-gray-800 text-gray-300' }}">

                <i class="fa-solid fa-users"></i>

                <span>Utilisateurs</span>

            </a>

            <!-- Trajets -->

            <a href="{{ url('/admin/trajets') }}"
                class="flex items-center gap-4 px-5 py-4 rounded-2xl mb-2 transition-all duration-300
                {{ request()->is('admin/trajets*') ? 'bg-amber-400 text-gray-900 font-semibold shadow-lg' : 'hover:bg-gray-800 text-gray-300' }}">

                <i class="fa-solid fa-route"></i>

                <span>Trajets</span>

            </a>

            <!-- Réservations -->

            <a href="{{ url('/admin/reservations') }}"
                class="flex items-center gap-4 px-5 py-4 rounded-2xl mb-2 transition-all duration-300
                {{ request()->is('admin/reservations*') ? 'bg-amber-400 text-gray-900 font-semibold shadow-lg' : 'hover:bg-gray-800 text-gray-300' }}">

                <i class="fa-solid fa-calendar-check"></i>

                <span>Réservations</span>

            </a>
                        <!-- Paiements -->

                        <a href="{{ url('/admin/paiements') }}"
                class="flex items-center gap-4 px-5 py-4 rounded-2xl mb-2 transition-all duration-300
                {{ request()->is('admin/paiements*') ? 'bg-amber-400 text-gray-900 font-semibold shadow-lg' : 'hover:bg-gray-800 text-gray-300' }}">

                <i class="fa-solid fa-credit-card"></i>

                <span>Paiements</span>

            </a>
            <a href="{{ url('/admin/abonnements') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-yellow-500 hover:text-black transition {{ request()->is('admin/abonnements*') ? 'bg-yellow-500 text-black' : '' }}">

    <i class="fas fa-id-card"></i>
    <span>Abonnements</span>
</a>

            <!-- Billets -->

            <a href="{{ url('/admin/billets') }}"
                class="flex items-center gap-4 px-5 py-4 rounded-2xl mb-2 transition-all duration-300
                {{ request()->is('admin/billets*') ? 'bg-amber-400 text-gray-900 font-semibold shadow-lg' : 'hover:bg-gray-800 text-gray-300' }}">

                <i class="fa-solid fa-ticket"></i>

                <span>Billets</span>

            </a>

            <!-- Avis -->

            <a href="{{ url('/admin/avis') }}"
                class="flex items-center gap-4 px-5 py-4 rounded-2xl mb-2 transition-all duration-300
                {{ request()->is('admin/avis*') ? 'bg-amber-400 text-gray-900 font-semibold shadow-lg' : 'hover:bg-gray-800 text-gray-300' }}">

                <i class="fa-solid fa-star"></i>

                <span>Avis</span>

            </a>

            <!-- Profil -->

            <a href="{{ route('profile.edit') }}"
                class="flex items-center gap-4 px-5 py-4 rounded-2xl mb-2 transition-all duration-300
                {{ request()->is('profile*') ? 'bg-amber-400 text-gray-900 font-semibold shadow-lg' : 'hover:bg-gray-800 text-gray-300' }}">

                <i class="fa-solid fa-user"></i>

                <span>Profil</span>

            </a>

        </nav>

        <!-- Déconnexion -->

        <div class="p-5 border-t border-gray-800">

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button
                    type="submit"
                    class="w-full flex items-center justify-center gap-3 bg-red-600 hover:bg-red-700 py-4 rounded-2xl font-semibold transition-all duration-300">

                    <i class="fa-solid fa-right-from-bracket"></i>

                    Déconnexion

                </button>

            </form>

        </div>

    </aside>

    <!-- ==========================
            CONTENU PRINCIPAL
    ========================== -->

    <div class="flex-1 flex flex-col overflow-hidden">
                <!-- ================= HEADER ================= -->

                <header class="bg-white border-b border-gray-200 shadow-sm px-8 py-5">

<div class="flex items-center justify-between">

    <!-- Titre + Recherche -->

    <div class="flex items-center gap-6">

        <h2 class="text-3xl font-bold text-gray-800">
            Tableau de bord
        </h2>

        <div class="relative hidden md:block">

            <input
                type="text"
                placeholder="Rechercher..."
                class="w-96 pl-12 pr-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-amber-400 focus:outline-none">

            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>

        </div>

    </div>

    <!-- Actions -->

    <div class="flex items-center gap-6">

        <!-- Notifications -->

        <button
            class="relative w-12 h-12 rounded-full bg-gray-100 hover:bg-gray-200 transition flex items-center justify-center">

            <i class="fa-solid fa-bell text-gray-700 text-lg"></i>

            <span
                class="absolute top-2 right-2 w-2.5 h-2.5 bg-red-500 rounded-full"></span>

        </button>

        <!-- Profil -->

        <div class="flex items-center gap-3">

            <div
                class="w-12 h-12 rounded-full bg-amber-400 flex items-center justify-center">

                <i class="fa-solid fa-user text-gray-900"></i>

            </div>

            <div class="hidden md:block">

                <h3 class="font-semibold text-gray-800">
                    {{ Auth::user()->name }}
                </h3>

                <p class="text-sm text-gray-500">
                    Administrateur
                </p>

            </div>

        </div>

    </div>

</div>

</header>

<!-- ================= CONTENU ================= -->

<main class="flex-1 overflow-y-auto bg-gray-100 p-8">

{{ $slot }}

</main>

</div>

</div>

</body>

</html>