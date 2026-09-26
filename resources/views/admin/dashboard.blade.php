<x-layouts.admin :header="'Dashboard'">

    @php

        /*
        |--------------------------------------------------------------------------
        | ABONNEMENTS ACTIFS
        |--------------------------------------------------------------------------
        */

        $abonnementsActifs =
            \App\Models\Abonnement::where(
                'statut',
                'Actif'
            )->count();


        /*
        |--------------------------------------------------------------------------
        | PAIEMENTS AGENCES EN RETARD
        |--------------------------------------------------------------------------
        */

        $paiementsEnRetard =
            \App\Models\PaiementAgence::where(
                'statut',
                'en attente'
            )
            ->whereDate(
                'date_prevue',
                '<',
                today()
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | DERNIERS PAIEMENTS AGENCES
        |--------------------------------------------------------------------------
        */

        $paiementsAgencesRecents =
            \App\Models\PaiementAgence::with([
                'agence',
                'abonnement'
            ])
            ->latest('date_prevue')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | DERNIERS AVIS
        |--------------------------------------------------------------------------
        */

        $avisRecents = $derniersAvis->take(5);

    @endphp


    <div class="w-full max-w-7xl mx-auto space-y-6">


        {{-- =========================================================
     EN-TÊTE
========================================================== --}}

<div
    class="
        bg-white
        rounded-2xl
        border
        border-gray-100
        shadow-sm
        px-6
        py-4
    "
    style="border-left:5px solid #FF6B00;"
>

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

        {{-- TEXTE --}}

        <div>

            <p
                class="
                    text-sm
                    font-bold
                "
                style="color:#FF6B00;"
            >
                TOKENDE
            </p>

            <h1
                class="
                    text-3xl
                    font-extrabold
                    mt-1
                "
                style="color:#0A2A66;"
            >
                Bonjour {{ auth()->user()->name }} 👋
            </h1>

            <p
                class="
                    text-sm
                    text-gray-500
                    mt-1
                "
            >
                Gérez la plateforme, les agences, les abonnements, les commissions et les règlements.
            </p>

        </div>


        {{-- DATE --}}

        <div
            class="
                rounded-xl
                px-5
                py-3
                text-white
                shadow-sm
                md:min-w-[155px]
            "
            style="background:#FF6B00;"
        >

            <p
                class="
                    text-xs
                    font-medium
                    opacity-90
                "
            >
                Aujourd'hui
            </p>

            <p
                class="
                    text-xl
                    font-extrabold
                    mt-1
                "
            >
                {{ now()->format('d/m/Y') }}
            </p>

        </div>

    </div>

</div>

        {{-- =========================================================
             STATISTIQUES PRINCIPALES
        ========================================================== --}}

        <div
            class="
                grid
                grid-cols-2
                lg:grid-cols-4
                gap-5
            "
        >


            {{-- =====================================================
                 AGENCES
            ====================================================== --}}

            <div
                class="
                    bg-white
                    rounded-2xl
                    border
                    border-gray-100
                    shadow-sm
                    p-5
                    min-h-[190px]
                    flex
                    flex-col
                    justify-between
                "
                style="border-top:5px solid #FF6B00;"
            >

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
                                text-base
                                font-semibold
                                text-gray-500
                            "
                        >
                            Agences
                        </p>

                        <p
                            class="
                                text-4xl
                                font-extrabold
                                mt-3
                            "
                            style="color:#0A2A66;"
                        >
                            {{ $nombreAgences }}
                        </p>

                    </div>


                    <div
                        class="
                            w-14
                            h-14
                            rounded-2xl
                            flex
                            items-center
                            justify-center
                            text-2xl
                            flex-shrink-0
                        "
                        style="background:#FFF3E8;"
                    >
                        🏢
                    </div>

                </div>


                <p
                    class="
                        text-sm
                        font-semibold
                        mt-4
                    "
                    style="color:#FF6B00;"
                >
                    🏢 Agences enregistrées
                </p>

            </div>



            {{-- =====================================================
                 UTILISATEURS
            ====================================================== --}}

            <div
                class="
                    bg-white
                    rounded-2xl
                    border
                    border-gray-100
                    shadow-sm
                    p-5
                    min-h-[190px]
                    flex
                    flex-col
                    justify-between
                "
                style="border-top:5px solid #0A2A66;"
            >

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
                                text-base
                                font-semibold
                                text-gray-500
                            "
                        >
                            Utilisateurs
                        </p>

                        <p
                            class="
                                text-4xl
                                font-extrabold
                                mt-3
                            "
                            style="color:#0A2A66;"
                        >
                            {{ $nombreUtilisateurs }}
                        </p>

                    </div>


                    <div
                        class="
                            w-14
                            h-14
                            rounded-2xl
                            flex
                            items-center
                            justify-center
                            text-2xl
                            flex-shrink-0
                        "
                        style="background:#EEF4FF;"
                    >
                        👥
                    </div>

                </div>


                <p
                    class="
                        text-sm
                        font-semibold
                        text-gray-500
                        mt-4
                    "
                >
                    👥 Comptes de la plateforme
                </p>

            </div>



            {{-- =====================================================
                 ABONNEMENTS
            ====================================================== --}}

            <div
                class="
                    bg-white
                    rounded-2xl
                    border
                    border-gray-100
                    shadow-sm
                    p-5
                    min-h-[190px]
                    flex
                    flex-col
                    justify-between
                "
                style="border-top:5px solid #FF6B00;"
            >

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
                                text-base
                                font-semibold
                                text-gray-500
                            "
                        >
                            Abonnements actifs
                        </p>

                        <p
                            class="
                                text-4xl
                                font-extrabold
                                mt-3
                            "
                            style="color:#0A2A66;"
                        >
                            {{ $abonnementsActifs }}
                        </p>

                    </div>


                    <div
                        class="
                            w-14
                            h-14
                            rounded-2xl
                            flex
                            items-center
                            justify-center
                            text-2xl
                            flex-shrink-0
                        "
                        style="background:#FFF3E8;"
                    >
                        📋
                    </div>

                </div>


                <p
                    class="
                        text-sm
                        font-semibold
                        mt-4
                    "
                    style="color:#FF6B00;"
                >
                    📋 Abonnements en cours
                </p>

            </div>



            {{-- =====================================================
                 PAIEMENTS EN RETARD
            ====================================================== --}}

            <div
                class="
                    bg-white
                    rounded-2xl
                    border
                    shadow-sm
                    p-5
                    min-h-[190px]
                    flex
                    flex-col
                    justify-between
                "
                style="
                    border-top:5px solid #DC2626;
                    border-color:#FECACA;
                "
            >

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
                                text-base
                                font-semibold
                                text-gray-500
                            "
                        >
                            Paiements en retard
                        </p>

                        <p
                            class="
                                text-4xl
                                font-extrabold
                                mt-3
                            "
                            style="color:#DC2626;"
                        >
                            {{ $paiementsEnRetard }}
                        </p>

                    </div>


                    <div
                        class="
                            w-14
                            h-14
                            rounded-2xl
                            flex
                            items-center
                            justify-center
                            text-2xl
                            flex-shrink-0
                        "
                        style="background:#FEF2F2;"
                    >
                        ⚠️
                    </div>

                </div>


                <p
                    class="
                        text-sm
                        font-semibold
                        mt-4
                    "
                    style="color:#DC2626;"
                >
                    🔴 Échéances dépassées
                </p>

            </div>

        </div>



        {{-- =========================================================
             PAIEMENTS AGENCES + AVIS
        ========================================================== --}}

        <div
            class="
                grid
                grid-cols-1
                xl:grid-cols-5
                gap-5
            "
        >


            {{-- =====================================================
                 PAIEMENTS AGENCES
            ====================================================== --}}

            <div
                class="
                    xl:col-span-3
                    bg-white
                    rounded-2xl
                    border
                    border-gray-100
                    shadow-sm
                    overflow-hidden
                "
            >

                <div
                    class="
                        px-6
                        py-5
                        flex
                        items-center
                        justify-between
                        border-b
                    "
                    style="background:#F8FAFD;"
                >

                    <div>

                        <h2
                            class="
                                text-xl
                                font-extrabold
                            "
                            style="color:#0A2A66;"
                        >
                            💰 Paiements agences
                        </h2>

                        <p
                            class="
                                text-sm
                                text-gray-500
                                mt-1
                            "
                        >
                            Suivi des règlements des agences vers TOKENDE.
                        </p>

                    </div>


                    @if(Route::has('paiements-agences.index'))

                        <a
                            href="{{ route('paiements-agences.index') }}"
                            class="
                                px-5
                                py-2.5
                                rounded-xl
                                text-white
                                text-sm
                                font-bold
                                shadow-sm
                            "
                            style="background:#FF6B00;"
                        >
                            Voir tout
                        </a>

                    @endif

                </div>


                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead
                            style="background:#EEF4FF;"
                        >

                            <tr>

                                <th
                                    class="
                                        px-6
                                        py-4
                                        text-left
                                        text-sm
                                        font-extrabold
                                    "
                                    style="color:#0A2A66;"
                                >
                                    Agence
                                </th>

                                <th
                                    class="
                                        px-6
                                        py-4
                                        text-right
                                        text-sm
                                        font-extrabold
                                    "
                                    style="color:#0A2A66;"
                                >
                                    Montant
                                </th>

                                <th
                                    class="
                                        px-6
                                        py-4
                                        text-center
                                        text-sm
                                        font-extrabold
                                    "
                                    style="color:#0A2A66;"
                                >
                                    Échéance
                                </th>

                                <th
                                    class="
                                        px-6
                                        py-4
                                        text-center
                                        text-sm
                                        font-extrabold
                                    "
                                    style="color:#0A2A66;"
                                >
                                    Statut
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse(
                                $paiementsAgencesRecents
                                as $paiement
                            )

                                @php

                                    $retard =
                                        $paiement->statut === 'en attente'
                                        &&
                                        $paiement->date_prevue
                                        &&
                                        $paiement->date_prevue->isPast();

                                @endphp


                                <tr
                                    class="
                                        border-t
                                        border-gray-100
                                        hover:bg-gray-50
                                    "
                                >

                                    <td class="px-6 py-4">

                                        <p
                                            class="
                                                text-base
                                                font-bold
                                            "
                                            style="color:#0A2A66;"
                                        >
                                            {{
                                                $paiement->agence->nom_agence
                                                ?? '-'
                                            }}
                                        </p>

                                        @if($paiement->abonnement)

                                            <p
                                                class="
                                                    text-sm
                                                    text-gray-500
                                                    mt-1
                                                "
                                            >
                                                {{
                                                    $paiement->abonnement->type
                                                }}
                                            </p>

                                        @endif

                                    </td>


                                    <td
                                        class="
                                            px-6
                                            py-4
                                            text-right
                                            text-base
                                            font-extrabold
                                        "
                                        style="color:#FF6B00;"
                                    >

                                        {{
                                            number_format(
                                                $paiement->montant,
                                                0,
                                                ',',
                                                ' '
                                            )
                                        }}

                                        FCFA

                                    </td>


                                    <td
                                        class="
                                            px-6
                                            py-4
                                            text-center
                                            text-sm
                                            text-gray-600
                                        "
                                    >

                                        {{
                                            $paiement->date_prevue
                                                ? $paiement->date_prevue->format('d/m/Y')
                                                : '-'
                                        }}

                                    </td>


                                    <td
                                        class="
                                            px-6
                                            py-4
                                            text-center
                                        "
                                    >

                                        @if(
                                            $paiement->statut
                                            === 'payé'
                                        )

                                            <span
                                                class="
                                                    inline-flex
                                                    px-3
                                                    py-1.5
                                                    rounded-full
                                                    text-xs
                                                    font-extrabold
                                                "
                                                style="
                                                    background:#DCFCE7;
                                                    color:#15803D;
                                                "
                                            >
                                                ✅ Payé
                                            </span>

                                        @elseif($retard)

                                            <span
                                                class="
                                                    inline-flex
                                                    px-3
                                                    py-1.5
                                                    rounded-full
                                                    text-xs
                                                    font-extrabold
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
                                                    py-1.5
                                                    rounded-full
                                                    text-xs
                                                    font-extrabold
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

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="4"
                                        class="
                                            px-6
                                            py-12
                                            text-center
                                            text-sm
                                            text-gray-500
                                        "
                                    >
                                        Aucun paiement agence enregistré.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>



            {{-- =====================================================
                 AVIS RÉCENTS
            ====================================================== --}}

            <div
                class="
                    xl:col-span-2
                    bg-white
                    rounded-2xl
                    border
                    border-gray-100
                    shadow-sm
                    overflow-hidden
                "
            >

                <div
                    class="
                        px-6
                        py-5
                        border-b
                    "
                    style="background:#F8FAFD;"
                >

                    <h2
                        class="
                            text-xl
                            font-extrabold
                        "
                        style="color:#0A2A66;"
                    >
                        ⭐ Avis récents
                    </h2>

                    <p
                        class="
                            text-sm
                            text-gray-500
                            mt-1
                        "
                    >
                        Retours des utilisateurs.
                    </p>

                </div>


                <div class="px-6">

                    @forelse(
                        $avisRecents
                        as $avis
                    )

                        <div
                            class="
                                py-5
                                border-b
                                border-gray-100
                            "
                        >

                            <div
                                class="
                                    flex
                                    items-center
                                    justify-between
                                    gap-3
                                "
                            >

                                <div>

                                    <p
                                        class="
                                            text-base
                                            font-bold
                                        "
                                        style="color:#0A2A66;"
                                    >
                                        {{
                                            $avis->user->name
                                            ?? 'Utilisateur'
                                        }}
                                    </p>

                                    <p
                                        class="
                                            text-sm
                                            text-gray-500
                                            mt-1
                                        "
                                    >
                                        {{
                                            $avis->created_at
                                                ? $avis->created_at->format('d/m/Y')
                                                : '-'
                                        }}
                                    </p>

                                </div>


                                <div
                                    class="
                                        px-3
                                        py-1.5
                                        rounded-xl
                                        text-sm
                                        font-extrabold
                                    "
                                    style="
                                        background:#FFF3E8;
                                        color:#FF6B00;
                                    "
                                >
                                    ⭐ {{ $avis->note ?? '-' }}
                                </div>

                            </div>


                            @if(!empty($avis->commentaire))

                                <p
                                    class="
                                        text-sm
                                        text-gray-600
                                        mt-3
                                    "
                                >

                                    {{
                                        \Illuminate\Support\Str::limit(
                                            $avis->commentaire,
                                            100
                                        )
                                    }}

                                </p>

                            @endif

                        </div>

                    @empty

                        <div
                            class="
                                py-12
                                text-center
                                text-sm
                                text-gray-500
                            "
                        >
                            Aucun avis récent.
                        </div>

                    @endforelse

                </div>


                <div
                    class="
                        px-6
                        py-5
                        border-t
                    "
                >

                    <a
                        href="{{ url('/admin/avis') }}"
                        class="
                            text-sm
                            font-bold
                        "
                        style="
                            color:#FF6B00;
                            text-decoration:none;
                        "
                    >
                        Voir tous les avis →
                    </a>

                </div>

            </div>

        </div>



        {{-- =========================================================
             RÉSUMÉ
        ========================================================== --}}

        <div
            class="
                grid
                grid-cols-1
                md:grid-cols-3
                gap-5
            "
        >

            {{-- AVIS --}}

            <div
                class="
                    rounded-2xl
                    p-6
                    text-white
                "
                style="background:#0A2A66;"
            >

                <p
                    class="
                        text-sm
                        font-semibold
                    "
                    style="color:#DCE8FF;"
                >
                    Avis enregistrés
                </p>

                <p
                    class="
                        text-3xl
                        font-extrabold
                        mt-2
                    "
                >
                    {{ $nombreAvis }}
                </p>

                <p
                    class="
                        text-sm
                        mt-2
                    "
                    style="color:#DCE8FF;"
                >
                    ⭐ Retours des utilisateurs
                </p>

            </div>


            {{-- PAIEMENTS --}}

            <div
                class="
                    rounded-2xl
                    p-6
                    text-white
                "
                style="background:#FF6B00;"
            >

                <p
                    class="
                        text-sm
                        font-semibold
                    "
                    style="color:#FFF3E8;"
                >
                    Paiements voyageurs
                </p>

                <p
                    class="
                        text-3xl
                        font-extrabold
                        mt-2
                    "
                >
                    {{ $nombrePaiements }}
                </p>

                <p
                    class="
                        text-sm
                        mt-2
                    "
                    style="color:#FFF3E8;"
                >
                    Suivi global de la plateforme
                </p>

            </div>


            {{-- STATUT --}}

            <div
                class="
                    rounded-2xl
                    p-6
                "
                style="
                    background:#FFF3E8;
                    border:1px solid #FFD7B8;
                "
            >

                <p
                    class="
                        text-sm
                        font-semibold
                    "
                    style="color:#9A3412;"
                >
                    Statut plateforme
                </p>

                <p
                    class="
                        text-3xl
                        font-extrabold
                        mt-2
                    "
                    style="color:#0A2A66;"
                >
                    En ligne
                </p>

                <p
                    class="
                        text-sm
                        mt-2
                    "
                    style="color:#9A3412;"
                >
                    🟠 TOKENDE fonctionne normalement
                </p>

            </div>

        </div>



        {{-- =========================================================
             FOOTER
        ========================================================== --}}

        <div
            class="
                text-center
                text-sm
                text-gray-400
                pb-3
            "
        >

            © {{ date('Y') }}

            <span
                class="font-bold"
                style="color:#FF6B00;"
            >
                TOKENDE
            </span>

            — Administration

        </div>

    </div>

</x-layouts.admin>