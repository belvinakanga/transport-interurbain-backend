<x-app-layout>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <h1 class="text-3xl font-bold mb-8">
                🚍 Liste des trajets disponibles
            </h1>

            @forelse($trajets as $trajet)

                <div class="bg-white shadow rounded-lg p-6 mb-6">

                    <h2 class="text-2xl font-bold text-blue-700 mb-4">
                        {{ $trajet->depart }} → {{ $trajet->arrivee }}
                    </h2>

                    <p class="mb-2">
                        <strong>📅 Date :</strong>
                        {{ $trajet->date_depart }}
                    </p>

                    <p class="mb-2">
                        <strong>🕒 Heure :</strong>
                        {{ $trajet->heure_depart }}
                    </p>

                    <p class="mb-2">
                        <strong>💰 Prix :</strong>
                        {{ number_format($trajet->prix, 0, ',', ' ') }} FCFA
                    </p>

                    <p class="mb-4">
                        <strong>💺 Places disponibles :</strong>
                        {{ $trajet->places_disponibles }}
                        /
                        {{ $trajet->places_totales }}
                    </p>

                    @if($trajet->reservationOuverte)

                        <!-- Réservations ouvertes -->

                        <div
                            style="
                                background:#dcfce7;
                                color:#166534;
                                padding:12px;
                                border-radius:6px;
                                margin-bottom:15px;
                            ">

                            <strong>🟢 Réservations ouvertes</strong>

                        </div>

                        <a
                            href="/reservation/create/{{ $trajet->id }}"
                            style="
                                display:inline-block;
                                background:#2563eb;
                                color:white;
                                padding:12px 22px;
                                border-radius:6px;
                                text-decoration:none;
                                font-weight:bold;
                            ">

                            Réserver

                        </a>

                    @else

                        <!-- Réservations fermées -->

                        <div
                            style="
                                background:#fee2e2;
                                color:#991b1b;
                                padding:15px;
                                border-radius:6px;
                            ">

                            <strong>🔴 Réservations clôturées</strong>

                            <br><br>

                            Ce trajet part dans moins de
                            <strong>72 heures</strong>.

                            <br>

                            Les réservations sont désormais fermées.

                        </div>

                    @endif

                </div>

            @empty

                <div class="bg-white shadow rounded-lg p-6 text-center">

                    <h2 class="text-xl font-semibold">

                        Aucun trajet disponible pour le moment.

                    </h2>

                </div>

            @endforelse

        </div>

    </div>

</x-app-layout>