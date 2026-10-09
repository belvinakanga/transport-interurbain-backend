<x-layouts.admin :header="'Rapports & Statistiques'">

<div class="tk-page">

    {{-- En-tête --}}

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
                <i class="fa-solid fa-chart-simple"></i>
            </span>

            Rapports & Statistiques

        </h1>

    </div>


    {{-- Statistiques --}}

    <div
        class="
            grid grid-cols-1
            sm:grid-cols-2
            xl:grid-cols-4 gap-4
        "
    >


        {{-- Utilisateurs --}}

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
                        {{ $users }}
                    </p>

                </div>


                <span
                    class="
                        tk-kpi-icon
                        bg-orange-50 text-brand
                    "
                >
                    <i class="fa-solid fa-users"></i>
                </span>

            </div>

        </div>


        {{-- Agences --}}

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
                        {{ $agences }}
                    </p>

                </div>


                <span
                    class="
                        tk-kpi-icon
                        bg-[#EEF4FF] text-navy
                    "
                >
                    <i class="fa-solid fa-building"></i>
                </span>

            </div>

        </div>


        {{-- Trajets --}}

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
                        Trajets
                    </p>

                    <p class="tk-kpi-value mt-3">
                        {{ $trajets }}
                    </p>

                </div>


                <span
                    class="
                        tk-kpi-icon
                        bg-orange-50 text-brand
                    "
                >
                    <i class="fa-solid fa-bus"></i>
                </span>

            </div>

        </div>


        {{-- Voyageurs --}}

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
                        Voyageurs
                    </p>

                    <p class="tk-kpi-value mt-3">
                        {{ $voyageurs }}
                    </p>

                </div>


                <span
                    class="
                        tk-kpi-icon
                        bg-[#EEF4FF] text-navy
                    "
                >
                    <i class="fa-solid fa-user"></i>
                </span>

            </div>

        </div>


        {{-- Réservations --}}

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
                        Réservations
                    </p>

                    <p class="tk-kpi-value mt-3">
                        {{ $reservations }}
                    </p>

                </div>


                <span
                    class="
                        tk-kpi-icon
                        bg-orange-50 text-brand
                    "
                >
                    <i class="fa-solid fa-ticket"></i>
                </span>

            </div>

        </div>


        {{-- Billets --}}

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
                        Billets
                    </p>

                    <p class="tk-kpi-value mt-3">
                        {{ $billets }}
                    </p>

                </div>


                <span
                    class="
                        tk-kpi-icon
                        bg-[#EEF4FF] text-navy
                    "
                >
                    <i class="fa-solid fa-receipt"></i>
                </span>

            </div>

        </div>


        {{-- Paiements --}}

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
                        Paiements
                    </p>

                    <p class="tk-kpi-value mt-3">
                        {{ $paiements }}
                    </p>

                </div>


                <span
                    class="
                        tk-kpi-icon
                        bg-orange-50 text-brand
                    "
                >
                    <i class="fa-solid fa-credit-card"></i>
                </span>

            </div>

        </div>


        {{-- Avis --}}

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
                        Avis
                    </p>

                    <p class="tk-kpi-value mt-3">
                        {{ $avis }}
                    </p>

                </div>


                <span
                    class="
                        tk-kpi-icon
                        bg-[#EEF4FF] text-navy
                    "
                >
                    <i class="fa-solid fa-star"></i>
                </span>

            </div>

        </div>


        {{-- Achats --}}

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
                        Achats
                    </p>

                    <p class="tk-kpi-value mt-3">
                        {{ $achats }}
                    </p>

                </div>


                <span
                    class="
                        tk-kpi-icon
                        bg-orange-50 text-brand
                    "
                >
                    <i class="fa-solid fa-cart-shopping"></i>
                </span>

            </div>

        </div>


        {{-- Abonnements --}}

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
                        Abonnements
                    </p>

                    <p class="tk-kpi-value mt-3">
                        {{ $abonnements }}
                    </p>

                </div>


                <span
                    class="
                        tk-kpi-icon
                        bg-[#EEF4FF] text-navy
                    "
                >
                    <i class="fa-solid fa-clipboard-list"></i>
                </span>

            </div>

        </div>

    </div>

</div>

</x-layouts.admin>
