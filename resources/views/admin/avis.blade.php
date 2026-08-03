<x-layouts.admin>

<div class="py-12">

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Titre -->

        <h1 class="text-3xl font-bold text-slate-800 mb-8">
            ⭐ Gestion des avis
        </h1>

        <!-- Barre de recherche -->

        <div class="bg-white rounded-xl shadow p-6 mb-6">

            <form action="/admin/avis" method="GET">

                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                    <!-- Recherche -->

                    <input
                        type="text"
                        name="recherche"
                        value="{{ request('recherche') }}"
                        placeholder="🔍 Rechercher un voyageur..."
                        class="border rounded-xl px-4 py-3">

                    <!-- Note -->

                    <select
                        name="note"
                        class="border rounded-xl px-4 py-3">

                        <option value="">Toutes les notes</option>

                        @for($i=5;$i>=1;$i--)

                            <option
                                value="{{ $i }}"
                                {{ request('note') == $i ? 'selected' : '' }}>

                                {{ $i }} ⭐

                            </option>

                        @endfor

                    </select>

                    <!-- Date -->

                    <input
                        type="date"
                        name="date"
                        value="{{ request('date') }}"
                        class="border rounded-xl px-4 py-3">

                    <!-- Pagination -->

                    <select
                        name="par_page"
                        class="border rounded-xl px-4 py-3">

                        @foreach([10,25,50,100] as $nb)

                            <option
                                value="{{ $nb }}"
                                {{ request('par_page',10)==$nb ? 'selected' : '' }}>

                                {{ $nb }} lignes

                            </option>

                        @endforeach

                    </select>

                    <!-- Boutons -->

                    <div class="flex gap-2">

                        <button
                            type="submit"
                            class="flex-1 bg-orange-500 hover:bg-orange-600 text-white rounded-xl">

                            Filtrer

                        </button>

                        <a
                            href="/admin/avis"
                            class="flex-1 bg-gray-600 hover:bg-gray-700 text-white rounded-xl flex items-center justify-center">

                            Réinitialiser

                        </a>

                    </div>

                </div>

                <div class="mt-5 text-gray-600">

                    Total :
                    <span class="font-bold text-orange-600">

                        {{ $avis->total() }}

                    </span>

                    avis

                </div>

            </form>

        </div>

        <!-- Tableau -->

        <div class="bg-white rounded-xl shadow overflow-hidden">

            <table class="min-w-full">

                <thead class="bg-slate-100">

                    <tr>

                        <th class="p-4 text-left">Voyageur</th>
                        <th class="p-4 text-center">Note</th>
                        <th class="p-4 text-left">Commentaire</th>
                        <th class="p-4 text-center">Date</th>
                        <th class="p-4 text-center">Actions</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($avis as $a)
                    <tr class="border-t hover:bg-orange-50 transition">

    <td class="p-4 font-semibold">
        {{ $a->user?->name ?? 'Utilisateur supprimé' }}
    </td>

    <td class="p-4 text-center">

        @for($i = 1; $i <= 5; $i++)

            @if($i <= $a->note)

                ⭐

            @else

                ☆

            @endif

        @endfor

    </td>

    <td class="p-4">

        @if($a->commentaire)

            {{ $a->commentaire }}

        @else

            <span class="text-gray-400 italic">
                Aucun commentaire
            </span>

        @endif

    </td>

    <td class="p-4 text-center">

        {{ $a->created_at->format('d/m/Y') }}

    </td>

    <td class="p-4">

        <div class="flex justify-center gap-2">

            <!-- Voir -->

            <a
                href="/admin/avis/{{ $a->id }}"
                class="bg-slate-600 hover:bg-slate-700 text-white px-3 py-2 rounded-lg">

                👁️

            </a>

            <!-- Supprimer -->

            <form
                action="/admin/avis/{{ $a->id }}"
                method="POST">

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    onclick="return confirm('Voulez-vous vraiment supprimer cet avis ?')"
                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg">

                    🗑️

                </button>

            </form>

        </div>

    </td>

</tr>

@empty

<tr>

    <td
        colspan="5"
        class="text-center py-10 text-gray-500">

        Aucun avis trouvé.

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
            {{ $avis->firstItem() ?? 0 }}
        </span>

        à

        <span class="font-semibold">
            {{ $avis->lastItem() ?? 0 }}
        </span>

        sur

        <span class="font-semibold text-orange-600">
            {{ $avis->total() }}
        </span>

        avis

    </div>

    <div>

        {{ $avis->links() }}

    </div>

</div>

</div>

</div>

</div>

</x-layouts.admin>