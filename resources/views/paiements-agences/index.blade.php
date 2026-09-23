@php

    $user = auth()->user();

    $isAgent = $user->role === 'agent';


    /*
    |--------------------------------------------------------------------------
    | STATISTIQUES
    |--------------------------------------------------------------------------
    */

    $totalPaye = $paiements
        ->filter(function ($paiement) {

            return strtolower(
                trim(
                    $paiement->statut
                )
            ) === 'payé';

        })
        ->sum('montant');


    $totalEnAttente = $paiements
        ->filter(function ($paiement) {

            return in_array(
                strtolower(
                    trim(
                        $paiement->statut
                    )
                ),
                [
                    'en attente',
                    'en retard'
                ]
            );

        })
        ->sum('montant');


    /*
    |--------------------------------------------------------------------------
    | PROCHAIN PAIEMENT
    |--------------------------------------------------------------------------
    */

    $prochainPaiement = $paiements
        ->filter(function ($paiement) {

            $statut = strtolower(
                trim(
                    $paiement->statut
                )
            );

            return in_array(
                $statut,
                [
                    'en attente',
                    'en retard'
                ]
            );

        })
        ->sortBy(function ($paiement) {

            return $paiement
                ->date_prevue
                ? $paiement->date_prevue->timestamp
                : PHP_INT_MAX;

        })
        ->first();

@endphp



{{-- =========================================================
     ESPACE AGENT
========================================================= --}}

