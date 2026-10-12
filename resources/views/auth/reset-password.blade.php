<x-guest-layout>

<div class="relative min-h-screen flex items-center justify-center bg-cover bg-center bg-no-repeat"
     style="background-image: url('{{ asset('tokende_bus_login.jpg') }}');">

    <!-- Voile sombre -->
    <div class="absolute inset-0 bg-black/60"></div>

    <!-- Contenu -->
    <div class="relative z-10 w-full max-w-6xl px-6">

        <div class="grid lg:grid-cols-2 gap-10 items-center">

            <!-- Partie gauche -->
            <div class="hidden lg:block text-white">

                <h1 class="text-7xl font-extrabold drop-shadow-lg">
                    TOK<span class="text-orange-500">ENDE</span>
                </h1>

                <h2 class="text-2xl tracking-[12px] mt-2 font-light">
                    CONGO
                </h2>

                <div class="w-32 h-1 bg-orange-500 mt-6 rounded-full"></div>

                <p class="mt-8 text-2xl leading-relaxed font-light">
                    Dernière étape.
                    <br>
                    Choisissez votre nouveau mot de passe.
                </p>

                <div class="mt-16 space-y-6">

                    <div class="flex items-center gap-5">

                        <div class="w-16 h-16 rounded-2xl bg-orange-500 flex items-center justify-center text-3xl shadow-lg">
                            <i class="fa-solid fa-key"></i>
                        </div>

                        <div>

                            <h3 class="text-xl font-bold">
                                Nouveau mot de passe
                            </h3>

                            <p class="text-gray-200">
                                8 caractères minimum.
                            </p>

                        </div>

                    </div>

                    <div class="flex items-center gap-5">

                        <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center text-3xl shadow-lg">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>

                        <div>

                            <h3 class="text-xl font-bold">
                                Lien à usage unique
                            </h3>

                            <p class="text-gray-200">
                                Il expire après 60 minutes.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Carte -->
            <div class="backdrop-blur-xl bg-white/15 border border-white/20 rounded-[35px] shadow-2xl p-10">

                <div class="text-center mb-8">

                    <h2 class="text-5xl font-bold text-white">
                        Réinitialisation
                    </h2>

                    <p class="text-gray-200 mt-4">
                        Définissez un nouveau mot de passe pour votre compte.
                    </p>

                    <div class="w-24 h-1 bg-orange-500 rounded-full mx-auto mt-6"></div>

                </div>

                <!-- Erreurs globales -->
                @if ($errors->any())

                    <div class="mb-6 rounded-xl border border-red-400/40 bg-red-500/20 px-5 py-4 text-white">

                        <div class="flex items-center gap-3 font-semibold">

                            <i class="fa-solid fa-circle-exclamation"></i>

                            Impossible de réinitialiser le mot de passe
                        </div>

                        <ul class="mt-2 list-disc space-y-1 pl-9 text-sm text-red-100">

                            @foreach ($errors->all() as $message)
                                <li>{{ $message }}</li>
                            @endforeach

                        </ul>

                    </div>

                @endif

                <form method="POST"
                      action="{{ route('password.store') }}"
                      class="space-y-6">

                    @csrf

                    <!-- Jeton de réinitialisation -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email -->
                    <div>

                        <x-input-label
                            for="email"
                            value="Adresse e-mail"
                            class="text-white" />

                        <x-text-input
                            id="email"
                            class="block mt-2 w-full rounded-xl bg-white/90 border-0"
                            type="email"
                            name="email"
                            :value="old('email', $request->email)"
                            required
                            autofocus
                            autocomplete="username" />

                    </div>

                    <!-- Nouveau mot de passe -->
                    <div>

                        <x-input-label
                            for="password"
                            value="Nouveau mot de passe"
                            class="text-white" />

                        <div class="relative mt-2">

                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="new-password"
                                class="w-full rounded-xl bg-white/90 border-0 py-3 px-4 pr-14 focus:ring-2 focus:ring-orange-500">

                            <button
                                type="button"
                                data-toggle-password="password"
                                aria-label="Afficher le mot de passe"
                                class="absolute inset-y-0 right-0 px-4 text-xl text-gray-600 hover:text-orange-500">

                                <i class="fa-solid fa-eye"></i>

                            </button>

                        </div>

                    </div>

                    <!-- Confirmation -->
                    <div>

                        <x-input-label
                            for="password_confirmation"
                            value="Confirmer le mot de passe"
                            class="text-white" />

                        <div class="relative mt-2">

                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                autocomplete="new-password"
                                class="w-full rounded-xl bg-white/90 border-0 py-3 px-4 pr-14 focus:ring-2 focus:ring-orange-500">

                            <button
                                type="button"
                                data-toggle-password="password_confirmation"
                                aria-label="Afficher le mot de passe"
                                class="absolute inset-y-0 right-0 px-4 text-xl text-gray-600 hover:text-orange-500">

                                <i class="fa-solid fa-eye"></i>

                            </button>

                        </div>

                    </div>

                    <button
                        type="submit"
                        class="w-full py-4 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 hover:scale-105 duration-300 text-white text-xl font-bold shadow-xl flex items-center justify-center gap-3">

                        Réinitialiser le mot de passe

                        <i class="fa-solid fa-rotate-right"></i>

                    </button>

                    <a href="{{ route('login') }}"
                       class="flex items-center justify-center gap-2 text-center text-orange-300 hover:text-orange-400 font-semibold">

                        <i class="fa-solid fa-arrow-left"></i>

                        Retour à la connexion

                    </a>

                </form>

                <div class="text-center mt-10 text-gray-200">

                    © {{ date('Y') }} TOKENDE

                </div>

            </div>

        </div>

    </div>

</div>

<script>

document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('[data-toggle-password]').forEach(function (toggle) {

        const champ = document.getElementById(toggle.dataset.togglePassword);

        toggle.addEventListener('click', function () {

            if (champ.type === 'password') {
                champ.type = 'text';
                toggle.querySelector('i').className = 'fa-solid fa-eye-slash';
            } else {
                champ.type = 'password';
                toggle.querySelector('i').className = 'fa-solid fa-eye';
            }

        });

    });

});

</script>

</x-guest-layout>
