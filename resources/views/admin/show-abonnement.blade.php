<x-layouts.admin :header="'Détails de l\'abonnement'">

<div class="tk-page">

    {{-- En-tête --}}

    <div
        class="
            tk-page-head
            flex flex-col gap-4
            md:flex-row md:items-center
            md:justify-between
        "
    >

        <div>

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

                Détails de l'abonnement

            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Informations détaillées de l'abonnement.
            </p>

        </div>

        <a
            href="{{ route('admin.abonnements') }}"
            class="tk-btn-ghost shrink-0"
        >

            <i class="fa-solid fa-arrow-left"></i>

            <span>Retour</span>

        </a>

    </div>


    {{-- Abonnement --}}

    <div class="tk-card p-6">

        <div>

            {{-- AGENCE --}}

            <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

                <span class="inline-flex items-center gap-2 font-semibold text-navy">
                    <i class="fa-solid fa-building text-slate-400"></i>
                    Agence
                </span>

                <span class="text-slate-600 text-right">
                    {{ $abonnement->agence->nom_agence ?? 'Agence inconnue' }}
                </span>

            </div>


            {{-- TYPE --}}

            <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

                <span class="inline-flex items-center gap-2 font-semibold text-navy">
                    <i class="fa-solid fa-box text-slate-400"></i>
                    Type d'abonnement
                </span>

                <span class="text-slate-600 text-right">
                    {{ $abonnement->type }}
                </span>

            </div>


            {{-- MONTANT --}}

            <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

                <span class="inline-flex items-center gap-2 font-semibold text-navy">
                    <i class="fa-solid fa-money-bill-wave text-slate-400"></i>
                    Montant
                </span>

                <span class="text-slate-600 text-right font-semibold">
                    {{ number_format($abonnement->montant, 0, ',', ' ') }} FCFA
                </span>

            </div>


            {{-- DATE DÉBUT --}}

            <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

                <span class="inline-flex items-center gap-2 font-semibold text-navy">
                    <i class="fa-solid fa-calendar text-slate-400"></i>
                    Date de début
                </span>

                <span class="text-slate-600 text-right">

                    {{ \Carbon\Carbon::parse($abonnement->date_debut)->format('d/m/Y') }}

                </span>

            </div>


            {{-- DATE FIN --}}

            <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

                <span class="inline-flex items-center gap-2 font-semibold text-navy">
                    <i class="fa-solid fa-calendar text-slate-400"></i>
                    Date de fin
                </span>

                <span class="text-slate-600 text-right">

                    {{ \Carbon\Carbon::parse($abonnement->date_fin)->format('d/m/Y') }}

                </span>

            </div>


            {{-- STATUT --}}

            <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

                <span class="inline-flex items-center gap-2 font-semibold text-navy">
                    <i class="fa-solid fa-toggle-on text-slate-400"></i>
                    Statut
                </span>

                <span class="text-right">

                    @if($abonnement->statut === 'Actif')

                        <span class="tk-badge tk-badge-green">
                            <i class="fa-solid fa-circle-check"></i>
                            Actif
                        </span>

                    @else

                        <span class="tk-badge tk-badge-red">
                            <i class="fa-solid fa-xmark"></i>
                            Expiré
                        </span>

                    @endif

                </span>

            </div>


            {{-- IDENTIFIANT --}}

            <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

                <span class="inline-flex items-center gap-2 font-semibold text-navy">
                    <i class="fa-solid fa-hashtag text-slate-400"></i>
                    Identifiant
                </span>

                <span class="text-slate-600 text-right">
                    #{{ $abonnement->id }}
                </span>

            </div>

        </div>


        {{-- ACTIONS --}}

        <div class="mt-6 flex flex-col sm:flex-row gap-3">

            <a
                href="{{ route('admin.abonnements.edit', $abonnement->id) }}"
                class="tk-btn-accent"
            >

                <i class="fa-solid fa-pen"></i>

                Modifier l'abonnement

            </a>

            <a
                href="{{ route('admin.abonnements') }}"
                class="tk-btn-ghost"
            >

                <i class="fa-solid fa-arrow-left"></i>

                Retour aux abonnements

            </a>

        </div>

    </div>


    {{-- Paiement agence --}}

    @if($abonnement->paiementAgences && $abonnement->paiementAgences->count() > 0)

        <div class="tk-card p-6">

            <h2 class="flex items-center gap-2.5 font-bold text-navy">

                <i class="fa-solid fa-credit-card text-brand"></i>

                Paiement de l'agence

            </h2>

            @foreach($abonnement->paiementAgences as $paiement)

                <div class="mt-4">

                    {{-- MONTANT PAYÉ --}}

                    <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

                        <span class="font-semibold text-navy">
                            Montant payé
                        </span>

                        <span class="text-slate-600 text-right font-semibold">
                            {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA
                        </span>

                    </div>


                    {{-- MODE DE PAIEMENT --}}

                    <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

                        <span class="font-semibold text-navy">
                            Mode de paiement
                        </span>

                        <span class="text-slate-600 text-right">
                            {{ $paiement->mode_paiement ?? 'Non renseigné' }}
                        </span>

                    </div>


                    {{-- DATE DU PAIEMENT --}}

                    <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

                        <span class="font-semibold text-navy">
                            Date du paiement
                        </span>

                        <span class="text-slate-600 text-right">
                            {{ $paiement->date_paiement
                                ? \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y')
                                : 'Non renseignée'
                            }}
                        </span>

                    </div>


                    {{-- STATUT DU PAIEMENT --}}

                    <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

                        <span class="font-semibold text-navy">
                            Statut du paiement
                        </span>

                        <span class="text-slate-600 text-right">
                            {{ $paiement->statut ?? 'Non renseigné' }}
                        </span>

                    </div>

                </div>

            @endforeach

        </div>

    @endif

</div>

</x-layouts.admin>
