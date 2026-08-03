<x-app-layout>

    <div class="py-12">

        <div class="max-w-2xl mx-auto">

            <div class="bg-white p-8 rounded-lg shadow">

                <h1 class="text-3xl font-bold mb-6 text-center">
                    Paiement de votre réservation
                </h1>

                <div class="mb-6">

                    <p><strong>Trajet :</strong>
                        {{ $reservation->trajet->depart }}
                        →
                        {{ $reservation->trajet->arrivee }}
                    </p>

                    <p><strong>Date :</strong>
                        {{ $reservation->trajet->date_depart }}
                    </p>

                    <p><strong>Heure :</strong>
                        {{ $reservation->trajet->heure_depart }}
                    </p>

                    <p><strong>Nombre de places :</strong>
                        {{ $reservation->nombre_places }}
                    </p>

                    <p class="text-xl mt-4">

                        <strong>Total :</strong>

                        {{ $reservation->trajet->prix * $reservation->nombre_places }}
                        FCFA

                    </p>

                </div>

                <form method="POST" action="/paiement/store">

                    @csrf

                    <input
                        type="hidden"
                        name="reservation_id"
                        value="{{ $reservation->id }}">

                    <input
                        type="hidden"
                        name="montant"
                        value="{{ $reservation->trajet->prix * $reservation->nombre_places }}">

                    <div class="mb-5">

                        <label class="block font-semibold mb-2">

                            Moyen de paiement

                        </label>

                        <select
                            name="mode_paiement"
                            class="border w-full rounded p-2">

                            <option value="Wave">
                                Wave
                            </option>

                            <option value="Orange Money">
                                Orange Money
                            </option>

                            <option value="MTN Mobile Money">
                                MTN Mobile Money
                            </option>

                            <option value="Carte Bancaire">
                                Carte Bancaire
                            </option>

                        </select>

                    </div>

                    <button
                        type="submit"
                        style="
                            background:#16a34a;
                            color:white;
                            padding:12px 25px;
                            border-radius:6px;
                            font-weight:bold;
                            width:100%;
                            cursor:pointer;
                        ">

                        💳 Payer maintenant

                    </button>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>