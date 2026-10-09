<x-layouts.admin :header="'Gestion des agences'">

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

        <h1 class="tk-page-title">

            <span
                class="
                    flex h-11 w-11 shrink-0
                    items-center justify-center
                    rounded-lg bg-orange-50
                    text-lg text-brand
                "
            >
                <i class="fa-solid fa-building"></i>
            </span>

            Gestion des agences

        </h1>

        <a
            href="/admin/agences/create"
            class="tk-btn-accent shrink-0"
        >

            <i class="fa-solid fa-plus"></i>

            <span>Ajouter</span>

        </a>

    </div>


    {{-- Barre de recherche et filtres --}}

    <div class="tk-card p-6">

        <form action="/admin/agences" method="GET">

            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">

                {{-- Recherche --}}

                <div class="md:col-span-2">

                    <input
                        type="text"
                        name="recherche"
                        value="{{ request('recherche') }}"
                        placeholder="Nom ou téléphone..."
                        class="tk-input">

                </div>


                {{-- Filtre Agence --}}

                <div>

                    <select
                        name="agence"
                        class="tk-input">

                        <option value="">
                            Toutes les agences
                        </option>

                        @foreach($listeAgences as $item)

                            <option
                                value="{{ $item->id }}"
                                {{ request('agence') == $item->id ? 'selected' : '' }}>

                                {{ $item->nom_agence }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Filtre Ville --}}

                <div>

                    <select
                        name="ville"
                        class="tk-input">

                        <option value="">
                            Toutes les villes
                        </option>

                        @foreach($listeVilles as $ville)

                            <option
                                value="{{ $ville }}"
                                {{ request('ville') == $ville ? 'selected' : '' }}>

                                {{ $ville }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Tri --}}

                <div>

                    <select
                        name="tri"
                        class="tk-input">

                        <option
                            value="recent"
                            {{ request('tri') == 'recent' ? 'selected' : '' }}>

                            Plus récent

                        </option>

                        <option
                            value="ancien"
                            {{ request('tri') == 'ancien' ? 'selected' : '' }}>

                            Plus ancien

                        </option>

                        <option
                            value="nom_asc"
                            {{ request('tri') == 'nom_asc' ? 'selected' : '' }}>

                            Nom A → Z

                        </option>

                        <option
                            value="nom_desc"
                            {{ request('tri') == 'nom_desc' ? 'selected' : '' }}>

                            Nom Z → A

                        </option>

                        <option
                            value="ville"
                            {{ request('tri') == 'ville' ? 'selected' : '' }}>

                            Ville

                        </option>

                    </select>

                </div>


                {{-- Pagination --}}

                <div>

                    <select
                        name="par_page"
                        class="tk-input">

                        <option
                            value="10"
                            {{ request('par_page', 10) == 10 ? 'selected' : '' }}>
                            10
                        </option>

                        <option
                            value="25"
                            {{ request('par_page') == 25 ? 'selected' : '' }}>
                            25
                        </option>

                        <option
                            value="50"
                            {{ request('par_page') == 50 ? 'selected' : '' }}>
                            50
                        </option>

                        <option
                            value="100"
                            {{ request('par_page') == 100 ? 'selected' : '' }}>
                            100
                        </option>

                    </select>

                </div>

            </div>


            {{-- Informations et boutons --}}

            <div
                class="
                    mt-6 flex flex-col gap-3
                    sm:flex-row sm:items-center
                    sm:justify-between
                "
            >

                <div class="text-sm text-slate-500">

                    Total :

                    <span class="font-bold text-brand">
                        {{ $agences->total() }}
                    </span>

                    agence(s)

                </div>


                <div class="flex gap-3">

                    <a
                        href="/admin/agences"
                        class="tk-btn-ghost"
                    >
                        Réinitialiser
                    </a>

                    <button
                        type="submit"
                        class="tk-btn-accent"
                    >
                        Filtrer
                    </button>

                </div>

            </div>

        </form>

    </div>


    {{-- Tableau des agences --}}

    <div class="tk-card overflow-hidden">

        <div class="overflow-x-auto">

            <table
                class="tk-table"
                style="min-width:1350px;"
            >

                {{-- Largeur des colonnes --}}

                <colgroup>

                    <col style="width:220px;">
                    <col style="width:170px;">
                    <col style="width:220px;">
                    <col style="width:220px;">
                    <col style="width:120px;">
                    <col style="width:160px;">
                    <col style="width:150px;">
                    <col style="width:190px;">

                </colgroup>


                {{-- EN-TÊTE --}}

                <thead>

                    <tr>

                        <th class="text-left whitespace-nowrap">
                            Agence
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Ville
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Adresse
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Téléphone
                        </th>

                        <th class="text-center whitespace-nowrap">
                            Trajets
                        </th>

                        <th class="text-center whitespace-nowrap">
                            Réservations
                        </th>

                        <th class="text-center whitespace-nowrap">
                            CA
                        </th>

                        <th class="text-center whitespace-nowrap">
                            Actions
                        </th>

                    </tr>

                </thead>


                {{-- CORPS DU TABLEAU --}}

                <tbody>

                    @forelse($agences as $agence)

                        <tr>


                            {{-- AGENCE --}}

                            <td class="font-semibold whitespace-nowrap">

                                {{ $agence->nom_agence }}

                            </td>


                            {{-- VILLE --}}

                            <td class="whitespace-nowrap">

                                {{ $agence->ville }}

                            </td>


                            {{-- ADRESSE --}}

                            <td class="whitespace-nowrap">

                                @if($agence->adresse)

                                    <span class="font-medium text-slate-700 whitespace-nowrap">

                                        <i class="fa-solid fa-location-dot text-slate-400"></i>

                                        {{ $agence->adresse }}

                                    </span>

                                @else

                                    <span class="font-medium text-slate-700 whitespace-nowrap">

                                        Non renseignée

                                    </span>

                                @endif

                            </td>


                            {{-- TÉLÉPHONE --}}

                            <td class="whitespace-nowrap">

                                {{ $agence->telephone }}

                            </td>


                            {{-- TRAJETS --}}

                            <td class="text-center whitespace-nowrap">

                                0

                            </td>


                            {{-- RÉSERVATIONS --}}

                            <td class="text-center whitespace-nowrap">

                                0

                            </td>


                            {{-- CHIFFRE D'AFFAIRES --}}

                            <td class="text-center whitespace-nowrap font-semibold text-navy">

                                0 FCFA

                            </td>


                            {{-- ACTIONS --}}

                            <td class="text-center whitespace-nowrap">

                                <div class="flex justify-center items-center gap-2">

                                    {{-- VOIR --}}

                                    <a
                                        href="/admin/agences/{{ $agence->id }}"
                                        title="Voir"
                                        class="tk-icon-btn tk-icon-btn-navy"
                                    >

                                        <i class="fa-solid fa-eye"></i>

                                    </a>


                                    {{-- MODIFIER --}}

                                    <a
                                        href="/admin/agences/{{ $agence->id }}/edit"
                                        title="Modifier"
                                        class="tk-icon-btn tk-icon-btn-brand"
                                    >

                                        <i class="fa-solid fa-pen"></i>

                                    </a>


                                    {{-- SUPPRIMER --}}

                                    <form
                                        action="/admin/agences/{{ $agence->id }}"
                                        method="POST"
                                        class="m-0"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Supprimer"
                                            onclick="event.preventDefault(); tkConfirm('Supprimer cette agence ?', () => this.form.submit())"
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

                                Aucune agence trouvée.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}

        <div class="border-t border-slate-200 px-6 py-4">

            {{ $agences->links() }}

        </div>

    </div>

</div>

</x-layouts.admin>
