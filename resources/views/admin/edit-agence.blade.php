<x-layouts.admin>

    <div class="py-12">

        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-8 rounded shadow">

                <h1 class="text-3xl font-bold mb-6">
                    ✏️ Modifier une agence
                </h1>

                <form method="POST" action="/admin/agences/{{ $agence->id }}">

                    @csrf

                    @method('PUT')

                    <div class="mb-4">

                        <label class="block font-semibold mb-2">
                            Nom de l'agence
                        </label>

                        <input
                            type="text"
                            name="nom_agence"
                            value="{{ $agence->nom_agence }}"
                            required
                            class="w-full border rounded p-3">

                    </div>

                    <div class="mb-4">

                        <label class="block font-semibold mb-2">
                            Ville
                        </label>

                        <input
                            type="text"
                            name="ville"
                            value="{{ $agence->ville }}"
                            required
                            class="w-full border rounded p-3">

                    </div>

                    <div class="mb-4">

                        <label class="block font-semibold mb-2">
                            Adresse
                        </label>

                        <input
                            type="text"
                            name="adresse"
                            value="{{ $agence->adresse }}"
                            required
                            class="w-full border rounded p-3">

                    </div>

                    <div class="mb-4">

                        <label class="block font-semibold mb-2">
                            Téléphone
                        </label>

                        <input
                            type="text"
                            name="telephone"
                            value="{{ $agence->telephone }}"
                            required
                            class="w-full border rounded p-3">

                    </div>

                    <div class="flex gap-4 mt-6">

                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded">

                            💾 Mettre à jour

                        </button>

                        <a
                            href="/admin/agences"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded">

                            Retour

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>
</x-layouts.admin>