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
                    <i class="fa-solid fa-credit-card"></i>
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
                    mt-1
                    text-2xl
                    font-bold
                    text-white
                "
            >

                {{ $agence->nom_agence }}

            </div>

            <div
                class="
                    mt-2
                    text-sm
                    text-[#DCE8FF]
                "
            >

                Paiements dus à TOKENDE

            </div>

        </div>


        {{-- =====================================================
             CALCULS
        ====================================================== --}}

        @php

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


            $prochainPaiement = $paiements
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
                ->sortBy(function ($paiement) {

                    return $paiement->date_prevue
                        ? $paiement->date_prevue->timestamp
                        : PHP_INT_MAX;

                })
                ->first();

        @endphp


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


            {{-- PAYÉ --}}

            <div class="tk-card p-6">

                <div
                    class="
                        flex
                        items-center
                        justify-between
                    "
                >

                    <div>

                        <p class="tk-label text-slate-500">
                            Total payé
                        </p>

                        <p
                            class="
                                mt-2
                                text-3xl
                                font-extrabold
                                tabular-nums
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

                    <div
                        class="
                            w-14
                            h-14
                            shrink-0
                            rounded-full
                            flex
                            items-center
                            justify-center
                            text-2xl
                            bg-emerald-50
                            text-emerald-600
                        "
                    >
                        <i class="fa-solid fa-circle-check"></i>
                    </div>

                </div>

            </div>


            {{-- À PAYER --}}

            <div class="tk-card p-6">

                <div
                    class="
                        flex
                        items-center
                        justify-between
                    "
                >

                    <div>

                        <p class="tk-label text-slate-500">
                            À payer
                        </p>

                        <p
                            class="
                                mt-2
                                text-3xl
                                font-extrabold
                                tabular-nums
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

                    <div
                        class="
                            w-14
                            h-14
                            shrink-0
                            rounded-full
                            flex
                            items-center
                            justify-center
                            text-2xl
                            bg-orange-50
                            text-brand
                        "
                    >
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>

                </div>

            </div>


            {{-- PROCHAINE ÉCHÉANCE --}}

            <div class="tk-card p-6">

                <div
                    class="
                        flex
                        items-center
                        justify-between
                    "
                >

                    <div>

                        <p class="tk-label text-slate-500">
                            Prochaine échéance
                        </p>

                        @if($prochainPaiement)

                            <p
                                class="
                                    mt-2
                                    text-2xl
                                    font-extrabold
                                    tabular-nums
                                    text-navy
                                "
                            >

                                {{ $prochainPaiement->date_prevue
                                    ? $prochainPaiement->date_prevue->format('d/m/Y')
                                    : '-'
                                }}

                            </p>

                            <p
                                class="
                                    mt-1
                                    text-sm
                                    text-slate-500
                                    tabular-nums
                                "
                            >

                                {{ number_format(
                                    $prochainPaiement->montant,
                                    0,
                                    ',',
                                    ' '
                                ) }}

                                FCFA

                            </p>

                        @else

                            <p
                                class="
                                    mt-2
                                    text-xl
                                    font-extrabold
                                    text-navy
                                "
                            >
                                Aucune
                            </p>

                        @endif

                    </div>

                    <div
                        class="
                            w-14
                            h-14
                            shrink-0
                            rounded-full
                            flex
                            items-center
                            justify-center
                            text-2xl
                            bg-[#EEF4FF]
                            text-navy
                        "
                    >
                        <i class="fa-solid fa-calendar"></i>
                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             PAIEMENT À VENIR
        ====================================================== --}}

        @if($prochainPaiement)

            @php

                $statutProchain = strtolower(
                    trim(
                        $prochainPaiement->statut
                    )
                );

                $estEnRetard =
                    $statutProchain === 'en attente'
                    &&
                    $prochainPaiement->date_prevue
                    &&
                    $prochainPaiement
                        ->date_prevue
                        ->isPast();

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
                                        ? 'fa-circle-exclamation'
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
                                    mt-2
                                    text-2xl
                                    font-bold
                                    text-navy
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
                                    mt-2
                                    text-sm
                                    text-slate-600
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
                                        mt-1
                                        text-sm
                                        text-slate-500
                                    "
                                >

                                    Abonnement :

                                    <strong>

                                        {{ $prochainPaiement->abonnement->type }}

                                    </strong>

                                </p>

                            @endif

                        </div>


                        {{-- BOUTON --}}

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
             HISTORIQUE
        ====================================================== --}}

        <div class="tk-card overflow-hidden">

            <div class="border-b border-slate-200 px-5 py-4">

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

                    Historique

                </h2>

                <p
                    class="
                        mt-1
                        text-sm
                        text-slate-500
                    "
                >

                    Historique des paiements de votre agence
                    vers TOKENDE.

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
                                Échéance
                            </th>

                            <th class="text-center whitespace-nowrap">
                                Paiement
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

                                $statut = strtolower(
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


                                {{-- ÉCHÉANCE --}}

                                <td class="text-center whitespace-nowrap tabular-nums">

                                    {{ $paiement->date_prevue
                                        ? $paiement->date_prevue->format('d/m/Y')
                                        : '-'
                                    }}

                                </td>


                                {{-- DATE PAIEMENT --}}

                                <td class="text-center whitespace-nowrap tabular-nums">

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

                                <td class="text-center whitespace-nowrap">

                                    @if(
                                        $affichageStatut
                                        === 'payé'
                                    )

                                        <span class="tk-badge tk-badge-green">

                                            <i class="fa-solid fa-circle-check"></i>

                                            Payé
                                        </span>

                                    @elseif(
                                        $affichageStatut
                                        === 'en retard'
                                    )

                                        <span class="tk-badge tk-badge-red">

                                            <i class="fa-solid fa-triangle-exclamation"></i>

                                            En retard
                                        </span>

                                    @else

                                        <span class="tk-badge tk-badge-orange">

                                            <i class="fa-solid fa-hourglass-half"></i>

                                            En attente
                                        </span>

                                    @endif

                                </td>


                                {{-- ACTION --}}

                                <td class="text-center whitespace-nowrap">

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
                                                text-slate-400
                                                font-semibold
                                            "
                                        >
                                            —
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

                                        Aucun paiement

                                    </p>

                                    <p
                                        class="
                                            mt-1
                                            text-sm
                                        "
                                    >

                                        Votre agence n'a encore
                                        aucun paiement enregistré.

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

    </div>

</x-layouts.agent>
