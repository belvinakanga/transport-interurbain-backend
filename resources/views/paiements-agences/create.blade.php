<x-layouts.admin
    :header="'Nouveau paiement agence'"
>

    <div class="max-w-3xl mx-auto space-y-6">

        {{-- =====================================================
             TITRE
        ====================================================== --}}

        <div>

            <h1
                class="text-3xl md:text-4xl font-bold"
                style="color:#0A2A66;"
            >
                💰 Nouveau paiement
            </h1>

            <p class="text-gray-500 mt-2">
                Enregistrez le paiement dû par une agence à TOKENDE.
            </p>

        </div>


        {{-- =====================================================
             ERREURS
        ====================================================== --}}

        @if($errors->any())

            <div
                class="rounded-xl p-4"
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
                class="
                    px-6
                    py-5
                    border-b
                "
                style="background:#F6F8FC;"
            >

                <h2
                    class="text-xl font-bold"
                    style="color:#0A2A66;"
                >
                    Informations du paiement
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Sélectionnez l’abonnement concerné.
                </p>

            </div>


            <form
                method="POST"
                action="{{ route('paiements-agences.store') }}"
                class="p-6 space-y-6"
            >

                @csrf


                {{-- =================================================
                     ABONNEMENT
                ================================================== --}}

                <div>

                    <label
                        for="abonnement_id"
                        class="
                            block
                            font-semibold
                            text-gray-700
                            mb-2
                        "
                    >
                        Abonnement concerné
                    </label>


                    @if($abonnements->count())

                        <select
                            id="abonnement_id"
                            name="abonnement_id"
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
                                -- Choisir un abonnement --
                            </option>

                            @foreach($abonnements as $abonnement)

                                <option
                                    value="{{ $abonnement->id }}"
                                    {{ old('abonnement_id') == $abonnement->id
                                        ? 'selected'
                                        : ''
                                    }}
                                >

                                    {{ $abonnement->agence->nom_agence ?? 'Agence inconnue' }}

                                    —
                                    {{ $abonnement->type }}

                                    —
                                    {{ number_format(
                                        $abonnement->montant,
                                        0,
                                        ',',
                                        ' '
                                    ) }}
                                    FCFA

                                    —

                                    échéance :
                                    {{ $abonnement->date_fin
                                        ? $abonnement->date_fin->format('d/m/Y')
                                        : '-'
                                    }}

                                </option>

                            @endforeach

                        </select>

                    @else

                        <div
                            class="
                                rounded-xl
                                p-4
                            "
                            style="
                                background:#FFF3E8;
                                color:#9A3412;
                            "
                        >

                            <p class="font-semibold">
                                ⚠️ Aucun abonnement actif disponible.
                            </p>

                            <p class="text-sm mt-1">
                                Créez d’abord un abonnement actif dans
                                la page « Abonnements ».
                            </p>

                        </div>

                    @endif

                </div>


                {{-- =================================================
                     INFORMATIONS AUTOMATIQUES
                ================================================== --}}

                <div
                    class="rounded-xl p-5"
                    style="
                        background:#EEF4FF;
                        border:1px solid #D8E6FF;
                    "
                >

                    <div class="flex items-start gap-3">

                        <div class="text-2xl">
                            ℹ️
                        </div>

                        <div>

                            <h3
                                class="font-bold"
                                style="color:#0A2A66;"
                            >
                                Fonctionnement
                            </h3>

                            <p class="text-sm text-gray-600 mt-2">

                                Le montant du paiement et la date
                                d’échéance seront automatiquement
                                récupérés depuis l’abonnement sélectionné.

                            </p>

                            <p class="text-sm text-gray-600 mt-2">

                                Le paiement sera enregistré comme
                                <strong style="color:#FF6B00;">
                                    « En attente »
                                </strong>
                                jusqu’à ce qu’il soit effectivement
                                réglé à TOKENDE.

                            </p>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     NOTE
                ================================================== --}}

                <div>

                    <label
                        for="note"
                        class="
                            block
                            font-semibold
                            text-gray-700
                            mb-2
                        "
                    >
                        Note
                    </label>

                    <textarea
                        id="note"
                        name="note"
                        rows="4"
                        maxlength="2000"
                        placeholder="Exemple : Paiement de l'abonnement du mois d'août 2026."
                        class="
                            w-full
                            border
                            border-gray-300
                            rounded-xl
                            px-4
                            py-3
                        "
                    >{{ old('note') }}</textarea>

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
                        href="{{ route('paiements-agences.index') }}"
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


                    @if($abonnements->count())

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
                            💰 Créer le paiement
                        </button>

                    @endif

                </div>

            </form>

        </div>

    </div>

</x-layouts.admin>