@if($isAgent)

    <x-layouts.agent
        :header="'Mes paiements'"
    >

        <div class="space-y-6">


            {{-- =====================================================
                 TITRE
            ====================================================== --}}

            <div>

                <h1
                    class="
                        text-3xl
                        md:text-4xl
                        font-bold
                        flex
                        items-center
                        gap-3
                    "
                    style="color:#0A2A66;"
                >

                    💰

                    <span>
                        Mes paiements
                    </span>

                </h1>


                <p class="mt-2 text-gray-500">

                    Consultez les paiements de votre agence
                    à TOKENDE.

                </p>

            </div>



            {{-- =====================================================
                 MESSAGE SUCCÈS
            ====================================================== --}}

            @if(session('success'))

                <div
                    class="rounded-xl p-4"
                    style="
                        background:#DCFCE7;
                        color:#15803D;
                    "
                >

                    ✅
                    {{ session('success') }}

                </div>

            @endif



            {{-- =====================================================
                 AGENCE
            ====================================================== --}}

            <div
                class="
                    rounded-2xl
                    shadow-md
                    px-6
                    py-5
                "
                style="
                    background:#0A2A66;
                "
            >

                <p
                    class="text-sm"
                    style="color:#DCE8FF;"
                >
                    Mon agence
                </p>


                <div
                    class="
                        text-2xl
                        font-bold
                        mt-1
                    "
                    style="color:#FFFFFF;"
                >

                    {{ $user->agence->nom_agence ?? 'Aucune agence' }}

                </div>


                <div
                    class="
                        text-sm
                        mt-2
                    "
                    style="color:#DCE8FF;"
                >

                    Agent :

                    <strong style="color:#FFFFFF;">

                        {{ $user->name }}

                    </strong>

                </div>

            </div>



            {{-- =====================================================
                 PAIEMENT À RÉGLER
            ====================================================== --}}

            @if($prochainPaiement)

                @php

                    $statutProchain =
                        strtolower(
                            trim(
                                $prochainPaiement->statut
                            )
                        );

                    $estEnRetard =
                        $statutProchain === 'en attente'
                        &&
                        $prochainPaiement->date_prevue
                        &&
                        $prochainPaiement->date_prevue->isPast();

                @endphp


                <div
                    class="
                        bg-white
                        rounded-2xl
                        shadow-sm
                        border
                        overflow-hidden
                    "
                    style="
                        border-color:
                        {{ $estEnRetard
                            ? '#FCA5A5'
                            : '#FFD7B8' }};
                    "
                >

                    <div
                        class="px-6 py-5"
                        style="
                            background:
                            {{ $estEnRetard
                                ? '#FEF2F2'
                                : '#FFF3E8' }};
                        "
                    >

                        <div
                            class="
                                flex
                                flex-col
                                md:flex-row
                                md:items-center
                                md:justify-between
                                gap-5
                            "
                        >

                            <div>

                                <p
                                    class="
                                        text-sm
                                        font-semibold
                                    "
                                    style="
                                        color:
                                        {{ $estEnRetard
                                            ? '#DC2626'
                                            : '#FF6B00' }};
                                    "
                                >

                                    {{ $estEnRetard
                                        ? '🔴 Paiement en retard'
                                        : '⏳ Paiement à effectuer'
                                    }}

                                </p>


                                <h2
                                    class="
                                        text-2xl
                                        font-bold
                                        mt-2
                                    "
                                    style="color:#0A2A66;"
                                >

                                    {{ number_format(
                                        $prochainPaiement->montant,
                                        0,
                                        ',',
                                        ' '
                                    ) }}

                                    FCFA

                                </h2>


                                <p
                                    class="
                                        text-sm
                                        text-gray-600
                                        mt-2
                                    "
                                >

                                    Échéance :

                                    <strong>

                                        {{ $prochainPaiement->date_prevue
                                            ? $prochainPaiement->date_prevue->format('d/m/Y')
                                            : '-'
                                        }}

                                    </strong>

                                </p>


                                @if($prochainPaiement->abonnement)

                                    <p
                                        class="
                                            text-sm
                                            text-gray-500
                                            mt-1
                                        "
                                    >

                                        Abonnement :

                                        <strong>

                                            {{ $prochainPaiement->abonnement->type }}

                                        </strong>

                                    </p>

                                @endif

                            </div>


                            {{-- BOUTON PAYER --}}

                            <a
                                href="{{ route(
                                    'paiements-agences.pay.form',
                                    $prochainPaiement->id
                                ) }}"
                                class="
                                    inline-flex
                                    items-center
                                    justify-center
                                    px-6
                                    py-3
                                    rounded-xl
                                    text-white
                                    font-bold
                                    shadow-md
                                    transition
                                "
                                style="
                                    background:#FF6B00;
                                "
                            >

                                💳

                                &nbsp;

                                Payer maintenant

                            </a>

                        </div>

                    </div>


                    <div class="px-6 py-4">

                        <p
                            class="
                                text-sm
                                text-gray-500
                            "
                        >

                            Référence :

                            <strong
                                style="color:#0A2A66;"
                            >

                                {{ $prochainPaiement->reference }}

                            </strong>

                        </p>

                    </div>

                </div>

            @endif



            {{-- =====================================================
                 STATISTIQUES
            ====================================================== --}}

            <div
                class="
                    grid
                    grid-cols-1
                    md:grid-cols-2
                    xl:grid-cols-3
                    gap-5
                "
            >


                {{-- TOTAL PAYÉ --}}

                <div
                    class="
                        bg-white
                        rounded-2xl
                        shadow-sm
                        border
                        border-gray-100
                        p-6
                    "
                >

                    <div
                        class="
                            flex
                            items-center
                            justify-between
                        "
                    >

                        <div>

                            <p
                                class="
                                    text-sm
                                    text-gray-500
                                "
                            >
                                Total payé
                            </p>


                            <p
                                class="
                                    text-3xl
                                    font-bold
                                    mt-2
                                "
                                style="color:#16A34A;"
                            >

                                {{ number_format(
                                    $totalPaye,
                                    0,
                                    ',',
                                    ' '
                                ) }}

                                FCFA

                            </p>

                        </div>


                        <div
                            class="
                                w-14
                                h-14
                                rounded-full
                                flex
                                items-center
                                justify-center
                                text-2xl
                            "
                            style="background:#DCFCE7;"
                        >

                            ✅

                        </div>

                    </div>

                </div>



                {{-- EN ATTENTE --}}

                <div
                    class="
                        bg-white
                        rounded-2xl
                        shadow-sm
                        border
                        border-gray-100
                        p-6
                    "
                >

                    <div
                        class="
                            flex
                            items-center
                            justify-between
                        "
                    >

                        <div>

                            <p
                                class="
                                    text-sm
                                    text-gray-500
                                "
                            >
                                À payer
                            </p>


                            <p
                                class="
                                    text-3xl
                                    font-bold
                                    mt-2
                                "
                                style="color:#FF6B00;"
                            >

                                {{ number_format(
                                    $totalEnAttente,
                                    0,
                                    ',',
                                    ' '
                                ) }}

                                FCFA

                            </p>

                        </div>


                        <div
                            class="
                                w-14
                                h-14
                                rounded-full
                                flex
                                items-center
                                justify-center
                                text-2xl
                            "
                            style="background:#FFF3E8;"
                        >

                            ⏳

                        </div>

                    </div>

                </div>



                {{-- PROCHAIN --}}

                <div
                    class="
                        bg-white
                        rounded-2xl
                        shadow-sm
                        border
                        border-gray-100
                        p-6
                    "
                >

                    <div
                        class="
                            flex
                            items-center
                            justify-between
                        "
                    >

                        <div>

                            <p
                                class="
                                    text-sm
                                    text-gray-500
                                "
                            >
                                Prochaine échéance
                            </p>


                            @if($prochainPaiement)

                                <p
                                    class="
                                        text-2xl
                                        font-bold
                                        mt-2
                                    "
                                    style="color:#0A2A66;"
                                >

                                    {{ $prochainPaiement->date_prevue
                                        ? $prochainPaiement->date_prevue->format('d/m/Y')
                                        : '-'
                                    }}

                                </p>

                            @else

                                <p
                                    class="
                                        text-xl
                                        font-bold
                                        mt-2
                                    "
                                    style="color:#0A2A66;"
                                >
                                    Aucune
                                </p>

                            @endif

                        </div>


                        <div
                            class="
                                w-14
                                h-14
                                rounded-full
                                flex
                                items-center
                                justify-center
                                text-2xl
                            "
                            style="background:#EEF4FF;"
                        >
                            📅
                        </div>

                    </div>

                </div>

            </div>



            {{-- =====================================================
                 HISTORIQUE
            ====================================================== --}}

            <div
                class="
                    bg-white
                    rounded-2xl
                    shadow-sm
                    border
                    border-gray-100
                    overflow-hidden
                "
            >

                <div
                    class="
                        px-6
                        py-5
                        border-b
                    "
                    style="background:#F6F8FC;"
                >

                    <h2
                        class="
                            text-xl
                            font-bold
                        "
                        style="color:#0A2A66;"
                    >

                        📋 Historique des paiements

                    </h2>


                    <p
                        class="
                            text-sm
                            text-gray-500
                            mt-1
                        "
                    >

                        Consultez les paiements de votre agence
                        à TOKENDE.

                    </p>

                </div>


                <div class="overflow-x-auto">

                    <table class="min-w-full">

                        <thead
                            style="background:#EEF4FF;"
                        >

                            <tr>

                                <th class="p-4 text-left">
                                    Référence
                                </th>

                                <th class="p-4 text-center">
                                    Date prévue
                                </th>

                                <th class="p-4 text-center">
                                    Date paiement
                                </th>

                                <th class="p-4 text-right">
                                    Montant
                                </th>

                                <th class="p-4 text-center">
                                    Statut
                                </th>

                                <th class="p-4 text-center">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($paiements as $paiement)

                                @php

                                    $statut =
                                        strtolower(
                                            trim(
                                                (string)
                                                $paiement->statut
                                            )
                                        );

                                    $affichageStatut =
                                        $statut;


                                    if (
                                        $statut === 'en attente'
                                        &&
                                        $paiement->date_prevue
                                        &&
                                        $paiement
                                            ->date_prevue
                                            ->isPast()
                                    ) {

                                        $affichageStatut =
                                            'en retard';

                                    }

                                @endphp


                                <tr
                                    class="
                                        border-t
                                        border-gray-100
                                        hover:bg-gray-50
                                        transition
                                    "
                                >

                                    {{-- RÉFÉRENCE --}}

                                    <td class="p-4">

                                        <span
                                            class="
                                                font-mono
                                                text-sm
                                                font-semibold
                                            "
                                            style="color:#0A2A66;"
                                        >

                                            {{ $paiement->reference ?? '-' }}

                                        </span>

                                    </td>


                                    {{-- DATE PRÉVUE --}}

                                    <td
                                        class="
                                            p-4
                                            text-center
                                        "
                                    >

                                        {{ $paiement->date_prevue
                                            ? $paiement->date_prevue->format('d/m/Y')
                                            : '-'
                                        }}

                                    </td>


                                    {{-- DATE PAIEMENT --}}

                                    <td
                                        class="
                                            p-4
                                            text-center
                                        "
                                    >

                                        {{ $paiement->date_paiement
                                            ? $paiement->date_paiement->format('d/m/Y')
                                            : '-'
                                        }}

                                    </td>


                                    {{-- MONTANT --}}

                                    <td
                                        class="
                                            p-4
                                            text-right
                                            font-bold
                                        "
                                        style="color:#FF6B00;"
                                    >

                                        {{ number_format(
                                            $paiement->montant,
                                            0,
                                            ',',
                                            ' '
                                        ) }}

                                        FCFA

                                    </td>


                                    {{-- STATUT --}}

                                    <td
                                        class="
                                            p-4
                                            text-center
                                        "
                                    >

                                        @if($affichageStatut === 'payé')

                                            <span
                                                class="
                                                    inline-flex
                                                    items-center
                                                    px-3
                                                    py-1
                                                    rounded-full
                                                    text-xs
                                                    font-bold
                                                "
                                                style="
                                                    background:#DCFCE7;
                                                    color:#15803D;
                                                "
                                            >
                                                ✅ Payé
                                            </span>

                                        @elseif($affichageStatut === 'en retard')

                                            <span
                                                class="
                                                    inline-flex
                                                    items-center
                                                    px-3
                                                    py-1
                                                    rounded-full
                                                    text-xs
                                                    font-bold
                                                "
                                                style="
                                                    background:#FEE2E2;
                                                    color:#DC2626;
                                                "
                                            >
                                                🔴 En retard
                                            </span>

                                        @elseif($affichageStatut === 'annulé')

                                            <span
                                                class="
                                                    inline-flex
                                                    items-center
                                                    px-3
                                                    py-1
                                                    rounded-full
                                                    text-xs
                                                    font-bold
                                                "
                                                style="
                                                    background:#F3F4F6;
                                                    color:#6B7280;
                                                "
                                            >
                                                Annulé
                                            </span>

                                        @else

                                            <span
                                                class="
                                                    inline-flex
                                                    items-center
                                                    px-3
                                                    py-1
                                                    rounded-full
                                                    text-xs
                                                    font-bold
                                                "
                                                style="
                                                    background:#FFF3E8;
                                                    color:#FF6B00;
                                                "
                                            >
                                                ⏳ En attente
                                            </span>

                                        @endif

                                    </td>


                                    {{-- ACTION --}}

                                    <td
                                        class="
                                            p-4
                                            text-center
                                        "
                                    >

                                        @if(
                                            $statut !== 'payé'
                                            &&
                                            $statut !== 'annulé'
                                        )

                                            <a
                                                href="{{ route(
                                                    'paiements-agences.pay.form',
                                                    $paiement->id
                                                ) }}"
                                                class="
                                                    inline-flex
                                                    items-center
                                                    justify-center
                                                    px-3
                                                    py-2
                                                    rounded-lg
                                                    text-white
                                                    text-sm
                                                    font-bold
                                                "
                                                style="
                                                    background:#FF6B00;
                                                "
                                            >

                                                💳 Payer

                                            </a>

                                        @else

                                            <span
                                                class="
                                                    text-sm
                                                    font-semibold
                                                    text-gray-400
                                                "
                                            >
                                                -

                                            </span>

                                        @endif

                                    </td>

                                </tr>


                            @empty

                                <tr>

                                    <td
                                        colspan="6"
                                        class="
                                            text-center
                                            py-14
                                            text-gray-500
                                        "
                                    >

                                        <div
                                            class="
                                                text-5xl
                                                mb-4
                                            "
                                        >
                                            💰
                                        </div>

                                        <p
                                            class="
                                                font-semibold
                                                text-gray-700
                                            "
                                        >
                                            Aucun paiement enregistré
                                        </p>

                                        <p
                                            class="
                                                text-sm
                                                mt-1
                                            "
                                        >
                                            Aucun paiement n’a encore
                                            été enregistré pour votre
                                            agence.
                                        </p>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                @if($paiements->hasPages())

                    <div
                        class="
                            p-5
                            border-t
                            border-gray-100
                        "
                    >

                        {{ $paiements->links() }}

                    </div>

                @endif

            </div>


            <div
                class="
                    text-center
                    text-sm
                    text-gray-400
                    pb-4
                "
            >

                © {{ date('Y') }} TOKENDE

            </div>

        </div>

    </x-layouts.agent>


