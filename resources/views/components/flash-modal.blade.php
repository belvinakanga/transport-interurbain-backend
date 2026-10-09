@php

    $flashMessages = [];
    $flashType     = 'success';
    $flashTitle    = 'Succès';

    if ($errors->any()) {

        $flashType     = 'error';
        $flashTitle    = 'Formulaire non valide';
        $flashMessages = $errors->all();

    } elseif (session('success')) {

        $flashMessages = [session('success')];

    } elseif (session('error')) {

        $flashType     = 'error';
        $flashTitle    = 'Erreur';
        $flashMessages = [session('error')];

    } elseif (session('status')) {

        $status = session('status');

        $known = [
            'profile-updated'          => 'Vos informations ont été mises à jour avec succès.',
            'password-updated'         => 'Mot de passe modifié avec succès.',
            'verification-link-sent'   => 'Un nouveau lien de vérification a été envoyé.',
        ];

        $flashMessages = array_filter([
            $known[$status] ?? (is_string($status) ? $status : ''),
        ]);

    }

@endphp

@if(count($flashMessages))

    <div
        x-data="{ open: true }"
        x-show="open"
        x-cloak
        @keydown.escape.window="open = false"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
        aria-label="{{ $flashTitle }}"
    >

        <div
            class="absolute inset-0 bg-slate-900/50"
            x-transition.opacity
            @click="open = false"
        ></div>

        <div
            x-transition
            class="
                relative w-full max-w-sm
                rounded-xl border border-slate-200
                bg-white p-5 shadow-xl
            "
        >

            <div class="flex items-start gap-3.5">

                <span
                    class="
                        flex h-10 w-10 shrink-0
                        items-center justify-center rounded-full
                        {{ $flashType === 'success'
                            ? 'bg-emerald-50 text-emerald-600'
                            : 'bg-red-50 text-red-600' }}
                    "
                >
                    <i
                        class="fa-solid text-lg {{ $flashType === 'success'
                            ? 'fa-circle-check'
                            : 'fa-triangle-exclamation' }}"
                    ></i>
                </span>


                <div class="min-w-0 flex-1">

                    <div class="text-sm font-bold text-navy">
                        {{ $flashTitle }}
                    </div>

                    <div class="mt-1 max-h-48 space-y-1 overflow-y-auto pr-1">

                        @foreach($flashMessages as $message)

                            <p class="text-sm leading-relaxed text-slate-600 break-words">
                                {{ $message }}
                            </p>

                        @endforeach

                    </div>

                </div>

            </div>


            <div class="mt-4 flex justify-end">

                <button
                    type="button"
                    x-ref="ok"
                    x-init="$refs.ok.focus()"
                    @click="open = false"
                    class="tk-btn-navy h-9 px-5 text-sm"
                >
                    OK
                </button>

            </div>

        </div>

    </div>

@endif
