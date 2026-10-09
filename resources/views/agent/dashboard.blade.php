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

        @php

            $nombreReservations = \App\Models\Reservation::whereHas(
                'trajet',
                function ($query) {
                    $query->where(
                        'agence_id',
                        auth()->user()->agence_id
                    );
                }
            )->count();

            $nombreAchats = \App\Models\Achat::whereHas(
                'trajet',
                function ($query) {
                    $query->where(
                        'agence_id',
                        auth()->user()->agence_id
                    );
                }
            )->count();

        @endphp


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
                    Réservations de mes trajets
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
                    Billets achetés
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

                            <th class="text-left">
                                Départ
                            </th>

                            <th class="text-left">
                                Arrivée
                            </th>

                            <th class="text-center">
                                Date
                            </th>

                            <th class="text-center">
                                Heure
                            </th>

                            <th class="text-right">
                                Prix
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($trajets as $trajet)

                            <tr>

                                <td class="font-semibold text-navy">
                                    {{ $trajet->depart }}
                                </td>


                                <td>
                                    {{ $trajet->arrivee }}
                                </td>


                                <td class="text-center text-slate-500">
                                    {{ $trajet->date_depart }}
                                </td>


                                <td class="text-center text-slate-500">
                                    {{ $trajet->heure_depart }}
                                </td>


                                <td class="text-right font-bold text-brand">

                                    {{ number_format(
                                        $trajet->prix,
                                        0,
                                        ',',
                                        ' '
                                    ) }}

                                    FCFA

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5">
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
