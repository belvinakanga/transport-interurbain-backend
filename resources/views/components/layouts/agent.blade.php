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

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>


<body
    style="
        margin:0;
        padding:0;
        background:#F8F9FB;
        color:#1F2937;
        font-family:Arial,sans-serif;
        overflow:hidden;
    "
>


<div
    style="
        display:flex;
        width:100%;
        height:100vh;
    "
>


    {{-- =========================================================
         SIDEBAR AGENT
    ========================================================== --}}

    <aside
        style="
            width:350px;
            height:100vh;
            background:#FFFFFF;
            border-right:1px solid #ebe9e5;
            box-shadow:2px 0 10px rgba(0,0,0,.04);
            display:flex;
            flex-direction:column;
            flex-shrink:0;
        "
    >


       {{-- =====================================================
     LOGO
====================================================== --}}

<div
    style="
        height:70px;
        min-height:70px;
        padding:0 16px;
        border-bottom:1px solid #E5E7EB;
        display:flex;
        align-items:center;
    "
>

    <div
        style="
            display:flex;
            align-items:center;
            gap:11px;
        "
    >

        <div
            style="
                width:48px;
                height:48px;
                border-radius:14px;
                background:#0A2A66;
                display:flex;
                align-items:center;
                justify-content:center;
                font-size:22px;
                flex-shrink:0;
            "
        >
            🚌
        </div>


        <div>

            <div
                style="
                    font-size:25px;
                    font-weight:800;
                    line-height:1;
                    color:#0A2A66;
                "
            >
                TOKENDE
            </div>

            <div
                style="
                    margin-top:3px;
                    font-size:14px;
                    font-weight:700;
                    color:#FF6B00;
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
    style="
        padding:13px 14px 12px;
        border-bottom:1px solid #E5E7EB;
    "
>

    <div
        style="
            display:flex;
            align-items:center;
            gap:9px;
        "
    >

        <div
            style="
                width:40px;
                height:40px;
                border-radius:50%;
                background:#EEF4FF;
                border:2px solid #FF6B00;
                display:flex;
                align-items:center;
                justify-content:center;
                font-size:17px;
                flex-shrink:0;
            "
        >
            👨‍💼
        </div>


        <div
            style="
                min-width:0;
            "
        >

            <p
                style="
                    margin:0;
                    font-size:17px;
                    font-weight:800;
                    color:#0A2A66;
                    white-space:nowrap;
                    overflow:hidden;
                    text-overflow:ellipsis;
                "
            >
                {{ auth()->user()->name }}
            </p>

           <p
    style="
        margin:2px 0 0;
        font-size:16px;
        font-weight:700;
        color:#FF6B00;
    "
>
    Agent
</p>

        </div>

    </div>


    {{-- AGENCE --}}

    <div
        style="
            margin-top:10px;
            background:#F8FAFD;
            border:1px solid #DCE8FF;
            border-radius:11px;
            padding:9px 12px;
        "
    >

        <p
            style="
                margin:0;
                font-size:11px;
                color:#6B7280;
            "
        >
            Mon agence
        </p>

        <p
            style="
                margin:4px 0 0;
                font-size:15px;
                font-weight:800;
                color:#0A2A66;
                white-space:nowrap;
                overflow:hidden;
                text-overflow:ellipsis;
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
    style="
        flex:1;
        padding:30px 20px;
        overflow-y:auto;
    "
>

            {{-- TITRE NAVIGATION --}}

            <div
    style="
        padding:0 14px;
        margin:0 0 18px;
        font-size:12px;
        font-weight:800;
        text-transform:uppercase;
        letter-spacing:1.5px;
        color:#9CA3AF;
    "
>
    Navigation
