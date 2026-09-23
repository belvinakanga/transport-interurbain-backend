{{-- resources/views/auth/login.blade.php --}}

<x-guest-layout>

<div class="relative min-h-screen flex items-center justify-center bg-cover bg-center bg-no-repeat"
     style="background-image: url('{{ asset('tokende_bus_login.jpg') }}');">

    <!-- Fond sombre -->
    <div class="absolute inset-0 bg-black/60"></div>

    <div class="relative z-10 w-full max-w-7xl px-6">

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
                    Voyagez en toute confiance,<br>
                    arrivez à destination.
                </p>

                <div class="mt-16 space-y-6">

                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-orange-500 flex items-center justify-center text-3xl shadow-lg">
                            🎫
                        </div>

                        <div>
                            <h3 class="text-xl font-bold">
                                Réservation facile
                            </h3>

                            <p class="text-gray-200">
                                Réservez votre billet en quelques clics.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center text-3xl shadow-lg">
                            💳
                        </div>

                        <div>
                            <h3 class="text-xl font-bold">
                                Paiement sécurisé
                            </h3>

                            <p class="text-gray-200">
                                Paiement rapide et sécurisé.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-indigo-600 flex items-center justify-center text-3xl shadow-lg">
                            🚌
                        </div>

                        <div>
                            <h3 class="text-xl font-bold">
                                Confort & Sécurité
                            </h3>

                            <p class="text-gray-200">
                                Voyagez sereinement avec TOKENDE.
                            </p>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Carte de connexion -->
            <div class="backdrop-blur-xl bg-white/15 border border-white/20 rounded-[35px] shadow-2xl p-10">

                <div class="text-center mb-10">

                    <h2 class="text-5xl font-bold text-white">
                        Connexion
                    </h2>

                    <p class="text-gray-200 mt-3">
                        Bienvenue dans votre espace Utilisateur
                    </p>

                    <div class="w-20 h-1 bg-orange-500 rounded-full mx-auto mt-6"></div>

                </div>

                <x-auth-session-status
                    class="mb-4 text-white"
                    :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">

                    @csrf

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
                            :value="old('email')"
                            required
                            autofocus />

                        <x-input-error
                            :messages="$errors->get('email')"
                            class="mt-2" />

                    </div>

                    <!-- Mot de passe -->
                    <div>

                        <x-input-label
                            for="password"
                            value="Mot de passe"
                            class="text-white" />

                        <div class="relative mt-2">

                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                class="w-full rounded-xl bg-white/90 border-0 py-3 px-4 pr-14 focus:ring-2 focus:ring-orange-500">

                            <button
                                type="button"
                                id="togglePassword"
                                class="absolute inset-y-0 right-0 px-4 text-xl text-gray-600 hover:text-orange-500">

                                👁️

                            </button>

                        </div>

                        <x-input-error
                            :messages="$errors->get('password')"
                            class="mt-2" />

                    </div>

                    <!-- Options -->
                    <div class="flex items-center justify-between text-white">

                        <label class="flex items-center gap-2">

                            <input
                                type="checkbox"
                                name="remember"
                                class="rounded">

                            Se souvenir de moi

                        </label>

                        @if (Route::has('password.request'))

                            <a href="{{ route('password.request') }}"
                               class="text-orange-300 hover:text-orange-400">

                                Mot de passe oublié ?

                            </a>

                        @endif

                    </div>

                    <button
                        type="submit"
                        class="w-full py-4 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 hover:scale-105 duration-300 text-white text-xl font-bold shadow-xl">

                        Se connecter →

                    </button>

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

    const password = document.getElementById('password');
    const toggle = document.getElementById('togglePassword');

    toggle.addEventListener('click', function () {

        if (password.type === 'password') {
            password.type = 'text';
            toggle.innerHTML = '🙈';
        } else {
            password.type = 'password';
            toggle.innerHTML = '👁️';
        }

    });

});

</script>

</x-guest-layout>