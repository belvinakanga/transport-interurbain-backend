<x-layouts.admin>

<div class="py-12">

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- En-tête --}}

        <div class="flex justify-between items-center mb-6">

            <h1 class="text-3xl font-bold text-slate-800">
                🏢 Gestion des agences
            </h1>

            <a
                href="/admin/agences/create"
                class="bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-xl shadow">

                ➕ Ajouter

            </a>

        </div>


        {{-- Barre de recherche et filtres --}}

        <div class="bg-white rounded-xl shadow p-6 mb-6">

            <form action="/admin/agences" method="GET">

                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">

                    {{-- Recherche --}}

                    <div class="md:col-span-2">

                        <input
                            type="text"
                            name="recherche"
                            value="{{ request('recherche') }}"
                            placeholder="🔍 Nom ou téléphone..."
                            class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500">

                    </div>


                    {{-- Filtre Agence --}}

                    <div>

                        <select
                            name="agence"
                            class="w-full border rounded-xl px-4 py-3">

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
                            class="w-full border rounded-xl px-4 py-3">

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
                            class="w-full border rounded-xl px-4 py-3">

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
                            class="w-full border rounded-xl px-4 py-3">

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

                <div class="flex justify-between items-center mt-6">

                    <div class="text-gray-600">

                        Total :

                        <span class="font-bold text-orange-600">
                            {{ $agences->total() }}
                        </span>

                        agence(s)

                    </div>


                    <div class="flex gap-3">

                        <a
                            href="/admin/agences"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-3 rounded-xl">

                            Réinitialiser

                        </a>

                        <button
                            type="submit"
                            class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-xl">

                            Filtrer

                        </button>

                    </div>

                </div>

            </form>

        </div>


        {{-- Tableau des agences --}}

<div class="bg-white rounded-xl shadow overflow-hidden">

    <div class="overflow-x-auto">

        <table
    class="w-full"
    style="min-width:1350px; width:max-content;"
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

            <thead class="bg-slate-100">

                <tr>

                    <th class="p-4 text-left whitespace-nowrap">
                        Agence
                    </th>

                    <th class="p-4 text-left whitespace-nowrap">
                        Ville
                    </th>

                    <th class="p-4 text-left whitespace-nowrap">
                        Adresse
                    </th>

                    <th class="p-4 text-left whitespace-nowrap">
                        Téléphone
                    </th>

                    <th class="p-4 text-center whitespace-nowrap">
                        Trajets
                    </th>

                    <th class="p-4 text-center whitespace-nowrap">
                        Réservations
                    </th>

                    <th class="p-4 text-center whitespace-nowrap">
                        CA
                    </th>

                    <th class="p-4 text-center whitespace-nowrap">
                        Actions
                    </th>

                </tr>

            </thead>


            {{-- CORPS DU TABLEAU --}}

            <tbody>

                @forelse($agences as $agence)

                    <tr class="border-t hover:bg-orange-50 transition">


                        {{-- AGENCE --}}

                        <td
                          class="p-4 font-semibold"
                          style="white-space:nowrap;"
                         >

                            {{ $agence->nom_agence }}

                        </td>


                        {{-- VILLE --}}

                        <td
                          class="p-4"
                          style="white-space:nowrap;"
                       >

                            {{ $agence->ville }}

                        </td>


                        {{-- ADRESSE --}}

                        <td
                          class="p-4"
                          style="white-space:nowrap;"
                          >

                            @if($agence->adresse)

                                <span class="font-medium text-slate-700 whitespace-nowrap">

                                    📍 {{ $agence->adresse }}

                                </span>

                            @else

                                <span
    class="font-medium text-slate-700"
    style="white-space:nowrap;"
>

                                    Non renseignée

                                </span>

                            @endif

                        </td>


                        {{-- TÉLÉPHONE --}}

                        <td
    class="p-4"
    style="white-space:nowrap;"
>

                            {{ $agence->telephone }}

                        </td>


                        {{-- TRAJETS --}}

                        <td class="p-4 text-center whitespace-nowrap">

                            0

                        </td>


                        {{-- RÉSERVATIONS --}}

                        <td class="p-4 text-center whitespace-nowrap">

                            0

                        </td>


                        {{-- CHIFFRE D'AFFAIRES --}}

                        <td class="p-4 text-center whitespace-nowrap">

                            0 FCFA

                        </td>


                        {{-- ACTIONS --}}

                        <td class="p-4 text-center whitespace-nowrap">

                            <div class="flex justify-center items-center gap-2">

                                {{-- VOIR --}}

                                <a
                                    href="/admin/agences/{{ $agence->id }}"
                                    title="Voir"
                                    class="w-10 h-10 flex items-center justify-center bg-slate-600 hover:bg-slate-700 text-white rounded-lg"
                                >

                                    👁️

                                </a>


                                {{-- MODIFIER --}}

                                <a
                                    href="/admin/agences/{{ $agence->id }}/edit"
                                    title="Modifier"
                                    class="w-10 h-10 flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-lg"
                                >

                                    ✏️

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
                                        onclick="return confirm('Supprimer cette agence ?')"
                                        class="w-10 h-10 flex items-center justify-center bg-red-600 hover:bg-red-700 text-white rounded-lg"
                                    >

                                        🗑️

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="text-center py-10 text-gray-500"
                        >

                            Aucune agence trouvée.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- PAGINATION --}}

    <div class="p-6 border-t">

        {{ $agences->links() }}

    </div>

    {{-- PAGINATION --}}

    <div class="p-6 border-t">

        {{ $agences->links() }}

    </div>

</div>

</div>

</div>

</x-layouts.admin>