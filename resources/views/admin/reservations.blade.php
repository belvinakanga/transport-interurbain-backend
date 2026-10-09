@php
    $layout = auth()->user()->role === 'agent'
        ? 'layouts.agent'
        : 'layouts.admin';

    $pageTitle = auth()->user()->role === 'agent'
        ? 'Réservations'
        : 'Gestion des réservations';
@endphp

<x-dynamic-component
    :component="$layout"
    :header="$pageTitle"
>

<div class="tk-page">

    {{-- En-tête --}}

    <div
        class="
            tk-page-head
            flex flex-col gap-4
            md:flex-row md:items-center
            md:justify-between
        "
    >

        <div class="min-w-0">

            <h1 class="tk-page-title">

                <span
                    class="
                        flex h-11 w-11 shrink-0
                        items-center justify-center
                        rounded-lg bg-orange-50
                        text-lg text-brand
                    "
                >
                    <i class="fa-solid fa-ticket"></i>
                </span>

                @if(auth()->user()->role === 'agent')
                    Réservations de mon agence
                @else
                    Gestion des réservations
                @endif

            </h1>

            @if(auth()->user()->role === 'agent')

                <p class="mt-3 text-sm text-slate-500">

                    Agence :

                    <strong class="font-semibold text-navy">
                        {{ auth()->user()->agence->nom_agence ?? 'Aucune agence' }}
                    </strong>

                </p>

            @endif

        </div>

    </div>


    {{-- Barre de recherche et filtres --}}

    <div class="tk-card p-6">

        <form
            action="{{ route('admin.reservations') }}"
            method="GET"
        >

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                {{-- Recherche --}}

                <input
                    type="text"
                    name="recherche"
                    value="{{ request('recherche') }}"
                    placeholder="Rechercher un voyageur..."
                    class="tk-input"
                >


                {{-- Statut --}}

                <select
                    name="statut"
                    class="tk-input"
                >

                    <option value="">
                        Tous les statuts
                    </option>

                    <option
                        value="confirmée"
                        {{ request('statut') == 'confirmée' ? 'selected' : '' }}
                    >
                        Confirmée
                    </option>

                    <option
                        value="en attente"
                        {{ request('statut') == 'en attente' ? 'selected' : '' }}
                    >
                        En attente
                    </option>

                    <option
                        value="annulée"
                        {{ request('statut') == 'annulée' ? 'selected' : '' }}
                    >
                        Annulée
                    </option>

                </select>


                {{-- Date --}}

                <input
                    type="date"
                    name="date"
                    value="{{ request('date') }}"
                    class="tk-input"
                >


                {{-- Pagination --}}

                <select
                    name="par_page"
                    class="tk-input"
                >

                    @foreach([10,25,50,100] as $nb)

                        <option
                            value="{{ $nb }}"
                            {{ request('par_page', 10) == $nb ? 'selected' : '' }}
                        >
                            {{ $nb }} lignes
                        </option>

                    @endforeach

                </select>


                {{-- Boutons --}}

                <div class="flex gap-2">

                    <button
                        type="submit"
                        class="tk-btn-accent flex-1"
                    >
                        Filtrer
                    </button>

                    <a
                        href="{{ route('admin.reservations') }}"
                        class="tk-btn-ghost flex-1"
                    >
                        Réinitialiser
                    </a>

                </div>

            </div>

        </form>

    </div>


    {{-- Tableau des réservations --}}

    <div class="tk-card overflow-hidden">

        <div class="overflow-x-auto">

            <table class="tk-table">

                {{-- EN-TÊTE --}}

                <thead>

                    <tr>

                        {{-- Voyageur --}}

                        <th class="text-left whitespace-nowrap">
                            Voyageur
                        </th>


                        {{-- Référence billet --}}

                        <th class="text-left whitespace-nowrap">
                            Référence billet
                        </th>


                        {{-- Agence --}}

                        <th class="text-left whitespace-nowrap">
                            Agence
                        </th>


                        {{-- Trajet --}}

                        <th class="text-left whitespace-nowrap">
                            Trajet
                        </th>


                        {{-- Date --}}

                        <th class="text-center whitespace-nowrap">
                            Date
                        </th>


                        {{-- Places --}}

                        <th class="text-center whitespace-nowrap">
                            Places
                        </th>


                        {{-- Montant --}}

                        <th class="text-right whitespace-nowrap">
                            Montant
                        </th>


                        {{-- Statut --}}

                        <th class="text-center whitespace-nowrap">
                            Statut
                        </th>

                    </tr>

                </thead>


                {{-- CORPS DU TABLEAU --}}

                <tbody>

                    @forelse($reservations as $reservation)

                        <tr>

                            {{-- VOYAGEUR --}}

                            <td class="whitespace-nowrap">

                                @if($reservation->voyageurs && $reservation->voyageurs->count())

                                    @foreach($reservation->voyageurs as $voyageur)

                                        <div class="font-semibold text-navy">
                                            {{ $voyageur->prenom ?? '' }}
                                            {{ $voyageur->nom ?? '' }}
                                        </div>

                                        @if(!empty($voyageur->telephone))

                                            <div class="text-sm text-slate-500 mt-1">

                                                <i class="fa-solid fa-phone"></i>

                                                {{ $voyageur->telephone }}

                                            </div>

                                        @endif

                                    @endforeach

                                @else

                                    <div class="font-semibold text-slate-600">
                                        Voyageur non renseigné
                                    </div>

                                @endif

                            </td>


                            {{-- RÉFÉRENCE BILLET --}}

                            <td class="whitespace-nowrap">

                                @if($reservation->billets && $reservation->billets->count())

                                    @foreach($reservation->billets as $billet)

                                        <div class="font-semibold text-navy whitespace-nowrap mb-1">

                                            {{ $billet->numero_billet }}

                                        </div>

                                    @endforeach

                                @else

                                    <span class="text-slate-400 whitespace-nowrap">
                                        —
                                    </span>

                                @endif

                            </td>


                            {{-- AGENCE --}}

                            <td class="whitespace-nowrap">

                                {{ $reservation->trajet->agence->nom_agence ?? '-' }}

                            </td>


                            {{-- TRAJET --}}

                            <td class="font-medium text-slate-700 whitespace-nowrap">

                                {{ $reservation->trajet->depart ?? '-' }}

                                →

                                {{ $reservation->trajet->arrivee ?? '-' }}

                            </td>


                            {{-- DATE --}}

                            <td class="text-center whitespace-nowrap">

                                {{ optional($reservation->created_at)->format('d/m/Y') }}

                            </td>


                            {{-- PLACES --}}

                            <td class="text-center whitespace-nowrap">

                                {{ $reservation->nombre_places }}

                            </td>


                            {{-- MONTANT --}}

                            <td class="text-right font-semibold text-navy whitespace-nowrap tabular-nums">

                                @if($reservation->trajet)

                                    {{ number_format(
                                        $reservation->trajet->prix * $reservation->nombre_places,
                                        0,
                                        ',',
                                        ' '
                                    ) }}

                                    FCFA

                                @else

                                    -

                                @endif

                            </td>


                            {{-- STATUT --}}

                            <td class="text-center whitespace-nowrap">

                                @if($reservation->statut == 'confirmée')

                                    <span class="tk-badge tk-badge-green">

                                        <i class="fa-solid fa-circle-check"></i>

                                        Confirmée

                                    </span>

                                @elseif($reservation->statut == 'en attente')

                                    <span class="tk-badge tk-badge-orange">

                                        <i class="fa-solid fa-hourglass-half"></i>

                                        En attente

                                    </span>

                                @elseif($reservation->statut == 'annulée')

                                    <span class="tk-badge tk-badge-red">

                                        <i class="fa-solid fa-xmark"></i>

                                        Annulée

                                    </span>

                                @else

                                    <span class="tk-badge tk-badge-slate">

                                        {{ ucfirst($reservation->statut) }}

                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="tk-empty"
                            >

                                Aucune réservation trouvée.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}

        @if(method_exists($reservations, 'links'))

            <div class="border-t border-slate-200 px-6 py-4">

                {{ $reservations->links() }}

            </div>

        @endif

    </div>

</div>

</x-dynamic-component>