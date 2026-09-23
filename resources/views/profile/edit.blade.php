@php
    $user = auth()->user();

    $isAgent = $user->role === 'agent';

    $layout = $isAgent
        ? 'layouts.agent'
        : 'layouts.admin';

    $roleLabel = match ($user->role) {
        'admin' => 'Administrateur',
        'agent' => 'Agent',
        default => 'Utilisateur',
    };

    $agencyName = $user->agence->nom_agence ?? 'Aucune agence';
@endphp


<x-dynamic-component
    :component="$layout"
    :header="'Mon Profil'"
>

    <div
        x-data="{
            profileModal: false,
            passwordModal: false,
            deleteModal: false
        }"
        class="space-y-6"
    >

        {{-- =========================================================
             TITRE
        ========================================================== --}}

        <div>

            <h1
                class="text-3xl md:text-4xl font-bold flex items-center gap-3"
                style="color:#0A2A66;"
            >
                👤 Mon Profil
            </h1>

            <p class="mt-2 text-gray-500">
                Consultez et gérez vos informations personnelles.
            </p>

        </div>


        {{-- =========================================================
             BANNIÈRE PROFIL
        ========================================================== --}}

        <div
            class="rounded-2xl shadow-lg overflow-hidden"
            style="background:#0A2A66;"
        >

            <div class="px-6 md:px-8 py-7">

                <div
                    class="
                        flex
                        flex-col
                        lg:flex-row
                        items-center
                        justify-between
                        gap-6
                    "
                >

                    {{-- PROFIL --}}

                    <div class="flex items-center gap-5">

                        {{-- Avatar --}}

                        <div
                            class="
                                w-24
                                h-24
                                rounded-full
                                flex
                                items-center
                                justify-center
                                text-4xl
                                font-bold
                                shadow-lg
                                shrink-0
                            "
                            style="
                                background:#FFFFFF;
                                color:#0A2A66;
                                border:4px solid #FF6B00;
                            "
                        >
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>


                        {{-- Informations --}}

                        <div class="min-w-0">

                            <h2
                                class="text-3xl font-bold"
                                style="color:#FFFFFF;"
                            >
                                {{ $user->name }}
                            </h2>

                            <p
                                class="mt-2 break-all"
                                style="color:#DCE8FF;"
                            >
                                ✉️ {{ $user->email }}
                            </p>


                            {{-- BADGES --}}

                            <div class="flex flex-wrap gap-3 mt-4">

                                {{-- ROLE --}}

                                <span
                                    class="
                                        px-4
                                        py-2
                                        rounded-full
                                        text-sm
                                        font-bold
                                    "
                                    style="
                                        background:#FF6B00;
                                        color:#FFFFFF;
                                    "
                                >

                                    @if($user->role === 'admin')
                                        👑
                                    @elseif($user->role === 'agent')
                                        👨‍💼
                                    @else
                                        👤
                                    @endif

                                    {{ $roleLabel }}

                                </span>


                                {{-- ACTIF --}}

                                <span
                                    class="
                                        px-4
                                        py-2
                                        rounded-full
                                        text-sm
                                        font-bold
                                    "
                                    style="
                                        background:#16A34A;
                                        color:#FFFFFF;
                                    "
                                >
                                    ✓ Actif
                                </span>


                                {{-- AGENCE --}}

                                @if($isAgent)

                                    <span
                                        class="
                                            px-4
                                            py-2
                                            rounded-full
                                            text-sm
                                            font-semibold
                                        "
                                        style="
                                            background:#163B80;
                                            color:#FFFFFF;
                                            border:1px solid #4265A5;
                                        "
                                    >
                                        🏢 {{ $agencyName }}
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>


                    {{-- BOUTON MODIFIER --}}

                    <button
                        type="button"
                        @click="profileModal = true"
                        class="
                            px-6
                            py-3
                            rounded-xl
                            font-bold
                            shadow-lg
                            transition
                            shrink-0
                        "
                        style="
                            background:#FF6B00;
                            color:#FFFFFF;
                        "
                    >
                        ✏️ Modifier
                    </button>

                </div>

            </div>

        </div>


        {{-- =========================================================
             INFORMATIONS
        ========================================================== --}}

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">


            {{-- INFORMATIONS PERSONNELLES --}}

            <div
                class="
                    lg:col-span-2
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
                        flex
                        items-center
                        justify-between
                        gap-4
                    "
                    style="background:#F6F8FC;"
                >

                    <h2
                        class="text-xl font-bold"
                        style="color:#0A2A66;"
                    >
                        🪪 Informations personnelles
                    </h2>

                    <button
                        type="button"
                        @click="profileModal = true"
                        class="
                            px-4
                            py-2
                            rounded-lg
                            font-semibold
                        "
                        style="
                            background:#FF6B00;
                            color:#FFFFFF;
                        "
                    >
                        ✏️ Modifier
                    </button>

                </div>


                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- NOM --}}

                        <div
                            class="rounded-xl p-5"
                            style="background:#EEF4FF;"
                        >

                            <p class="text-sm text-gray-500">
                                Nom
                            </p>

                            <p
                                class="mt-2 text-xl font-bold"
                                style="color:#0A2A66;"
                            >
                                {{ $user->name }}
                            </p>

                        </div>


                        {{-- EMAIL --}}

                        <div
                            class="rounded-xl p-5"
                            style="background:#FFF3E8;"
                        >

                            <p class="text-sm text-gray-500">
                                Adresse e-mail
                            </p>

                            <p
                                class="mt-2 text-lg font-bold break-all"
                                style="color:#0A2A66;"
                            >
                                {{ $user->email }}
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            {{-- INFORMATIONS DU COMPTE --}}

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
                    class="px-6 py-5 border-b"
                    style="background:#F6F8FC;"
                >

                    <h2
                        class="text-xl font-bold"
                        style="color:#0A2A66;"
                    >
                        ℹ️ Informations du compte
                    </h2>

                </div>


                <div class="p-6 space-y-4">

                    {{-- ROLE --}}

                    <div
                        class="
                            flex
                            items-center
                            justify-between
                            gap-4
                            rounded-xl
                            px-4
                            py-3
                        "
                        style="background:#FFF3E8;"
                    >

                        <span class="text-gray-500">
                            Rôle
                        </span>

                        <span
                            class="font-bold"
                            style="color:#FF6B00;"
                        >
                            {{ $roleLabel }}
                        </span>

                    </div>


                    {{-- AGENCE --}}

                    @if($isAgent)

                        <div
                            class="
                                flex
                                items-center
                                justify-between
                                gap-4
                                rounded-xl
                                px-4
                                py-3
                            "
                            style="background:#EEF4FF;"
                        >

                            <span class="text-gray-500">
                                Agence
                            </span>

                            <span
                                class="
                                    font-bold
                                    text-right
                                "
                                style="color:#0A2A66;"
                            >
                                {{ $agencyName }}
                            </span>

                        </div>

                    @endif


                    {{-- STATUT --}}

                    <div
                        class="
                            flex
                            items-center
                            justify-between
                            gap-4
                            rounded-xl
                            px-4
                            py-3
                        "
                        style="background:#F0FDF4;"
                    >

                        <span class="text-gray-500">
                            Statut
                        </span>

                        <span
                            class="font-bold"
                            style="color:#16A34A;"
                        >
                            ✓ Actif
                        </span>

                    </div>


                    {{-- DATE --}}

                    <div
                        class="
                            flex
                            items-center
                            justify-between
                            gap-4
                            px-4
                            py-3
                        "
                    >

                        <span class="text-gray-500">
                            Créé le
                        </span>

                        <span class="font-semibold text-gray-800">
                            {{ optional($user->created_at)->format('d/m/Y') }}
                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
             SÉCURITÉ
        ========================================================== --}}

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
                    flex
                    flex-col
                    sm:flex-row
                    sm:items-center
                    sm:justify-between
                    gap-4
                "
                style="background:#F6F8FC;"
            >

                <h2
                    class="text-xl font-bold"
                    style="color:#0A2A66;"
                >
                    🔐 Sécurité
                </h2>

                <button
                    type="button"
                    @click="passwordModal = true"
                    class="
                        px-4
                        py-2
                        rounded-lg
                        font-semibold
                    "
                    style="
                        background:#FF6B00;
                        color:#FFFFFF;
                    "
                >
                    🔑 Modifier le mot de passe
                </button>

            </div>


            <div class="p-6">

                <div
                    class="
                        flex
                        flex-col
                        sm:flex-row
                        sm:items-center
                        sm:justify-between
                        gap-5
                    "
                >

                    <div>

                        <p class="text-sm text-gray-500">
                            Mot de passe
                        </p>

                        <p
                            class="
                                mt-1
                                text-2xl
                                tracking-[6px]
                                font-bold
                            "
                            style="color:#0A2A66;"
                        >
                            ••••••••••••
                        </p>

                    </div>


                    <span
                        class="
                            inline-flex
                            items-center
                            px-4
                            py-2
                            rounded-full
                            font-semibold
                        "
                        style="
                            background:#DCFCE7;
                            color:#15803D;
                        "
                    >
                        🔒 Sécurisé
                    </span>

                </div>

            </div>

        </div>


        {{-- =========================================================
             ZONE DANGEREUSE
        ========================================================== --}}

        <div
            class="
                bg-white
                rounded-2xl
                shadow-sm
                border-l-4
                border-red-500
                overflow-hidden
            "
        >

            <div
                class="
                    px-6
                    py-5
                    border-b
                    flex
                    flex-col
                    sm:flex-row
                    sm:items-center
                    sm:justify-between
                    gap-4
                "
            >

                <div>

                    <h2 class="text-xl font-bold text-red-600">
                        ⚠️ Zone dangereuse
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        La suppression du compte est définitive.
                    </p>

                </div>


                <button
                    type="button"
                    @click="deleteModal = true"
                    class="
                        bg-red-600
                        hover:bg-red-700
                        text-white
                        px-5
                        py-2
                        rounded-lg
                        font-semibold
                    "
                >
                    🗑️ Supprimer
                </button>

            </div>


            <div class="p-6">

                <p class="text-gray-600">
                    Cette action supprimera définitivement votre compte
                    ainsi que toutes les données associées.
                </p>

            </div>

        </div>


       
{{-- =========================================================
     MODAL MODIFICATION PROFIL
========================================================= --}}

