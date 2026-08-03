<x-guest-layout>

<div class="relative min-h-screen flex items-center justify-center bg-cover bg-center bg-no-repeat"
     style="background-image: url('{{ asset('login-bg.jpg') }}');">

    <!-- Voile sombre -->
    <div class="absolute inset-0 bg-black/60"></div>

    <!-- Contenu -->
    <div class="relative z-10 w-full max-w-6xl px-6">

        <div class="grid lg:grid-cols-2 gap-10 items-center">

            <!-- Partie gauche -->
            <div class="hidden lg:block text-white">

                <h1 class="text-7xl font-extrabold drop-shadow-lg">
                    Inter<span class="text-orange-500">GO</span>
                </h1>

                <h2 class="text-2xl tracking-[12px] mt-2 font-light">
                    CONGO
                </h2>

                <div class="w-32 h-1 bg-orange-500 mt-6 rounded-full"></div>

                <p class="mt-8 text-2xl leading-relaxed font-light">
                    Vous avez oublié votre mot de passe ?
                    <br>
                    Aucun souci.
                </p>

                <div class="mt-16 space-y-6">

                    <div class="flex items-center gap-5">

                        <div class="w-16 h-16 rounded-2xl bg-orange-500 flex items-center justify-center text-3xl shadow-lg">
                            🔒
                        </div>

                        <div>

                            <h3 class="text-xl font-bold">
                                Sécurité
                            </h3>

                            <p class="text-gray-200">
                                Votre compte reste protégé.
                            </p>

                        </div>

                    </div>

                    <div class="flex items-center gap-5">

                        <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center text-3xl shadow-lg">
                            📧
                        </div>

                        <div>

                            <h3 class="text-xl font-bold">
                                Réinitialisation
                            </h3>

                            <p class="text-gray-200">
                                Recevez un lien par e-mail.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Carte -->
            <div class="backdrop-blur-xl bg-white/15 border border-white/20 rounded-[35px] shadow-2xl p-10">

                <div class="text-center mb-8">

                    <h2 class="text-5xl font-bold text-white">
                        Mot de passe oublié
                    </h2>

                    <p class="text-gray-200 mt-4">

                        Entrez votre adresse e-mail.
                        <br>
                        Nous vous enverrons un lien pour réinitialiser votre mot de passe.

                    </p>

                    <div class="w-24 h-1 bg-orange-500 rounded-full mx-auto mt-6"></div>

                </div>

                <x-auth-session-status
                    class="mb-4 text-green-300"
                    :status="session('status')" />

                <form method="POST"
                      action="{{ route('password.email') }}"
                      class="space-y-6">

                    @csrf

                    <div>

                        <x-input-label
                            for="email"
                            value="Adresse e-mail"
                            class="text-white"/>

                        <x-text-input
                            id="email"
                            class="block mt-2 w-full rounded-xl bg-white/90 border-0"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus/>

                        <x-input-error
                            :messages="$errors->get('email')"
                            class="mt-2"/>

                    </div>

                    <button
                        type="submit"
                        class="w-full py-4 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 hover:scale-105 duration-300 text-white text-xl font-bold shadow-xl">

                        📩 Envoyer le lien de réinitialisation

                    </button>

                    <a href="{{ route('login') }}"
                       class="block text-center text-orange-300 hover:text-orange-400 font-semibold">

                        ← Retour à la connexion

                    </a>

                </form>

                <div class="text-center mt-10 text-gray-200">

                    © {{ date('Y') }} InterGO Congo

                </div>

            </div>

        </div>

    </div>

</div>

</x-guest-layout>