<x-layouts.admin :header="'Gestion des abonnements'">

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
                <i class="fa-solid fa-clipboard-list"></i>
            </span>

            Gestion des abonnements

        </h1>

        <a
            href="{{ route('admin.abonnements.create') }}"
            class="tk-btn-accent shrink-0"
        >

            <i class="fa-solid fa-plus"></i>

            <span>Créer un abonnement</span>

        </a>

    </div>


    {{-- Message erreur --}}


    {{-- Recherche et filtres --}}

    <div class="tk-card p-6">

        <form action="/admin/abonnements" method="GET">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- Recherche --}}

                <div>

                    <input
                        type="text"
                        name="recherche"
                        value="{{ request('recherche') }}"
                        placeholder="Rechercher une agence..."
                        class="tk-input">

                </div>


                {{-- Statut --}}

                <div>

                    <select
                        name="statut"
                        class="tk-input">

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

                </div>


                {{-- Nombre de lignes --}}

                <div>

                    <select
                        name="par_page"
                        class="tk-input">

                        @foreach([10,25,50,100] as $nb)

                            <option
                                value="{{ $nb }}"
                                {{ request('par_page', 10) == $nb ? 'selected' : '' }}
                            >
                                {{ $nb }} lignes
                            </option>

                        @endforeach

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
                        {{ $abonnements->total() }}
                    </span>

                    abonnement(s)

                </div>


                <div class="flex gap-3">

                    <a
                        href="/admin/abonnements"
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


    {{-- Tableau des abonnements --}}

    <div class="tk-card overflow-hidden">

        <div class="overflow-x-auto">

            <table
                class="tk-table"
                style="min-width:1100px;"
            >

                {{-- EN-TÊTE --}}

                <thead>

                    <tr>

                        <th class="text-left whitespace-nowrap">
                            Agence
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Type
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Montant
                        </th>

                        <th class="text-center whitespace-nowrap">
                            Début
                        </th>

                        <th class="text-center whitespace-nowrap">
                            Fin
                        </th>

                        <th class="text-center whitespace-nowrap">
                            Statut
                        </th>

                        <th class="text-center whitespace-nowrap">
                            Actions
                        </th>

                    </tr>

                </thead>


                {{-- CORPS DU TABLEAU --}}

                <tbody>

                    @forelse($abonnements as $abonnement)

                        <tr>

                            {{-- AGENCE --}}

                            <td class="font-semibold whitespace-nowrap">

                                {{ $abonnement->agence->nom_agence ?? 'Agence inconnue' }}

                            </td>


                            {{-- TYPE --}}

                            <td class="whitespace-nowrap">

                                <span class="tk-badge tk-badge-navy">

                                    {{ $abonnement->type }}

                                </span>

                            </td>


                            {{-- MONTANT --}}

                            <td class="font-semibold text-emerald-600 whitespace-nowrap">

                                @if($abonnement->montant !== null)

                                    {{ number_format($abonnement->montant, 0, ',', ' ') }} FCFA

                                @else

                                    —

                                @endif

                            </td>


                            {{-- DÉBUT --}}

                            <td class="text-center whitespace-nowrap">

                                @if($abonnement->date_debut)

                                    {{ \Carbon\Carbon::parse($abonnement->date_debut)->format('d/m/Y') }}

                                @else

                                    —

                                @endif

                            </td>


                            {{-- FIN --}}

                            <td class="text-center whitespace-nowrap">

                                @if($abonnement->date_fin)

                                    {{ \Carbon\Carbon::parse($abonnement->date_fin)->format('d/m/Y') }}

                                @else

                                    —

                                @endif

                            </td>


                            {{-- STATUT --}}

                            <td class="text-center whitespace-nowrap">

                                @if($abonnement->statut == 'Actif')

                                    <span class="tk-badge tk-badge-green">

                                        <i class="fa-solid fa-circle-check"></i>

                                        Actif

                                    </span>

                                @elseif($abonnement->statut == 'En attente')

                                    <span class="tk-badge tk-badge-orange">

                                        <i class="fa-solid fa-hourglass-half"></i>

                                        En attente

                                    </span>

                                @else

                                    <span class="tk-badge tk-badge-red">

                                        <i class="fa-solid fa-xmark"></i>

                                        Expiré

                                    </span>

                                @endif

                            </td>


                            {{-- ACTIONS --}}

                            <td class="text-center whitespace-nowrap">

                                @if($abonnement->id)

                                    <div class="flex justify-center items-center gap-2">

                                        {{-- VOIR --}}

                                        <a
                                            href="{{ route('admin.abonnements.show', $abonnement->id) }}"
                                            title="Voir"
                                            class="tk-icon-btn tk-icon-btn-navy"
                                        >

                                            <i class="fa-solid fa-eye"></i>

                                        </a>


                                        {{-- MODIFIER --}}

                                        <a
                                            href="{{ route('admin.abonnements.edit', $abonnement->id) }}"
                                            title="Modifier"
                                            class="tk-icon-btn tk-icon-btn-brand"
                                        >

                                            <i class="fa-solid fa-pen"></i>

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
                                                onclick="event.preventDefault(); tkConfirm('Supprimer cet abonnement ?', () => this.form.submit())"
                                                class="tk-icon-btn tk-icon-btn-red"
                                            >

                                                <i class="fa-solid fa-trash"></i>

                                            </button>

                                        </form>

                                    </div>

                                @else

                                    <span class="text-slate-400 text-sm whitespace-nowrap">

                                        Aucun abonnement créé

                                    </span>

                                @endif

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="tk-empty"
                            >

                                <i class="fa-solid fa-clipboard-list mb-3 block text-4xl mx-auto"></i>

                                <p class="text-lg font-semibold text-slate-500">
                                    Aucun abonnement trouvé.
                                </p>

                                <p class="mt-2 text-sm text-slate-400">
                                    Essayez de modifier les filtres de recherche.
                                </p>

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
                    {{ $abonnements->firstItem() ?? 0 }}
                </span>

                à

                <span class="font-semibold">
                    {{ $abonnements->lastItem() ?? 0 }}
                </span>

                sur

                <span class="font-bold text-brand">
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
