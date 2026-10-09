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
======================================================== --}}

@if($isAgent)

    <x-layouts.agent
        :header="'Mes paiements'"
    >

        <div class="tk-page">


            {{-- =====================================================
                 TITRE
            ====================================================== --}}

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
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </span>

                    Mes paiements

                </h1>

                <p class="mt-2 text-sm text-slate-500">

                    Consultez les paiements de votre agence
                    à TOKENDE.

                </p>

            </div>


            {{-- =====================================================
                 AGENCE
            ====================================================== --}}

            <div class="tk-stat-navy">

                <p class="text-sm text-[#DCE8FF]">
                    Mon agence
                </p>


                <div
                    class="
                        text-2xl
                        font-bold
                        text-white
                        mt-1
                    "
                >

                    {{ $user->agence->nom_agence ?? 'Aucune agence' }}

                </div>


                <div
                    class="
                        text-sm
                        text-[#DCE8FF]
                        mt-2
                    "
                >

                    Agent :

                    <strong class="text-white">

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
                        rounded-xl
                        border
                        bg-white
                        overflow-hidden
                        {{ $estEnRetard
                            ? 'border-red-300'
                            : 'border-[#FFD7B8]'
                        }}
                    "
                >

                    <div
                        class="
                            px-6
                            py-5
                            {{ $estEnRetard
                                ? 'bg-red-50'
                                : 'bg-[#FFF3E8]'
                            }}
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
                                        flex
                                        items-center
                                        gap-2
                                        text-sm
                                        font-semibold
                                        {{ $estEnRetard
                                            ? 'text-red-600'
                                            : 'text-brand'
                                        }}
                                    "
                                >

                                    <i
                                        class="fa-solid {{ $estEnRetard
                                            ? 'fa-triangle-exclamation'
                                            : 'fa-hourglass-half'
                                        }}"
                                    ></i>

                                    {{
                                        $estEnRetard
                                            ? 'Paiement en retard'
                                            : 'Paiement à effectuer'
                                    }}

                                </p>


                                <h2
                                    class="
                                        text-2xl
                                        font-bold
                                        text-navy
                                        mt-2
                                    "
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
                                        text-slate-600
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
                                            text-slate-500
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
                                class="tk-btn-accent shrink-0"
                            >

                                <i class="fa-solid fa-credit-card"></i>

                                <span>Payer maintenant</span>

                            </a>

                        </div>

                    </div>


                    <div class="px-6 py-4">

                        <p
                            class="
                                text-sm
                                text-slate-500
                            "
                        >

                            Référence :

                            <strong class="text-navy">

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

                <div class="tk-kpi">

                    <div
                        class="
                            flex
                            items-start
                            justify-between
                            gap-3
                        "
                    >

                        <div>

                            <p
                                class="
                                    text-sm
                                    font-medium
                                    text-slate-500
                                "
                            >
                                Total payé
                            </p>


                            <p
                                class="
                                    tk-kpi-value
                                    mt-3
                                    text-emerald-600
                                "
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


                        <span
                            class="
                                tk-kpi-icon
                                bg-emerald-50
                                text-emerald-600
                            "
                        >

                            <i class="fa-solid fa-circle-check"></i>

                        </span>

                    </div>

                </div>


                {{-- EN ATTENTE --}}

                <div class="tk-kpi">

                    <div
                        class="
                            flex
                            items-start
                            justify-between
                            gap-3
                        "
                    >

                        <div>

                            <p
                                class="
                                    text-sm
                                    font-medium
                                    text-slate-500
                                "
                            >
                                À payer
                            </p>


                            <p
                                class="
                                    tk-kpi-value
                                    mt-3
                                    text-brand
                                "
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


                        <span
                            class="
                                tk-kpi-icon
                                bg-orange-50
                                text-brand
                            "
                        >

                            <i class="fa-solid fa-hourglass-half"></i>

                        </span>

                    </div>

                </div>


                {{-- PROCHAIN --}}

                <div class="tk-kpi">

                    <div
                        class="
                            flex
                            items-start
                            justify-between
                            gap-3
                        "
                    >

                        <div>

                            <p
                                class="
                                    text-sm
                                    font-medium
                                    text-slate-500
                                "
                            >
                                Prochaine échéance
                            </p>


                            @if($prochainPaiement)

                                <p
                                    class="
                                        tk-kpi-value
                                        mt-3
                                    "
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
                                        font-extrabold
                                        text-navy
                                        mt-2
                                    "
                                >
                                    Aucune
                                </p>

                            @endif

                        </div>


                        <span
                            class="
                                tk-kpi-icon
                                bg-[#EEF4FF]
                                text-navy
                            "
                        >
                            <i class="fa-solid fa-calendar"></i>
                        </span>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 HISTORIQUE
            ====================================================== --}}

            <div class="tk-card overflow-hidden">

                <div class="border-b border-slate-200 px-6 py-5">

                    <h2
                        class="
                            flex
                            items-center
                            gap-2
                            text-xl
                            font-bold
                            text-navy
                        "
                    >

                        <i class="fa-solid fa-list"></i>

                        Historique des paiements

                    </h2>


                    <p
                        class="
                            text-sm
                            text-slate-500
                            mt-1
                        "
                    >

                        Consultez les paiements de votre agence
                        à TOKENDE.

                    </p>

                </div>


                <div class="overflow-x-auto">

                    <table class="tk-table">

                        <thead>

                            <tr>

                                <th class="text-left whitespace-nowrap">
                                    Référence
                                </th>

                                <th class="text-center whitespace-nowrap">
                                    Date prévue
                                </th>

                                <th class="text-center whitespace-nowrap">
                                    Date paiement
                                </th>

                                <th class="text-right whitespace-nowrap">
                                    Montant
                                </th>

                                <th class="text-center whitespace-nowrap">
                                    Statut
                                </th>

                                <th class="text-center whitespace-nowrap">
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


                                <tr>

                                    {{-- RÉFÉRENCE --}}

                                    <td class="whitespace-nowrap">

                                        <span
                                            class="
                                                font-mono
                                                text-sm
                                                font-semibold
                                                text-navy
                                            "
                                        >

                                            {{ $paiement->reference ?? '-' }}

                                        </span>

                                    </td>


                                    {{-- DATE PRÉVUE --}}

                                    <td
                                        class="
                                            text-center
                                            whitespace-nowrap
                                            tabular-nums
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
                                            text-center
                                            whitespace-nowrap
                                            tabular-nums
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
                                            whitespace-nowrap
                                            text-right
                                            font-bold
                                            tabular-nums
                                            text-brand
                                        "
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
                                            text-center
                                            whitespace-nowrap
                                        "
                                    >

                                        @if($affichageStatut === 'payé')

                                            <span
                                                class="
                                                    tk-badge
                                                    tk-badge-green
                                                "
                                            >
                                                <i class="fa-solid fa-circle-check"></i>

                                                Payé
                                            </span>

                                        @elseif($affichageStatut === 'en retard')

                                            <span
                                                class="
                                                    tk-badge
                                                    tk-badge-red
                                                "
                                            >
                                                <i class="fa-solid fa-triangle-exclamation"></i>

                                                En retard
                                            </span>

                                        @elseif($affichageStatut === 'annulé')

                                            <span
                                                class="
                                                    tk-badge
                                                    tk-badge-slate
                                                "
                                            >
                                                Annulé
                                            </span>

                                        @else

                                            <span
                                                class="
                                                    tk-badge
                                                    tk-badge-orange
                                                "
                                            >
                                                <i class="fa-solid fa-hourglass-half"></i>

                                                En attente
                                            </span>

                                        @endif

                                    </td>


                                    {{-- ACTION --}}

                                    <td
                                        class="
                                            text-center
                                            whitespace-nowrap
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
                                                class="tk-btn-accent"
                                            >

                                                <i class="fa-solid fa-credit-card"></i>

                                                <span>Payer</span>

                                            </a>

                                        @else

                                            <span
                                                class="
                                                    text-sm
                                                    font-semibold
                                                    text-slate-400
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
                                        class="tk-empty"
                                    >

                                        <div class="mb-4 block text-4xl text-slate-300">
                                            <i class="fa-solid fa-money-bill-wave"></i>
                                        </div>

                                        <p
                                            class="
                                                font-semibold
                                                text-slate-600
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

                    <div class="border-t border-slate-200 px-6 py-4">

                        {{ $paiements->links() }}

                    </div>

                @endif

            </div>


            <div
                class="
                    text-center
                    text-sm
                    text-slate-400
                    pb-4
                "
            >

                © {{ date('Y') }} TOKENDE

            </div>

        </div>

    </x-layouts.agent>


