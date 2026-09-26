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
                            value="{{ old('nom_agence', $agence->nom_agence) }}"
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
                            value="{{ old('ville', $agence->ville) }}"
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
                            value="{{ old('adresse', $agence->adresse) }}"
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
                            value="{{ old('telephone', $agence->telephone) }}"
                            required
                            class="w-full border rounded p-3">

                    </div>

                    {{-- MODÈLE ÉCONOMIQUE --}}

                    <div class="mb-6">

                        <label class="block font-semibold mb-3">
                            Modèle économique
                        </label>

                        <div class="space-y-3">

                            {{-- COMMISSION --}}

                            <label class="flex items-center gap-3 border rounded-lg p-4 cursor-pointer hover:bg-gray-50">

                                <input
                                    type="radio"
                                    name="modele_economique"
                                    value="commission"
                                    {{ old('modele_economique', $agence->modele_economique) === 'commission' ? 'checked' : '' }}
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

                            {{-- ABONNEMENT --}}

                            <label class="flex items-center gap-3 border rounded-lg p-4 cursor-pointer hover:bg-gray-50">

                                <input
                                    type="radio"
                                    name="modele_economique"
                                    value="abonnement"
                                    {{ old('modele_economique', $agence->modele_economique) === 'abonnement' ? 'checked' : '' }}
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