<x-layouts.admin :header="'Gestion des billets'">

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
                <i class="fa-solid fa-ticket"></i>
            </span>

            Gestion des billets

        </h1>

    </div>


    {{-- Barre de recherche --}}

    <div class="tk-card p-6">

        <form action="/admin/billets" method="GET">

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                {{-- Recherche --}}

                <input
                    type="text"
                    name="recherche"
                    value="{{ request('recherche') }}"
                    placeholder="Numéro de billet ou voyageur..."
                    class="tk-input">


                {{-- Agence --}}

                <select
                    name="agence"
                    class="tk-input">

                    <option value="">
                        Toutes les agences
                    </option>

                    @foreach($listeAgences as $agence)

                        <option
                            value="{{ $agence->id }}"
                            {{ request('agence') == $agence->id ? 'selected' : '' }}>

                            {{ $agence->nom_agence }}

                        </option>

                    @endforeach

                </select>


                {{-- Date --}}

                <input
                    type="date"
                    name="date"
                    value="{{ request('date') }}"
                    class="tk-input">


                {{-- Pagination --}}

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


                {{-- Boutons --}}

                <div class="flex gap-2">

                    <button
                        type="submit"
                        class="tk-btn-accent flex-1">

                        Filtrer

                    </button>

                    <a
                        href="/admin/billets"
                        class="tk-btn-ghost flex-1">

                        Réinitialiser

                    </a>

                </div>

            </div>


            {{-- Informations --}}

            <div class="mt-5 text-sm text-slate-500">

                Total :

                <span class="font-bold text-brand">

                    {{ $billets->total() }}

                </span>

                billet(s)

            </div>

        </form>

    </div>


    {{-- Vérification d'un billet --}}

    <div class="tk-card p-6">

        <h2 class="mb-4 flex items-center gap-2.5 text-base font-bold text-navy">

            <i class="fa-solid fa-search text-brand"></i>

            Vérifier un billet

        </h2>

        <form action="{{ route('admin.billets.verifier') }}" method="POST">

            @csrf

            <div class="flex flex-col md:flex-row gap-3">

                <input
                    type="text"
                    name="numero_billet"
                    placeholder="Exemple : TOK-2026-000157"
                    class="tk-input flex-1"
                    required
                >

                <button
                    type="submit"
                    class="tk-btn-accent px-6">

                    Vérifier le billet

                </button>

            </div>

        </form>

    </div>


    {{-- Tableau --}}

    <div class="tk-card overflow-hidden">

        <div class="overflow-x-auto">

            <table class="tk-table">

                {{-- EN-TÊTE --}}

                <thead>

                    <tr>

                        <th class="text-left whitespace-nowrap">
                            N° Billet
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Voyageur
                        </th>

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
                            QR Code
                        </th>

                        <th class="text-center whitespace-nowrap">
                            Actions
                        </th>

                    </tr>

                </thead>


                {{-- CORPS DU TABLEAU --}}

                <tbody>

                    @forelse($billets as $billet)

                        <tr>

                            {{-- N° BILLET --}}

                            <td class="font-semibold whitespace-nowrap">

                                {{ $billet->numero_billet }}

                            </td>


                            {{-- VOYAGEUR --}}

                            <td class="whitespace-nowrap">

                                {{ $billet->reservation?->user?->name ?? 'Utilisateur supprimé' }}

                            </td>


                            {{-- AGENCE --}}

                            <td class="whitespace-nowrap">

                                {{ $billet->reservation?->trajet?->agence?->nom_agence ?? '-' }}

                            </td>


                            {{-- DÉPART --}}

                            <td class="whitespace-nowrap">

                                {{ $billet->reservation?->trajet?->depart ?? '-' }}

                            </td>


                            {{-- ARRIVÉE --}}

                            <td class="whitespace-nowrap">

                                {{ $billet->reservation?->trajet?->arrivee ?? '-' }}

                            </td>


                            {{-- QR CODE --}}

                            <td class="whitespace-nowrap">

                                {{ $billet->qr_code }}

                            </td>


                            {{-- ACTIONS --}}

                            <td class="text-center whitespace-nowrap">

                                <div class="flex justify-center items-center gap-2">

                                    {{-- VOIR --}}

                                    <a
                                        href="/admin/billets/{{ $billet->id }}"
                                        title="Voir"
                                        class="tk-icon-btn tk-icon-btn-navy"
                                    >

                                        <i class="fa-solid fa-eye"></i>

                                    </a>


                                    {{-- IMPRIMER --}}

                                    <a
                                        href="/admin/billets/{{ $billet->id }}"
                                        target="_blank"
                                        title="Imprimer"
                                        class="tk-icon-btn bg-emerald-600 hover:bg-emerald-700"
                                    >

                                        <i class="fa-solid fa-print"></i>

                                    </a>


                                    {{-- SUPPRIMER --}}

                                    <form
                                        action="/admin/billets/{{ $billet->id }}"
                                        method="POST"
                                        class="m-0"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Supprimer"
                                            onclick="event.preventDefault(); tkConfirm('Voulez-vous vraiment supprimer ce billet ?', () => this.form.submit())"
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
                                colspan="7"
                                class="tk-empty"
                            >

                                Aucun billet trouvé.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}

        <div class="border-t border-slate-200 px-6 py-4">

            <div
                class="
                    flex flex-col gap-3
                    sm:flex-row sm:items-center
                    sm:justify-between
                "
            >

                <div class="text-sm text-slate-500">

                    Affichage de

                    <span class="font-semibold">
                        {{ $billets->firstItem() ?? 0 }}
                    </span>

                    à

                    <span class="font-semibold">
                        {{ $billets->lastItem() ?? 0 }}
                    </span>

                    sur

                    <span class="font-bold text-brand">
                        {{ $billets->total() }}
                    </span>

                    billet(s)

                </div>

                {{ $billets->links() }}

            </div>

        </div>

    </div>

</div>

</x-layouts.admin>
