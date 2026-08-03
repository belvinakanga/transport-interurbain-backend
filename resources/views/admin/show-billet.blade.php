<x-layouts.admin>

<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-xl shadow-lg p-8">

        <h1 class="text-3xl font-bold mb-8">
            🎟 Détails du billet
        </h1>

        <div class="space-y-5">

            <p>
                <strong>N° Billet :</strong>
                {{ $billet->numero_billet }}
            </p>

            <p>
                <strong>QR Code :</strong>
                {{ $billet->qr_code }}
            </p>

            <p>
                <strong>Voyageur :</strong>
                {{ $billet->reservation?->user?->name ?? 'Utilisateur supprimé' }}
            </p>

            <p>
                <strong>Agence :</strong>
                {{ $billet->reservation?->trajet?->agence?->nom_agence ?? '-' }}
            </p>

            <p>
                <strong>Départ :</strong>
                {{ $billet->reservation?->trajet?->depart ?? '-' }}
            </p>

            <p>
                <strong>Arrivée :</strong>
                {{ $billet->reservation?->trajet?->arrivee ?? '-' }}
            </p>

            <p>
                <strong>Date :</strong>
                {{ $billet->reservation?->trajet?->date_depart ?? '-' }}
            </p>

            <p>
                <strong>Heure :</strong>
                {{ $billet->reservation?->trajet?->heure_depart ?? '-' }}
            </p>

            <p>
                <strong>Prix :</strong>
                {{ $billet->reservation?->trajet?->prix ?? '-' }} FCFA
            </p>

            <p>
                <strong>Statut :</strong>
                {{ $billet->reservation?->statut ?? 'Réservation supprimée' }}
            </p>

        </div>

        <div class="flex gap-4 mt-10">

            <a href="{{ url('/admin/billets') }}"
               class="bg-gray-700 text-white px-6 py-3 rounded-lg">
                ⬅ Retour
            </a>

            <button onclick="window.print()"
                    class="bg-green-600 text-white px-6 py-3 rounded-lg">
                🖨 Imprimer
            </button>

        </div>

    </div>

</div>

</x-layouts.admin>