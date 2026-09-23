<x-app-layout>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-xl rounded-2xl p-8">

                <h1 class="text-3xl font-bold text-blue-700">
                    👨‍💼 Tableau de bord Agent
                </h1>

                <p class="mt-3 text-gray-600">
                    Bienvenue,
                    <strong>{{ auth()->user()->name }}</strong>
                </p>

                <div class="mt-6 grid md:grid-cols-2 gap-6">

                    <div class="bg-blue-50 rounded-xl p-5">
                        <h2 class="font-bold text-blue-700">
                            🏢 Mon agence
                        </h2>

                        <p class="mt-2 text-lg">
                            {{ auth()->user()->agence->nom_agence ?? 'Aucune agence' }}
                        </p>
                    </div>

                    <div class="bg-orange-50 rounded-xl p-5">
                        <h2 class="font-bold text-orange-700">
                            🚍 Mes trajets
                        </h2>

                        <p class="mt-2">
                            Vous pourrez gérer ici les trajets de votre agence.
                        </p>
                    </div>

                </div>

            </div>

        </div>
    </div>

</x-app-layout>