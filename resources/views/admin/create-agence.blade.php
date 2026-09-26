<x-layouts.admin>

    <div class="py-12">

        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-8 rounded-lg shadow">

                <h1 class="text-3xl font-bold mb-6">
                    ➕ Ajouter une agence
                </h1>

                @if ($errors->any())
                    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">

                        <strong>Des erreurs ont été détectées :</strong>

                        <ul class="mt-2 list-disc list-inside">

                            @foreach ($errors->all() as $error)

                                <li>{{ $error }}</li>

                            @endforeach

                        </ul>

                    </div>
                @endif

                <form method="POST" action="/admin/agences/store">

                    @csrf

                    <div class="mb-4">

                        <label class="block font-semibold mb-2">
                            Nom de l'agence
                        </label>

                        <input
                            type="text"
                            name="nom_agence"
                            value="{{ old('nom_agence') }}"
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
                            value="{{ old('ville') }}"
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
                            value="{{ old('adresse') }}"
                            required
                            class="w-full border rounded p-3">

                    </div>

                    <div class="mb-6">

                        <label class="block font-semibold mb-2">
                            Téléphone
                        </label>

                        <input
                            type="text"
                            name="telephone"
                            value="{{ old('telephone') }}"
                            required
                            class="w-full border rounded p-3">

                    </div>

                    {{-- MODÈLE ÉCONOMIQUE --}}

                    <div class="mb-6">

                        <label class="block font-semibold mb-3">
                            Modèle économique
                        </label>

                        <div class="space-y-3">

                            <label class="flex items-center gap-3 border rounded-lg p-4 cursor-pointer hover:bg-gray-50">

                                <input
                                    type="radio"
                                    name="modele_economique"
                                    value="commission"
                                    {{ old('modele_economique', 'abonnement') === 'commission' ? 'checked' : '' }}
                                    required>

                                <div>
                                    <div class="font-semibold">
                                        Commission par billet
                                    </div>

                                    <div class="text-sm text-gray-600">
                                        100 FCFA sont ajoutés à chaque billet :
                                        80 FCFA pour Tokende et 20 FCFA pour l'agence.
                                    </div>
                                </div>

                            </label>

                            <label class="flex items-center gap-3 border rounded-lg p-4 cursor-pointer hover:bg-gray-50">

                                <input
                                    type="radio"
                                    name="modele_economique"
                                    value="abonnement"
                                    {{ old('modele_economique', 'abonnement') === 'abonnement' ? 'checked' : '' }}
                                    required>

                                <div>
                                    <div class="font-semibold">
                                        Abonnement mensuel
                                    </div>

                                    <div class="text-sm text-gray-600">
                                        Aucun montant supplémentaire n'est ajouté
                                        aux billets. L'agence paie un abonnement mensuel à Tokende.
                                    </div>
                                </div>

                            </label>

                        </div>

                    </div>

                    <div class="flex gap-4">

                        <button
                            type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded">

                            💾 Enregistrer

                        </button>

                        <a
                            href="/admin/agences"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded">

                            ↩ Retour

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-layouts.admin>