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

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- =========================================================
                 TITRE
            ========================================================== --}}

            <div class="mb-6">

                <h1 class="text-3xl font-bold text-slate-800">

                    @if(auth()->user()->role === 'agent')
                        🎫 Réservations de mon agence
                    @else
                        🎫 Gestion des réservations
                    @endif

                </h1>

                @if(auth()->user()->role === 'agent')

                    <p class="text-gray-500 mt-2 whitespace-nowrap">

                        Agence :

                        <strong class="text-blue-700">
                            {{ auth()->user()->agence->nom_agence ?? 'Aucune agence' }}
                        </strong>

                    </p>

                @endif

            </div>


            {{-- =========================================================
                 RECHERCHE ET FILTRES
            ========================================================== --}}

            <div class="bg-white rounded-xl shadow p-6 mb-6">

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
                            placeholder="🔍 Rechercher un voyageur..."
                            class="border rounded-xl px-4 py-3"
                        >


                        {{-- Statut --}}

                        <select
                            name="statut"
                            class="border rounded-xl px-4 py-3"
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
                            class="border rounded-xl px-4 py-3"
                        >


                        {{-- Pagination --}}

                        <select
                            name="par_page"
                            class="border rounded-xl px-4 py-3"
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
                                class="flex-1 bg-orange-500 hover:bg-orange-600 text-white rounded-xl py-3"
                            >
                                Filtrer
                            </button>

                            <a
                                href="{{ route('admin.reservations') }}"
                                class="flex-1 bg-gray-600 hover:bg-gray-700 text-white rounded-xl flex items-center justify-center"
                            >
                                Réinitialiser
                            </a>

                        </div>

                    </div>

                </form>

            </div>


            {{-- =========================================================
                 TABLEAU
            ========================================================== --}}

            <div class="bg-white rounded-xl shadow overflow-hidden">

                {{-- Défilement horizontal si nécessaire --}}

                <div class="overflow-x-auto">

                    <table class="min-w-max w-full">

                        <thead class="bg-slate-100">

                            <tr>

                                {{-- Voyageur --}}

                                <th class="p-4 text-left whitespace-nowrap">
                                    Voyageur
                                </th>


                                {{-- Référence billet --}}

                                <th class="p-4 text-left whitespace-nowrap">
                                    Référence billet
                                </th>


                                {{-- Agence --}}

                                <th class="p-4 text-left whitespace-nowrap">
                                    Agence
                                </th>


                                {{-- Trajet --}}

                                <th class="p-4 text-left whitespace-nowrap">
                                    Trajet
                                </th>


                                {{-- Date --}}

                                <th class="p-4 text-center whitespace-nowrap">
                                    Date
                                </th>


                                {{-- Places --}}

                                <th class="p-4 text-center whitespace-nowrap">
                                    Places
                                </th>


                                {{-- Montant --}}

                                <th class="p-4 text-right whitespace-nowrap">
                                    Montant
                                </th>


                                {{-- Statut --}}

                                <th class="p-4 text-center whitespace-nowrap">
                                    Statut
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($reservations as $reservation)

                                <tr class="border-t hover:bg-orange-50 transition">


                                    {{-- =================================================
                                         VOYAGEUR
                                    ================================================== --}}

                                    <td class="p-4 whitespace-nowrap">

    @if($reservation->voyageurs && $reservation->voyageurs->count())

        @foreach($reservation->voyageurs as $voyageur)

            <div class="font-semibold text-gray-800">
                {{ $voyageur->prenom ?? '' }}
                {{ $voyageur->nom ?? '' }}
            </div>

            @if(!empty($voyageur->telephone))
                <div class="text-sm text-gray-600 mt-1">
                    📞 {{ $voyageur->telephone }}
                </div>
            @endif

        @endforeach

    @else

        <div class="font-semibold text-gray-800">
            Voyageur non renseigné
        </div>

    @endif



                                    {{-- =================================================
                                         RÉFÉRENCE BILLET
                                    ================================================== --}}

                                    <td class="p-4 whitespace-nowrap">

                                        @if($reservation->billets && $reservation->billets->count())

                                            @foreach($reservation->billets as $billet)

                                                <div class="font-semibold text-blue-700 whitespace-nowrap mb-1">

                                                    {{ $billet->numero_billet }}

                                                </div>

                                            @endforeach

                                        @else

                                            <span class="text-gray-400 whitespace-nowrap">
                                                —
                                            </span>

                                        @endif

                                    </td>


                                    {{-- =================================================
                                         AGENCE
                                    ================================================== --}}

                                    <td class="p-4 whitespace-nowrap">

                                        {{ $reservation->trajet->agence->nom_agence ?? '-' }}

                                    </td>


                                    {{-- =================================================
                                         TRAJET
                                    ================================================== --}}

                                    <td class="p-4 font-medium whitespace-nowrap">

                                        {{ $reservation->trajet->depart ?? '-' }}

                                        →

                                        {{ $reservation->trajet->arrivee ?? '-' }}

                                    </td>


                                    {{-- =================================================
                                         DATE
                                    ================================================== --}}

                                    <td class="p-4 text-center whitespace-nowrap">

                                        {{ optional($reservation->created_at)->format('d/m/Y') }}

                                    </td>


                                    {{-- =================================================
                                         PLACES
                                    ================================================== --}}

                                    <td class="p-4 text-center whitespace-nowrap">

                                        {{ $reservation->nombre_places }}

                                    </td>


                                    {{-- =================================================
                                         MONTANT
                                    ================================================== --}}

                                    <td class="p-4 text-right font-semibold text-green-600 whitespace-nowrap">

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


                                    {{-- =================================================
                                         STATUT
                                    ================================================== --}}

                                    <td class="p-4 text-center whitespace-nowrap">

                                        @if($reservation->statut == 'confirmée')

                                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold whitespace-nowrap">

                                                Confirmée

                                            </span>

                                        @elseif($reservation->statut == 'en attente')

                                            <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm font-semibold whitespace-nowrap">

                                                En attente

                                            </span>

                                        @elseif($reservation->statut == 'annulée')

                                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-semibold whitespace-nowrap">

                                                Annulée

                                            </span>

                                        @else

                                            <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm whitespace-nowrap">

                                                {{ ucfirst($reservation->statut) }}

                                            </span>

                                        @endif

                                    </td>

                                </tr>


                            @empty

                                <tr>

                                    <td
                                        colspan="8"
                                        class="text-center py-10 text-gray-500"
                                    >

                                        🎫 Aucune réservation trouvée.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- =========================================================
                     PAGINATION
                ========================================================== --}}

                @if(method_exists($reservations, 'links'))

                    <div class="p-6 border-t">

                        {{ $reservations->links() }}

                    </div>

                @endif

            </div>

        </div>

    </div>

</x-dynamic-component>