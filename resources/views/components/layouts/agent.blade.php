<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        {{ $title ?? 'TOKENDE - Espace Agent' }}
    </title>

    <script>
        (function () {
            try {
                var t = localStorage.getItem('tk-theme');
                if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}

            window.tkToggleTheme = function () {
                var dark = document.documentElement.classList.toggle('dark');
                try { localStorage.setItem('tk-theme', dark ? 'dark' : 'light'); } catch (e) {}
                window.tkSyncTheme();
            };

            window.tkSyncTheme = function () {
                var dark = document.documentElement.classList.contains('dark');
                document.querySelectorAll('[data-theme-icon]').forEach(function (el) {
                    el.className = 'fa-solid ' + (dark ? 'fa-sun' : 'fa-moon');
                });
                document.querySelectorAll('[data-theme-toggle]').forEach(function (el) {
                    el.setAttribute('aria-label', dark ? 'Passer en mode clair' : 'Passer en mode sombre');
                    el.setAttribute('title', dark ? 'Mode clair' : 'Mode sombre');
                });
            };

            document.addEventListener('DOMContentLoaded', window.tkSyncTheme);
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap"
    >

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>


<body
    class="m-0 bg-canvas text-slate-800 overflow-hidden antialiased"
>


<div
    x-data="{
        sidebarOpen: false,
        collapsed: localStorage.getItem('tk-collapsed') === '1',
        toggleCollapse() {
            this.collapsed = !this.collapsed;
            localStorage.setItem('tk-collapsed', this.collapsed ? '1' : '0');
        }
    }"
    class="flex w-full h-screen"
>


    {{-- =========================================================
         SIDEBAR AGENT
    ========================================================== --}}

    <aside
        :class="[
            sidebarOpen ? 'translate-x-0' : '-translate-x-full',
            collapsed ? 'tk-sidebar-collapsed' : '',
        ]"
        class="
            fixed inset-y-0 left-0 z-40
            flex w-[264px] shrink-0
            flex-col
            bg-white border-r border-slate-200
            transition-[transform,width] duration-200 ease-out
            lg:static lg:z-auto lg:translate-x-0
        "
    >


        {{-- =====================================================
             LOGO
        ====================================================== --}}

        <div
            class="
                tk-logo-box flex h-16 items-center
                px-5 border-b border-slate-200
            "
        >

            <div class="flex items-center gap-3">

                <div
                    class="
                        flex h-10 w-10 shrink-0 items-center
                        justify-center rounded-xl
                        bg-navy text-white text-lg
                    "
                >
                    <i class="fa-solid fa-bus"></i>
                </div>


                <div class="tk-brand">

                    <div
                        class="
                            text-[19px] font-extrabold
                            leading-none text-navy
                            tracking-tight
                        "
                    >
                        TOKENDE
                    </div>

                    <div
                        class="
                            mt-1 text-xs
                            font-semibold text-brand
                        "
                    >
                        Espace Agent
                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             INFORMATIONS AGENT
        ====================================================== --}}

        <div
            class="
                tk-collaps-hide
                border-b border-slate-200
                px-4 py-3
            "
        >

            <div
                class="flex items-center gap-3"
            >

                <div
                    class="
                        flex h-10 w-10 shrink-0
                        items-center justify-center
                        rounded-full bg-[#EEF4FF]
                        text-navy text-sm
                    "
                >
                    <i class="fa-solid fa-user-tie"></i>
                </div>


                <div class="min-w-0">

                    <p
                        class="
                            m-0 truncate text-sm
                            font-semibold text-slate-800
                        "
                    >
                        {{ auth()->user()->name }}
                    </p>

                    <p
                        class="
                            m-0 mt-0.5 text-xs
                            font-semibold text-brand
                        "
                    >
                        Agent
                    </p>

                </div>

            </div>


            {{-- AGENCE --}}

            <div
                class="
                    mt-3 rounded-lg
                    border border-slate-200
                    bg-slate-50 px-3 py-2
                "
            >

                <p
                    class="
                        m-0 text-[11px]
                        text-slate-500
                    "
                >
                    Mon agence
                </p>

                <p
                    class="
                        m-0 mt-0.5 truncate
                        text-sm font-bold
                        text-navy
                    "
                >
                    {{ auth()->user()->agence->nom_agence ?? 'Aucune agence' }}
                </p>

            </div>

        </div>


        {{-- =====================================================
             NAVIGATION
        ====================================================== --}}

        <nav
            class="flex-1 overflow-y-auto px-3 py-4"
        >

            <span class="tk-nav-label">
                Navigation
            </span>


            {{-- =================================================
                 TABLEAU DE BORD
            ================================================== --}}

            <a
                href="{{ route('agent.dashboard') }}" title="Tableau de bord"
                @class([
                    'tk-nav-link',
                    'is-active' => request()->routeIs('agent.dashboard'),
                ])
            >

                <span class="flex w-5 justify-center text-[15px]">
                    <i class="fa-solid fa-gauge-high"></i>
                </span>

                <span>
                    Tableau de bord
                </span>

            </a>


            {{-- =================================================
                 MES TRAJETS
            ================================================== --}}

            <a
                href="{{ route('admin.trajets') }}" title="Mes trajets"
                @class([
                    'tk-nav-link',
                    'is-active' => request()->is('admin/trajets*'),
                ])
            >

                <span class="flex w-5 justify-center text-[15px]">
                    <i class="fa-solid fa-bus"></i>
                </span>

                <span>
                    Mes trajets
                </span>

            </a>


            {{-- =================================================
                 RÉSERVATIONS
            ================================================== --}}

            <a
                href="{{ route('admin.reservations') }}" title="Réservations"
                @class([
                    'tk-nav-link',
                    'is-active' => request()->is('admin/reservations*'),
                ])
            >

                <span class="flex w-5 justify-center text-[15px]">
                    <i class="fa-solid fa-ticket"></i>
                </span>

                <span>
                    Réservations
                </span>

            </a>


            {{-- =================================================
                 ACHATS
            ================================================== --}}

            @if(Route::has('achats.index'))

                <a
                    href="{{ route('achats.index') }}" title="Achats"
                    @class([
                        'tk-nav-link',
                        'is-active' => request()->is('achats*'),
                    ])
                >

                    <span class="flex w-5 justify-center text-[15px]">
                        <i class="fa-solid fa-credit-card"></i>
                    </span>

                    <span>
                        Achats
                    </span>

                </a>

            @endif


            {{-- =================================================
                 MES PAIEMENTS
            ================================================== --}}

            @if(Route::has('agent.paiements'))

                <a
                    href="{{ route('agent.paiements') }}" title="Mes paiements"
                    @class([
                        'tk-nav-link',
                        'is-active' => request()->routeIs('agent.paiements'),
                    ])
                >

                    <span class="flex w-5 justify-center text-[15px]">
                        <i class="fa-solid fa-wallet"></i>
                    </span>

                    <span>
                        Mes paiements
                    </span>

                </a>

            @endif


        </nav>

    </aside>


    {{-- OVERLAY MOBILE --}}

    <div
        x-show="sidebarOpen"
        x-transition.opacity
        @click="sidebarOpen = false"
        class="
            fixed inset-0 z-30
            bg-slate-900/40
            lg:hidden
        "
        aria-hidden="true"
    ></div>


    {{-- =========================================================
         CONTENU PRINCIPAL
    ========================================================== --}}

    <div
        class="
            flex h-screen min-w-0
            flex-1 flex-col
            overflow-hidden
        "
    >


        {{-- =====================================================
             HEADER
        ====================================================== --}}

        <header
            class="
                flex h-16 min-h-16 items-center
                justify-between gap-4
                border-b border-slate-200
                bg-white px-4 sm:px-6
            "
        >

            <div class="flex min-w-0 items-center gap-3">

                <button
                    type="button"
                    @click="sidebarOpen = !sidebarOpen"
                    class="
                        flex h-10 w-10 items-center justify-center
                        rounded-lg text-navy
                        transition-colors hover:bg-slate-100
                        lg:hidden
                    "
                    aria-label="Menu"
                >

                    <i class="fa-solid fa-bars"></i>

                </button>


                <button
                    type="button"
                    @click="toggleCollapse()"
                    class="
                        hidden h-10 w-10 items-center justify-center
                        rounded-lg text-navy
                        transition-colors hover:bg-slate-100
                        lg:flex
                    "
                    :aria-label="collapsed ? 'Déplier le menu' : 'Réduire le menu'"
                    x-bind:title="collapsed ? 'Déplier le menu' : 'Réduire le menu'"
                >

                    <i
                        class="fa-solid"
                        :class="collapsed ? 'fa-chevron-right' : 'fa-chevron-left'"
                    ></i>

                </button>

                <div class="min-w-0">

                    <div
                        class="
                            truncate text-lg sm:text-xl
                            font-bold text-navy
                        "
                    >
                        {{ $header ?? 'Espace Agent' }}
                    </div>

                    <div
                        class="
                            mt-0.5 truncate text-xs
                            text-slate-500
                        "
                    >
                        {{ auth()->user()->agence->nom_agence ?? 'Aucune agence' }}
                    </div>

                </div>

            </div>


            <div class="flex items-center gap-3">

                {{-- THEME CLAIR / SOMBRE --}}

                <button
                    type="button"
                    data-theme-toggle
                    onclick="tkToggleTheme()"
                    aria-label="Passer en mode sombre"
                    title="Mode sombre"
                    class="
                        flex h-10 w-10 items-center justify-center
                        rounded-lg border border-slate-200
                        bg-white text-navy
                        transition-colors hover:bg-slate-50
                    "
                >
                    <i data-theme-icon class="fa-solid fa-moon"></i>
                </button>


                <div
                    class="relative"
                    x-data="{ avatarOpen: false }"
                    @click.outside="avatarOpen = false"
                    @keydown.escape.window="avatarOpen = false"
                >

                <button
                    type="button"
                    @click="avatarOpen = !avatarOpen"
                    class="
                        flex items-center gap-3
                        rounded-lg p-1.5
                        transition-colors hover:bg-slate-50
                    "
                    aria-label="Menu du compte"
                    aria-haspopup="true"
                    x-bind:aria-expanded="avatarOpen"
                >

                    <span
                        class="
                            flex h-10 w-10 shrink-0
                            items-center justify-center
                            rounded-full bg-[#EEF4FF]
                            text-navy text-sm
                        "
                    >
                        <i class="fa-solid fa-user-tie"></i>
                    </span>


                    <span class="hidden sm:block text-left">

                        <span
                            class="
                                block text-sm font-semibold
                                text-slate-800
                            "
                        >
                            {{ auth()->user()->name }}
                        </span>

                        <span
                            class="
                                block mt-0.5 text-xs
                                text-slate-500
                            "
                        >
                            Agent
                        </span>

                    </span>


                    <i
                        class="
                            fa-solid fa-chevron-down
                            text-xs text-slate-400
                            transition-transform
                        "
                        :class="avatarOpen && 'rotate-180'"
                    ></i>

                </button>


                <div
                    x-show="avatarOpen"
                    x-transition.origin.top.right
                    x-cloak
                    class="
                        absolute right-0 top-full z-50 mt-2
                        w-56 overflow-hidden rounded-xl
                        border border-slate-200 bg-white
                        shadow-lg
                    "
                >

                    <div
                        class="
                            border-b border-slate-100
                            bg-slate-50 px-4 py-3
                        "
                    >

                        <div
                            class="
                                truncate text-sm
                                font-bold text-navy
                            "
                        >
                            {{ auth()->user()->name }}
                        </div>

                        <div
                            class="
                                mt-0.5 truncate text-xs
                                text-slate-500
                            "
                        >
                            Agent
                        </div>

                    </div>


                    @if(Route::has('profile.edit'))

                        <a
                            href="{{ route('profile.edit') }}"
                            class="
                                flex items-center gap-3
                                px-4 py-3 text-sm
                                font-semibold text-slate-600
                                transition-colors hover:bg-slate-50
                                hover:text-navy
                            "
                        >

                            <i class="fa-solid fa-user w-4 text-center"></i>

                            Mon profil

                        </a>

                    @endif


                    <div class="border-t border-slate-100">

                        <form
                            method="POST"
                            action="{{ route('logout') }}"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="
                                    flex w-full items-center gap-3
                                    px-4 py-3 text-sm
                                    font-semibold text-red-600
                                    transition-colors hover:bg-red-50
                                "
                            >

                                <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>

                                Se déconnecter

                            </button>

                        </form>

                    </div>

                </div>

            </div>

            </div>

        </header>


        {{-- =====================================================
             CONTENU
        ====================================================== --}}

        <main
            class="
                flex-1 overflow-y-auto
                overflow-x-hidden
                bg-canvas p-4 sm:p-6
            "
        >

            {{ $slot }}

        </main>

    </div>

</div>


<x-confirm-modal />

<x-flash-modal />

</body>

</html>
