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
        class="tk-page"
    >

        {{-- =========================================================
             TITRE
        ========================================================== --}}

        <div class="tk-page-head">

            <div
                class="
                    flex flex-col gap-4
                    md:flex-row md:items-center
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
                            <i class="fa-solid fa-user"></i>
                        </span>

                        Mon Profil

                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Consultez et gérez vos informations personnelles.
                    </p>

                </div>

            </div>

        </div>


        {{-- =========================================================
             BANNIÈRE PROFIL
        ========================================================== --}}

        <div class="bg-navy rounded-xl overflow-hidden text-white">

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
                                shrink-0
                                bg-white
                                text-navy
                                border-4
                                border-brand
                            "
                        >
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>


                        {{-- Informations --}}

                        <div class="min-w-0">

                            <h2 class="text-3xl font-bold text-white">
                                {{ $user->name }}
                            </h2>

                            <p class="mt-2 break-all text-[#DCE8FF]">
                                <i class="fa-solid fa-envelope"></i>
                                {{ $user->email }}
                            </p>


                            {{-- BADGES --}}

                            <div class="flex flex-wrap gap-3 mt-4">

                                {{-- ROLE --}}

                                <span class="tk-badge tk-badge-orange">

                                    @if($user->role === 'admin')
                                        <i class="fa-solid fa-crown"></i>
                                    @elseif($user->role === 'agent')
                                        <i class="fa-solid fa-user-tie"></i>
                                    @else
                                        <i class="fa-solid fa-user"></i>
                                    @endif

                                    {{ $roleLabel }}

                                </span>


                                {{-- ACTIF --}}

                                <span class="tk-badge tk-badge-green">
                                    <i class="fa-solid fa-check"></i>
                                    Actif
                                </span>


                                {{-- AGENCE --}}

                                @if($isAgent)

                                    <span
                                        class="
                                            tk-badge
                                            bg-white/10
                                            text-[#DCE8FF]
                                        "
                                    >
                                        <i class="fa-solid fa-building"></i>
                                        {{ $agencyName }}
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>


                    {{-- BOUTON MODIFIER --}}

                    <button
                        type="button"
                        @click="profileModal = true"
                        class="tk-btn-accent shrink-0"
                    >
                        <i class="fa-solid fa-pen"></i>
                        Modifier
                    </button>

                </div>

            </div>

        </div>


        {{-- =========================================================
             INFORMATIONS
        ========================================================== --}}

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">


            {{-- INFORMATIONS PERSONNELLES --}}

            <div class="lg:col-span-2 tk-card overflow-hidden">

                <div
                    class="
                        px-6
                        py-5
                        border-b
                        border-slate-200
                        bg-slate-50
                        flex
                        items-center
                        justify-between
                        gap-4
                    "
                >

                    <h2
                        class="
                            text-xl font-bold
                            text-navy
                            flex items-center gap-2.5
                        "
                    >
                        <i class="fa-solid fa-id-card text-brand"></i>
                        Informations personnelles
                    </h2>

                    <button
                        type="button"
                        @click="profileModal = true"
                        class="tk-btn-accent"
                    >
                        <i class="fa-solid fa-pen"></i>
                        Modifier
                    </button>

                </div>


                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- NOM --}}

                        <div class="rounded-xl p-5 bg-[#EEF4FF]">

                            <p class="text-sm text-slate-500">
                                Nom
                            </p>

                            <p class="mt-2 text-xl font-bold text-navy">
                                {{ $user->name }}
                            </p>

                        </div>


                        {{-- EMAIL --}}

                        <div
                            class="
                                rounded-xl p-5
                                bg-[#FFF3E8]
                                border border-[#FFD7B8]
                            "
                        >

                            <p class="text-sm text-slate-500">
                                Adresse e-mail
                            </p>

                            <p class="mt-2 text-lg font-bold break-all text-navy">
                                {{ $user->email }}
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            {{-- INFORMATIONS DU COMPTE --}}

            <div class="tk-card overflow-hidden">

                <div
                    class="
                        px-6 py-5
                        border-b border-slate-200
                        bg-slate-50
                    "
                >

                    <h2
                        class="
                            text-xl font-bold
                            text-navy
                            flex items-center gap-2.5
                        "
                    >
                        <i class="fa-solid fa-circle-info text-brand"></i>
                        Informations du compte
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
                            bg-[#FFF3E8]
                        "
                    >

                        <span class="text-slate-500">
                            Rôle
                        </span>

                        <span class="font-bold text-brand">
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
                                bg-[#EEF4FF]
                            "
                        >

                            <span class="text-slate-500">
                                Agence
                            </span>

                            <span class="font-bold text-right text-navy">
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
                            bg-emerald-50
                        "
                    >

                        <span class="text-slate-500">
                            Statut
                        </span>

                        <span class="font-bold text-emerald-600">
                            <i class="fa-solid fa-check"></i>
                            Actif
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

                        <span class="text-slate-500">
                            Créé le
                        </span>

                        <span class="font-semibold text-slate-800">
                            {{ optional($user->created_at)->format('d/m/Y') }}
                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
             SÉCURITÉ
        ========================================================== --}}

        <div class="tk-card overflow-hidden">

            <div
                class="
                    px-6
                    py-5
                    border-b
                    border-slate-200
                    bg-slate-50
                    flex
                    flex-col
                    sm:flex-row
                    sm:items-center
                    sm:justify-between
                    gap-4
                "
            >

                <h2
                    class="
                        text-xl font-bold
                        text-navy
                        flex items-center gap-2.5
                    "
                >
                    <i class="fa-solid fa-lock text-brand"></i>
                    Sécurité
                </h2>

                <button
                    type="button"
                    @click="passwordModal = true"
                    class="tk-btn-accent"
                >
                    <i class="fa-solid fa-key"></i>
                    Modifier le mot de passe
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

                        <p class="text-sm text-slate-500">
                            Mot de passe
                        </p>

                        <p
                            class="
                                mt-1
                                text-2xl
                                tracking-[6px]
                                font-bold
                                text-navy
                            "
                        >
                            ••••••••••••
                        </p>

                    </div>


                    <span class="tk-badge tk-badge-green">
                        <i class="fa-solid fa-lock"></i>
                        Sécurisé
                    </span>

                </div>

            </div>

        </div>


        {{-- =========================================================
             ZONE DANGEREUSE
        ========================================================== --}}

        <div
            class="
                tk-card
                overflow-hidden
            "
        >

            <div
                class="
                    px-6
                    py-5
                    border-b
                    border-slate-200
                    flex
                    flex-col
                    sm:flex-row
                    sm:items-center
                    sm:justify-between
                    gap-4
                "
            >

                <div>

                    <h2
                        class="
                            text-xl font-bold
                            text-red-600
                            flex items-center gap-2.5
                        "
                    >
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        Zone dangereuse
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        La suppression du compte est définitive.
                    </p>

                </div>


                <button
                    type="button"
                    @click="deleteModal = true"
                    class="tk-btn bg-red-600 text-white hover:bg-red-700"
                >
                    <i class="fa-solid fa-trash"></i>
                    Supprimer
                </button>

            </div>


            <div class="p-6">

                <p class="text-slate-600">
                    Cette action supprimera définitivement votre compte
                    ainsi que toutes les données associées.
                </p>

            </div>

        </div>



{{-- =========================================================
     MODAL MODIFICATION PROFIL
========================================================== --}}

<div
    x-show="profileModal"
    x-transition.opacity
    style="display:none;"
    class="
        fixed
        inset-0
        z-50
        flex
        items-center
        justify-center
        bg-slate-900/60
        p-4
    "
>

    <div
        @click.outside="profileModal = false"
        class="
            tk-card
            w-full
            max-w-lg
            max-h-[85vh]
            overflow-hidden
            flex
            flex-col
        "
    >

        {{-- =====================================================
             HEADER
        ====================================================== --}}

        <div
            class="
                bg-navy
                px-6
                py-4
                flex
                items-center
                justify-between
            "
        >

            <div class="flex items-center gap-3">

                <div
                    class="
                        flex h-10 w-10 shrink-0
                        items-center justify-center
                        rounded-lg bg-white/10
                        text-white
                    "
                >
                    <i class="fa-solid fa-user"></i>
                </div>

                <div>

                    <div class="text-lg font-bold text-white">
                        Modifier mes informations
                    </div>

                    <div class="text-xs text-[#DCE8FF] mt-1">
                        Nom et adresse e-mail
                    </div>

                </div>

            </div>


            {{-- FERMER --}}

            <button
                type="button"
                @click="profileModal = false"
                class="
                    flex h-9 w-9
                    items-center justify-center
                    rounded-lg bg-white/10
                    text-white hover:bg-white/20
                "
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>


        {{-- =====================================================
             CONTENU
        ====================================================== --}}

        <div class="p-6 overflow-y-auto">

            {{-- PETITE INFO --}}

            <div
                class="
                    rounded-lg
                    bg-[#EEF4FF]
                    px-4 py-3
                    mb-5
                    text-sm text-navy
                "
            >
                <i class="fa-solid fa-pen"></i>
                Modifiez uniquement les informations de votre profil.
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
========================================================== --}}

<div
    x-show="passwordModal"
    x-transition.opacity
    class="
        fixed
        inset-0
        z-50
        flex
        items-center
        justify-center
        bg-slate-900/60
        p-4
    "
    style="display:none;"
>

    <div
        @click.outside="passwordModal = false"
        class="
            tk-card
            w-full
            max-w-xl
            max-h-[85vh]
            overflow-y-auto
        "
    >

        {{-- EN-TÊTE --}}

        <div
            class="
                bg-navy
                px-6
                py-4
                flex
                items-center
                justify-between
                sticky
                top-0
                z-10
            "
        >

            <div class="flex items-center gap-3">

                <div
                    class="
                        flex h-10 w-10 shrink-0
                        items-center justify-center
                        rounded-lg bg-white/10
                        text-white
                    "
                >
                    <i class="fa-solid fa-lock"></i>
                </div>

                <div>

                    <h2 class="text-lg font-bold text-white">
                        Modifier le mot de passe
                    </h2>

                    <p class="text-xs mt-1 text-[#DCE8FF]">
                        Sécurisez votre compte
                    </p>

                </div>

            </div>


            {{-- FERMER --}}

            <button
                type="button"
                @click="passwordModal = false"
                class="
                    flex h-9 w-9
                    items-center justify-center
                    rounded-lg bg-white/10
                    text-white hover:bg-white/20
                "
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>


        {{-- CONTENU --}}

        <div class="p-6">

            <div
                class="
                    rounded-xl
                    bg-[#EEF4FF]
                    px-4
                    py-3
                    mb-5
                "
            >

                <p class="text-sm text-navy">
                    <i class="fa-solid fa-lock"></i>
                    Choisissez un mot de passe sécurisé d'au moins
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
                z-50
                flex
                items-center
                justify-center
                bg-slate-900/60
                p-4
            "
            style="display:none;"
        >

            <div
                @click.outside="deleteModal = false"
                class="
                    tk-card
                    w-full
                    max-w-2xl
                    overflow-hidden
                "
            >

                <div
                    class="
                        bg-red-600
                        px-6
                        py-5
                        flex
                        items-center
                        justify-between
                    "
                >

                    <h2
                        class="
                            text-xl font-bold text-white
                            flex items-center gap-2.5
                        "
                    >
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        Confirmation
                    </h2>

                    <button
                        type="button"
                        @click="deleteModal = false"
                        class="text-white"
                    >
                        <i class="fa-solid fa-xmark text-xl"></i>
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
