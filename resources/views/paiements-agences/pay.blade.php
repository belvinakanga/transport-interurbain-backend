<x-dynamic-component
    :component="auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.agent'"
    :header="'Paiement à TOKENDE'"
>

    <div class="tk-page">

        {{-- TITRE --}}

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
                    <i class="fa-solid fa-credit-card"></i>
                </span>

                Paiement à TOKENDE

            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Vérifiez les informations avant de confirmer votre paiement.
            </p>

        </div>


        {{-- CARTE --}}

        <div class="tk-card overflow-hidden">

            {{-- HEADER --}}

            <div class="bg-navy px-6 py-5">

                <p class="text-sm text-[#DCE8FF]">
                    Agence
                </p>

                <h2
                    class="
                        mt-1
                        text-2xl
                        font-bold
                        text-white
                    "
                >
                    {{ $paiement->agence->nom_agence ?? '-' }}
                </h2>

            </div>


            {{-- INFORMATIONS --}}

            <div class="p-6 space-y-5">

                {{-- MONTANT --}}

                <div
                    class="
                        rounded-xl
                        p-5
                        text-center
                        bg-[#FFF3E8]
                    "
                >

                    <p class="text-sm text-slate-500">
                        Montant à payer
                    </p>

                    <p
                        class="
                            mt-2
                            text-4xl
                            font-bold
                            text-brand
                        "
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

                    <div class="rounded-xl bg-[#EEF4FF] p-4">

                        <p class="text-xs text-slate-500">
                            Référence
                        </p>

                        <p
                            class="
                                mt-1
                                font-mono
                                font-bold
                                text-navy
                            "
                        >
                            {{ $paiement->reference }}
                        </p>

                    </div>


                    <div class="rounded-xl bg-[#EEF4FF] p-4">

                        <p class="text-xs text-slate-500">
                            Date d’échéance
                        </p>

                        <p
                            class="
                                mt-1
                                font-bold
                                text-navy
                            "
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
                            border border-slate-100
                            p-4
                        "
                    >

                        <p class="text-sm text-slate-500">
                            Abonnement
                        </p>

                        <p
                            class="
                                mt-1
                                text-lg
                                font-bold
                                text-navy
                            "
                        >
                            {{ $paiement->abonnement->type }}
                        </p>

                    </div>

                @endif


                {{-- MESSAGE --}}

                <div
                    class="
                        rounded-xl
                        bg-[#EEF4FF]
                        p-4
                        text-navy
                    "
                >

                    <p class="flex items-center gap-2 font-semibold">

                        <i class="fa-solid fa-circle-info"></i>

                        Paiement de démonstration

                    </p>

                    <p class="mt-2 text-sm text-slate-600">

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
                        class="tk-btn-accent w-full"
                        onclick="event.preventDefault(); tkConfirm('Confirmer le paiement de {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA ?', () => this.form.submit())"
                    >

                        <i class="fa-solid fa-credit-card"></i>

                        Confirmer le paiement

                    </button>

                </form>


                {{-- RETOUR --}}

                <a
                    href="{{ route(
                        'paiements-agences.index'
                    ) }}"
                    class="tk-btn-ghost w-full"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Retour à mes paiements

                </a>

            </div>

        </div>

    </div>

</x-dynamic-component>
