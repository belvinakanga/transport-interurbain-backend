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

            <p class="text-gray-500 mt-2">

                Consultez les paiements de votre agence
                à TOKENDE.

            </p>

        </div>


        {{-- =====================================================
             MESSAGE
        ====================================================== --}}

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
            style="background:#0A2A66;"
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

                {{ $agence->nom_agence }}

            </div>

            <div
                class="
                    text-sm
                    mt-2
                "
                style="color:#DCE8FF;"
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

                        <p class="text-sm text-gray-500">
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


            {{-- À PAYER --}}

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

                        <p class="text-sm text-gray-500">
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


            {{-- PROCHAINE ÉCHÉANCE --}}

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

                        <p class="text-sm text-gray-500">
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

                            <p
                                class="
                                    text-sm
                                    text-gray-500
                                    mt-1
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
                        : '#FFD7B8'
                    }};
                "
            >

                <div
                    class="px-6 py-5"
                    style="
                        background:
                        {{ $estEnRetard
                            ? '#FEF2F2'
                            : '#FFF3E8'
                        }};
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
                                        : '#FF6B00'
                                    }};
                                "
                            >

                                {{
                                    $estEnRetard
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


                        {{-- BOUTON --}}

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

                    📋 Historique

                </h2>

                <p
                    class="
                        text-sm
                        text-gray-500
                        mt-1
                    "
                >

                    Historique des paiements de votre agence
                    vers TOKENDE.

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
                                Échéance
                            </th>

                            <th class="p-4 text-center">
                                Paiement
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


                            <tr
                                class="
                                    border-t
                                    border-gray-100
                                    hover:bg-gray-50
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


                                {{-- ÉCHÉANCE --}}

                                <td class="p-4 text-center">

                                    {{ $paiement->date_prevue
                                        ? $paiement->date_prevue->format('d/m/Y')
                                        : '-'
                                    }}

                                </td>


                                {{-- DATE PAIEMENT --}}

                                <td class="p-4 text-center">

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

                                <td class="p-4 text-center">

                                    @if(
                                        $affichageStatut
                                        === 'payé'
                                    )

                                        <span
                                            class="
                                                inline-flex
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

                                    @elseif(
                                        $affichageStatut
                                        === 'en retard'
                                    )

                                        <span
                                            class="
                                                inline-flex
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

                                    @else

                                        <span
                                            class="
                                                inline-flex
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

                                <td class="p-4 text-center">

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
                                                text-gray-400
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
                                    class="
                                        text-center
                                        py-14
                                        text-gray-500
                                    "
                                >

                                    <div class="text-5xl mb-4">
                                        💰
                                    </div>

                                    <p
                                        class="
                                            font-semibold
                                            text-gray-700
                                        "
                                    >

                                        Aucun paiement

                                    </p>

                                    <p
                                        class="
                                            text-sm
                                            mt-1
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

</x-layouts.agent>