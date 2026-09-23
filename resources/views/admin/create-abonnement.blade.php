<x-layouts.admin
    :header="'Nouvel abonnement'"
>

    <div class="max-w-3xl mx-auto">

        {{-- =====================================================
             TITRE
        ====================================================== --}}

        <div class="mb-6">

            <h1
                class="text-3xl md:text-4xl font-bold"
                style="color:#0A2A66;"
            >
                💼 Nouvel abonnement
            </h1>

            <p class="text-gray-500 mt-2">
                Créez l’abonnement d’une agence TOKENDE.
            </p>

        </div>


        {{-- =====================================================
             ERREURS
        ====================================================== --}}

        @if($errors->any())

            <div
                class="rounded-xl p-4 mb-6"
                style="
                    background:#FEE2E2;
                    color:#B91C1C;
                "
            >

                <p class="font-bold mb-2">
                    ⚠️ Vérifiez les informations suivantes :
                </p>

                <ul class="list-disc ml-5">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- =====================================================
             FORMULAIRE
        ====================================================== --}}

        <div
            class="
                bg-white
                rounded-2xl
                shadow-sm
                border
                border-gray-100
                overflow-hidden
            "
        >

            {{-- EN-TÊTE --}}

            <div
                class="px-6 py-5 border-b"
                style="background:#F6F8FC;"
            >

                <h2
                    class="text-xl font-bold"
                    style="color:#0A2A66;"
                >
                    Informations de l’abonnement
                </h2>

                <p class="text-sm text-gray-500 mt-1">
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
                        class="
                            block
                            font-semibold
                            text-gray-700
                            mb-2
                        "
                    >
                        Agence
                    </label>

                    <select
                        id="agence_id"
                        name="agence_id"
                        required
                        class="
                            w-full
                            border
                            border-gray-300
                            rounded-xl
                            px-4
                            py-3
                            bg-white
                            focus:outline-none
                        "
                    >

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
                        class="
                            block
                            font-semibold
                            text-gray-700
                            mb-2
                        "
                    >
                        Type d’abonnement
                    </label>

                    <select
                        id="type"
                        name="type"
                        required
                        class="
                            w-full
                            border
                            border-gray-300
                            rounded-xl
                            px-4
                            py-3
                            bg-white
                        "
                    >

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
                        class="
                            block
                            font-semibold
                            text-gray-700
                            mb-2
                        "
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
                            class="
                                w-full
                                border
                                border-gray-300
                                rounded-xl
                                px-4
                                py-3
                                pr-20
                            "
                        >

                        <span
                            class="
                                absolute
                                right-4
                                top-1/2
                                -translate-y-1/2
                                font-semibold
                            "
                            style="color:#FF6B00;"
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
                        grid
                        grid-cols-1
                        md:grid-cols-2
                        gap-5
                    "
                >

                    {{-- DATE DÉBUT --}}

                    <div>

                        <label
                            for="date_debut"
                            class="
                                block
                                font-semibold
                                text-gray-700
                                mb-2
                            "
                        >
                            Date de début
                        </label>

                        <input
                            id="date_debut"
                            type="date"
                            name="date_debut"
                            value="{{ old('date_debut') }}"
                            required
                            class="
                                w-full
                                border
                                border-gray-300
                                rounded-xl
                                px-4
                                py-3
                            "
                        >

                    </div>


                    {{-- DATE FIN --}}

                    <div>

                        <label
                            for="date_fin"
                            class="
                                block
                                font-semibold
                                text-gray-700
                                mb-2
                            "
                        >
                            Date de fin
                        </label>

                        <input
                            id="date_fin"
                            type="date"
                            name="date_fin"
                            value="{{ old('date_fin') }}"
                            required
                            class="
                                w-full
                                border
                                border-gray-300
                                rounded-xl
                                px-4
                                py-3
                            "
                        >

                    </div>

                </div>


                {{-- =================================================
                     INFORMATION
                ================================================== --}}

                <div
                    class="rounded-xl p-4"
                    style="
                        background:#EEF4FF;
                        border:1px solid #DCE8FF;
                    "
                >

                    <p
                        class="font-bold"
                        style="color:#0A2A66;"
                    >
                        ℹ️ Fonctionnement
                    </p>

                    <p
                        class="
                            text-sm
                            text-gray-600
                            mt-2
                        "
                    >

                        Le nouvel abonnement sera automatiquement
                        enregistré avec le statut
                        <strong style="color:#16A34A;">
                            Actif
                        </strong>.

                    </p>

                </div>


                {{-- =================================================
                     BOUTONS
                ================================================== --}}

                <div
                    class="
                        flex
                        flex-col
                        sm:flex-row
                        justify-end
                        gap-3
                        pt-2
                    "
                >

                    <a
                        href="{{ route('admin.abonnements') }}"
                        class="
                            px-5
                            py-3
                            rounded-xl
                            bg-gray-200
                            hover:bg-gray-300
                            text-gray-700
                            font-semibold
                            text-center
                        "
                    >
                        Annuler
                    </a>


                    <button
                        type="submit"
                        class="
                            px-6
                            py-3
                            rounded-xl
                            text-white
                            font-bold
                            shadow-md
                        "
                        style="background:#FF6B00;"
                    >

                        💾 Enregistrer l’abonnement

                    </button>

                </div>

            </form>

        </div>

    </div>

</x-layouts.admin>