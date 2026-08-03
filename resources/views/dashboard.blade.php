<x-app-layout>

<div class="py-12">

<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

    <!-- MESSAGE SUCCÈS -->

    @if(session('success'))

        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">

            {{ session('success') }}

        </div>

    @endif

    <!-- MESSAGE ERREUR -->

    @if(session('error'))

        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">

            {{ session('error') }}

        </div>

    @endif


    <!-- PROFIL UTILISATEUR -->

    <div class="bg-white p-6 rounded shadow">

        <h2 class="text-2xl font-bold">
            Bienvenue {{ $user->name }}
        </h2>

        <p>
            Email : {{ $user->email }}
        </p>

    </div>


    <!-- RESERVATIONS -->

    <div class="bg-white mt-6 p-6 rounded shadow">

        <h3 class="text-xl font-bold mb-4">
            Mes réservations
        </h3>

        @forelse($reservations as $reservation)

            <div class="border p-3 mb-3 rounded">

                <p>
                    <strong>Trajet :</strong>
                    {{ $reservation->trajet->depart ?? '' }}
                    -
                    {{ $reservation->trajet->arrivee ?? '' }}
                </p>

                <p>
                    <strong>Places :</strong>
                    {{ $reservation->nombre_places }}
                </p>

                <p>
                    <strong>Statut :</strong>
                    {{ $reservation->statut }}
                </p>

                @if($reservation->statut != 'annulée')

                    <form
                        action="/reservations/{{ $reservation->id }}/annuler"
                        method="POST"
                        class="mt-3">

                        @csrf
                        @method('PUT')

                        <button
                            type="submit"
                            class="bg-red-600 text-white px-4 py-2 rounded">

                            Annuler la réservation

                        </button>

                    </form>

                @endif

            </div>

        @empty

            <p>
                Aucune réservation pour le moment
            </p>

        @endforelse

    </div>


    <!-- BILLETS -->

    <div class="bg-white mt-6 p-6 rounded shadow">

        <h3 class="text-xl font-bold">
            Mes billets
        </h3>

        @forelse($billets as $billet)

            <p>
                <strong>Numéro :</strong>
                {{ $billet->numero_billet }}
            </p>

            <p>
                <strong>QR :</strong>
                {{ $billet->qr_code }}
            </p>

            <hr class="my-3">

        @empty

            <p>
                Aucun billet disponible
            </p>

        @endforelse

    </div>


    <!-- PAIEMENTS -->

    <div class="bg-white mt-6 p-6 rounded shadow">

        <h3 class="text-xl font-bold">
            Mes paiements
        </h3>

        @forelse($paiements as $paiement)

            <p>
                <strong>Montant :</strong>
                {{ $paiement->montant }} FCFA
            </p>

            <p>
                <strong>Statut :</strong>
                {{ $paiement->statut }}
            </p>

            <hr class="my-3">

        @empty

            <p>
                Aucun paiement
            </p>

        @endforelse

    </div>

</div>

</div>

</x-app-layout>