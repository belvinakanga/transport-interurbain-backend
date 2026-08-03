<x-layouts.admin>

<div class="p-8">

    <h1 class="text-4xl font-bold mb-8">
        📊 Rapports & Statistiques
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Utilisateurs -->
        <div class="bg-blue-500 text-white rounded-xl shadow-lg p-6">
            <h2 class="text-lg font-semibold">👥 Utilisateurs</h2>
            <p class="text-4xl font-bold mt-4">{{ $users }}</p>
        </div>

        <!-- Agences -->
        <div class="bg-green-500 text-white rounded-xl shadow-lg p-6">
            <h2 class="text-lg font-semibold">🏢 Agences</h2>
            <p class="text-4xl font-bold mt-4">{{ $agences }}</p>
        </div>

        <!-- Trajets -->
        <div class="bg-yellow-500 text-white rounded-xl shadow-lg p-6">
            <h2 class="text-lg font-semibold">🚌 Trajets</h2>
            <p class="text-4xl font-bold mt-4">{{ $trajets }}</p>
        </div>

        <!-- Voyageurs -->
        <div class="bg-purple-500 text-white rounded-xl shadow-lg p-6">
            <h2 class="text-lg font-semibold">🧍 Voyageurs</h2>
            <p class="text-4xl font-bold mt-4">{{ $voyageurs }}</p>
        </div>

        <!-- Réservations -->
        <div class="bg-red-500 text-white rounded-xl shadow-lg p-6">
            <h2 class="text-lg font-semibold">🎫 Réservations</h2>
            <p class="text-4xl font-bold mt-4">{{ $reservations }}</p>
        </div>

        <!-- Billets -->
        <div class="bg-indigo-500 text-white rounded-xl shadow-lg p-6">
            <h2 class="text-lg font-semibold">🎟️ Billets</h2>
            <p class="text-4xl font-bold mt-4">{{ $billets }}</p>
        </div>

        <!-- Paiements -->
        <div class="bg-pink-500 text-white rounded-xl shadow-lg p-6">
            <h2 class="text-lg font-semibold">💳 Paiements</h2>
            <p class="text-4xl font-bold mt-4">{{ $paiements }}</p>
        </div>

        <!-- Avis -->
        <div class="bg-orange-500 text-white rounded-xl shadow-lg p-6">
            <h2 class="text-lg font-semibold">⭐ Avis</h2>
            <p class="text-4xl font-bold mt-4">{{ $avis }}</p>
        </div>

        <!-- Achats -->
        <div class="bg-teal-500 text-white rounded-xl shadow-lg p-6">
            <h2 class="text-lg font-semibold">🛒 Achats</h2>
            <p class="text-4xl font-bold mt-4">{{ $achats }}</p>
        </div>

        <!-- Abonnements -->
        <div class="bg-gray-700 text-white rounded-xl shadow-lg p-6">
            <h2 class="text-lg font-semibold">💼 Abonnements</h2>
            <p class="text-4xl font-bold mt-4">{{ $abonnements }}</p>
        </div>

    </div>

</div>

</x-layouts.admin>