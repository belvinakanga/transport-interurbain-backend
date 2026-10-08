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

    <div class="py-10">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- =========================================================
                 TITRE
            ========================================================== --}}

            <div class="mb-6">

                <h1 class="text-3xl font-bold text-[#0A2A66]">

                    @if(auth()->user()->role === 'agent')
                        💳 Achats de mon agence
                    @else
                        💳 Gestion des achats
                    @endif

                </h1>


                @if(auth()->user()->role === 'agent')

                    <p class="mt-2 text-gray-500">

                        Agence :

                        <strong class="text-[#0A2A66]">
                            {{ auth()->user()->agence->nom_agence ?? 'Aucune agence' }}
                        </strong>

                    </p>

                @else

                    <p class="mt-2 text-gray-500">
                        Consultez l'ensemble des achats effectués sur la plateforme.
                    </p>

                @endif

            </div>


            {{-- =========================================================
                 MESSAGE
            ========================================================== --}}

            @if(session('success'))

                <div class="mb-6 bg-green-100 border border-green-200 text-green-700 px-5 py-4 rounded-xl">

                    {{ session('success') }}

                </div>

            @endif


            {{-- =========================================================
                 TABLEAU
            ========================================================== --}}

            <div class="bg-white rounded-2xl shadow overflow-hidden">

                <div class="overflow-x-auto">

                    <table
    class="w-max min-w-full"
    style="table-layout: auto; width: max-content; min-width: 100%; white-space: nowrap; word-break: normal; overflow-wrap: normal;"
>

                        <thead class="bg-[#F8F9FB] border-b">

                            <tr>

                                <th class="p-4 text-left whitespace-nowrap">
                                    Voyageur
                                </th>

                                <th class="p-4 text-left whitespace-nowrap">
                                    Agence
                                </th>

                                <th class="p-4 text-left whitespace-nowrap">
                                    Trajet
                                </th>

                                <th class="p-4 text-center whitespace-nowrap">
                                    Date
                                </th>

                                <th class="p-4 text-center whitespace-nowrap">
                                    Siège
                                </th>

                                <th class="p-4 text-right whitespace-nowrap min-w-[150px]">
    Montant payé
</th>

<th class="p-4 text-right whitespace-nowrap min-w-[140px]">
    Part agence
</th>
                                <th class="p-4 text-left whitespace-nowrap">
                                    Référence billet
                                </th>

                                <th class="p-4 text-left whitespace-nowrap">
                                    Référence achat
                                </th>

                                <th class="p-4 text-center whitespace-nowrap">
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


                                <tr class="border-b hover:bg-[#FFF8F3] transition">


                                    {{-- =================================================
                                         VOYAGEUR
                                    ================================================== --}}

                                    <td class="p-4 whitespace-nowrap min-w-max break-normal">

                                        @if($billets->isNotEmpty())

                                            @foreach($billets as $billet)

                                                @php

                                                    $voyageur =
                                                        $billet->voyageur;

                                                @endphp


                                                <div class="mb-4 last:mb-0">

                                                    @if($voyageur)

                                                        <div class="font-semibold text-[#0A2A66] whitespace-nowrap">
                                                         {{ $voyageur->prenom }} {{ $voyageur->nom }}
                                                        </div>

                                                        @if(!empty($voyageur->email))

                                                            <div class="text-sm text-gray-500 mt-1">

                                                                {{ $voyageur->email }}

                                                            </div>

                                                        @endif


                                                        @if(!empty($voyageur->telephone))

                                                            <div class="text-sm text-gray-600 mt-1 whitespace-nowrap">
                                                             📞 {{ $voyageur->telephone }}
                                                           </div>

                                                        @endif

                                                    @else

                                                        <div class="font-semibold text-gray-500">

                                                            Voyageur introuvable

                                                        </div>

                                                    @endif

                                                </div>

                                            @endforeach

                                        @else

                                            {{-- Compatibilité avec les anciens achats --}}

                                            <div class="font-semibold text-[#0A2A66]">

                                                {{ $achat->user->name ?? 'Utilisateur supprimé' }}

                                            </div>


                                            @if($achat->user)

                                                <div class="text-sm text-gray-500 mt-1">

                                                    {{ $achat->user->email }}

                                                </div>

                                            @endif

                                        @endif

                                    </td>


                                    {{-- =================================================
                                         AGENCE
                                    ================================================== --}}

                                    <td class="p-4 whitespace-nowrap min-w-max break-normal">

                                        <span class="font-medium whitespace-nowrap">
                                         {{ $agence->nom_agence ?? '-' }}
                                        </span>

                                    </td>


                                    {{-- =================================================
                                         TRAJET
                                    ================================================== --}}

                                    <td class="p-4 font-medium whitespace-nowrap min-w-max break-normal">

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

                                    <td class="p-4 text-center whitespace-nowrap">

                                        {{ optional($achat->created_at)->format('d/m/Y') }}

                                    </td>


                                    {{-- =================================================
                                         SIÈGE
                                    ================================================== --}}

                                    <td class="p-4 text-center whitespace-nowrap">

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

                                                        <span class="text-gray-400">

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

<td class="p-4 text-right whitespace-nowrap min-w-max break-normal">

    <span class="font-bold text-[#FF6B00]">
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

<td class="p-4 text-right whitespace-nowrap min-w-max break-normal">

    <span class="font-bold text-green-600">
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

                                    <td class="p-4 whitespace-nowrap min-w-max">

                                        @if($billets->isNotEmpty())

                                            @foreach($billets as $billet)

                                                <div class="mb-4 last:mb-0">

                                                    <span class="font-mono font-bold text-blue-700 whitespace-nowrap">
                                                    {{ $billet->numero_billet }}
                                                   </span>

                                                </div>

                                            @endforeach

                                        @else

                                            <span class="text-gray-400">
                                                —
                                            </span>

                                        @endif

                                    </td>


                                    {{-- =================================================
                                         RÉFÉRENCE ACHAT
                                    ================================================== --}}

                                    <td class="p-4 whitespace-nowrap min-w-max">

                                        <span class="font-mono text-sm text-gray-600 whitespace-nowrap">
                                         {{ $achat->reference ?? '-' }}
                                        </span>

                                    </td>


                                    {{-- =================================================
                                         STATUT
                                    ================================================== --}}

                                    <td class="p-4 text-center whitespace-nowrap">

                                        @if(
                                            strtolower((string) $achat->statut)
                                            === 'payé'
                                        )

                                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700">

                                                ✓ Payé

                                            </span>

                                        @elseif(
                                            strtolower((string) $achat->statut)
                                            === 'en attente'
                                        )

                                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700">

                                                En attente

                                            </span>

                                        @elseif(
                                            strtolower((string) $achat->statut)
                                            === 'annulé'
                                            ||
                                            strtolower((string) $achat->statut)
                                            === 'annulée'
                                        )

                                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-700">

                                                Annulé

                                            </span>

                                        @else

                                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-700">

                                                {{ ucfirst($achat->statut ?? '-') }}

                                            </span>

                                        @endif

                                    </td>

                                </tr>


                            @empty

                                <tr>

                                    <td
                                        colspan="10"
                                        class="py-12 text-center text-gray-500"
                                    >

                                        <div class="text-4xl mb-3">
                                            💳
                                        </div>

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

                    <div class="p-6 border-t">

                        {{ $achats->links() }}

                    </div>

                @endif

            </div>

        </div>

    </div>

</x-dynamic-component>