{{-- =========================================================
     ESPACE ADMIN
======================================================== --}}

@else

    <x-layouts.admin
        :header="'Règlements des agences'"
    >

        <div class="tk-page">

            <div
                class="
                    tk-page-head
                    flex
                    flex-col
                    gap-4
                    md:flex-row
                    md:items-center
                    md:justify-between
                "
            >

                <div>

                    <h1 class="tk-page-title">

                        <span
                            class="
                                flex h-11 w-11 shrink-0
                                items-center justify-center
                                rounded-lg bg-orange-50
                                text-lg text-brand
                            "
                        >
                            <i class="fa-solid fa-money-bill-wave"></i>
                        </span>

                        Paiements des agences

                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Suivez les paiements des agences vers TOKENDE.
                    </p>

                </div>


                <a
                    href="{{ route(
                        'paiements-agences.create'
                    ) }}"
                    class="tk-btn-accent shrink-0"
                >

                    <i class="fa-solid fa-plus"></i>

                    <span>Créer une échéance</span>

                </a>

            </div>


            <div class="tk-card p-6">

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
                        class="tk-input"
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
                        class="tk-input"
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
                        class="tk-input"
                    >


                    <button
                        type="submit"
                        class="tk-btn-accent"
                    >
                        Filtrer
                    </button>

                </form>

            </div>


            <div class="tk-card overflow-hidden">

                <div class="overflow-x-auto">

                    <table class="tk-table">

                        <thead>

                            <tr>

                                <th class="text-left whitespace-nowrap">
                                    Agence
                                </th>

                                <th class="text-left whitespace-nowrap">
                                    Référence
                                </th>

                                <th class="text-center whitespace-nowrap">
                                    Date prévue
                                </th>

                                <th class="text-center whitespace-nowrap">
                                    Date paiement
                                </th>

                                <th class="text-right whitespace-nowrap">
                                    Montant
                                </th>

                                <th class="text-center whitespace-nowrap">
                                    Statut
                                </th>

                                <th class="text-center whitespace-nowrap">
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


                                <tr>

                                    <td class="whitespace-nowrap">

                                        <span
                                            class="
                                                font-semibold
                                                text-navy
                                            "
                                        >

                                            {{ $paiement->agence->nom_agence ?? '-' }}

                                        </span>

                                    </td>


                                    <td class="whitespace-nowrap">

                                        <span
                                            class="font-mono text-sm"
                                        >

                                            {{ $paiement->reference ?? '-' }}

                                        </span>

                                    </td>


                                    <td
                                        class="
                                            text-center
                                            whitespace-nowrap
                                            tabular-nums
                                        "
                                    >

                                        {{ $paiement->date_prevue
                                            ? $paiement->date_prevue->format('d/m/Y')
                                            : '-'
                                        }}

                                    </td>


                                    <td
                                        class="
                                            text-center
                                            whitespace-nowrap
                                            tabular-nums
                                        "
                                    >

                                        {{ $paiement->date_paiement
                                            ? $paiement->date_paiement->format('d/m/Y')
                                            : '-'
                                        }}

                                    </td>


                                    <td
                                        class="
                                            whitespace-nowrap
                                            text-right
                                            font-bold
                                            tabular-nums
                                            text-brand
                                        "
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
                                            text-center
                                            whitespace-nowrap
                                        "
                                    >

                                        @if($statut === 'payé')

                                            <span
                                                class="
                                                    tk-badge
                                                    tk-badge-green
                                                "
                                            >
                                                <i class="fa-solid fa-circle-check"></i>

                                                Payé
                                            </span>

                                        @elseif($statut === 'en retard')

                                            <span
                                                class="
                                                    tk-badge
                                                    tk-badge-red
                                                "
                                            >
                                                <i class="fa-solid fa-triangle-exclamation"></i>

                                                En retard
                                            </span>

                                        @elseif($statut === 'annulé')

                                            <span
                                                class="
                                                    tk-badge
                                                    tk-badge-slate
                                                "
                                            >
                                                Annulé
                                            </span>

                                        @else

                                            <span
                                                class="
                                                    tk-badge
                                                    tk-badge-orange
                                                "
                                            >
                                                <i class="fa-solid fa-hourglass-half"></i>

                                                En attente
                                            </span>

                                        @endif

                                    </td>


                                    <td
                                        class="
                                            text-center
                                            whitespace-nowrap
                                        "
                                    >

                                        @if($statut !== 'payé')

                                            <div
                                                class="
                                                    flex flex-wrap
                                                    items-center
                                                    justify-center
                                                    gap-2
                                                "
                                            >

                                                <a
                                                    href="{{ route(
                                                        'paiements-agences.pay.form',
                                                        $paiement->id
                                                    ) }}"
                                                    class="tk-btn-ghost"
                                                >
                                                    <i class="fa-solid fa-credit-card"></i>

                                                    <span>Payer</span>
                                                </a>

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'paiements-agences.paid',
                                                    $paiement->id
                                                ) }}"
                                                onsubmit="event.preventDefault(); tkConfirm('Marquer ce paiement comme payé ?', () => this.submit())"
                                            >

                                                @csrf

                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="
                                                        tk-btn
                                                        bg-emerald-600
                                                        text-white
                                                        hover:bg-emerald-700
                                                    "
                                                >
                                                    <i class="fa-solid fa-check"></i>

                                                    <span>Valider paiement</span>
                                                </button>

                                            </form>

                                            </div>

                                        @else

                                            <span
                                                class="
                                                    text-sm
                                                    font-semibold
                                                    text-slate-400
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
                                        class="tk-empty"
                                    >

                                        <div class="mb-3 block text-4xl text-slate-300">
                                            <i class="fa-solid fa-money-bill-wave"></i>
                                        </div>

                                        Aucun paiement enregistré.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                @if($paiements->hasPages())

                    <div class="border-t border-slate-200 px-6 py-4">

                        {{ $paiements->links() }}

                    </div>

                @endif

            </div>

        </div>

    </x-layouts.admin>

@endif
