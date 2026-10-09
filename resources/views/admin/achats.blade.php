@php
    $layout = auth()->user()->role === 'agent'
        ? 'layouts.agent'
        : 'layouts.admin';

    $pageTitle = auth()->user()->role === 'agent'
        ? 'Achats de mon agence'
        : 'Gestion des achats';
@endphp

<x-dynamic-component
    :component="$layout"
    :header="$pageTitle"
>

<div class="tk-page">

    {{-- =========================================================
         TITRE
    ========================================================== --}}

    <div class="tk-page-head">

        <h1 class="tk-page-title">

            <span
                class="
                    flex h-11 w-11 shrink-0
                    items-center justify-center
                    rounded-lg bg-orange-50
                    text-lg text-brand
                "
            >
                <i class="fa-solid fa-credit-card"></i>
            </span>

            @if(auth()->user()->role === 'agent')
                Achats de mon agence
            @else
                Gestion des achats
            @endif

        </h1>


        @if(auth()->user()->role === 'agent')

            <p class="mt-2 text-sm text-slate-500">

                Agence :

                <strong class="text-navy">
                    {{ auth()->user()->agence->nom_agence ?? 'Aucune agence' }}
                </strong>

            </p>

        @else

            <p class="mt-2 text-sm text-slate-500">
                Consultez l'ensemble des achats effectués sur la plateforme.
            </p>

        @endif

    </div>


    {{-- =========================================================
         TABLEAU
    ========================================================== --}}

    <div class="tk-card overflow-hidden">

        <div class="overflow-x-auto">

            <table
                class="tk-table"
                style="table-layout: auto; width: max-content; min-width: 100%; white-space: nowrap; word-break: normal; overflow-wrap: normal;"
            >

                <thead>

                    <tr>

                        <th class="text-left whitespace-nowrap">
                            Voyageur
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Agence
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Trajet
                        </th>

                        <th class="text-center whitespace-nowrap">
                            Date
                        </th>

                        <th class="text-center whitespace-nowrap">
                            Siège
                        </th>

                        <th class="text-right whitespace-nowrap min-w-[150px]">
                            Montant payé
                        </th>

                        <th class="text-right whitespace-nowrap min-w-[140px]">
                            Part agence
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Référence billet
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Référence achat
                        </th>

                        <th class="text-center whitespace-nowrap">
                            Statut
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($achats as $achat)

                        @php

                            /*
                            |--------------------------------------------------------------------------
                            | TRAJET
                            |--------------------------------------------------------------------------
                            */

                            $trajet =
                                $achat->trajet
                                ?? $achat->reservation?->trajet;


                            /*
                            |--------------------------------------------------------------------------
                            | AGENCE
                            |--------------------------------------------------------------------------
                            */

                            $agence =
                                $trajet?->agence;


                            /*
                            |--------------------------------------------------------------------------
                            | BILLETS
                            |--------------------------------------------------------------------------
                            */

                            $billets =
                                $achat->reservation?->billets
                                ?? collect();


                            /*
                            |--------------------------------------------------------------------------
                            | SIÈGES
                            |--------------------------------------------------------------------------
                            */

                            $sieges =
                                $achat->reservation?->sieges
                                ?? collect();

                        @endphp


                        <tr>


                            {{-- =================================================
                                 VOYAGEUR
                            ================================================== --}}

                            <td class="whitespace-nowrap min-w-max break-normal">

                                @if($billets->isNotEmpty())

                                    @foreach($billets as $billet)

                                        @php

                                            $voyageur =
                                                $billet->voyageur;

                                        @endphp


                                        <div class="mb-4 last:mb-0">

                                            @if($voyageur)

                                                <div class="font-semibold text-navy whitespace-nowrap">
                                                    {{ $voyageur->prenom }} {{ $voyageur->nom }}
                                                </div>

                                                @if(!empty($voyageur->email))

                                                    <div class="text-sm text-slate-500 mt-1">

                                                        {{ $voyageur->email }}

                                                    </div>

                                                @endif


                                                @if(!empty($voyageur->telephone))

                                                    <div class="text-sm text-slate-600 mt-1 whitespace-nowrap">

                                                        <i class="fa-solid fa-phone text-slate-400"></i>

                                                        {{ $voyageur->telephone }}

                                                    </div>

                                                @endif

                                            @else

                                                <div class="font-semibold text-slate-500">

                                                    Voyageur introuvable

                                                </div>

                                            @endif

                                        </div>

                                    @endforeach

                                @else

                                    {{-- Compatibilité avec les anciens achats --}}

                                    <div class="font-semibold text-navy">

                                        {{ $achat->user->name ?? 'Utilisateur supprimé' }}

                                    </div>


                                    @if($achat->user)

                                        <div class="text-sm text-slate-500 mt-1">

                                            {{ $achat->user->email }}

                                        </div>

                                    @endif

                                @endif

                            </td>


                            {{-- =================================================
                                 AGENCE
                            ================================================== --}}

                            <td class="whitespace-nowrap min-w-max break-normal">

                                <span class="font-medium whitespace-nowrap">
                                    {{ $agence->nom_agence ?? '-' }}
                                </span>

                            </td>


                            {{-- =================================================
                                 TRAJET
                            ================================================== --}}

                            <td class="font-medium whitespace-nowrap min-w-max break-normal">

                                @if($trajet)

                                    <span class="whitespace-nowrap">
                                        {{ $trajet->depart }} → {{ $trajet->arrivee }}
                                    </span>

                                @else

                                    -

                                @endif

                            </td>


                            {{-- =================================================
                                 DATE
                            ================================================== --}}

                            <td class="text-center whitespace-nowrap">

                                {{ optional($achat->created_at)->format('d/m/Y') }}

                            </td>


                            {{-- =================================================
                                 SIÈGE
                            ================================================== --}}

                            <td class="text-center whitespace-nowrap">

                                @if($billets->isNotEmpty())

                                    @foreach($billets as $billet)

                                        @php

                                            $voyageur =
                                                $billet->voyageur;

                                            $siege =
                                                $voyageur
                                                    ? $sieges->firstWhere(
                                                        'voyageur_id',
                                                        $voyageur->id
                                                    )
                                                    : null;

                                        @endphp


                                        <div class="mb-4 last:mb-0">

                                            @if($siege)

                                                <span class="font-semibold">

                                                    {{ $siege->numero_siege }}

                                                </span>

                                            @else

                                                <span class="text-slate-400">

                                                    —

                                                </span>

                                            @endif

                                        </div>

                                    @endforeach

                                @elseif($sieges->isNotEmpty())

                                    {{ $sieges->pluck('numero_siege')->implode(', ') }}

                                @else

                                    -

                                @endif

                            </td>


                            {{-- =================================================
                                 MONTANT PAYÉ
                            ================================================== --}}

                            <td class="text-right whitespace-nowrap min-w-max break-normal">

                                <span class="font-bold text-brand">
                                    {{ number_format(
                                        $achat->montant,
                                        0,
                                        ',',
                                        ' '
                                    ) }}
                                    FCFA
                                </span>

                            </td>


                            {{-- =================================================
                                 PART AGENCE
                            ================================================== --}}

                            <td class="text-right whitespace-nowrap min-w-max break-normal">

                                <span class="font-bold text-emerald-600">
                                    {{ number_format(
                                        ($achat->montant_base ?? 0) + ($achat->part_agence ?? 0),
                                        0,
                                        ',',
                                        ' '
                                    ) }}
                                    FCFA
                                </span>

                            </td>


                            {{-- =================================================
                                 RÉFÉRENCE BILLET
                            ================================================== --}}

                            <td class="whitespace-nowrap min-w-max">

                                @if($billets->isNotEmpty())

                                    @foreach($billets as $billet)

                                        <div class="mb-4 last:mb-0">

                                            <span class="font-mono font-bold text-navy whitespace-nowrap">
                                                {{ $billet->numero_billet }}
                                            </span>

                                        </div>

                                    @endforeach

                                @else

                                    <span class="text-slate-400">
                                        —
                                    </span>

                                @endif

                            </td>


                            {{-- =================================================
                                 RÉFÉRENCE ACHAT
                            ================================================== --}}

                            <td class="whitespace-nowrap min-w-max">

                                <span class="font-mono text-sm text-slate-600 whitespace-nowrap">
                                    {{ $achat->reference ?? '-' }}
                                </span>

                            </td>


                            {{-- =================================================
                                 STATUT
                            ================================================== --}}

                            <td class="text-center whitespace-nowrap">

                                @if(
                                    strtolower((string) $achat->statut)
                                    === 'payé'
                                )

                                    <span class="tk-badge tk-badge-green">

                                        <i class="fa-solid fa-circle-check"></i>

                                        Payé

                                    </span>

                                @elseif(
                                    strtolower((string) $achat->statut)
                                    === 'en attente'
                                )

                                    <span class="tk-badge tk-badge-orange">

                                        <i class="fa-solid fa-hourglass-half"></i>

                                        En attente

                                    </span>

                                @elseif(
                                    strtolower((string) $achat->statut)
                                    === 'annulé'
                                    ||
                                    strtolower((string) $achat->statut)
                                    === 'annulée'
                                )

                                    <span class="tk-badge tk-badge-red">

                                        <i class="fa-solid fa-triangle-exclamation"></i>

                                        Annulé

                                    </span>

                                @else

                                    <span class="tk-badge tk-badge-slate">

                                        {{ ucfirst($achat->statut ?? '-') }}

                                    </span>

                                @endif

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="10"
                                class="tk-empty"
                            >

                                <p class="font-semibold text-lg">
                                    Aucun achat trouvé
                                </p>

                                @if(auth()->user()->role === 'agent')

                                    <p class="mt-2 text-sm">

                                        Aucun achat n'a encore été effectué
                                        sur les trajets de votre agence.

                                    </p>

                                @endif

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- =========================================================
             PAGINATION
        ========================================================== --}}

        @if(method_exists($achats, 'links'))

            <div class="border-t border-slate-200 px-6 py-4">

                {{ $achats->links() }}

            </div>

        @endif

    </div>

</div>

</x-dynamic-component>
