<x-layouts.agent
    :header="'Tableau de bord'"
>

    <div class="space-y-6">

        {{-- =====================================================
             BIENVENUE
        ====================================================== --}}

        <div>

            <h1
                style="
                    margin:0;
                    font-size:28px;
                    font-weight:800;
                    color:#0A2A66;
                "
            >
                Bonjour {{ auth()->user()->name }} 👋
            </h1>

            <p
                style="
                    margin-top:6px;
                    font-size:14px;
                    color:#6B7280;
                "
            >
                Bienvenue dans l’espace de gestion de votre agence.
            </p>

        </div>


        {{-- =====================================================
             MESSAGE SUCCÈS
        ====================================================== --}}

        @if(session('success'))

            <div
                style="
                    background:#DCFCE7;
                    color:#15803D;
                    padding:14px 18px;
                    border-radius:12px;
                    font-size:14px;
                    font-weight:600;
                "
            >

                ✅ {{ session('success') }}

            </div>

        @endif


        {{-- =====================================================
             MESSAGE ERREUR
        ====================================================== --}}

        @if(session('error'))

            <div
                style="
                    background:#FEF2F2;
                    color:#DC2626;
                    border:1px solid #FECACA;
                    padding:15px 18px;
                    border-radius:12px;
                    font-size:14px;
                "
            >

                <strong>
                    ⚠️ Attention
                </strong>

                <div style="margin-top:5px;">
                    {{ session('error') }}
                </div>

            </div>

        @endif


        {{-- =====================================================
             AGENCE
        ====================================================== --}}

        <div
            style="
                background:#0A2A66;
                color:#FFFFFF;
                border-radius:18px;
                padding:22px 24px;
                box-shadow:0 8px 20px rgba(10,42,102,.12);
            "
        >

            <div
                style="
                    font-size:12px;
                    color:#DCE8FF;
                    text-transform:uppercase;
                    letter-spacing:.8px;
                    font-weight:700;
                "
            >
                Mon agence
            </div>

            <div
                style="
                    margin-top:6px;
                    font-size:25px;
                    font-weight:800;
                "
            >
                {{ $agence->nom_agence }}
            </div>

            @if(!empty($agence->ville))

    <div
        style="
            margin-top:5px;
            font-size:13px;
            color:#DCE8FF;
        "
    >
        🏙️ Ville : {{ $agence->ville }}
    </div>

@endif

@if(!empty($agence->adresse))

    <div
        style="
            margin-top:5px;
            font-size:13px;
            color:#DCE8FF;
        "
    >
        📍 Adresse : {{ $agence->adresse }}
    </div>

