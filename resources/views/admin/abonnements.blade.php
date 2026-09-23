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

        <div class="bg-white rounded-xl shadow overflow-hidden">

            <table class="min-w-full">

                <thead class="bg-slate-100">

                    <tr>

                        <th class="p-4 text-left">
                            Agence
                        </th>

                        <th class="p-4 text-left">
                            Type
                        </th>

                        <th class="p-4 text-left">
                            Montant
                        </th>

                        <th class="p-4 text-center">
                            Début
                        </th>

                        <th class="p-4 text-center">
                            Fin
                        </th>

                        <th class="p-4 text-center">
                            Statut
                        </th>

                        <th class="p-4 text-center">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($abonnements as $abonnement)

                        <tr class="border-t hover:bg-orange-50 transition duration-200">

                            <!-- Agence -->

                            <td class="p-4 font-semibold text-slate-800">

                                {{ $abonnement->agence->nom_agence ?? 'Agence inconnue' }}

                            </td>


                            <!-- Type -->

                            <td class="p-4">

                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">

                                    {{ $abonnement->type }}

                                </span>

                            </td>


                            <!-- Montant -->

                            <td class="p-4 font-semibold text-green-600">

                                {{ number_format($abonnement->montant, 0, ',', ' ') }} FCFA

                            </td>


                            <!-- Date début -->

                            <td class="p-4 text-center">

                                {{ \Carbon\Carbon::parse($abonnement->date_debut)->format('d/m/Y') }}

                            </td>


                            <!-- Date fin -->

                            <td class="p-4 text-center">

                                {{ \Carbon\Carbon::parse($abonnement->date_fin)->format('d/m/Y') }}

                            </td>


                            <!-- Statut -->

                            <td class="p-4 text-center">

                                @if($abonnement->statut == 'Actif')

                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full font-semibold">

                                        ✅ Actif

                                    </span>

                                @else

                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full font-semibold">

                                        ❌ Expiré

                                    </span>

                                @endif

                            </td>


                            <!-- Actions -->

                            <td class="p-4 text-center">

                                <div class="flex justify-center items-center gap-2">

                                    <!-- Voir -->

                                    <a
                                        href="{{ route('admin.abonnements.show', $abonnement->id) }}"
                                        title="Voir"
                                        class="w-10 h-10 flex items-center justify-center rounded-lg bg-slate-600 hover:bg-slate-700 text-white"
                                    >
                                        👁️
                                    </a>


                                    <!-- Modifier -->

                                    <a
                                        href="{{ route('admin.abonnements.edit', $abonnement->id) }}"
                                        title="Modifier"
                                        class="w-10 h-10 flex items-center justify-center rounded-lg bg-orange-500 hover:bg-orange-600 text-white"
                                    >
                                        ✏️
                                    </a>


                                    <!-- Supprimer -->

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


            <!-- Pagination -->

            <div class="p-6 border-t flex flex-col md:flex-row justify-between items-center gap-4">

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

</div>

</x-layouts.admin>