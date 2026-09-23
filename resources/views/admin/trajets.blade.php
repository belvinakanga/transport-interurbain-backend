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

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- =========================================================
                 TITRE
            ========================================================== --}}

            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">

                <div>

                    <h1 class="text-3xl font-bold text-slate-800">

                        @if(auth()->user()->role === 'agent')
                            🚌 Mes trajets
                        @else
                            🚌 Gestion des trajets
                        @endif

                    </h1>

                    @if(auth()->user()->role === 'agent')

                        <p class="text-gray-500 mt-2">
                            Trajets de votre agence :
                            <strong class="text-blue-700">
                                {{ auth()->user()->agence->nom_agence ?? 'Aucune agence' }}
                            </strong>
                        </p>

                    @endif

                </div>


                {{-- Ajouter --}}

                <a
                    href="{{ url('/admin/trajets/create') }}"
                    class="bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-xl shadow inline-flex items-center justify-center"
                >
                    ➕ Ajouter un trajet
                </a>

            </div>


            {{-- =========================================================
                 MESSAGE SUCCÈS
            ========================================================== --}}

            @if(session('success'))

                <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-6">
                    {{ session('success') }}
                </div>

            @endif


            {{-- =========================================================
                 RECHERCHE ET FILTRES
            ========================================================== --}}

            <div class="bg-white rounded-xl shadow p-6 mb-6">

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
                            placeholder="🔍 Départ ou arrivée..."
                            class="border rounded-xl px-4 py-3"
                        >


                        {{-- Agence --}}

                        @if(auth()->user()->role === 'admin')

                            <select
                                name="agence"
                                class="border rounded-xl px-4 py-3"
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

                            <div class="border rounded-xl px-4 py-3 bg-gray-100 text-gray-700">

                                🏢
                                {{ auth()->user()->agence->nom_agence ?? 'Aucune agence' }}

                            </div>

                        @endif


                        {{-- Tri --}}

                        <select
                            name="tri"
                            class="border rounded-xl px-4 py-3"
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
                                href="{{ route('admin.trajets') }}"
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

                <div class="overflow-x-auto">

                    <table class="min-w-full">

                        <thead class="bg-slate-100">

                            <tr>

                                <th class="p-4 text-left">
                                    Agence
                                </th>

                                <th class="p-4 text-left">
                                    Départ
                                </th>

                                <th class="p-4 text-left">
                                    Arrivée
                                </th>

                                <th class="p-4 text-left">
                                    Date
                                </th>

                                <th class="p-4 text-left">
                                    Heure
                                </th>

                                <th class="p-4 text-left">
                                    Prix
                                </th>

                                <th class="p-4 text-center">
                                    Places
                                </th>

                                <th class="p-4 text-center">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($trajets as $trajet)

                                <tr class="border-t hover:bg-orange-50 transition">

                                    {{-- Agence --}}

                                    <td class="p-4">

                                        {{ $trajet->agence->nom_agence ?? '-' }}

                                    </td>


                                    {{-- Départ --}}

                                    <td class="p-4 font-medium">

                                        {{ $trajet->depart }}

                                    </td>


                                    {{-- Arrivée --}}

                                    <td class="p-4 font-medium">

                                        {{ $trajet->arrivee }}

                                    </td>


                                    {{-- Date --}}

                                    <td class="p-4">

                                        {{ $trajet->date_depart }}

                                    </td>


                                    {{-- Heure --}}

                                    <td class="p-4">

                                        {{ $trajet->heure_depart }}

                                    </td>


                                    {{-- Prix --}}

                                    <td class="p-4 font-semibold text-green-600">

                                        {{ number_format($trajet->prix, 0, ',', ' ') }}
                                        FCFA

                                    </td>


                                    {{-- Places --}}

                                    <td class="p-4 text-center">

                                        <span class="font-semibold">
                                            {{ $trajet->places_disponibles }}
                                        </span>

                                        /

                                        {{ $trajet->places_totales }}

                                    </td>


                                    {{-- Actions --}}

                                    <td class="p-4">

                                        <div class="flex justify-center gap-2">

                                            {{-- Voir --}}

                                            <a
                                                href="{{ url('/admin/trajets/' . $trajet->id) }}"
                                                class="bg-slate-600 hover:bg-slate-700 text-white px-3 py-2 rounded-lg"
                                                title="Voir"
                                            >
                                                👁️
                                            </a>


                                            {{-- Modifier --}}

                                            <a
                                                href="{{ url('/admin/trajets/' . $trajet->id . '/edit') }}"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg"
                                                title="Modifier"
                                            >
                                                ✏️
                                            </a>


                                            {{-- Supprimer --}}

                                            <form
                                                action="{{ url('/admin/trajets/' . $trajet->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Supprimer ce trajet ?')"
                                            >

                                                @csrf

                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg"
                                                    title="Supprimer"
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

                                        🚍 Aucun trajet trouvé.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- Pagination --}}

                <div class="p-6 border-t">

                    {{ $trajets->links() }}

                </div>

            </div>

        </div>

    </div>

</x-dynamic-component>