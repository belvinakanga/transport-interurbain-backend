<x-layouts.admin>

<div class="min-h-screen bg-gray-100 p-8">

    <!-- ================= EN-TÊTE ================= -->

    <div class="bg-white rounded-2xl shadow-sm p-8 border-l-4 border-yellow-500 mb-8">

        <div class="flex flex-col lg:flex-row justify-between items-center">

            <div>

                <h1 class="text-3xl font-bold text-gray-800">
                    👋 Tableau de bord
                </h1>

                <p class="text-gray-500 mt-2">
                    Bienvenue sur
                    <span class="font-semibold text-yellow-600">
                        InterGO Congo
                    </span>
                    <br>
                    Plateforme de gestion du transport interurbain.
                </p>

            </div>

            <div class="mt-6 lg:mt-0">

                <div class="bg-yellow-500 text-white px-6 py-4 rounded-xl shadow">

                    <p class="text-sm">
                        Aujourd'hui
                    </p>

                    <h2 class="text-2xl font-bold">
                        {{ now()->format('d/m/Y') }}
                    </h2>

                </div>

            </div>

        </div>

    </div>

    <!-- ================= STATISTIQUES ================= -->

    <h2 class="text-2xl font-bold text-gray-700 mb-6">
        📊 Vue d'ensemble
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

        <!-- Utilisateurs -->

        <div class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-lg transition">

            <div class="flex justify-between items-center">

                <div>

                    <p class="text-gray-500">
                        Utilisateurs
                    </p>

                    <h2 class="text-4xl font-bold text-gray-800 mt-3">
                        {{ $nombreUtilisateurs }}
                    </h2>

                </div>

                <div class="w-16 h-16 rounded-full bg-yellow-100 flex items-center justify-center text-3xl">

                    👥

                </div>

            </div>

        </div>

        <!-- Agences -->

        <div class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-lg transition">

            <div class="flex justify-between items-center">

                <div>

                    <p class="text-gray-500">
                        Agences
                    </p>

                    <h2 class="text-4xl font-bold text-gray-800 mt-3">
                        {{ $nombreAgences }}
                    </h2>

                </div>

                <div class="w-16 h-16 rounded-full bg-yellow-100 flex items-center justify-center text-3xl">

                    🏢

                </div>

            </div>

        </div>

        <!-- Trajets -->

        <div class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-lg transition">

            <div class="flex justify-between items-center">

                <div>

                    <p class="text-gray-500">
                        Trajets
                    </p>

                    <h2 class="text-4xl font-bold text-gray-800 mt-3">
                        {{ $nombreTrajets }}
                    </h2>

                </div>

                <div class="w-16 h-16 rounded-full bg-yellow-100 flex items-center justify-center text-3xl">

                    🚌

                </div>

            </div>

        </div>

        <!-- Revenu -->

        <div class="bg-gray-800 rounded-2xl shadow-sm p-6 text-white">

            <p class="text-gray-300">
                Revenu total
            </p>

            <h2 class="text-3xl font-bold text-yellow-400 mt-3">
                {{ number_format($revenuTotal,0,',',' ') }} FCFA
            </h2>

        </div>

    </div>
        <!-- ================= STATISTIQUES SECONDAIRES ================= -->

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-10">

<!-- Réservations -->
<div class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-lg transition">

    <div class="flex justify-between items-center">

        <div>

            <p class="text-gray-500">
                Réservations
            </p>

            <h2 class="text-4xl font-bold text-gray-800 mt-3">
                {{ $nombreReservations }}
            </h2>

        </div>

        <div class="w-16 h-16 rounded-full bg-yellow-100 flex items-center justify-center text-3xl">
            📋
        </div>

    </div>

</div>

<!-- Paiements -->
<div class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-lg transition">

    <div class="flex justify-between items-center">

        <div>

            <p class="text-gray-500">
                Paiements
            </p>

            <h2 class="text-4xl font-bold text-gray-800 mt-3">
                {{ $nombrePaiements }}
            </h2>

        </div>

        <div class="w-16 h-16 rounded-full bg-yellow-100 flex items-center justify-center text-3xl">
            💳
        </div>

    </div>

</div>

<!-- Billets -->
<div class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-lg transition">

    <div class="flex justify-between items-center">

        <div>

            <p class="text-gray-500">
                Billets
            </p>

            <h2 class="text-4xl font-bold text-gray-800 mt-3">
                {{ $nombreBillets }}
            </h2>

        </div>

        <div class="w-16 h-16 rounded-full bg-yellow-100 flex items-center justify-center text-3xl">
            🎫
        </div>

    </div>

