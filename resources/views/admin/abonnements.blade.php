<x-layouts.admin>

<div class="py-12">

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Titre -->

        <div class="flex justify-between items-center mb-6">

            <h1 class="text-3xl font-bold text-slate-800">
                💼 Gestion des abonnements
            </h1>

            <a
                href="{{ route('admin.abonnements.create') }}"
                class="inline-flex items-center gap-2 px-5 py-3 rounded-xl text-white font-bold shadow-md transition hover:opacity-90"
                style="background:#FF6B00;"
            >
                ➕
                Créer un abonnement
            </a>

        </div>


        <!-- Message succès -->

        @if(session('success'))

            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6">

                {{ session('success') }}

            </div>

        @endif


        <!-- Message erreur -->

        @if(session('error'))

            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">

                {{ session('error') }}

            </div>

        @endif


        <!-- Recherche -->

        <div class="bg-white rounded-xl shadow p-6 mb-6">

            <form action="/admin/abonnements" method="GET">

                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                    <!-- Recherche -->

                    <input
                        type="text"
                        name="recherche"
                        value="{{ request('recherche') }}"
                        placeholder="🔍 Rechercher une agence..."
                        class="border rounded-xl px-4 py-3"
                    >


                    <!-- Statut -->

                    <select
                        name="statut"
                        class="border rounded-xl px-4 py-3"
                    >

                        <option value="">
                            Tous les statuts
                        </option>

                        <option
                            value="Actif"
                            {{ request('statut') == 'Actif' ? 'selected' : '' }}
                        >
                            Actif
                        </option>

                        <option
                            value="Expiré"
                            {{ request('statut') == 'Expiré' ? 'selected' : '' }}
                        >
                            Expiré
                        </option>

                        <option
                            value="En attente"
                            {{ request('statut') == 'En attente' ? 'selected' : '' }}
                        >
                            En attente
                        </option>

                    </select>


                    <!-- Nombre de lignes -->

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


                    <!-- Filtrer -->

                    <button
                        type="submit"
                        class="bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-semibold"
                    >
                        Filtrer
                    </button>


                    <!-- Réinitialiser -->

                    <a
                        href="/admin/abonnements"
                        class="bg-slate-600 hover:bg-slate-700 text-white rounded-xl flex items-center justify-center font-semibold"
                    >
                        Réinitialiser
                    </a>

                </div>


                <!-- Total -->

                <div class="mt-5 text-gray-600">

                    Total :

                    <span class="font-bold text-orange-600">
                        {{ $abonnements->total() }}
                    </span>

                    abonnement(s)

                </div>

            </form>

        </div>


        <!-- Tableau -->

        <div class="bg-white rounded-xl shadow overflow-x-auto">

            <table class="w-full min-w-[1100px]">

                <thead class="bg-slate-100">

                    <tr>

                        <th class="p-4 text-left whitespace-nowrap">
                            Agence
                        </th>

                        <th class="p-4 text-left whitespace-nowrap">
                            Type
                        </th>

                        <th class="p-4 text-left whitespace-nowrap">
                            Montant
                        </th>

                        <th class="p-4 text-center whitespace-nowrap">
                            Début
                        </th>

                        <th class="p-4 text-center whitespace-nowrap">
                            Fin
                        </th>

                        <th class="p-4 text-center whitespace-nowrap">
                            Statut
                        </th>

                        <th class="p-4 text-center whitespace-nowrap">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($abonnements as $abonnement)

                        <tr class="border-t hover:bg-orange-50 transition duration-200">

                            {{-- AGENCE --}}

                            <td class="p-4 font-semibold text-slate-800 whitespace-nowrap">

                                {{ $abonnement->agence->nom_agence ?? 'Agence inconnue' }}

                            </td>


                            {{-- TYPE --}}

                            <td class="p-4 whitespace-nowrap">

                                <span class="inline-flex items-center bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold whitespace-nowrap">

                                    {{ $abonnement->type }}

                                </span>

                            </td>


                            {{-- MONTANT --}}

                            <td class="p-4 font-semibold text-green-600 whitespace-nowrap">

                                @if($abonnement->montant !== null)

                                    {{ number_format($abonnement->montant, 0, ',', ' ') }} FCFA

                                @else

                                    —

                                @endif

                            </td>


                            {{-- DÉBUT --}}

                            <td class="p-4 text-center whitespace-nowrap">

                                @if($abonnement->date_debut)

                                    {{ \Carbon\Carbon::parse($abonnement->date_debut)->format('d/m/Y') }}

                                @else

                                    —

                                @endif

                            </td>


                            {{-- FIN --}}

                            <td class="p-4 text-center whitespace-nowrap">

                                @if($abonnement->date_fin)

                                    {{ \Carbon\Carbon::parse($abonnement->date_fin)->format('d/m/Y') }}

                                @else

                                    —

                                @endif

                            </td>


                            {{-- STATUT --}}

                            <td class="p-4 text-center whitespace-nowrap">

                                @if($abonnement->statut == 'Actif')

                                    <span class="inline-flex items-center whitespace-nowrap bg-green-100 text-green-700 px-3 py-1 rounded-full font-semibold">

                                        ✅ Actif

                                    </span>

                                @elseif($abonnement->statut == 'En attente')

                                    <span class="inline-flex items-center whitespace-nowrap bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full font-semibold">

                                        ⏳ En attente

                                    </span>

                                @else

                                    <span class="inline-flex items-center whitespace-nowrap bg-red-100 text-red-700 px-3 py-1 rounded-full font-semibold">

                                        ❌ Expiré

                                    </span>

                                @endif

                            </td>


                            {{-- ACTIONS --}}

                            <td class="p-4 text-center whitespace-nowrap">

                                @if($abonnement->id)

                                    <div class="flex justify-center items-center gap-2">

                                        {{-- VOIR --}}

                                        <a
                                            href="{{ route('admin.abonnements.show', $abonnement->id) }}"
                                            title="Voir"
                                            class="w-10 h-10 flex items-center justify-center rounded-lg bg-slate-600 hover:bg-slate-700 text-white"
                                        >
                                            👁️
                                        </a>


                                        {{-- MODIFIER --}}

                                        <a
                                            href="{{ route('admin.abonnements.edit', $abonnement->id) }}"
                                            title="Modifier"
                                            class="w-10 h-10 flex items-center justify-center rounded-lg bg-orange-500 hover:bg-orange-600 text-white"
                                        >
                                            ✏️
                                        </a>


                                        {{-- SUPPRIMER --}}

                                        <form
                                            action="{{ route('admin.abonnements.destroy', $abonnement->id) }}"
                                            method="POST"
                                            class="m-0"
                                        >

                                            @csrf

                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                title="Supprimer"
                                                onclick="return confirm('Supprimer cet abonnement ?')"
                                                class="w-10 h-10 flex items-center justify-center rounded-lg bg-red-600 hover:bg-red-700 text-white"
                                            >
                                                🗑️
                                            </button>

                                        </form>

                                    </div>

                                @else

                                    <span class="text-gray-400 text-sm whitespace-nowrap">

                                        Aucun abonnement créé

                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="text-center py-12 text-gray-500"
                            >

                                <div class="text-5xl mb-3">
                                    💼
                                </div>

                                <p class="text-lg font-semibold">
                                    Aucun abonnement trouvé.
                                </p>

                                <p class="text-sm text-gray-400 mt-2">
                                    Essayez de modifier les filtres de recherche.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <!-- Pagination -->

        <div class="bg-white rounded-xl shadow mt-4 p-6 flex flex-col md:flex-row justify-between items-center gap-4">

            <div class="text-gray-600">

                Affichage de

                <span class="font-semibold">
                    {{ $abonnements->firstItem() ?? 0 }}
                </span>

                à

                <span class="font-semibold">
                    {{ $abonnements->lastItem() ?? 0 }}
                </span>

                sur

                <span class="font-bold text-orange-600">
                    {{ $abonnements->total() }}
                </span>

                abonnement(s)

            </div>


            <div>

                {{ $abonnements->links() }}

            </div>

        </div>

    </div>

</div>

</x-layouts.admin>