<div
    x-show="profileModal"
    x-transition.opacity
    style="
        display:none;
        position:fixed;
        inset:0;
        z-index:2000;
        background:rgba(0,0,0,0.60);
        align-items:center;
        justify-content:center;
        padding:20px;
    "
>

    <div
        @click.outside="profileModal = false"
        style="
            width:520px;
            max-width:calc(100vw - 40px);
            max-height:85vh;
            background:#FFFFFF;
            border-radius:18px;
            box-shadow:0 25px 60px rgba(0,0,0,0.25);
            overflow:hidden;
        "
    >

        {{-- =====================================================
             HEADER
        ====================================================== --}}

        <div
            style="
                background:#0A2A66;
                padding:18px 22px;
                display:flex;
                align-items:center;
                justify-content:space-between;
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
                        width:38px;
                        height:38px;
                        border-radius:50%;
                        background:#163B80;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        color:#FFFFFF;
                        font-size:18px;
                    "
                >
                    👤
                </div>

                <div>

                    <div
                        style="
                            color:#FFFFFF;
                            font-size:18px;
                            font-weight:700;
                        "
                    >
                        Modifier mes informations
                    </div>

                    <div
                        style="
                            color:#DCE8FF;
                            font-size:12px;
                            margin-top:3px;
                        "
                    >
                        Nom et adresse e-mail
                    </div>

                </div>

            </div>


            {{-- FERMER --}}

            <button
                type="button"
                @click="profileModal = false"
                style="
                    width:36px;
                    height:36px;
                    border:none;
                    border-radius:50%;
                    background:#163B80;
                    color:#FFFFFF;
                    font-size:20px;
                    cursor:pointer;
                "
            >
                ✕
            </button>

        </div>


        {{-- =====================================================
             CONTENU
        ====================================================== --}}

        <div
            style="
                padding:22px;
                max-height:calc(85vh - 75px);
                overflow-y:auto;
            "
        >

            {{-- PETITE INFO --}}

            <div
                style="
                    background:#EEF4FF;
                    border-radius:12px;
                    padding:12px 14px;
                    margin-bottom:18px;
                    color:#0A2A66;
                    font-size:13px;
                "
            >
                ✏️ Modifiez uniquement les informations de votre profil.
            </div>


            {{-- FORMULAIRE EXISTANT --}}

            @include(
                'profile.partials.update-profile-information-form'
            )

        </div>

    </div>

