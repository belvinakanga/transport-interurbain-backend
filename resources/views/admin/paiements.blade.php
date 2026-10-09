<x-layouts.admin :header="'Gestion des paiements'">

<div class="tk-page">

    {{-- En-tête --}}

    <div class="tk-page-head">

        <h1 class="tk-page-title">

            <span
                class="
                    flex h-11 w-11 shrink-0
                    items-center justify-center
                    rounded-lg bg-orange-50
                    text-lg text-brand
                "
            >
                <i class="fa-solid fa-credit-card"></i>
            </span>

            Gestion des paiements

        </h1>

    </div>


    {{-- Barre de recherche et filtres --}}

    <div class="tk-card p-6">

        <form action="/admin/paiements" method="GET">

            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">

                {{-- Recherche --}}

                <input
                    type="text"
                    name="recherche"
                    value="{{ request('recherche') }}"
                    placeholder="Rechercher un voyageur..."
                    class="tk-input">


                {{-- Statut --}}

                <select
                    name="statut"
                    class="tk-input">

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


                {{-- Date --}}

                <input
                    type="date"
                    name="date"
                    value="{{ request('date') }}"
                    class="tk-input">


                {{-- Nombre de lignes --}}

                <select
                    name="par_page"
                    class="tk-input">

                    @foreach([10,25,50,100] as $nb)

                        <option
                            value="{{ $nb }}"
                            {{ request('par_page',10) == $nb ? 'selected' : '' }}>

                            {{ $nb }} lignes

                        </option>

                    @endforeach

                </select>


                {{-- Boutons --}}

                <div class="md:col-span-2 flex gap-2">

                    <button
                        type="submit"
                        class="tk-btn-accent flex-1">

                        Filtrer

                    </button>

                    <a
                        href="/admin/paiements"
                        class="tk-btn-ghost flex-1">

                        Réinitialiser

                    </a>

                </div>

            </div>

        </form>

    </div>


    {{-- Tableau --}}

    <div class="tk-card overflow-hidden">

        <div class="overflow-x-auto">

            <table class="tk-table">

                <thead>

                    <tr>

                        <th class="text-left whitespace-nowrap">
                            Voyageur
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Trajet
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Montant
                        </th>

                        <th class="text-center whitespace-nowrap">
                            Statut
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Date
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($paiements as $paiement)

                        <tr>

                            {{-- VOYAGEUR --}}

                            <td class="whitespace-nowrap">

                                {{ $paiement->reservation->user->name ?? '---' }}

                            </td>


                            {{-- TRAJET --}}

                            <td class="whitespace-nowrap">

                                {{ $paiement->reservation->trajet->depart ?? '' }}
                                →
                                {{ $paiement->reservation->trajet->arrivee ?? '' }}

                            </td>


                            {{-- MONTANT --}}

                            <td class="whitespace-nowrap font-semibold text-navy">

                                {{ $paiement->montant }} FCFA

                            </td>


                            {{-- STATUT --}}

                            <td class="text-center whitespace-nowrap">

                                @if($paiement->statut === 'Payé')

                                    <span class="tk-badge tk-badge-green">

                                        <i class="fa-solid fa-circle-check"></i>

                                        {{ $paiement->statut }}

                                    </span>

                                @elseif($paiement->statut === 'En attente')

                                    <span class="tk-badge tk-badge-orange">

                                        <i class="fa-solid fa-hourglass-half"></i>

                                        {{ $paiement->statut }}

                                    </span>

                                @elseif($paiement->statut === 'Échoué')

                                    <span class="tk-badge tk-badge-red">

                                        <i class="fa-solid fa-triangle-exclamation"></i>

                                        {{ $paiement->statut }}

                                    </span>

                                @else

                                    <span class="tk-badge tk-badge-slate">

                                        {{ $paiement->statut }}

                                    </span>

                                @endif

                            </td>


                            {{-- DATE --}}

                            <td class="whitespace-nowrap tabular-nums text-slate-500">

                                {{ $paiement->created_at }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="tk-empty"
                            >

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
