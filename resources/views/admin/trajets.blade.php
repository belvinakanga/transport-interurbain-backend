@php
    $layout = auth()->user()->role === 'agent'
        ? 'layouts.agent'
        : 'layouts.admin';

    $pageTitle = auth()->user()->role === 'agent'
        ? 'Mes trajets'
        : 'Gestion des trajets';
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
                    <i class="fa-solid fa-bus"></i>
                </span>

                @if(auth()->user()->role === 'agent')
                    Mes trajets
                @else
                    Gestion des trajets
                @endif

            </h1>

            @if(auth()->user()->role === 'agent')

                <p class="mt-3 text-sm text-slate-500">

                    Trajets de votre agence :

                    <strong class="font-semibold text-navy">
                        {{ auth()->user()->agence->nom_agence ?? 'Aucune agence' }}
                    </strong>

                </p>

            @endif

        </div>


        {{-- Ajouter --}}

        <a
            href="{{ url('/admin/trajets/create') }}"
            class="tk-btn-accent shrink-0"
        >

            <i class="fa-solid fa-plus"></i>

            <span>Ajouter un trajet</span>

        </a>

    </div>


    {{-- Barre de recherche et filtres --}}

    <div class="tk-card p-6">

        <form
            action="{{ route('admin.trajets') }}"
            method="GET"
        >

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                {{-- Recherche --}}

                <input
                    type="text"
                    name="recherche"
                    value="{{ request('recherche') }}"
                    placeholder="Départ ou arrivée..."
                    class="tk-input"
                >


                {{-- Agence --}}

                @if(auth()->user()->role === 'admin')

                    <select
                        name="agence"
                        class="tk-input"
                    >

                        <option value="">
                            Toutes les agences
                        </option>

                        @foreach($listeAgences as $agence)

                            <option
                                value="{{ $agence->id }}"
                                {{ request('agence') == $agence->id ? 'selected' : '' }}
                            >
                                {{ $agence->nom_agence }}
                            </option>

                        @endforeach

                    </select>

                @else

                    {{-- Agent : agence imposée --}}

                    <div class="tk-input flex items-center gap-2 text-slate-600">

                        <i class="fa-solid fa-building"></i>

                        {{ auth()->user()->agence->nom_agence ?? 'Aucune agence' }}

                    </div>

                @endif


                {{-- Tri --}}

                <select
                    name="tri"
                    class="tk-input"
                >

                    <option value="recent"
                        {{ request('tri', 'recent') == 'recent' ? 'selected' : '' }}>
                        Plus récents
                    </option>

                    <option value="ancien"
                        {{ request('tri') == 'ancien' ? 'selected' : '' }}>
                        Plus anciens
                    </option>

                    <option value="depart"
                        {{ request('tri') == 'depart' ? 'selected' : '' }}>
                        Départ A → Z
                    </option>

                    <option value="arrivee"
                        {{ request('tri') == 'arrivee' ? 'selected' : '' }}>
                        Arrivée A → Z
                    </option>

                    <option value="prix"
                        {{ request('tri') == 'prix' ? 'selected' : '' }}>
                        Prix
                    </option>

                </select>


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
                        href="{{ route('admin.trajets') }}"
                        class="tk-btn-ghost flex-1"
                    >
                        Réinitialiser
                    </a>

                </div>

            </div>

        </form>

    </div>


    {{-- Tableau des trajets --}}

    <div class="tk-card overflow-hidden">

        <div class="overflow-x-auto">

            <table class="tk-table">

                {{-- EN-TÊTE --}}

                <thead>

                    <tr>

                        <th class="text-left whitespace-nowrap">
                            Agence
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Départ
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Arrivée
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Date
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Heure
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Prix
                        </th>

                        <th class="text-center whitespace-nowrap">
                            Places
                        </th>

                        <th class="text-center whitespace-nowrap">
                            Actions
                        </th>

                    </tr>

                </thead>


                {{-- CORPS DU TABLEAU --}}

                <tbody>

                    @forelse($trajets as $trajet)

                        <tr>

                            {{-- Agence --}}

                            <td class="whitespace-nowrap">

                                {{ $trajet->agence->nom_agence ?? '-' }}

                            </td>


                            {{-- Départ --}}

                            <td class="font-semibold text-navy whitespace-nowrap">

                                {{ $trajet->depart }}

                            </td>


                            {{-- Arrivée --}}

                            <td class="font-semibold text-navy whitespace-nowrap">

                                {{ $trajet->arrivee }}

                            </td>


                            {{-- Date --}}

                            <td class="whitespace-nowrap">

                                {{ $trajet->date_depart }}

                            </td>


                            {{-- Heure --}}

                            <td class="whitespace-nowrap">

                                {{ $trajet->heure_depart }}

                            </td>


                            {{-- Prix --}}

                            <td class="font-semibold text-navy whitespace-nowrap">

                                {{ number_format($trajet->prix, 0, ',', ' ') }}
                                FCFA

                            </td>


                            {{-- Places --}}

                            <td class="text-center whitespace-nowrap">

                                <span class="font-semibold text-navy">
                                    {{ $trajet->places_disponibles }}
                                </span>

                                /

                                {{ $trajet->places_totales }}

                            </td>


                            {{-- Actions --}}

                            <td class="text-center whitespace-nowrap">

                                <div class="flex justify-center items-center gap-2">

                                    {{-- Voir --}}

                                    <a
                                        href="{{ url('/admin/trajets/' . $trajet->id) }}"
                                        title="Voir"
                                        class="tk-icon-btn tk-icon-btn-navy"
                                    >

                                        <i class="fa-solid fa-eye"></i>

                                    </a>


                                    {{-- Modifier --}}

                                    <a
                                        href="{{ url('/admin/trajets/' . $trajet->id . '/edit') }}"
                                        title="Modifier"
                                        class="tk-icon-btn tk-icon-btn-brand"
                                    >

                                        <i class="fa-solid fa-pen"></i>

                                    </a>


                                    {{-- Supprimer --}}

                                    <form
                                        action="{{ url('/admin/trajets/' . $trajet->id) }}"
                                        method="POST"
                                        onsubmit="event.preventDefault(); tkConfirm('Supprimer ce trajet ?', () => this.submit())"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Supprimer"
                                            class="tk-icon-btn tk-icon-btn-red"
                                        >

                                            <i class="fa-solid fa-trash"></i>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="tk-empty"
                            >

                                Aucun trajet trouvé.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}

        <div class="border-t border-slate-200 px-6 py-4">

            {{ $trajets->links() }}

        </div>

    </div>

</div>

</x-dynamic-component>
