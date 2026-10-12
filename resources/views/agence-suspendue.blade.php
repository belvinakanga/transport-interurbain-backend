<x-guest-layout>

<div class="relative min-h-screen flex items-center justify-center bg-cover bg-center bg-no-repeat"
     style="background-image: url('{{ asset('tokende_bus_login.jpg') }}');">

    <div class="absolute inset-0" style="background-color: rgba(2, 6, 23, 0.82);"></div>

    <div class="relative z-10 w-full max-w-xl px-6">

        <div class="backdrop-blur-2xl border border-white/15 rounded-[35px] shadow-2xl p-10" style="background-color: rgba(8, 14, 32, 0.88);">

            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 rounded-2xl bg-white/90 flex items-center justify-center">
                    <img
                        src="{{ asset('transparent-logo.png') }}"
                        alt="Tokende"
                        class="w-14 h-14 object-contain"
                    >
                </div>
            </div>

            <h1 class="text-3xl font-bold text-white text-center">
                Accès suspendu
            </h1>

            <div class="w-20 h-1 bg-orange-500 rounded-full mx-auto mt-5"></div>

            <p class="mt-7 text-gray-100 leading-relaxed text-center">
                L’accès de votre agence
                @if (auth()->user()?->agence?->nom_agence)
                    <span class="font-semibold text-white">
                        « {{ auth()->user()->agence->nom_agence }} »
                    </span>
                @endif
                a été suspendu par l’administrateur.
            </p>

            <p class="mt-4 text-gray-200 leading-relaxed text-center">
                Vos trajets ne sont plus proposés aux voyageurs et vous ne
                pouvez plus accéder à votre espace. Vos données restent
                conservées : la réactivation rétablit tout en l’état.
            </p>

            @if (auth()->user()?->agence?->motif_desactivation)
                <div class="mt-7 rounded-2xl border border-white/20 px-5 py-4" style="background-color: rgba(0, 0, 0, 0.35);">
                    <div class="text-xs uppercase tracking-wider text-gray-300">
                        Motif indiqué
                    </div>
                    <div class="mt-1 text-white">
                        {{ auth()->user()->agence->motif_desactivation }}
                    </div>
                </div>
            @endif

            <div class="mt-8 rounded-2xl border border-white/15 px-5 py-5" style="background-color: rgba(0, 0, 0, 0.30);">
                <div class="text-white font-semibold mb-3">
                    Contacter l’administration Tokende
                </div>

                <div class="space-y-2 text-gray-100">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-phone text-orange-400"></i>
                        <span>+242 06 821 60 25</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-envelope text-orange-400"></i>
                        <span>appassistancetok@gmail.com</span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="mt-8">
                @csrf

                <button
                    type="submit"
                    class="w-full py-4 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 hover:scale-[1.02] duration-300 text-white text-lg font-bold shadow-xl flex items-center justify-center gap-3"
                >
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    Se déconnecter
                </button>
            </form>

            <div class="text-center mt-8 text-gray-300 text-sm">
                © {{ date('Y') }} TOKENDE
            </div>

        </div>

    </div>

</div>

</x-guest-layout>
