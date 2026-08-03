<x-layouts.admin>

<div class="py-12">

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white rounded-xl shadow p-8">

            <h1 class="text-3xl font-bold mb-8">
                👁️ Détails du trajet
            </h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <p class="text-gray-500">Agence</p>
                    <p class="font-semibold text-lg">
                        {{ $trajet->agence->nom_agence ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500">Départ</p>
                    <p class="font-semibold text-lg">
                        {{ $trajet->depart }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500">Arrivée</p>
                    <p class="font-semibold text-lg">
                        {{ $trajet->arrivee }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500">Date de départ</p>
                    <p class="font-semibold text-lg">
                        {{ $trajet->date_depart }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500">Heure de départ</p>
                    <p class="font-semibold text-lg">
                        {{ $trajet->heure_depart }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500">Prix</p>
                    <p class="font-semibold text-lg">
                        {{ number_format($trajet->prix,0,',',' ') }} FCFA
                    </p>
                </div>

                <div>
                    <p class="text-gray-500">Places disponibles</p>
                    <p class="font-semibold text-lg">
                        {{ $trajet->places_disponibles }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500">Places totales</p>
                    <p class="font-semibold text-lg">
                        {{ $trajet->places_totales }}
                    </p>
                </div>

            </div>

            <div class="mt-8 flex gap-4">

                <a
                    href="/admin/trajets/{{ $trajet->id }}/edit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl">

                    ✏️ Modifier

                </a>

                <a
                    href="/admin/trajets"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-xl">

                    ← Retour

                </a>

            </div>

        </div>

    </div>

</div>

</x-layouts.admin>