<x-layouts.admin
    :header="'Nouveau paiement agence'"
>

    <div class="tk-page">

        {{-- =====================================================
             TITRE
        ====================================================== --}}

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
                    <i class="fa-solid fa-money-bill-wave"></i>
                </span>

                Nouveau paiement

            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Enregistrez le paiement dû par une agence à TOKENDE.
            </p>

        </div>


        {{-- =====================================================
             FORMULAIRE
        ====================================================== --}}

        <div class="tk-card overflow-hidden">

            {{-- EN-TÊTE --}}

            <div class="border-b border-slate-200 px-5 py-4">

                <h2
                    class="
                        flex
                        items-center
                        gap-2
                        text-xl
                        font-bold
                        text-navy
                    "
                >
                    <i class="fa-solid fa-receipt"></i>

                    Informations du paiement
                </h2>

                <p class="mt-1 text-sm text-slate-500">
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
                        class="tk-form-label"
                    >
                        Abonnement concerné
                    </label>


                    @if($abonnements->count())

                        <select
                            id="abonnement_id"
                            name="abonnement_id"
                            required
                            class="tk-input"
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
                                border border-[#FFD7B8]
                                bg-[#FFF3E8]
                                p-4
                                text-sm
                                text-[#9A3412]
                            "
                        >

                            <p class="flex items-center gap-2 font-semibold">

                                <i class="fa-solid fa-triangle-exclamation"></i>

                                Aucun abonnement actif disponible.

                            </p>

                            <p class="mt-1">
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
                    class="
                        rounded-xl
                        border border-[#D8E6FF]
                        bg-[#EEF4FF]
                        p-5
                    "
                >

                    <div class="flex items-start gap-3">

                        <div class="text-xl text-navy">
                            <i class="fa-solid fa-circle-info"></i>
                        </div>

                        <div>

                            <h3 class="font-bold text-navy">
                                Fonctionnement
                            </h3>

                            <p class="text-sm text-slate-600 mt-2">

                                Le montant du paiement et la date
                                d’échéance seront automatiquement
                                récupérés depuis l’abonnement sélectionné.

                            </p>

                            <p class="text-sm text-slate-600 mt-2">

                                Le paiement sera enregistré comme
                                <strong class="text-brand">
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
                        class="tk-form-label"
                    >
                        Note
                    </label>

                    <textarea
                        id="note"
                        name="note"
                        rows="4"
                        maxlength="2000"
                        placeholder="Exemple : Paiement de l'abonnement du mois d'août 2026."
                        class="tk-input h-auto py-2.5 min-h-[100px]"
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
                        class="tk-btn-ghost"
                    >

                        <i class="fa-solid fa-arrow-left"></i>

                        Annuler

                    </a>


                    @if($abonnements->count())

                        <button
                            type="submit"
                            class="tk-btn-accent"
                        >

                            <i class="fa-solid fa-check"></i>

                            Créer le paiement

                        </button>

                    @endif

                </div>

            </form>

        </div>

    </div>

</x-layouts.admin>
