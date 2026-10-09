<x-layouts.admin>

<div class="tk-page">

    {{-- =========================================================
         EN-TÊTE DE LA CONVERSATION
    ========================================================== --}}

    <div class="tk-page-head">


        {{-- RETOUR --}}

        <div class="mb-4">

            <a
                href="{{ route('admin.support.index') }}"
                class="tk-btn-ghost"
            >

                <i class="fa-solid fa-arrow-left"></i>

                <span>Retour aux conversations</span>

            </a>

        </div>


        <div
            class="
                flex flex-col gap-4
                md:flex-row md:items-start
                md:justify-between
            "
        >

            <div class="min-w-0">

                <h1 class="tk-page-title">

                    <span
                        class="
                            flex h-11 w-11 shrink-0
                            items-center justify-center
                            rounded-lg bg-orange-50
                            text-lg text-brand
                        "
                    >
                        <i class="fa-solid fa-message"></i>
                    </span>

                    {{ $conversation->subject }}

                </h1>

                <p class="mt-2 text-sm text-slate-500">

                    Conversation avec

                    <strong class="text-navy">
                        {{ $conversation->user->name ?? 'Voyageur' }}
                    </strong>

                </p>

            </div>


            {{-- STATUT --}}

            <div class="shrink-0">

                @if($conversation->status === 'open')

                    <span class="tk-badge tk-badge-orange">

                        <i class="fa-solid fa-circle text-[6px]"></i>

                        Ouverte

                    </span>

                @else

                    <span class="tk-badge tk-badge-red">

                        <i class="fa-solid fa-lock"></i>

                        Fermée

                    </span>

                @endif

            </div>

        </div>

    </div>


    {{-- =========================================================
         CARTE DU VOYAGEUR
    ========================================================== --}}

    <div class="tk-card p-6">

        <div
            class="
                flex flex-wrap items-center
                justify-between gap-4
            "
        >

            <div class="flex items-center gap-3">

                <span
                    class="
                        tk-kpi-icon
                        bg-[#EEF4FF] text-navy
                    "
                >
                    <i class="fa-solid fa-user"></i>
                </span>

                <div>

                    <p class="tk-label text-brand">
                        VOYAGEUR
                    </p>

                    <p class="mt-1 text-base font-bold text-navy">
                        {{ $conversation->user->name ?? 'Voyageur' }}
                    </p>

                    <p
                        class="
                            mt-1 flex items-center
                            gap-1.5 text-xs
                            text-slate-500
                        "
                    >

                        <i class="fa-solid fa-envelope text-brand"></i>

                        {{ $conversation->user->email ?? 'Email inconnu' }}

                    </p>

                </div>

            </div>


            <div class="text-right">

                <p class="tk-label text-brand">
                    CONVERSATION CRÉÉE LE
                </p>

                <p class="mt-1 text-sm font-bold text-navy tabular-nums">
                    {{ $conversation->created_at->format('d/m/Y à H:i') }}
                </p>

            </div>

        </div>

    </div>


    {{-- =========================================================
         MESSAGES + RÉPONSE
    ========================================================== --}}

    <div class="tk-card overflow-hidden">


        {{-- =====================================================
             HEADER MESSAGES
        ====================================================== --}}

        <div
            class="
                flex items-center justify-between
                gap-4 border-b border-slate-200
                px-6 py-4
            "
        >

            <div class="flex items-center gap-3">

                <span
                    class="
                        tk-kpi-icon
                        bg-orange-50 text-brand
                    "
                >
                    <i class="fa-solid fa-comment"></i>
                </span>

                <div>

                    <h2 class="text-base font-bold text-navy">
                        Messages
                    </h2>

                    <p class="mt-0.5 text-xs text-slate-500">
                        Historique de la conversation
                    </p>

                </div>

            </div>


            <span class="tk-badge tk-badge-navy shrink-0">

                <i class="fa-solid fa-message"></i>

                <strong>{{ $conversation->messages->count() }}</strong>

                message{{ $conversation->messages->count() > 1 ? 's' : '' }}

            </span>

        </div>


        {{-- =====================================================
             ZONE DES MESSAGES
        ====================================================== --}}

        <div
            class="
                max-h-[570px] overflow-y-auto
                p-6 space-y-5
            "
        >

            @forelse($conversation->messages as $message)

                @if($message->sender_type === 'admin')

                    {{-- =================================================
                         MESSAGE ADMIN
                    ================================================== --}}

                    <div class="flex justify-end">

                        <div class="max-w-[70%]">

                            <div
                                class="
                                    mb-1.5 flex items-center
                                    justify-end gap-2
                                    text-xs font-semibold
                                    text-slate-500
                                "
                            >

                                <span
                                    class="
                                        flex h-6 w-6 shrink-0
                                        items-center justify-center
                                        rounded-lg bg-[#EEF4FF]
                                        text-[11px] text-navy
                                    "
                                >
                                    <i class="fa-solid fa-headset"></i>
                                </span>

                                <span>
                                    Administration TOKENDÉ
                                </span>

                                <span class="tk-badge tk-badge-orange">
                                    Vous
                                </span>

                            </div>


                            <div class="rounded-lg bg-slate-50 p-4">

                                <p class="text-sm text-slate-600">
                                    {{ $message->message }}
                                </p>

                                <div
                                    class="
                                        mt-2 flex items-center
                                        justify-end gap-1.5
                                        text-xs text-slate-400
                                    "
                                >

                                    <span>
                                        {{ $message->created_at->format('d/m/Y à H:i') }}
                                    </span>

                                    <i class="fa-solid fa-check"></i>

                                </div>

                            </div>

                        </div>

                    </div>

                @else

                    {{-- =================================================
                         MESSAGE VOYAGEUR
                    ================================================== --}}

                    <div class="flex justify-start">

                        <div class="max-w-[70%]">

                            <div
                                class="
                                    mb-1.5 flex items-center
                                    gap-2 text-xs font-semibold
                                    text-slate-500
                                "
                            >

                                <span
                                    class="
                                        flex h-6 w-6 shrink-0
                                        items-center justify-center
                                        rounded-lg bg-orange-50
                                        text-[11px] text-brand
                                    "
                                >
                                    <i class="fa-solid fa-user"></i>
                                </span>

                                <span>
                                    {{ $message->user->name ?? 'Voyageur' }}
                                </span>

                            </div>


                            <div class="rounded-lg bg-slate-50 p-4">

                                <p class="text-sm text-slate-600">
                                    {{ $message->message }}
                                </p>

                                <div
                                    class="
                                        mt-2 flex items-center
                                        gap-1.5 text-xs
                                        text-slate-400
                                    "
                                >

                                    <i class="fa-solid fa-clock"></i>

                                    <span>
                                        {{ $message->created_at->format('d/m/Y à H:i') }}
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                @endif

            @empty

                {{-- =================================================
                     AUCUN MESSAGE
                ================================================== --}}

                <div class="tk-empty">

                    <i class="fa-solid fa-inbox mb-3 block text-3xl text-slate-300"></i>

                    <p class="text-sm font-bold text-navy">
                        Aucun message
                    </p>

                    <p class="mt-1">
                        Aucun message n'a encore été envoyé.
                    </p>

                </div>

            @endforelse

        </div>


        {{-- =====================================================
             ZONE DE RÉPONSE
        ====================================================== --}}

        @if($conversation->status === 'open')

            <div class="border-t border-slate-200 p-6">

                <div class="mb-4 flex items-center gap-3">

                    <span
                        class="
                            tk-kpi-icon
                            bg-orange-50 text-brand
                        "
                    >
                        <i class="fa-solid fa-reply"></i>
                    </span>

                    <div>

                        <h3 class="text-sm font-bold text-navy">
                            Répondre au voyageur
                        </h3>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Votre réponse sera ajoutée à la conversation.
                        </p>

                    </div>

                </div>


                <form
                    method="POST"
                    action="{{ route('admin.support.reply', $conversation->id) }}"
                >

                    @csrf


                    <textarea
                        name="message"
                        rows="4"
                        placeholder="Écrivez votre réponse au voyageur..."
                        required
                        class="tk-input h-auto py-2.5 min-h-[110px]"
                    >{{ old('message') }}</textarea>


                    <div
                        class="
                            mt-3 flex flex-col gap-3
                            sm:flex-row sm:items-center
                            sm:justify-between
                        "
                    >

                        <p
                            class="
                                flex items-center gap-2
                                text-xs text-slate-500
                            "
                        >

                            <i class="fa-solid fa-circle-info text-brand"></i>

                            <span>
                                Le voyageur verra votre réponse dans son application.
                            </span>

                        </p>


                        <button
                            type="submit"
                            class="tk-btn-accent shrink-0"
                        >

                            <i class="fa-solid fa-paper-plane"></i>

                            <span>
                                Envoyer la réponse
                            </span>

                        </button>

                    </div>

                </form>

            </div>

        @else

            {{-- =====================================================
                 CONVERSATION FERMÉE
            ====================================================== --}}

            <div
                class="
                    border-t border-slate-200 p-6
                    flex flex-col gap-4
                    sm:flex-row sm:items-center
                    sm:justify-between
                "
            >

                <div class="flex items-center gap-3">

                    <span
                        class="
                            tk-kpi-icon
                            bg-slate-100 text-slate-500
                        "
                    >
                        <i class="fa-solid fa-lock"></i>
                    </span>

                    <div>

                        <h3 class="text-sm font-bold text-navy">
                            Conversation fermée
                        </h3>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Cette conversation est actuellement fermée.
                        </p>

                    </div>

                </div>


                <form
                    method="POST"
                    action="{{ route('admin.support.reopen', $conversation->id) }}"
                >

                    @csrf

                    <button
                        type="submit"
                        class="tk-btn-accent shrink-0"
                    >

                        <i class="fa-solid fa-rotate-left"></i>

                        Réouvrir la conversation

                    </button>

                </form>

            </div>

        @endif

    </div>


    {{-- =========================================================
         ACTION FERMER
    ========================================================== --}}

    @if($conversation->status === 'open')

        <div
            class="
                tk-card p-5
                flex flex-col gap-4
                sm:flex-row sm:items-center
                sm:justify-between
            "
        >

            <div class="flex items-center gap-3">

                <span
                    class="
                        tk-kpi-icon
                        bg-orange-50 text-brand
                    "
                >
                    <i class="fa-solid fa-lock"></i>
                </span>

                <div>

                    <strong class="text-sm text-navy">
                        Terminer cette conversation ?
                    </strong>

                    <p class="mt-0.5 text-xs text-slate-500">
                        Vous pourrez la rouvrir ultérieurement.
                    </p>

                </div>

            </div>


            <form
                method="POST"
                action="{{ route('admin.support.close', $conversation->id) }}"
            >

                @csrf

                <button
                    type="submit"
                    onclick="event.preventDefault(); tkConfirm('Voulez-vous vraiment fermer cette conversation ?', () => this.form.submit())"
                    class="tk-btn bg-red-600 text-white hover:bg-red-700 shrink-0"
                >

                    <i class="fa-solid fa-lock"></i>

                    Fermer la conversation

                </button>

            </form>

        </div>

    @endif

</div>

</x-layouts.admin>
