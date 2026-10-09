<x-layouts.admin
    :header="'Nouvel abonnement'"
>

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
                <i class="fa-solid fa-clipboard-list"></i>
            </span>

            Nouvel abonnement

        </h1>

        <p class="mt-2 text-sm text-slate-500">
            Créez l’abonnement d’une agence TOKENDE.
        </p>

    </div>


    {{-- Formulaire --}}

    <div class="tk-card overflow-hidden">

        {{-- EN-TÊTE --}}

        <div class="border-b border-slate-200 px-6 py-5">

            <h2 class="flex items-center gap-2.5 text-base font-bold text-navy">

                <i class="fa-solid fa-file-lines text-brand"></i>

                Informations de l’abonnement

            </h2>

            <p class="mt-1 text-xs text-slate-500">
                Tous les champs sont obligatoires.
            </p>

        </div>


        <form
            method="POST"
            action="{{ route('admin.abonnements.store') }}"
            class="p-6 space-y-6"
        >

            @csrf


            {{-- =================================================
                 AGENCE
            ================================================== --}}

            <div>

                <label
                    for="agence_id"
                    class="tk-form-label"
                >
                    Agence
                </label>

                <select
                    id="agence_id"
                    name="agence_id"
                    required
                    class="tk-input">

                    <option value="">
                        -- Sélectionner une agence --
                    </option>

                    @foreach($agences as $agence)

                        <option
                            value="{{ $agence->id }}"
                            {{ old('agence_id') == $agence->id
                                ? 'selected'
                                : ''
                            }}
                        >

                            {{ $agence->nom_agence }}

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- =================================================
                 TYPE
            ================================================== --}}

            <div>

                <label
                    for="type"
                    class="tk-form-label"
                >
                    Type d’abonnement
                </label>

                <select
                    id="type"
                    name="type"
                    required
                    class="tk-input">

                    <option value="">
                        -- Sélectionner une formule --
                    </option>

                    <option
                        value="Standard"
                        {{ old('type') === 'Standard'
                            ? 'selected'
                            : ''
                        }}
                    >
                        Standard
                    </option>

                    <option
                        value="Premium"
                        {{ old('type') === 'Premium'
                            ? 'selected'
                            : ''
                        }}
                    >
                        Premium
                    </option>

                    <option
                        value="Entreprise"
                        {{ old('type') === 'Entreprise'
                            ? 'selected'
                            : ''
                        }}
                    >
                        Entreprise
                    </option>

                </select>

            </div>


            {{-- =================================================
                 MONTANT
            ================================================== --}}

            <div>

                <label
                    for="montant"
                    class="tk-form-label"
                >
                    Montant de l’abonnement
                </label>

                <div class="relative">

                    <input
                        id="montant"
                        type="number"
                        name="montant"
                        value="{{ old('montant') }}"
                        min="0"
                        step="0.01"
                        required
                        placeholder="Exemple : 50000"
                        class="tk-input pr-20"
                    >

                    <span
                        class="
                            absolute right-4
                            top-1/2 -translate-y-1/2
                            font-semibold text-brand
                        "
                    >
                        FCFA
                    </span>

                </div>

            </div>


            {{-- =================================================
                 DATES
            ================================================== --}}

            <div
                class="
                    grid grid-cols-1
                    md:grid-cols-2
                    gap-5
                "
            >

                {{-- DATE DÉBUT --}}

                <div>

                    <label
                        for="date_debut"
                        class="tk-form-label"
                    >
                        Date de début
                    </label>

                    <input
                        id="date_debut"
                        type="date"
                        name="date_debut"
                        value="{{ old('date_debut') }}"
                        required
                        class="tk-input"
                    >

                </div>


                {{-- DATE FIN --}}

                <div>

                    <label
                        for="date_fin"
                        class="tk-form-label"
                    >
                        Date de fin
                    </label>

                    <input
                        id="date_fin"
                        type="date"
                        name="date_fin"
                        value="{{ old('date_fin') }}"
                        required
                        class="tk-input"
                    >

                </div>

            </div>


            {{-- =================================================
                 INFORMATION
            ================================================== --}}

            <div
                class="
                    rounded-xl p-4
                    border border-[#DCE8FF]
                    bg-[#EEF4FF]
                "
            >

                <p class="flex items-center gap-2 font-bold text-navy">

                    <i class="fa-solid fa-circle-info"></i>

                    Fonctionnement

                </p>

                <p
                    class="
                        mt-2 text-sm
                        text-slate-600
                    "
                >

                    Le nouvel abonnement sera automatiquement
                    enregistré avec le statut
                    <strong class="text-emerald-600">
                        Actif
                    </strong>.

                </p>

            </div>


            {{-- =================================================
                 BOUTONS
            ================================================== --}}

            <div
                class="
                    flex flex-col sm:flex-row
                    justify-end gap-3 pt-2
                "
            >

                <a
                    href="{{ route('admin.abonnements') }}"
                    class="tk-btn-ghost"
                >
                    Annuler
                </a>


                <button
                    type="submit"
                    class="tk-btn-accent"
                >

                    <i class="fa-solid fa-check"></i>

                    Enregistrer l’abonnement

                </button>

            </div>

        </form>

    </div>

</div>

</x-layouts.admin>