</div>

<!-- Avis -->
<div class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-lg transition">

    <div class="flex justify-between items-center">

        <div>

            <p class="text-gray-500">
                Avis
            </p>

            <h2 class="text-4xl font-bold text-gray-800 mt-3">
                {{ $nombreAvis }}
            </h2>

        </div>

        <div class="w-16 h-16 rounded-full bg-yellow-100 flex items-center justify-center text-3xl">
            ⭐
        </div>

    </div>

</div>

</div>

<!-- ================= ACTIVITÉ + ACCÈS RAPIDE ================= -->

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">

<!-- Activité -->

<div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">

    <h2 class="text-xl font-bold text-gray-700 mb-6">
        📅 Activité du jour
    </h2>

    <div class="space-y-5">

        <div class="flex justify-between items-center border-b pb-4">

            <div>

                <h3 class="font-semibold text-gray-700">
                    Réservations
                </h3>

                <p class="text-sm text-gray-500">
                    Nombre de réservations effectuées aujourd'hui
                </p>

            </div>

            <span class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded-full font-bold">
                {{ $reservationsAujourdhui }}
            </span>

        </div>

        <div class="flex justify-between items-center border-b pb-4">

            <div>

                <h3 class="font-semibold text-gray-700">
                    Paiements
                </h3>

                <p class="text-sm text-gray-500">
                    Paiements enregistrés aujourd'hui
                </p>

            </div>

            <span class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded-full font-bold">
                {{ $paiementsAujourdhui }}
            </span>

        </div>

        <div class="flex justify-between items-center">

            <div>

                <h3 class="font-semibold text-gray-700">
                    Billets générés
                </h3>

                <p class="text-sm text-gray-500">
                    Billets créés aujourd'hui
                </p>

            </div>

            <span class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded-full font-bold">
                {{ $billetsAujourdhui }}
            </span>

        </div>

    </div>

</div>

<!-- Accès rapide -->

<div class="bg-white rounded-2xl shadow-sm p-6">

    <h2 class="text-xl font-bold text-gray-700 mb-6">
        ⚡ Accès rapide
    </h2>

    <div class="space-y-4">

        <a href="{{ url('/admin/agences') }}"
           class="flex items-center justify-between bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl p-4 transition">
            <span>🏢 Gérer les agences</span>
            ➜
        </a>

        <a href="{{ url('/admin/trajets') }}"
           class="flex items-center justify-between bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl p-4 transition">
            <span>🚌 Gérer les trajets</span>
            ➜
        </a>

        <a href="{{ url('/admin/reservations') }}"
           class="flex items-center justify-between bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl p-4 transition">
            <span>📋 Réservations</span>
            ➜
        </a>

        <a href="{{ url('/admin/paiements') }}"
           class="flex items-center justify-between bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl p-4 transition">
            <span>💳 Paiements</span>
            ➜
        </a>

    </div>

</div>

