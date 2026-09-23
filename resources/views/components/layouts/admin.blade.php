<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        {{ $title ?? 'TOKENDE | Administration' }}
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    >

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
         SIDEBAR ADMIN
    ========================================================== --}}

    <aside
        style="
            width:285px;
            height:100vh;
            background:#FFFFFF;
            border-right:1px solid #E5E7EB;
            box-shadow:2px 0 12px rgba(0,0,0,.04);
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
                padding:20px;
                border-bottom:1px solid #E5E7EB;
            "
        >

            <div
                style="
                    display:flex;
                    align-items:center;
                    gap:12px;
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
                        color:#FFFFFF;
                        font-size:22px;
                    "
                >
                    🚌
                </div>


                <div>

                    <div
                        style="
                            font-size:25px;
                            font-weight:800;
                            color:#0A2A66;
                            line-height:1;
                        "
                    >
                        TOKENDE
                    </div>

                    <div
                        style="
                            margin-top:5px;
                            font-size:12px;
                            color:#6B7280;
                        "
                    >
                        Congo Administration
                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             NAVIGATION
        ====================================================== --}}

        <nav
            style="
                flex:1;
                padding:18px 14px;
                overflow-y:auto;
            "
        >

            <div
                style="
                    padding:0 12px;
                    margin-bottom:10px;
                    font-size:11px;
                    font-weight:800;
                    text-transform:uppercase;
                    letter-spacing:1.5px;
                    color:#9CA3AF;
                "
            >
                Navigation
            </div>


            {{-- DASHBOARD --}}

            <a
                href="{{ url('/admin') }}"
                style="
                    display:flex;
                    align-items:center;
                    gap:13px;
                    height:46px;
                    padding:0 14px;
                    margin-bottom:5px;
                    border-radius:11px;
                    text-decoration:none;
                    font-size:14px;
                    font-weight:700;
                    transition:.2s;

                    {{ request()->is('admin')
                        ? 'background:#0A2A66;color:#FFFFFF;box-shadow:0 4px 10px rgba(10,42,102,.15);'
                        : 'color:#0A2A66;background:transparent;'
                    }}
                "
            >

                <span
                    style="
                        width:21px;
                        text-align:center;
                        font-size:17px;
                    "
                >
                    📊
                </span>

                <span>
                    Dashboard
                </span>

            </a>


            {{-- AGENCES --}}

            <a
                href="{{ url('/admin/agences') }}"
                style="
                    display:flex;
                    align-items:center;
                    gap:13px;
                    height:46px;
                    padding:0 14px;
                    margin-bottom:5px;
                    border-radius:11px;
                    text-decoration:none;
                    font-size:14px;
                    font-weight:700;

                    {{ request()->is('admin/agences*')
                        ? 'background:#0A2A66;color:#FFFFFF;box-shadow:0 4px 10px rgba(10,42,102,.15);'
                        : 'color:#0A2A66;background:transparent;'
                    }}
                "
            >

                <span
                    style="
                        width:21px;
                        text-align:center;
                        font-size:17px;
                    "
                >
                    🏢
                </span>

                <span>
                    Agences
                </span>

            </a>


            {{-- UTILISATEURS --}}

            <a
                href="{{ url('/admin/voyageurs') }}"
                style="
                    display:flex;
                    align-items:center;
                    gap:13px;
                    height:46px;
                    padding:0 14px;
                    margin-bottom:5px;
                    border-radius:11px;
                    text-decoration:none;
                    font-size:14px;
                    font-weight:700;

                    {{ request()->is('admin/voyageurs*')
                        ? 'background:#0A2A66;color:#FFFFFF;box-shadow:0 4px 10px rgba(10,42,102,.15);'
                        : 'color:#0A2A66;background:transparent;'
                    }}
                "
            >

                <span
                    style="
                        width:21px;
                        text-align:center;
                        font-size:17px;
                    "
                >
                    👥
                </span>

                <span>
                    Utilisateurs
                </span>

            </a>


            {{-- ABONNEMENTS --}}

            <a
                href="{{ route('admin.abonnements') }}"
                style="
                    display:flex;
                    align-items:center;
                    gap:13px;
                    height:46px;
                    padding:0 14px;
                    margin-bottom:5px;
                    border-radius:11px;
                    text-decoration:none;
                    font-size:14px;
                    font-weight:700;

                    {{ request()->is('admin/abonnements*')
                        ? 'background:#0A2A66;color:#FFFFFF;box-shadow:0 4px 10px rgba(10,42,102,.15);'
                        : 'color:#0A2A66;background:transparent;'
                    }}
                "
            >

                <span
                    style="
                        width:21px;
                        text-align:center;
                        font-size:17px;
                    "
                >
                    📋
                </span>

                <span>
                    Abonnements
                </span>

            </a>


            {{-- PAIEMENTS AGENCES --}}

            @if(Route::has('paiements-agences.index'))

                <a
                    href="{{ route('paiements-agences.index') }}"
                    style="
                        display:flex;
                        align-items:center;
                        gap:13px;
                        height:46px;
                        padding:0 14px;
                        margin-bottom:5px;
                        border-radius:11px;
                        text-decoration:none;
                        font-size:14px;
                        font-weight:700;

                        {{ request()->is('paiements-agences*')
                            ? 'background:#0A2A66;color:#FFFFFF;box-shadow:0 4px 10px rgba(10,42,102,.15);'
                            : 'color:#0A2A66;background:transparent;'
                        }}
                    "
                >

                    <span
                        style="
                            width:21px;
                            text-align:center;
                            font-size:17px;
                        "
                    >
                        💰
                    </span>

                    <span>
                        Paiements agences
                    </span>

                </a>

            @endif


            {{-- AVIS --}}

            <a
                href="{{ url('/admin/avis') }}"
                style="
                    display:flex;
                    align-items:center;
                    gap:13px;
                    height:46px;
                    padding:0 14px;
                    margin-bottom:5px;
                    border-radius:11px;
                    text-decoration:none;
                    font-size:14px;
                    font-weight:700;

                    {{ request()->is('admin/avis*')
                        ? 'background:#0A2A66;color:#FFFFFF;box-shadow:0 4px 10px rgba(10,42,102,.15);'
                        : 'color:#0A2A66;background:transparent;'
                    }}
                "
            >

                <span
                    style="
                        width:21px;
                        text-align:center;
                        font-size:17px;
                    "
                >
                    ⭐
                </span>

                <span>
                    Avis
                </span>

            </a>


            {{-- ESPACE --}}

            <div style="height:18px;"></div>


            {{-- PROFIL --}}

            @if(Route::has('profile.edit'))

                <a
                    href="{{ route('profile.edit') }}"
                    style="
                        display:flex;
                        align-items:center;
                        gap:13px;
                        height:46px;
                        padding:0 14px;
                        margin-bottom:5px;
                        border-radius:11px;
                        text-decoration:none;
                        font-size:14px;
                        font-weight:700;

                        {{ request()->is('profile*')
                            ? 'background:#0A2A66;color:#FFFFFF;box-shadow:0 4px 10px rgba(10,42,102,.15);'
                            : 'color:#0A2A66;background:transparent;'
                        }}
                    "
                >

                    <span
                        style="
                            width:21px;
                            text-align:center;
                            font-size:17px;
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
                padding:14px;
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
                        height:46px;
                        border:none;
                        border-radius:11px;
                        background:#FF6B00;
                        color:#FFFFFF;
                        font-size:14px;
                        font-weight:700;
                        cursor:pointer;
                        box-shadow:0 4px 10px rgba(255,107,0,.18);
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
                height:78px;
                min-height:78px;
                background:#FFFFFF;
                border-bottom:1px solid #E5E7EB;
                display:flex;
                align-items:center;
                justify-content:space-between;
                padding:0 30px;
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
                    {{ $header ?? 'Administration' }}
                </div>

                <div
                    style="
                        margin-top:3px;
                        font-size:13px;
                        color:#6B7280;
                    "
                >
                    TOKENDE
                </div>

            </div>


            <div
                style="
                    display:flex;
                    align-items:center;
                    gap:18px;
                "
            >

                {{-- NOTIFICATION --}}

                <button
                    type="button"
                    style="
                        width:42px;
                        height:42px;
                        border:none;
                        border-radius:50%;
                        background:#F8F9FB;
                        color:#0A2A66;
                        cursor:pointer;
                        position:relative;
                        font-size:16px;
                    "
                >

                    🔔

                    <span
                        style="
                            position:absolute;
                            top:7px;
                            right:7px;
                            width:8px;
                            height:8px;
                            background:#FF6B00;
                            border-radius:50%;
                            border:2px solid #FFFFFF;
                        "
                    ></span>

                </button>


                {{-- PROFIL ADMIN --}}

                <div
                    style="
                        display:flex;
                        align-items:center;
                        gap:10px;
                    "
                >

                    <div
                        style="
                            width:42px;
                            height:42px;
                            border-radius:50%;
                            background:#EAF2FF;
                            border:2px solid #D8E6FF;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            color:#0A2A66;
                            font-size:17px;
                        "
                    >
                        👤
                    </div>


                    <div>

                        <div
                            style="
                                font-size:14px;
                                font-weight:700;
                                color:#0A2A66;
                            "
                        >

                            {{ optional(Auth::user())->name ?? 'Administrateur' }}

                        </div>

                        <div
                            style="
                                margin-top:2px;
                                font-size:12px;
                                color:#6B7280;
                            "
                        >
                            Administrateur
                        </div>

                    </div>

                </div>

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
                padding:30px;
            "
        >

            {{ $slot }}

        </main>

    </div>

</div>


</body>

</html>