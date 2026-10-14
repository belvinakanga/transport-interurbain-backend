<x-layouts.agent
    :header="'Tableau de bord'"
>

    <div class="space-y-6">

        {{-- =====================================================
             BIENVENUE
        ====================================================== --}}

        <div>

            <h1 class="text-2xl font-extrabold text-navy">
                Bonjour {{ auth()->user()->name }}
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Bienvenue dans l’espace de gestion de votre agence.
            </p>

        </div>


        {{-- =====================================================
             MESSAGE ERREUR
        ====================================================== --}}


        {{-- =====================================================
             AGENCE
        ====================================================== --}}

        <div class="tk-card p-6">

            <div class="tk-label text-slate-500">
                Mon agence
            </div>

            <div class="mt-1 text-xl font-extrabold text-navy">
                {{ $agence->nom_agence }}
            </div>

            @if(!empty($agence->ville))

                <div class="mt-2 flex items-center gap-2 text-sm text-slate-500">
                    <i class="fa-solid fa-city w-4 text-center"></i>
                    <span>Ville : {{ $agence->ville }}</span>
                </div>

            @endif

            @if(!empty($agence->adresse))

                <div class="mt-1 flex items-center gap-2 text-sm text-slate-500">
                    <i class="fa-solid fa-location-dot w-4 text-center"></i>
                    <span>Adresse : {{ $agence->adresse }}</span>
                </div>

            @endif

        </div>


        {{-- =====================================================
             STATISTIQUES AGENT
        ====================================================== --}}



        <div class="grid gap-4 sm:grid-cols-3">

            {{-- TRAJETS --}}

            <div class="tk-card flex flex-col items-start gap-1 p-5">

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand/10 text-brand">
                    <i class="fa-solid fa-bus"></i>
                </div>

                <div class="mt-2 tk-label text-slate-500">
                    Mes trajets
                </div>

                <div class="text-3xl font-extrabold text-navy">
                    {{ $nombreTrajets }}
                </div>

                <div class="text-xs text-brand">
                    Trajets de mon agence
                </div>

            </div>


            {{-- RÉSERVATIONS --}}

            <div class="tk-card flex flex-col items-start gap-1 p-5">

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-navy/10 text-navy">
                    <i class="fa-solid fa-ticket"></i>
                </div>

                <div class="mt-2 tk-label text-slate-500">
                    Réservations
                </div>

                <div class="text-3xl font-extrabold text-navy">
                    {{ $nombreReservations }}
                </div>

                <div class="text-xs text-slate-500">
                    Réservées en ligne, payées ou non
                </div>

            </div>


            {{-- ACHATS --}}

            <div class="tk-card flex flex-col items-start gap-1 p-5">

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand/10 text-brand">
                    <i class="fa-solid fa-credit-card"></i>
                </div>

                <div class="mt-2 tk-label text-slate-500">
                    Achats
                </div>

                <div class="text-3xl font-extrabold text-navy">
                    {{ $nombreAchats }}
                </div>

                <div class="text-xs text-brand">
                    Achats directs au guichet
                </div>

            </div>

        </div>


        {{-- =====================================================
             RECETTES
             ------------------------------------------------------
             Le détail évite à l'agence de refaire le calcul : ce que
             rapportent les billets, et sa part des frais de service.
        ====================================================== --}}

        <div class="tk-card overflow-hidden">

            <div class="border-b border-slate-200 px-5 py-4">

                <div class="tk-label text-slate-500">
                    Recettes des ventes payées
                </div>

                <div class="mt-1 text-xs text-slate-500">
                    Le voyageur paie le prix du billet plus des frais de
                    service, partagés entre Tokende et votre agence.
                </div>

            </div>

            {{-- Les filets et les fonds viennent des variables du thème :
                 les couleurs Tailwind brutes ne suivent pas le mode sombre,
                 et « bg-slate-200 » n’y est pas surchargé. --}}
            <div
                class="grid gap-px sm:grid-cols-4"
                style="background: var(--tk-border);"
            >

                <div class="p-5" style="background: var(--tk-surface);">
                    <div class="tk-label text-slate-500">Billets</div>
                    <div class="mt-1 text-2xl font-extrabold" style="color: var(--tk-heading);">
                        {{ number_format($recettes['billets'], 0, ',', ' ') }}
                        <span class="text-sm font-semibold">FCFA</span>
                    </div>
                    <div class="mt-1 text-xs text-slate-500">
                        Revient à votre agence
                    </div>
                </div>

                <div class="p-5" style="background: var(--tk-surface);">
                    <div class="tk-label text-slate-500">Votre part des frais</div>
                    <div class="mt-1 text-2xl font-extrabold" style="color: var(--tk-success-fg);">
                        {{ number_format($recettes['part_agence'], 0, ',', ' ') }}
                        <span class="text-sm font-semibold">FCFA</span>
                    </div>
                    <div class="mt-1 text-xs text-slate-500">
                        {{ (int) config('tokende.frais_service.part_agence') }} FCFA
                        par billet
                    </div>
                </div>

                <div class="p-5" style="background: var(--tk-surface);">
                    <div class="tk-label text-slate-500">Commission Tokende</div>
                    <div class="mt-1 text-2xl font-extrabold" style="color: var(--tk-text-3);">
                        {{ number_format($recettes['commission'], 0, ',', ' ') }}
                        <span class="text-sm font-semibold">FCFA</span>
                    </div>
                    <div class="mt-1 text-xs text-slate-500">
                        {{ (int) config('tokende.frais_service.commission') }} FCFA
                        par billet
                    </div>
                </div>

                <div class="p-5" style="background: var(--tk-surface);">
                    <div class="tk-label text-slate-500">Total encaissé</div>
                    <div class="mt-1 text-2xl font-extrabold" style="color: var(--tk-accent);">
                        {{ number_format($recettes['encaisse'], 0, ',', ' ') }}
                        <span class="text-sm font-semibold">FCFA</span>
                    </div>
                    <div class="mt-1 text-xs text-slate-500">
                        Billets + frais de service
                    </div>
                </div>

            </div>

        </div>


        {{-- =====================================================
             DERNIERS TRAJETS
        ====================================================== --}}

        <div class="tk-card overflow-hidden">

            <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4">

                <div>

                    <div class="font-bold text-navy">
                        Mes derniers trajets
                    </div>

                    <div class="mt-1 text-xs text-slate-500">
                        Les trajets récemment ajoutés par votre agence.
                    </div>

                </div>


                <a
                    href="{{ route('admin.trajets') }}"
                    class="tk-btn tk-btn-primary"
                >
                    <i class="fa-solid fa-route"></i>
                    <span>Voir mes trajets</span>
                </a>

            </div>


            <div class="overflow-x-auto">

                <table class="tk-table">

                    <thead>

                        <tr>

                            {{-- Une seule colonne pour les deux villes : à
                                 430 px, cinq colonnes repoussaient le prix
                                 hors de l'écran. --}}
                            <th class="text-left">
                                Trajet
                            </th>

                            <th class="text-center">
                                Date
                            </th>

                            <th class="text-right">
                                Prix <span class="font-normal normal-case">(FCFA)</span>
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($trajets as $trajet)

                            <tr>

                                {{-- Un nom de ville ne se coupe pas :
                                     « Pointe-Noire » devenait « te- / e ». --}}
                                {{-- Le repli se fait entre les deux villes,
                                     jamais à l'intérieur d'un nom. --}}
                                <td class="font-semibold text-navy">
                                    <span class="whitespace-nowrap">{{ $trajet->depart }}</span>
                                    <span class="px-1 text-slate-400">→</span>
                                    <span class="whitespace-nowrap">{{ $trajet->arrivee }}</span>
                                </td>





                                {{-- Le libellé du modèle, déjà normalisé, sur une
                                     seule ligne : « 2026-10-16 » se coupait en
                                     deux sur un écran de téléphone. --}}
                                {{-- Date et heure dans la même colonne : à
                                     430 px, une colonne de plus repoussait le
                                     prix hors de l'écran. --}}
                                <td class="text-center text-slate-500 whitespace-nowrap">

                                    <div>
                                        {{ rescue(
                                            fn () => \Illuminate\Support\Carbon::parse(
                                                $trajet->date_depart
                                            )->format('d/m/Y'),
                                            $trajet->date_depart,
                                            false
                                        ) }}
                                    </div>

                                    <div class="text-xs">
                                        {{ $trajet->heure_depart_libelle
                                            ?? $trajet->heure_depart }}
                                    </div>

                                </td>





                                <td class="text-right font-bold text-brand whitespace-nowrap">

                                    {{ number_format(
                                        $trajet->prix,
                                        0,
                                        ',',
                                        ' '
                                    ) }}


                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="3">
                                    Aucun trajet enregistré.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- =====================================================
             PAIEMENT EN RETARD
        ====================================================== --}}

        @php

            $paiementEnRetard =
                \App\Models\PaiementAgence::where(
                    'agence_id',
                    auth()->user()->agence_id
                )
                ->where(
                    'statut',
                    'en attente'
                )
                ->whereDate(
                    'date_prevue',
                    '<',
                    today()
                )
                ->latest('date_prevue')
                ->first();

        @endphp


        @if($paiementEnRetard)

            <div class="tk-card flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <div class="flex items-center gap-2 font-bold text-red-600">

                        <i class="fa-solid fa-circle-exclamation"></i>

                        <span>Paiement en retard</span>

                    </div>

                    <div class="mt-1 text-sm text-slate-500">

                        Votre agence a une échéance de

                        <strong class="text-slate-800">
                            {{
                                number_format(
                                    $paiementEnRetard->montant,
                                    0,
                                    ',',
                                    ' '
                                )
                            }}
                            FCFA
                        </strong>

                        non réglée.

                    </div>

                </div>


                @if(Route::has('agent.paiements'))

                    <a
                        href="{{ route('agent.paiements') }}"
                        class="tk-btn tk-btn-primary shrink-0"
                    >
                        <i class="fa-solid fa-credit-card"></i>
                        <span>Régler maintenant</span>
                    </a>

                @endif

            </div>

        @endif


    </div>

</x-layouts.agent>
