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


    <div class="tk-page">


        {{-- =========================================================
             EN-TÊTE
        ========================================================== --}}

        <div class="tk-page-head">

            <div
                class="
                    flex flex-col
                    md:flex-row md:items-center
                    md:justify-between gap-4
                "
            >

                {{-- TEXTE --}}

                <div>

                    <p
                        class="
                            text-xs font-bold
                            uppercase tracking-wider
                            text-brand
                        "
                    >
                        TOKENDE
                    </p>

                    <h1
                        class="
                            mt-1 text-2xl sm:text-[28px]
                            font-extrabold
                            text-navy tracking-tight
                        "
                    >
                        Bonjour {{ auth()->user()->name }}
                    </h1>

                    <p
                        class="
                            mt-1 text-sm
                            text-slate-500
                        "
                    >
                        Gérez la plateforme, les agences, les abonnements, les commissions et les règlements.
                    </p>

                </div>


                {{-- DATE --}}

                <div
                    class="
                        rounded-lg
                        bg-navy px-4 py-2.5
                        text-white
                        md:min-w-[150px]
                    "
                >

                    <p
                        class="
                            text-[11px] font-semibold
                            uppercase tracking-wider
                            text-white/60
                        "
                    >
                        Aujourd'hui
                    </p>

                    <p
                        class="
                            mt-0.5 text-base
                            font-bold text-white
                            tabular-nums
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
                grid grid-cols-1
                sm:grid-cols-2
                xl:grid-cols-4 gap-4
            "
        >


            {{-- =====================================================
                 AGENCES
            ====================================================== --}}

            <div class="tk-kpi">

                <div
                    class="
                        flex items-start
                        justify-between gap-3
                    "
                >

                    <div>

                        <p
                            class="
                                text-sm font-medium
                                text-slate-500
                            "
                        >
                            Agences
                        </p>

                        <p class="tk-kpi-value mt-3">
                            {{ $nombreAgences }}
                        </p>

                    </div>


                    <span
                        class="
                            tk-kpi-icon
                            bg-orange-50 text-brand
                        "
                    >
                        <i class="fa-solid fa-building"></i>
                    </span>

                </div>


                <p
                    class="
                        flex items-center gap-2
                        text-xs font-semibold
                        text-brand
                    "
                >
                    <i class="fa-solid fa-building text-brand/40"></i>
                    Agences enregistrées
                </p>

            </div>



            {{-- =====================================================
                 UTILISATEURS
            ====================================================== --}}

            <div class="tk-kpi">

                <div
                    class="
                        flex items-start
                        justify-between gap-3
                    "
                >

                    <div>

                        <p
                            class="
                                text-sm font-medium
                                text-slate-500
                            "
                        >
                            Utilisateurs
                        </p>

                        <p class="tk-kpi-value mt-3">
                            {{ $nombreUtilisateurs }}
                        </p>

                    </div>


                    <span
                        class="
                            tk-kpi-icon
                            bg-[#EEF4FF] text-navy
                        "
                    >
                        <i class="fa-solid fa-users"></i>
                    </span>

                </div>


                <p
                    class="
                        flex items-center gap-2
                        text-xs font-semibold
                        text-navy
                    "
                >
                    <i class="fa-solid fa-users text-navy/40"></i>
                    Comptes de la plateforme
                </p>

            </div>



            {{-- =====================================================
                 ABONNEMENTS
            ====================================================== --}}

            <div class="tk-kpi">

                <div
                    class="
                        flex items-start
                        justify-between gap-3
                    "
                >

                    <div>

                        <p
                            class="
                                text-sm font-medium
                                text-slate-500
                            "
                        >
                            Abonnements actifs
                        </p>

                        <p class="tk-kpi-value mt-3">
                            {{ $abonnementsActifs }}
                        </p>

                    </div>


                    <span
                        class="
                            tk-kpi-icon
                            bg-orange-50 text-brand
                        "
                    >
                        <i class="fa-solid fa-clipboard-list"></i>
                    </span>

                </div>


                <p
                    class="
                        flex items-center gap-2
                        text-xs font-semibold
                        text-brand
                    "
                >
                    <i class="fa-solid fa-clipboard-list text-brand/40"></i>
                    Abonnements en cours
                </p>

            </div>



            {{-- =====================================================
                 PAIEMENTS EN RETARD
            ====================================================== --}}

            <div class="tk-kpi">

                <div
                    class="
                        flex items-start
                        justify-between gap-3
                    "
                >

                    <div>

                        <p
                            class="
                                text-sm font-medium
                                text-slate-500
                            "
                        >
                            Paiements en retard
                        </p>

                        <p
                            class="
                                tk-kpi-value
                                mt-3 text-red-600
                            "
                        >
                            {{ $paiementsEnRetard }}
                        </p>

                    </div>


                    <span
                        class="
                            tk-kpi-icon
                            bg-red-50 text-red-600
                        "
                    >
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </span>

                </div>


                <p
                    class="
                        flex items-center gap-2
                        text-xs font-semibold
                        text-red-500
                    "
                >
                    <i class="fa-solid fa-circle text-[6px]"></i>
                    Échéances dépassées
                </p>

            </div>

        </div>



        {{-- =========================================================
             PAIEMENTS AGENCES + AVIS
        ========================================================== --}}

        <div
            class="
                grid grid-cols-1
                xl:grid-cols-5 gap-4
            "
        >


            {{-- =====================================================
                 PAIEMENTS AGENCES
            ====================================================== --}}

            <div
                class="
                    xl:col-span-3
                    tk-card overflow-hidden
                "
            >

                <div
                    class="
                        flex items-center
                        justify-between gap-3
                        border-b border-slate-200
                        px-6 py-4
                    "
                >

                    <div>

                        <h2
                            class="
                                flex items-center gap-2.5
                                text-base font-bold
                                text-navy
                            "
                        >
                            <i class="fa-solid fa-money-bill-wave text-brand"></i>
                            Paiements agences
                        </h2>

                        <p
                            class="
                                mt-1 text-xs
                                text-slate-500
                            "
                        >
                            Suivi des règlements des agences vers TOKENDE.
                        </p>

                    </div>


                    @if(Route::has('paiements-agences.index'))

                        <a
                            href="{{ route('paiements-agences.index') }}"
                            class="tk-btn-accent shrink-0"
                        >
                            Voir tout
                        </a>

                    @endif

                </div>


                <div class="overflow-x-auto">

                    <table class="tk-table">

                        <thead>

                            <tr>

                                <th class="text-left">
                                    Agence
                                </th>

                                <th class="text-right">
                                    Montant
                                </th>

                                <th class="text-center">
                                    Échéance
                                </th>

                                <th class="text-center">
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


                                <tr>

                                    <td>

                                        <p
                                            class="
                                                text-sm
                                                font-semibold
                                                text-navy
                                            "
                                        >
                                            {{
                                                $paiement->agence->nom_agence
                                                ?? '-'
                                            }}
                                        </p>

                                        @if($paiement->abonnement)

                                            <p
                                                class="
                                                    mt-0.5
                                                    text-xs
                                                    text-slate-400
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
                                            text-right
                                            text-sm font-bold
                                            text-slate-800
                                            tabular-nums
                                        "
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
                                            text-center
                                            text-sm
                                            text-slate-500
                                            tabular-nums
                                        "
                                    >

                                        {{
                                            $paiement->date_prevue
                                                ? $paiement->date_prevue->format('d/m/Y')
                                                : '-'
                                        }}

                                    </td>


                                    <td class="text-center">

                                        @if(
                                            $paiement->statut
                                            === 'payé'
                                        )

                                            <span class="tk-badge tk-badge-green">
                                                <i class="fa-solid fa-circle-check"></i>
                                                Payé
                                            </span>

                                        @elseif($retard)

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

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="4"
                                        class="tk-empty"
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
                    tk-card overflow-hidden
                "
            >

                <div
                    class="
                        border-b border-slate-200
                        px-6 py-4
                    "
                >

                    <h2
                        class="
                            flex items-center gap-2.5
                            text-base font-bold
                            text-navy
                        "
                    >
                        <i class="fa-solid fa-star text-brand"></i>
                        Avis récents
                    </h2>

                    <p
                        class="
                            mt-1 text-xs
                            text-slate-500
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
                                border-b
                                border-slate-100
                                py-4 last:border-b-0
                            "
                        >

                            <div
                                class="
                                    flex items-center
                                    justify-between gap-3
                                "
                            >

                                <div class="min-w-0">

                                    <p
                                        class="
                                            truncate text-sm
                                            font-semibold
                                            text-navy
                                        "
                                    >
                                        {{
                                            $avis->user->name
                                            ?? 'Utilisateur'
                                        }}
                                    </p>

                                    <p
                                        class="
                                            mt-0.5 text-xs
                                            text-slate-400
                                            tabular-nums
                                        "
                                    >
                                        {{
                                            $avis->created_at
                                                ? $avis->created_at->format('d/m/Y')
                                                : '-'
                                        }}
                                    </p>

                                </div>


                                <span
                                    class="tk-badge tk-badge-orange"
                                >
                                    <i class="fa-solid fa-star"></i>
                                    {{ $avis->note ?? '-' }}
                                </span>

                            </div>


                            @if(!empty($avis->commentaire))

                                <p
                                    class="
                                        mt-2 text-sm
                                        text-slate-600
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

                        <div class="tk-empty">
                            Aucun avis récent.
                        </div>

                    @endforelse

                </div>


                <div
                    class="
                        border-t border-slate-200
                        px-6 py-4
                    "
                >

                    <a
                        href="{{ url('/admin/avis') }}"
                        class="
                            inline-flex items-center
                            gap-2 text-sm
                            font-semibold text-brand
                            hover:underline
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
                grid grid-cols-1
                md:grid-cols-3 gap-4
            "
        >

            {{-- AVIS --}}

            <div class="tk-stat-navy">

                <div
                    class="
                        flex items-center
                        justify-between gap-3
                    "
                >

                    <p
                        class="
                            text-sm font-semibold
                            text-[#DCE8FF]
                        "
                    >
                        Avis enregistrés
                    </p>

                    <i class="fa-solid fa-star text-brand"></i>

                </div>

                <p
                    class="
                        mt-2 text-3xl
                        font-extrabold
                        text-white tabular-nums
                    "
                >
                    {{ $nombreAvis }}
                </p>

                <p
                    class="
                        mt-2 flex items-center
                        gap-2 text-xs
                        text-[#DCE8FF]
                    "
                >
                    <i class="fa-solid fa-star text-brand"></i>
                    Retours des utilisateurs
                </p>

            </div>


            {{-- PAIEMENTS --}}

            <div class="tk-stat-brand">

                <div
                    class="
                        flex items-center
                        justify-between gap-3
                    "
                >

                    <p
                        class="
                            text-sm font-semibold
                            text-[#FFF3E8]
                        "
                    >
                        Paiements voyageurs
                    </p>

                    <i class="fa-solid fa-credit-card text-white/50"></i>

                </div>

                <p
                    class="
                        mt-2 text-3xl
                        font-extrabold
                        text-white tabular-nums
                    "
                >
                    {{ $nombrePaiements }}
                </p>

                <p
                    class="
                        mt-2 text-xs
                        text-[#FFF3E8]
                    "
                >
                    Suivi global de la plateforme
                </p>

            </div>


            {{-- STATUT --}}

            <div class="tk-stat-tint">

                <div
                    class="
                        flex items-center
                        justify-between gap-3
                    "
                >

                    <p
                        class="
                            text-sm font-semibold
                            text-[#9A3412]
                        "
                    >
                        Statut plateforme
                    </p>

                    <i class="fa-solid fa-signal text-[#9A3412]/40"></i>

                </div>

                <p
                    class="
                        mt-2 text-3xl
                        font-extrabold
                        text-navy
                    "
                >
                    En ligne
                </p>

                <p
                    class="
                        mt-2 flex items-center
                        gap-2 text-xs
                        text-[#9A3412]
                    "
                >
                    <i class="fa-solid fa-circle text-[6px] text-brand"></i>
                    TOKENDE fonctionne normalement
                </p>

            </div>

        </div>



        {{-- =========================================================
             FOOTER
        ========================================================== --}}

        <div
            class="
                pb-3 text-center
                text-xs text-slate-400
            "
        >

            © {{ date('Y') }}

            <span class="font-bold text-brand">
                TOKENDE
            </span>

            — Administration

        </div>

    </div>

</x-layouts.admin>
