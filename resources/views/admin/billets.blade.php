<x-layouts.admin>

<div class="py-12">

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Titre -->

        <div class="flex justify-between items-center mb-6">

            <h1 class="text-3xl font-bold text-slate-800">
                🎟 Gestion des billets
            </h1>

        </div>

        <!-- Barre de recherche -->

        <div class="bg-white rounded-xl shadow p-6 mb-6">

            <form action="/admin/billets" method="GET">

                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                    <!-- Recherche -->

                    <input
                        type="text"
                        name="recherche"
                        value="{{ request('recherche') }}"
                        placeholder="🔍 Numéro de billet ou voyageur..."
                        class="border rounded-xl px-4 py-3">

                    <!-- Agence -->

                    <select
                        name="agence"
                        class="border rounded-xl px-4 py-3">

                        <option value="">
                            Toutes les agences
                        </option>

                        @foreach($listeAgences as $agence)

                            <option
                                value="{{ $agence->id }}"
                                {{ request('agence') == $agence->id ? 'selected' : '' }}>

                                {{ $agence->nom_agence }}

                            </option>

                        @endforeach

                    </select>

                    <!-- Date -->

                    <input
                        type="date"
                        name="date"
                        value="{{ request('date') }}"
                        class="border rounded-xl px-4 py-3">

                    <!-- Pagination -->

                    <select
                        name="par_page"
                        class="border rounded-xl px-4 py-3">

                        @foreach([10,25,50,100] as $nb)

                            <option
                                value="{{ $nb }}"
                                {{ request('par_page',10)==$nb ? 'selected' : '' }}>

                                {{ $nb }} lignes

                            </option>

                        @endforeach

                    </select>

                    <!-- Boutons -->

                    <div class="flex gap-2">

                        <button
                            type="submit"
                            class="flex-1 bg-orange-500 hover:bg-orange-600 text-white rounded-xl">

                            Filtrer

                        </button>

                        <a
                            href="/admin/billets"
                            class="flex-1 bg-gray-600 hover:bg-gray-700 text-white rounded-xl flex items-center justify-center">

                            Réinitialiser

                        </a>

                    </div>

                </div>

                <div class="mt-5 text-gray-600">

                    Total :
                    <span class="font-bold text-orange-600">

                        {{ $billets->total() }}

                    </span>

                    billet(s)

                </div>

            </form>

        </div>

        <!-- Tableau -->

        <div class="bg-white rounded-xl shadow overflow-hidden">

            <table class="min-w-full">

                <thead class="bg-slate-100">

                    <tr>

                        <th class="p-4 text-left">N° Billet</th>
                        <th class="p-4 text-left">Voyageur</th>
                        <th class="p-4 text-left">Agence</th>
                        <th class="p-4 text-left">Départ</th>
                        <th class="p-4 text-left">Arrivée</th>
                        <th class="p-4 text-left">QR Code</th>
                        <th class="p-4 text-center">Actions</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($billets as $billet)
                    <tr class="border-t hover:bg-orange-50 transition">

    <td class="p-4 font-semibold">
        {{ $billet->numero_billet }}
    </td>

    <td class="p-4">
        {{ $billet->reservation?->user?->name ?? 'Utilisateur supprimé' }}
    </td>

    <td class="p-4">
        {{ $billet->reservation?->trajet?->agence?->nom_agence ?? '-' }}
    </td>

    <td class="p-4">
        {{ $billet->reservation?->trajet?->depart ?? '-' }}
    </td>

    <td class="p-4">
        {{ $billet->reservation?->trajet?->arrivee ?? '-' }}
    </td>

    <td class="p-4">
        {{ $billet->qr_code }}
    </td>

    <td class="p-4">

        <div class="flex justify-center gap-2">

            <!-- Voir -->

            <a
                href="/admin/billets/{{ $billet->id }}"
                class="bg-slate-600 hover:bg-slate-700 text-white px-3 py-2 rounded-lg">

                👁️

            </a>

            <!-- Imprimer -->

            <a
                href="/admin/billets/{{ $billet->id }}"
                target="_blank"
                class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg">

                🖨️

            </a>

            <!-- Supprimer -->

            <form
                action="/admin/billets/{{ $billet->id }}"
                method="POST">

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    onclick="return confirm('Voulez-vous vraiment supprimer ce billet ?')"
                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg">

                    🗑️

                </button>

            </form>

        </div>

    </td>

</tr>

@empty

<tr>

    <td
        colspan="7"
        class="text-center py-10 text-gray-500">

        Aucun billet trouvé.

    </td>

</tr>

@endforelse
</tbody>

</table>

<!-- Pagination -->

<div class="p-6 border-t flex justify-between items-center">

    <div class="text-gray-600">

        Affichage de

        <span class="font-semibold">
            {{ $billets->firstItem() ?? 0 }}
        </span>

        à

        <span class="font-semibold">
            {{ $billets->lastItem() ?? 0 }}
        </span>

        sur

        <span class="font-semibold text-orange-600">
            {{ $billets->total() }}
        </span>

        billet(s)

    </div>

    {{ $billets->links() }}

</div>

</div>

</div>

</div>

</x-layouts.admin>