</div>
    <!-- ========================= -->
    <!-- RÉSUMÉ + INFORMATIONS -->
    <!-- ========================= -->

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        <!-- Résumé -->

        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">

            <h2 class="text-xl font-bold text-gray-700 mb-6">
                📌 Résumé de la plateforme
            </h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">

                <div class="text-center">
                    <div class="text-4xl mb-2">🛒</div>
                    <h3 class="text-gray-500">Achats</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        {{ $nombreAchats }}
                    </p>
                </div>

                <div class="text-center">
                    <div class="text-4xl mb-2">🎫</div>
                    <h3 class="text-gray-500">Billets</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        {{ $nombreBillets }}
                    </p>
                </div>

                <div class="text-center">
                    <div class="text-4xl mb-2">⭐</div>
                    <h3 class="text-gray-500">Avis</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        {{ $nombreAvis }}
                    </p>
                </div>

                <div class="text-center">
                    <div class="text-4xl mb-2">💳</div>
                    <h3 class="text-gray-500">Paiements</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        {{ $nombrePaiements }}
                    </p>
                </div>

            </div>

        </div>

        <!-- Informations -->

        <div class="bg-gray-800 rounded-2xl shadow-sm p-6 text-white">

            <h2 class="text-xl font-bold mb-6">
                ℹ️ Informations
            </h2>

            <div class="space-y-4">

                <div class="flex justify-between">
                    <span>Application</span>
                    <strong class="text-yellow-400">InterGO Congo</strong>
                </div>

                <div class="flex justify-between">
                    <span>Version</span>
                    <strong>1.0</strong>
                </div>

                <div class="flex justify-between">
                    <span>Statut</span>
                    <span class="bg-green-500 px-3 py-1 rounded-full text-sm">
                        En ligne
                    </span>
                </div>

                <div class="flex justify-between">
                    <span>Interface</span>
                    <strong>Administration</strong>
                </div>

            </div>

        </div>

    </div>

    <!-- ========================= -->
    <!-- GRAPHIQUES -->
    <!-- ========================= -->

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">

        <!-- Graphique principal -->

        <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm p-6">

            <div class="flex justify-between items-center mb-4">

                <div>

                    <h2 class="text-xl font-bold text-gray-800">
                        Évolution des réservations
                    </h2>

                    <p class="text-sm text-gray-500">
                        Activité de la plateforme
                    </p>

                </div>

                <span class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded-full text-sm font-semibold">
                    Cette année
                </span>

            </div>

            <div class="h-72">
                <canvas id="reservationChart"></canvas>
            </div>

        </div>

        <!-- Répartition -->

        <div class="bg-white rounded-2xl shadow-sm p-6">

            <h2 class="text-xl font-bold text-gray-800 mb-4">
                Répartition
            </h2>

            <div class="h-56 flex items-center justify-center">

                <canvas id="repartitionChart"></canvas>

            </div>

            <div class="mt-5 space-y-3">

                <div class="flex justify-between">
                    <span class="text-gray-600">Billets</span>
                    <strong>{{ $nombreBillets }}</strong>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-600">Paiements</span>
                    <strong>{{ $nombrePaiements }}</strong>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-600">Avis</span>
                    <strong>{{ $nombreAvis }}</strong>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-600">Réservations</span>
                    <strong>{{ $nombreReservations }}</strong>
                </div>

            </div>

        </div>

    </div>
        <!-- ========================= -->
    <!-- TABLEAUX -->
    <!-- ========================= -->

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">

        <!-- Dernières réservations -->

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center">

                <h2 class="text-xl font-bold text-gray-800">
                    Dernières réservations
                </h2>

                <a href="{{ url('/admin/reservations') }}"
                   class="text-yellow-600 hover:text-yellow-700 font-semibold">
                    Voir tout →
                </a>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">
                                Voyageur
                            </th>

                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">
                                Trajet
                            </th>

                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">
                                Date
                            </th>

                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">
                                Statut
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse($dernieresReservations as $reservation)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4">
                                    {{ $reservation->voyageur->nom ?? '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $reservation->trajet->ville_depart ?? '-' }}
                                    →
                                    {{ $reservation->trajet->ville_arrivee ?? '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $reservation->created_at->format('d/m/Y') }}
                                </td>

                                <td class="px-6 py-4 text-center">

                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">

                                        Confirmée

                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4" class="text-center py-8 text-gray-500">
                                    Aucune réservation.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        <!-- Derniers paiements -->

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center">

                <h2 class="text-xl font-bold text-gray-800">
                    Derniers paiements
                </h2>

                <a href="{{ url('/admin/paiements') }}"
                   class="text-yellow-600 hover:text-yellow-700 font-semibold">
                    Voir tout →
                </a>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">
                                Client
                            </th>

                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">
                                Montant
                            </th>

                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">
                                Date
                            </th>

                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">
                                Statut
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse($derniersPaiements as $paiement)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4">
                                    {{ $paiement->reservation->voyageur->nom ?? '-' }}
                                </td>

                                <td class="px-6 py-4 font-semibold text-green-600">
                                    {{ number_format($paiement->montant,0,',',' ') }} FCFA
                                </td>

                                <td class="px-6 py-4">
                                    {{ $paiement->created_at->format('d/m/Y') }}
                                </td>

                                <td class="px-6 py-4 text-center">

                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">

                                        Payé

                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4" class="text-center py-8 text-gray-500">
                                    Aucun paiement.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <!-- ========================= -->
    <!-- FOOTER -->
    <!-- ========================= -->

    <div class="mt-10 text-center text-gray-500 text-sm">

        © {{ date('Y') }}

        <span class="font-semibold text-yellow-600">
            InterGO Congo
        </span>

        — Plateforme de gestion du transport interurbain.

    </div>

</div>

</x-layouts.admin>