</div>
       {{-- =========================================================
     MODAL MOT DE PASSE
========================================================= --}}

<div
    x-show="passwordModal"
    x-transition.opacity
    class="
        fixed
        inset-0
        z-[100]
        flex
        items-center
        justify-center
        bg-black/60
        p-4
    "
    style="display:none;"
>

    <div
        @click.outside="passwordModal = false"
        class="
            bg-white
            rounded-2xl
            shadow-2xl
            w-full
            max-w-xl
            max-h-[85vh]
            overflow-y-auto
        "
    >

        {{-- EN-TÊTE --}}

        <div
            class="
                px-6
                py-4
                flex
                items-center
                justify-between
                sticky
                top-0
                z-10
            "
            style="background:#0A2A66;"
        >

            <div class="flex items-center gap-3">

                <div
                    class="
                        w-10
                        h-10
                        rounded-full
                        flex
                        items-center
                        justify-center
                    "
                    style="
                        background:#163B80;
                        color:#FFFFFF;
                    "
                >
                    🔐
                </div>

                <div>

                    <h2
                        class="text-lg font-bold"
                        style="color:#FFFFFF;"
                    >
                        Modifier le mot de passe
                    </h2>

                    <p
                        class="text-xs mt-1"
                        style="color:#DCE8FF;"
                    >
                        Sécurisez votre compte
                    </p>

                </div>

            </div>


            {{-- FERMER --}}

            <button
                type="button"
                @click="passwordModal = false"
                class="
                    w-9
                    h-9
                    rounded-full
                    flex
                    items-center
                    justify-center
                    text-xl
                "
                style="
                    background:#163B80;
                    color:#FFFFFF;
                "
            >
                ✕
            </button>

        </div>


        {{-- CONTENU --}}

        <div class="p-6">

            <div
                class="
                    rounded-xl
                    px-4
                    py-3
                    mb-5
                "
                style="background:#EEF4FF;"
            >

                <p
                    class="text-sm"
                    style="color:#0A2A66;"
                >
                    🔒 Choisissez un mot de passe sécurisé d'au moins
                    8 caractères.
                </p>

            </div>


            {{-- FORMULAIRE EXISTANT --}}

            <div class="text-sm">

                @include(
                    'profile.partials.update-password-form'
                )

            </div>

        </div>

    </div>

</div>


        {{-- =========================================================
             MODAL SUPPRESSION
        ========================================================== --}}

        <div
            x-show="deleteModal"
            x-transition
            class="
                fixed
                inset-0
                z-[100]
                flex
                items-center
                justify-center
                bg-black/60
                p-4
            "
            style="display:none;"
        >

            <div
                @click.outside="deleteModal = false"
                class="
                    bg-white
                    rounded-2xl
                    shadow-2xl
                    w-full
                    max-w-2xl
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
                    "
                    style="background:#DC2626;"
                >

                    <h2 class="text-xl font-bold text-white">
                        ⚠️ Confirmation
                    </h2>

                    <button
                        type="button"
                        @click="deleteModal = false"
                        class="text-white text-2xl"
                    >
                        ✕
                    </button>

                </div>

                <div class="p-6">

                    @include(
                        'profile.partials.delete-user-form'
                    )

                </div>

            </div>

        </div>

    </div>

</x-dynamic-component>