<x-layouts.agent
    :header="'Paiement à TOKENDE'"
>

    <div class="max-w-2xl mx-auto">

        {{-- TITRE --}}

        <div class="mb-6">

            <h1
                class="
                    text-3xl
                    md:text-4xl
                    font-bold
                "
                style="color:#0A2A66;"
            >
                💳 Paiement à TOKENDE
            </h1>

            <p class="text-gray-500 mt-2">
                Vérifiez les informations avant de confirmer votre paiement.
            </p>

        </div>


        {{-- CARTE --}}

        <div
            class="
                bg-white
                rounded-2xl
                shadow-lg
                border
                border-gray-100
                overflow-hidden
            "
        >

            {{-- HEADER --}}

            <div
                class="
                    px-6
                    py-5
                "
                style="background:#0A2A66;"
            >

                <p
                    class="text-sm"
                    style="color:#DCE8FF;"
                >
                    Agence
                </p>

                <h2
                    class="
                        text-2xl
                        font-bold
                        mt-1
                    "
                    style="color:#FFFFFF;"
                >
                    {{ $paiement->agence->nom_agence ?? '-' }}
                </h2>

            </div>


            {{-- INFORMATIONS --}}

            <div class="p-6 space-y-5">

                {{-- MONTANT --}}

                <div
                    class="
                        rounded-2xl
                        p-5
                        text-center
                    "
                    style="background:#FFF3E8;"
                >

                    <p class="text-sm text-gray-500">
                        Montant à payer
                    </p>

                    <p
                        class="
                            text-4xl
                            font-bold
                            mt-2
                        "
                        style="color:#FF6B00;"
                    >

                        {{ number_format(
                            $paiement->montant,
                            0,
                            ',',
                            ' '
                        ) }}

                        FCFA

                    </p>

                </div>


                {{-- INFOS --}}

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div
                        class="
                            rounded-xl
                            p-4
                        "
                        style="background:#EEF4FF;"
                    >

                        <p class="text-xs text-gray-500">
                            Référence
                        </p>

                        <p
                            class="
                                font-mono
                                font-bold
                                mt-1
                            "
                            style="color:#0A2A66;"
                        >
                            {{ $paiement->reference }}
                        </p>

                    </div>


                    <div
                        class="
                            rounded-xl
                            p-4
                        "
                        style="background:#EEF4FF;"
                    >

                        <p class="text-xs text-gray-500">
                            Date d’échéance
                        </p>

                        <p
                            class="
                                font-bold
                                mt-1
                            "
                            style="color:#0A2A66;"
                        >

                            {{ $paiement->date_prevue
                                ? $paiement->date_prevue->format('d/m/Y')
                                : '-'
                            }}

                        </p>

                    </div>

                </div>


                {{-- ABONNEMENT --}}

                @if($paiement->abonnement)

                    <div
                        class="
                            rounded-xl
                            p-4
                            border
                            border-gray-100
                        "
                    >

                        <p
                            class="
                                text-sm
                                text-gray-500
                            "
                        >
                            Abonnement
                        </p>

                        <p
                            class="
                                text-lg
                                font-bold
                                mt-1
                            "
                            style="color:#0A2A66;"
                        >
                            {{ $paiement->abonnement->type }}
                        </p>

                    </div>

                @endif


                {{-- MESSAGE --}}

                <div
                    class="
                        rounded-xl
                        p-4
                    "
                    style="
                        background:#EEF4FF;
                        color:#0A2A66;
                    "
                >

                    <p class="font-semibold">
                        ℹ️ Paiement de démonstration
                    </p>

                    <p class="text-sm mt-2 text-gray-600">

                        En confirmant, le paiement sera enregistré dans
                        l’application comme effectué et sa date de paiement
                        sera automatiquement enregistrée.

                    </p>

                </div>


                {{-- FORMULAIRE --}}

                <form
                    method="POST"
                    action="{{ route(
                        'paiements-agences.pay',
                        $paiement->id
                    ) }}"
                >

                    @csrf

                    <button
                        type="submit"
                        class="
                            w-full
                            py-4
                            rounded-xl
                            text-white
                            font-bold
                            text-lg
                            shadow-md
                        "
                        style="background:#FF6B00;"
                        onclick="
                            return confirm(
                                'Confirmer le paiement de {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA ?'
                            )
                        "
                    >

                        💳
                        Confirmer le paiement

                    </button>

                </form>


                {{-- RETOUR --}}

                <a
                    href="{{ route(
                        'paiements-agences.index'
                    ) }}"
                    class="
                        block
                        text-center
                        py-3
                        rounded-xl
                        bg-gray-100
                        hover:bg-gray-200
                        text-gray-700
                        font-semibold
                    "
                >
                    ← Retour à mes paiements
                </a>

            </div>

        </div>

    </div>

</x-layouts.agent>