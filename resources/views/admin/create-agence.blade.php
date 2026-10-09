<x-layouts.admin :header="'Ajouter une agence'">

<div class="tk-page">

    {{-- En-tête --}}

    <div class="tk-page-head">

        <h1 class="tk-page-title">

            <span
                class="
                    flex h-11 w-11 shrink-0
                    items-center justify-center
                    rounded-lg bg-orange-50
                    text-lg text-brand
                "
            >
                <i class="fa-solid fa-building"></i>
            </span>

            Ajouter une agence

        </h1>

    </div>


    {{-- Formulaire --}}

    <div class="tk-card p-6">


        <form method="POST" action="/admin/agences/store">

            @csrf

            <div class="mb-4">

                <label class="tk-form-label">
                    Nom de l'agence
                </label>

                <input
                    type="text"
                    name="nom_agence"
                    value="{{ old('nom_agence') }}"
                    required
                    class="tk-input">

            </div>

            <div class="mb-4">

                <label class="tk-form-label">
                    Ville
                </label>

                <input
                    type="text"
                    name="ville"
                    value="{{ old('ville') }}"
                    required
                    class="tk-input">

            </div>

            <div class="mb-4">

                <label class="tk-form-label">
                    Adresse
                </label>

                <input
                    type="text"
                    name="adresse"
                    value="{{ old('adresse') }}"
                    required
                    class="tk-input">

            </div>

            <div class="mb-6">

                <label class="tk-form-label">
                    Téléphone
                </label>

                <input
                    type="text"
                    name="telephone"
                    value="{{ old('telephone') }}"
                    required
                    class="tk-input">

            </div>

            {{-- MODÈLE ÉCONOMIQUE --}}

            <div class="mb-6">

                <label class="tk-form-label">
                    Modèle économique
                </label>

                <div class="space-y-3">

                    <label class="flex items-center gap-3 rounded-lg border border-slate-200 p-4 cursor-pointer hover:bg-slate-50 transition-colors">

                        <input
                            type="radio"
                            name="modele_economique"
                            value="commission"
                            {{ old('modele_economique', 'abonnement') === 'commission' ? 'checked' : '' }}
                            required>

                        <div>
                            <div class="font-semibold text-navy">
                                Commission par billet
                            </div>

                            <div class="text-sm text-slate-500">
                                100 FCFA sont ajoutés à chaque billet :
                                80 FCFA pour Tokende et 20 FCFA pour l'agence.
                            </div>
                        </div>

                    </label>

                    <label class="flex items-center gap-3 rounded-lg border border-slate-200 p-4 cursor-pointer hover:bg-slate-50 transition-colors">

                        <input
                            type="radio"
                            name="modele_economique"
                            value="abonnement"
                            {{ old('modele_economique', 'abonnement') === 'abonnement' ? 'checked' : '' }}
                            required>

                        <div>
                            <div class="font-semibold text-navy">
                                Abonnement mensuel
                            </div>

                            <div class="text-sm text-slate-500">
                                Aucun montant supplémentaire n'est ajouté
                                aux billets. L'agence paie un abonnement mensuel à Tokende.
                            </div>
                        </div>

                    </label>

                </div>

            </div>

            <div class="flex gap-3">

                <button
                    type="submit"
                    class="tk-btn-accent"
                >

                    <i class="fa-solid fa-check"></i>

                    Enregistrer

                </button>

                <a
                    href="/admin/agences"
                    class="tk-btn-ghost"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Retour

                </a>

            </div>

        </form>

    </div>

</div>

</x-layouts.admin>
