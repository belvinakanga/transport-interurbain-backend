<x-layouts.admin>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <h1 class="text-3xl font-bold mb-6">
                Gestion des paiements
            </h1>

            <!-- Barre de recherche et filtres -->

<div class="bg-white rounded-xl shadow p-6 mb-6">

<form action="/admin/paiements" method="GET">

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

            <option value="Payé"
                {{ request('statut') == 'Payé' ? 'selected' : '' }}>
                Payé
            </option>

            <option value="En attente"
                {{ request('statut') == 'En attente' ? 'selected' : '' }}>
                En attente
            </option>

            <option value="Échoué"
                {{ request('statut') == 'Échoué' ? 'selected' : '' }}>
                Échoué
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
                    {{ request('par_page',10) == $nb ? 'selected' : '' }}>

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
                href="/admin/paiements"
                class="flex-1 bg-gray-600 hover:bg-gray-700 text-white rounded-xl flex items-center justify-center">

                Réinitialiser

            </a>

        </div>

    </div>

</form>

</div>


            <div class="bg-white p-6 rounded shadow">

                <table class="table-auto w-full border">

                    <thead>

                        <tr>

                            
                            <th class="border p-2">Voyageur</th>
                            <th class="border p-2">Trajet</th>
                            <th class="border p-2">Montant</th>
                            <th class="border p-2">Statut</th>
                            <th class="border p-2">Date</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($paiements as $paiement)

                            <tr>

                    

                                <td class="border p-2">
                                    {{ $paiement->reservation->user->name ?? '---' }}
                                </td>

                                <td class="border p-2">
                                    {{ $paiement->reservation->trajet->depart ?? '' }}
                                    →
                                    {{ $paiement->reservation->trajet->arrivee ?? '' }}
                                </td>

                                <td class="border p-2">
                                    {{ $paiement->montant }} FCFA
                                </td>

                                <td class="border p-2">
                                    {{ $paiement->statut }}
                                </td>

                                <td class="border p-2">
                                    {{ $paiement->created_at }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="border p-4 text-center">

                                    Aucun paiement enregistré

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</x-layouts.admin>