@endif


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


        <div
            style="
                display:grid;
                grid-template-columns:repeat(3,minmax(0,1fr));
                gap:16px;
            "
        >

            {{-- TRAJETS --}}

            <div
                style="
                    background:#FFFFFF;
                    border:1px solid #E8EDF5;
                    border-left:4px solid #FF6B00;
                    border-radius:16px;
                    padding:18px;
                    box-shadow:0 4px 14px rgba(10,42,102,.05);
                "
            >

                <div
                    style="
                        font-size:12px;
                        color:#6B7280;
                        font-weight:600;
                    "
                >
                    Mes trajets
                </div>

                <div
                    style="
                        margin-top:7px;
                        font-size:30px;
                        font-weight:800;
                        color:#0A2A66;
                    "
                >
                    {{ $nombreTrajets }}
                </div>

                <div
                    style="
                        margin-top:3px;
                        font-size:12px;
                        color:#FF6B00;
                    "
                >
                    🚌 Trajets de mon agence
                </div>

            </div>


            {{-- RÉSERVATIONS --}}

            <div
                style="
                    background:#FFFFFF;
                    border:1px solid #E8EDF5;
                    border-left:4px solid #0A2A66;
                    border-radius:16px;
                    padding:18px;
                    box-shadow:0 4px 14px rgba(10,42,102,.05);
                "
            >

                <div
                    style="
                        font-size:12px;
                        color:#6B7280;
                        font-weight:600;
                    "
                >
                    Réservations
                </div>

                <div
                    style="
                        margin-top:7px;
                        font-size:30px;
                        font-weight:800;
                        color:#0A2A66;
                    "
                >
                    {{ $nombreReservations }}
                </div>

                <div
                    style="
                        margin-top:3px;
                        font-size:12px;
                        color:#6B7280;
                    "
                >
                    🎫 Réservations de mes trajets
                </div>

            </div>


            {{-- ACHATS --}}

            <div
                style="
                    background:#FFFFFF;
                    border:1px solid #E8EDF5;
                    border-left:4px solid #FF6B00;
                    border-radius:16px;
                    padding:18px;
                    box-shadow:0 4px 14px rgba(10,42,102,.05);
                "
            >

                <div
                    style="
                        font-size:12px;
                        color:#6B7280;
                        font-weight:600;
                    "
                >
                    Achats
                </div>

                <div
                    style="
                        margin-top:7px;
                        font-size:30px;
                        font-weight:800;
                        color:#0A2A66;
                    "
                >
                    {{ $nombreAchats }}
                </div>

                <div
                    style="
                        margin-top:3px;
                        font-size:12px;
                        color:#FF6B00;
                    "
                >
                    💳 Billets achetés
                </div>

            </div>

        </div>


        {{-- =====================================================
             DERNIERS TRAJETS
        ====================================================== --}}

        <div
            style="
                background:#FFFFFF;
                border:1px solid #E8EDF5;
                border-radius:16px;
                overflow:hidden;
                box-shadow:0 4px 14px rgba(10,42,102,.05);
            "
        >

            <div
                style="
                    padding:17px 20px;
                    border-bottom:1px solid #E8EDF5;
                    display:flex;
                    align-items:center;
                    justify-content:space-between;
                    gap:10px;
                "
            >

                <div>

                    <div
                        style="
                            font-size:17px;
                            font-weight:800;
                            color:#0A2A66;
                        "
                    >
                        🚌 Mes derniers trajets
                    </div>

                    <div
                        style="
                            margin-top:3px;
                            font-size:12px;
                            color:#6B7280;
                        "
                    >
                        Les trajets récemment ajoutés par votre agence.
                    </div>

                </div>


                <a
                    href="{{ route('admin.trajets') }}"
                    style="
                        display:inline-flex;
                        align-items:center;
                        justify-content:center;
                        padding:8px 13px;
                        border-radius:9px;
                        background:#FF6B00;
                        color:#FFFFFF;
                        text-decoration:none;
                        font-size:12px;
                        font-weight:700;
                    "
                >
                    Voir mes trajets
                </a>

            </div>


            <div style="overflow-x:auto;">

                <table
                    style="
                        width:100%;
                        border-collapse:collapse;
                    "
                >

                    <thead
                        style="
                            background:#EEF4FF;
                        "
                    >

                        <tr>

                            <th
                                style="
                                    padding:13px 16px;
                                    text-align:left;
                                    font-size:12px;
                                    color:#0A2A66;
                                "
                            >
                                Départ
                            </th>

                            <th
                                style="
                                    padding:13px 16px;
                                    text-align:left;
                                    font-size:12px;
                                    color:#0A2A66;
                                "
                            >
                                Arrivée
                            </th>

                            <th
                                style="
                                    padding:13px 16px;
                                    text-align:center;
                                    font-size:12px;
                                    color:#0A2A66;
                                "
                            >
                                Date
                            </th>

                            <th
                                style="
                                    padding:13px 16px;
                                    text-align:center;
                                    font-size:12px;
                                    color:#0A2A66;
                                "
                            >
                                Heure
                            </th>

                            <th
                                style="
                                    padding:13px 16px;
                                    text-align:right;
                                    font-size:12px;
                                    color:#0A2A66;
                                "
                            >
                                Prix
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($trajets as $trajet)

                            <tr
                                style="
                                    border-top:1px solid #F0F2F5;
                                "
                            >

                                <td
                                    style="
                                        padding:13px 16px;
                                        font-size:13px;
                                        font-weight:700;
                                        color:#0A2A66;
                                    "
                                >
                                    {{ $trajet->depart }}
                                </td>


                                <td
                                    style="
                                        padding:13px 16px;
                                        font-size:13px;
                                        color:#374151;
                                    "
                                >
                                    {{ $trajet->arrivee }}
                                </td>


                                <td
                                    style="
                                        padding:13px 16px;
                                        text-align:center;
                                        font-size:13px;
                                        color:#6B7280;
                                    "
                                >
                                    {{ $trajet->date_depart }}
                                </td>


                                <td
                                    style="
                                        padding:13px 16px;
                                        text-align:center;
                                        font-size:13px;
                                        color:#6B7280;
                                    "
                                >
                                    {{ $trajet->heure_depart }}
                                </td>


                                <td
                                    style="
                                        padding:13px 16px;
                                        text-align:right;
                                        font-size:13px;
                                        font-weight:800;
                                        color:#FF6B00;
                                    "
                                >

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

                                <td
                                    colspan="5"
                                    style="
                                        padding:35px;
                                        text-align:center;
                                        color:#9CA3AF;
                                        font-size:13px;
                                    "
                                >

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

            <div
                style="
                    background:#FEF2F2;
                    border:1px solid #FECACA;
                    border-left:5px solid #DC2626;
                    border-radius:14px;
                    padding:17px 20px;
                "
            >

                <div
                    style="
                        display:flex;
                        align-items:center;
                        justify-content:space-between;
                        gap:20px;
                    "
                >

                    <div>

                        <div
                            style="
                                font-size:16px;
                                font-weight:800;
                                color:#DC2626;
                            "
                        >
                            🔴 Paiement en retard
                        </div>

                        <div
                            style="
                                margin-top:5px;
                                font-size:13px;
                                color:#6B7280;
                            "
                        >

                            Votre agence a une échéance de

                            <strong>
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
                            style="
                                display:inline-flex;
                                align-items:center;
                                justify-content:center;
                                padding:10px 15px;
                                border-radius:10px;
                                background:#FF6B00;
                                color:#FFFFFF;
                                text-decoration:none;
                                font-size:13px;
                                font-weight:700;
                                white-space:nowrap;
                            "
                        >
                            💳 Régler maintenant
                        </a>

                    @endif

                </div>

            </div>

        @endif


    </div>

</x-layouts.agent>