{{-- =========================================================
     ESPACE ADMIN
========================================================= --}}

@else

    <x-layouts.admin
        :header="'Règlements des agences'"
    >

        <div class="space-y-6">

            <div
                class="
                    flex
                    flex-col
                    md:flex-row
                    md:items-center
                    md:justify-between
                    gap-4
                "
            >

                <div>

                    <h1
                        class="
                            text-3xl
                            md:text-4xl
                            font-bold
                        "
                        style="color:#0A2A66;"
                    >
                        💰 Paiements des agences
                    </h1>

                    <p class="text-gray-500 mt-2">
                        Suivez les paiements des agences vers TOKENDE.
                    </p>

                </div>


                <a
                    href="{{ route(
                        'paiements-agences.create'
                    ) }}"
                    class="
                        inline-flex
                        items-center
                        justify-center
                        px-5
                        py-3
                        rounded-xl
                        text-white
                        font-bold
                        shadow-md
                    "
                    style="background:#FF6B00;"
                >
                    ➕ Créer une échéance
                </a>

            </div>


            @if(session('success'))

                <div
                    class="rounded-xl p-4"
                    style="
                        background:#DCFCE7;
                        color:#15803D;
                    "
                >

                    ✅ {{ session('success') }}

                </div>

            @endif


            <div
                class="
                    bg-white
                    rounded-2xl
                    shadow-sm
                    border
                    border-gray-100
                    p-6
                "
            >

                <form
                    method="GET"
                    action="{{ route(
                        'paiements-agences.index'
                    ) }}"
                    class="
                        grid
                        grid-cols-1
                        md:grid-cols-4
                        gap-4
                    "
                >

                    <select
                        name="agence"
                        class="
                            border
                            border-gray-200
                            rounded-xl
                            px-4
                            py-3
                        "
                    >

                        <option value="">
                            Toutes les agences
                        </option>

                        @foreach($listeAgences as $agence)

                            <option
                                value="{{ $agence->id }}"
                                {{ request('agence') == $agence->id
                                    ? 'selected'
                                    : ''
                                }}
                            >

                                {{ $agence->nom_agence }}

                            </option>

                        @endforeach

                    </select>


                    <select
                        name="statut"
                        class="
                            border
                            border-gray-200
                            rounded-xl
                            px-4
                            py-3
                        "
                    >

                        <option value="">
                            Tous les statuts
                        </option>

                        <option
                            value="en attente"
                            {{ request('statut') === 'en attente'
                                ? 'selected'
                                : ''
                            }}
                        >
                            En attente
                        </option>

                        <option
                            value="payé"
                            {{ request('statut') === 'payé'
                                ? 'selected'
                                : ''
                            }}
                        >
                            Payé
                        </option>

                        <option
                            value="en retard"
                            {{ request('statut') === 'en retard'
                                ? 'selected'
                                : ''
                            }}
                        >
                            En retard
                        </option>

                        <option
                            value="annulé"
                            {{ request('statut') === 'annulé'
                                ? 'selected'
                                : ''
                            }}
                        >
                            Annulé
                        </option>

                    </select>


                    <input
                        type="date"
                        name="date"
                        value="{{ request('date') }}"
                        class="
                            border
                            border-gray-200
                            rounded-xl
                            px-4
                            py-3
                        "
                    >


                    <button
                        type="submit"
                        class="
                            rounded-xl
                            text-white
                            font-bold
                            px-4
                            py-3
                        "
                        style="background:#0A2A66;"
                    >
                        Filtrer
                    </button>

                </form>

            </div>


            <div
                class="
                    bg-white
                    rounded-2xl
                    shadow-sm
                    border
                    border-gray-100
                    overflow-hidden
                "
            >

                <div class="overflow-x-auto">

                    <table class="min-w-full">

                        <thead
                            style="background:#EEF4FF;"
                        >

                            <tr>

                                <th class="p-4 text-left">
                                    Agence
                                </th>

                                <th class="p-4 text-left">
                                    Référence
                                </th>

                                <th class="p-4 text-center">
                                    Date prévue
                                </th>

                                <th class="p-4 text-center">
                                    Date paiement
                                </th>

                                <th class="p-4 text-right">
                                    Montant
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

                            @forelse($paiements as $paiement)

                                @php

                                    $statut =
                                        strtolower(
                                            trim(
                                                (string)
                                                $paiement->statut
                                            )
                                        );

                                    if (
                                        $statut === 'en attente'
                                        &&
                                        $paiement->date_prevue
                                        &&
                                        $paiement->date_prevue->isPast()
                                    ) {
                                        $statut =
                                            'en retard';
                                    }

                                @endphp


                                <tr
                                    class="
                                        border-t
                                        border-gray-100
                                        hover:bg-gray-50
                                        transition
                                    "
                                >

                                    <td class="p-4">

                                        <span
                                            class="
                                                font-semibold
                                            "
                                            style="color:#0A2A66;"
                                        >

                                            {{ $paiement->agence->nom_agence ?? '-' }}

                                        </span>

                                    </td>


                                    <td class="p-4">

                                        <span
                                            class="font-mono text-sm"
                                        >

                                            {{ $paiement->reference ?? '-' }}

                                        </span>

                                    </td>


                                    <td class="p-4 text-center">

                                        {{ $paiement->date_prevue
                                            ? $paiement->date_prevue->format('d/m/Y')
                                            : '-'
                                        }}

                                    </td>


                                    <td class="p-4 text-center">

                                        {{ $paiement->date_paiement
                                            ? $paiement->date_paiement->format('d/m/Y')
                                            : '-'
                                        }}

                                    </td>


                                    <td
                                        class="
                                            p-4
                                            text-right
                                            font-bold
                                        "
                                        style="color:#FF6B00;"
                                    >

                                        {{ number_format(
                                            $paiement->montant,
                                            0,
                                            ',',
                                            ' '
                                        ) }}

                                        FCFA

                                    </td>


                                    <td
                                        class="
                                            p-4
                                            text-center
                                        "
                                    >

                                        @if($statut === 'payé')

                                            <span
                                                class="
                                                    px-3
                                                    py-1
                                                    rounded-full
                                                    text-xs
                                                    font-bold
                                                "
                                                style="
                                                    background:#DCFCE7;
                                                    color:#15803D;
                                                "
                                            >
                                                ✅ Payé
                                            </span>

                                        @elseif($statut === 'en retard')

                                            <span
                                                class="
                                                    px-3
                                                    py-1
                                                    rounded-full
                                                    text-xs
                                                    font-bold
                                                "
                                                style="
                                                    background:#FEE2E2;
                                                    color:#DC2626;
                                                "
                                            >
                                                🔴 En retard
                                            </span>

                                        @elseif($statut === 'annulé')

                                            <span
                                                class="
                                                    px-3
                                                    py-1
                                                    rounded-full
                                                    text-xs
                                                    font-bold
                                                "
                                                style="
                                                    background:#F3F4F6;
                                                    color:#6B7280;
                                                "
                                            >
                                                Annulé
                                            </span>

                                        @else

                                            <span
                                                class="
                                                    px-3
                                                    py-1
                                                    rounded-full
                                                    text-xs
                                                    font-bold
                                                "
                                                style="
                                                    background:#FFF3E8;
                                                    color:#FF6B00;
                                                "
                                            >
                                                ⏳ En attente
                                            </span>

                                        @endif

                                    </td>


                                    <td
                                        class="
                                            p-4
                                            text-center
                                        "
                                    >

                                        @if($statut !== 'payé')

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'paiements-agences.paid',
                                                    $paiement->id
                                                ) }}"
                                                onsubmit="
                                                    return confirm(
                                                        'Marquer ce paiement comme payé ?'
                                                    )
                                                "
                                            >

                                                @csrf

                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="
                                                        px-3
                                                        py-2
                                                        rounded-lg
                                                        text-white
                                                        font-semibold
                                                    "
                                                    style="
                                                        background:#16A34A;
                                                    "
                                                >
                                                    ✓ Valider paiement
                                                </button>

                                            </form>

                                        @else

                                            <span
                                                class="
                                                    text-sm
                                                    font-semibold
                                                    text-gray-400
                                                "
                                            >
                                                Terminé
                                            </span>

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="7"
                                        class="
                                            text-center
                                            py-12
                                            text-gray-500
                                        "
                                    >

                                        <div
                                            class="
                                                text-5xl
                                                mb-3
                                            "
                                        >
                                            💰
                                        </div>

                                        Aucun paiement enregistré.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                @if($paiements->hasPages())

                    <div
                        class="
                            p-5
                            border-t
                            border-gray-100
                        "
                    >

                        {{ $paiements->links() }}

                    </div>

                @endif

            </div>

        </div>

    </x-layouts.admin>

@endif