</div>


            {{-- =================================================
                 TABLEAU DE BORD
            ================================================== --}}

            <a
                href="{{ route('agent.dashboard') }}"
                style="
                    display:flex;
                    align-items:center;
                    gap:11px;
                    min-height:52px;
                    padding:0 16px;
                    margin-bottom:8px; 
                    text-decoration:none;
                    font-size:15px;
                    font-weight:800;

                    {{
                        request()->routeIs('agent.dashboard')
                            ? 'background:#0A2A66;color:#FFFFFF;box-shadow:0 4px 10px rgba(10,42,102,.15);'
                            : 'background:transparent;color:#0A2A66;'
                    }}
                "
            >

                <span
                    style="
                        width:23px;
                        text-align:center;
                        font-size:18px;
                    "
                >
                    📊
                </span>

                <span>
                    Tableau de bord
                </span>

            </a>


            {{-- =================================================
                 MES TRAJETS
            ================================================== --}}

            <a
                href="{{ route('admin.trajets') }}"
                style="
                    display:flex;
                    align-items:center;
                    gap:11px;
                    min-height:44px;
                    padding:0 12px;
                    margin-bottom:4px;
                    border-radius:11px;
                    text-decoration:none;
                    font-size:15px;
                    font-weight:800;

                    {{
                        request()->is('admin/trajets*')
                            ? 'background:#0A2A66;color:#FFFFFF;box-shadow:0 4px 10px rgba(10,42,102,.15);'
                            : 'background:transparent;color:#0A2A66;'
                    }}
                "
            >

                <span
                    style="
                        width:23px;
                        text-align:center;
                        font-size:18px;
                    "
                >
                    🚌
                </span>

                <span>
                    Mes trajets
                </span>

            </a>


            {{-- =================================================
                 RÉSERVATIONS
            ================================================== --}}

            <a
                href="{{ route('admin.reservations') }}"
                style="
                    display:flex;
                    align-items:center;
                    gap:11px;
                    min-height:44px;
                    padding:0 12px;
                    margin-bottom:4px;
                    border-radius:11px;
                    text-decoration:none;
                    font-size:15px;
                    font-weight:800;

                    {{
                        request()->is('admin/reservations*')
                            ? 'background:#0A2A66;color:#FFFFFF;box-shadow:0 4px 10px rgba(10,42,102,.15);'
                            : 'background:transparent;color:#0A2A66;'
                    }}
                "
            >

                <span
                    style="
                        width:23px;
                        text-align:center;
                        font-size:18px;
                    "
                >
                    🎫
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
                    href="{{ route('achats.index') }}"
                    style="
                        display:flex;
                        align-items:center;
                        gap:11px;
                        min-height:44px;
                        padding:0 12px;
                        margin-bottom:4px;
                        border-radius:11px;
                        text-decoration:none;
                        font-size:15px;
                        font-weight:800;

                        {{
                            request()->is('achats*')
                                ? 'background:#0A2A66;color:#FFFFFF;box-shadow:0 4px 10px rgba(10,42,102,.15);'
                                : 'background:transparent;color:#0A2A66;'
                        }}
                    "
                >

                    <span
                        style="
                            width:23px;
                            text-align:center;
                            font-size:18px;
                        "
                    >
                        💳
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
                    href="{{ route('agent.paiements') }}"
                    style="
                        display:flex;
                        align-items:center;
                        gap:11px;
                        min-height:44px;
                        padding:0 12px;
                        margin-bottom:4px;
                        border-radius:11px;
                        text-decoration:none;
                        font-size:15px;
                        font-weight:800;

                        {{
                            request()->routeIs('agent.paiements')
                                ? 'background:#0A2A66;color:#FFFFFF;box-shadow:0 4px 10px rgba(10,42,102,.15);'
                                : 'background:transparent;color:#0A2A66;'
                        }}
                    "
                >

                    <span
                        style="
                            width:23px;
                            text-align:center;
                            font-size:18px;
                        "
                    >
                        💰
                    </span>

                    <span>
                        Mes paiements
                    </span>

                </a>

            @endif


            {{-- =================================================
                 PROFIL
            ================================================== --}}

            @if(Route::has('profile.edit'))

                <a
                    href="{{ route('profile.edit') }}"
                    style="
                        display:flex;
                        align-items:center;
                        gap:11px;
                        min-height:44px;
                        padding:0 12px;
                        margin-top:10px;
                        border-radius:11px;
                        text-decoration:none;
                        font-size:15px;
                        font-weight:800;

                        {{
                            request()->is('profile*')
                                ? 'background:#0A2A66;color:#FFFFFF;box-shadow:0 4px 10px rgba(10,42,102,.15);'
                                : 'background:transparent;color:#0A2A66;'
                        }}
                    "
                >

                    <span
                        style="
                            width:23px;
                            text-align:center;
                            font-size:18px;
                        "
                    >
                        👤
                    </span>

                    <span>
                        Profil
                    </span>

                </a>

            @endif

        </nav>


        {{-- =====================================================
             DÉCONNEXION
        ====================================================== --}}

        <div
            style="
                padding:10px;
                border-top:1px solid #E5E7EB;
                background:#FFFFFF;
            "
        >

            <form
                method="POST"
                action="{{ route('logout') }}"
            >

                @csrf

                <button
                    type="submit"
                    style="
                        width:100%;
                        min-height:45px;
                        border:none;
                        border-radius:11px;
                        background:#FF6B00;
                        color:#FFFFFF;
                        font-size:15px;
                        font-weight:800;
                        cursor:pointer;
                        box-shadow:0 4px 10px rgba(255,107,0,.16);
                    "
                >

                    🚪
                    &nbsp;
                    Déconnexion

                </button>

            </form>

        </div>

    </aside>



    {{-- =========================================================
         CONTENU PRINCIPAL
    ========================================================== --}}

    <div
        style="
            flex:1;
            min-width:0;
            height:100vh;
            display:flex;
            flex-direction:column;
            overflow:hidden;
        "
    >


        {{-- =====================================================
             HEADER
        ====================================================== --}}

        <header
            style="
                height:70px;
                min-height:70px;
                background:#FFFFFF;
                border-bottom:1px solid #E5E7EB;
                display:flex;
                align-items:center;
                justify-content:space-between;
                padding:0 24px;
                box-shadow:0 1px 5px rgba(0,0,0,.03);
            "
        >

            <div>

                <div
                    style="
                        font-size:25px;
                        font-weight:800;
                        color:#0A2A66;
                    "
                >
                    {{ $header ?? 'Espace Agent' }}
                </div>

                <div
                    style="
                        margin-top:2px;
                        font-size:13px;
                        color:#6B7280;
                    "
                >
                    {{ auth()->user()->agence->nom_agence ?? 'Aucune agence' }}
                </div>

            </div>


            <div
                style="
                    width:40px;
                    height:40px;
                    border-radius:50%;
                    background:#FFF3E8;
                    border:2px solid #FF6B00;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-size:18px;
                "
            >
                👨‍💼
            </div>

        </header>


        {{-- =====================================================
             CONTENU
        ====================================================== --}}

        <main
            style="
                flex:1;
                overflow-y:auto;
                overflow-x:hidden;
                background:#F8F9FB;
                padding:24px;
            "
        >

            {{ $slot }}

        </main>

    </div>

</div>


</body>

</html>