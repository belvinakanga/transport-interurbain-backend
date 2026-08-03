<x-layouts.admin>

<div class="py-12">

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Titre -->

        <div class="flex justify-between items-center mb-6">

            <h1 class="text-3xl font-bold text-slate-800">
                🎫 Gestion des réservations
            </h1>
            <!-- Barre de recherche et filtres -->

<div class="bg-white rounded-xl shadow p-6 mb-6">

<form action="/admin/reservations" method="GET">

    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">

        <!-- Recherche -->

        <input
            type="text"
            name="recherche"
            value="{{ request('recherche') }}"
            placeholder="🔍 Rechercher un voyageur..."
            class="border rounded-xl px-4 py-3">

        <!-- Statut -->

        <select
            name="statut"
            class="border rounded-xl px-4 py-3">

            <option value="">Tous les statuts</option>

            <option value="confirmée"
                {{ request('statut')=='confirmée' ? 'selected' : '' }}>
                Confirmée
            </option>

            <option value="en attente"
                {{ request('statut')=='en attente' ? 'selected' : '' }}>
                En attente
            </option>

            <option value="annulée"
                {{ request('statut')=='annulée' ? 'selected' : '' }}>
                Annulée
            </option>

        </select>

        <!-- Date -->

        <input
            type="date"
            name="date"
            value="{{ request('date') }}"
            class="border rounded-xl px-4 py-3">

        <!-- Nombre de lignes -->

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

        <div class="md:col-span-2 flex gap-2">

            <button
                type="submit"
                class="flex-1 bg-orange-500 hover:bg-orange-600 text-white rounded-xl">

                Filtrer

            </button>

            <a
                href="/admin/reservations"
                class="flex-1 bg-gray-600 hover:bg-gray-700 text-white rounded-xl flex items-center justify-center">

                Réinitialiser

            </a>

        </div>

    </div>

</form>

</div>

        </div>

        <!-- Tableau -->

        <div class="bg-white rounded-xl shadow overflow-hidden">

            <table class="min-w-full">

                <thead class="bg-slate-100">

                    <tr>
                        <th class="p-4 text-center">
                           Actions
                        </th>
                    
                        <th class="p-4 text-left">
                            Voyageur
                        </th>

                        <th class="p-4 text-left">
                            Agence
                        </th>

                        <th class="p-4 text-left">
                            Trajet
                        </th>

                        <th class="p-4 text-center">
                            Date
                        </th>

                        <th class="p-4 text-center">
                            Places
                        </th>

                        <th class="p-4 text-right">
                            Montant
                        </th>

                        <th class="p-4 text-center">
                            Statut
                            
                        </th>

                    </tr>

                </thead>

                <tbody>

                @forelse($reservations as $reservation)

                    <tr class="border-t hover:bg-orange-50 transition">

                        <!-- Voyageur -->

                        <td class="p-4 font-semibold">

                            {{ $reservation->user->name ?? 'Utilisateur supprimé' }}

                        </td>

                        <!-- Agence -->

                        <td class="p-4">

                            {{ $reservation->trajet->agence->nom_agence ?? '-' }}

                        </td>

                        <!-- Trajet -->

                        <td class="p-4">

                            {{ $reservation->trajet->depart ?? '-' }}
                            →
                            {{ $reservation->trajet->arrivee ?? '-' }}

                        </td>

                        <!-- Date -->

                        <td class="p-4 text-center">

                            {{ optional($reservation->created_at)->format('d/m/Y') }}

                        </td>

                        <!-- Places -->

                        <td class="p-4 text-center">

                            {{ $reservation->nombre_places }}

                        </td>

                        <!-- Montant -->

                        <td class="p-4 text-right font-semibold text-green-600">

                            @if($reservation->trajet)

                                {{ number_format($reservation->trajet->prix * $reservation->nombre_places,0,',',' ') }} FCFA

                            @else

                                -

                            @endif

                        </td>

                        <!-- Statut -->

                        <td class="p-4 text-center">

                            @if($reservation->statut == 'confirmée')

                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">
                                    Confirmée
                                </span>

                            @elseif($reservation->statut == 'en attente')

                                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm font-semibold">
                                    En attente
                                </span>

                            @elseif($reservation->statut == 'annulée')

                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-semibold">
                                    Annulée
                                </span>

                            @else

                                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">

                                    {{ ucfirst($reservation->statut) }}

                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7" class="text-center py-10 text-gray-500">

                            Aucune réservation trouvée.

                        </td>
                        

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

</x-layouts.admin>