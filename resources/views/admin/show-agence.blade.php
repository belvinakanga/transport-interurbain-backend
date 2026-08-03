<x-layouts.admin>

    <div class="py-12">

        <div class="max-w-4xl mx-auto">

            <div class="bg-white rounded-2xl shadow-lg p-8">

                <div class="flex justify-between items-center mb-8">

                    <h1 class="text-3xl font-bold text-slate-800">
                        👁️ Détails de l'agence
                    </h1>

                    <a href="/admin/agences"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-3 rounded-xl">

                        ← Retour

                    </a>

                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <p class="text-gray-500 text-sm">ID</p>
                        <p class="text-xl font-bold">{{ $agence->id }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-sm">Nom de l'agence</p>
                        <p class="text-xl font-bold">{{ $agence->nom_agence }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-sm">Ville</p>
                        <p class="text-xl">{{ $agence->ville }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500 text-sm">Téléphone</p>
                        <p class="text-xl">{{ $agence->telephone }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-gray-500 text-sm">Adresse</p>
                        <p class="text-xl">{{ $agence->adresse }}</p>
                    </div>

                </div>

                <div class="flex gap-4 mt-10">

                    <a href="/admin/agences/{{ $agence->id }}/edit"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl">

                        ✏️ Modifier

                    </a>

                    <form action="/admin/agences/{{ $agence->id }}" method="POST">

                        @csrf
                        @method('DELETE')

                        <button
                            onclick="return confirm('Supprimer cette agence ?')"
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl">

                            🗑️ Supprimer

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</x-layouts.admin>