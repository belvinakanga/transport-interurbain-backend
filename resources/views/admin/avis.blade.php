<x-layouts.admin :header="'Gestion des avis'">

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
                <i class="fa-solid fa-star"></i>
            </span>

            Gestion des avis

        </h1>

    </div>


    {{-- Barre de recherche et filtres --}}

    <div class="tk-card p-6">

        <form action="/admin/avis" method="GET">

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                {{-- Recherche --}}

                <div>

                    <input
                        type="text"
                        name="recherche"
                        value="{{ request('recherche') }}"
                        placeholder="Rechercher un voyageur..."
                        class="tk-input">

                </div>


                {{-- Note --}}

                <div>

                    <select
                        name="note"
                        class="tk-input">

                        <option value="">Toutes les notes</option>

                        @for($i=5;$i>=1;$i--)

                            <option
                                value="{{ $i }}"
                                {{ request('note') == $i ? 'selected' : '' }}>

                                {{ $i }}

                            </option>

                        @endfor

                    </select>

                </div>


                {{-- Date --}}

                <div>

                    <input
                        type="date"
                        name="date"
                        value="{{ request('date') }}"
                        class="tk-input">

                </div>


                {{-- Pagination --}}

                <div>

                    <select
                        name="par_page"
                        class="tk-input">

                        @foreach([10,25,50,100] as $nb)

                            <option
                                value="{{ $nb }}"
                                {{ request('par_page',10)==$nb ? 'selected' : '' }}>

                                {{ $nb }} lignes

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Boutons --}}

                <div class="flex gap-2">

                    <button
                        type="submit"
                        class="tk-btn-accent flex-1"
                    >

                        Filtrer

                    </button>

                    <a
                        href="/admin/avis"
                        class="tk-btn-ghost flex-1"
                    >

                        Réinitialiser

                    </a>

                </div>

            </div>


            {{-- Informations --}}

            <div class="mt-5 text-sm text-slate-500">

                Total :

                <span class="font-bold text-brand">

                    {{ $avis->total() }}

                </span>

                avis

            </div>

        </form>

    </div>


    {{-- Tableau des avis --}}

    <div class="tk-card overflow-hidden">

        <div class="overflow-x-auto">

            <table class="tk-table">

                {{-- EN-TÊTE --}}

                <thead>

                    <tr>

                        <th class="text-left whitespace-nowrap">
                            Voyageur
                        </th>

                        <th class="text-center whitespace-nowrap">
                            Note
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Commentaire
                        </th>

                        <th class="text-center whitespace-nowrap">
                            Date
                        </th>

                        <th class="text-center whitespace-nowrap">
                            Actions
                        </th>

                    </tr>

                </thead>


                {{-- CORPS DU TABLEAU --}}

                <tbody>

                    @forelse($avis as $a)

                        <tr>


                            {{-- VOYAGEUR --}}

                            <td class="font-semibold whitespace-nowrap">

                                {{ $a->user?->name ?? 'Utilisateur supprimé' }}

                            </td>


                            {{-- NOTE --}}

                            <td class="text-center whitespace-nowrap">

                                <span class="inline-flex gap-0.5">

                                    @for($i = 1; $i <= 5; $i++)

                                        @if($i <= $a->note)

                                            <i class="fa-solid fa-star text-brand"></i>

                                        @else

                                            <i class="fa-solid fa-star text-slate-300"></i>

                                        @endif

                                    @endfor

                                </span>

                            </td>


                            {{-- COMMENTAIRE --}}

                            <td>

                                @if($a->commentaire)

                                    {{ $a->commentaire }}

                                @else

                                    <span class="text-slate-400 italic">
                                        Aucun commentaire
                                    </span>

                                @endif

                            </td>


                            {{-- DATE --}}

                            <td class="text-center whitespace-nowrap tabular-nums">

                                {{ $a->created_at->format('d/m/Y') }}

                            </td>


                            {{-- ACTIONS --}}

                            <td class="text-center whitespace-nowrap">

                                <div class="flex justify-center items-center gap-2">

                                    {{-- VOIR --}}

                                    <a
                                        href="/admin/avis/{{ $a->id }}"
                                        class="tk-icon-btn tk-icon-btn-navy"
                                    >

                                        <i class="fa-solid fa-eye"></i>

                                    </a>


                                    {{-- SUPPRIMER --}}

                                    <form
                                        action="/admin/avis/{{ $a->id }}"
                                        method="POST"
                                        class="m-0"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            onclick="event.preventDefault(); tkConfirm('Voulez-vous vraiment supprimer cet avis ?', () => this.form.submit())"
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
                                colspan="5"
                                class="tk-empty"
                            >

                                Aucun avis trouvé.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}

        <div
            class="
                border-t border-slate-200 px-6 py-4
                flex flex-col md:flex-row
                justify-between items-center gap-4
            "
        >

            <div class="text-sm text-slate-500">

                Affichage de

                <span class="font-semibold">
                    {{ $avis->firstItem() ?? 0 }}
                </span>

                à

                <span class="font-semibold">
                    {{ $avis->lastItem() ?? 0 }}
                </span>

                sur

                <span class="font-bold text-brand">
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

</x-layouts.admin>
