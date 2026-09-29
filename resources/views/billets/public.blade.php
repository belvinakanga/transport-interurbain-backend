<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Billet {{ $billet->numero_billet }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .ticket {
                box-shadow: none !important;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen py-10">

    <div class="max-w-3xl mx-auto px-4">

        <div class="ticket bg-white rounded-2xl shadow-lg p-8">

            <!-- EN-TÊTE -->
            <div class="text-center border-b pb-6 mb-6">

                <h1 class="text-3xl font-bold">
                    🎫 Billet de voyage
                </h1>

                <p class="text-gray-500 mt-2">
                    TOKENDÉ
                </p>

            </div>


            <!-- AGENCE -->
            <div class="mb-6">

                <h2 class="text-xl font-bold">
                    {{ $billet->reservation?->trajet?->agence?->nom_agence ?? 'Agence' }}
                </h2>

                @if($billet->reservation?->trajet?->agence?->adresse)
                    <p class="text-gray-600 mt-1">
                        📍 Adresse : {{ $billet->reservation->trajet->agence->adresse }}
                    </p>
                @endif

            </div>


            <!-- TRAJET -->
            <div class="bg-gray-50 rounded-xl p-5 mb-6">

                <h3 class="font-semibold text-lg mb-4">
                    🚌 Trajet
                </h3>

                <div class="grid grid-cols-2 gap-4">

                    <div>
                        <p class="text-sm text-gray-500">
                            Départ
                        </p>

                        <p class="font-semibold">
                            {{ $billet->reservation?->trajet?->depart ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">
                            Arrivée
                        </p>

                        <p class="font-semibold">
                            {{ $billet->reservation?->trajet?->arrivee ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">
                            Date
                        </p>

                        <p class="font-semibold">
                            {{ $billet->reservation?->trajet?->date_depart ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">
                            Heure
                        </p>

                        <p class="font-semibold">
                            {{ $billet->reservation?->trajet?->heure_depart ?? '-' }}
                        </p>
                    </div>

                </div>

            </div>


            <!-- INFORMATIONS VOYAGEUR -->
            <div class="mb-6">

                <h3 class="font-semibold text-lg mb-4">
                    👤 Informations du voyageur
                </h3>

                <div class="space-y-3">

                    <p>
                        <strong>Voyageur :</strong>
                        {{ $billet->reservation?->user?->name ?? 'Utilisateur supprimé' }}
                    </p>

                    <p>
                        <strong>Siège :</strong>
                        {{ $billet->siege?->numero_siege ?? '-' }}
                    </p>

                    <p>
                        <strong>N° Billet :</strong>
                        {{ $billet->numero_billet }}
                    </p>

                </div>

            </div>


            <!-- QR CODE -->
            <div class="border-t pt-6 text-center">

                <h3 class="font-semibold text-lg mb-4">
                    🔳 QR Code
                </h3>

                <div class="flex justify-center mb-4">

                    <div class="border rounded-xl p-4 bg-white">

                        {!! QrCode::size(220)->generate(
                            url('/billet/' . $billet->qr_code)
                        ) !!}

                    </div>

                </div>

                <p class="text-sm text-gray-500">
                    Scannez ce QR code pour consulter ce billet.
                </p>

            </div>


            <!-- MESSAGE CONTRÔLE -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mt-6 text-center">

                <p class="font-semibold text-yellow-800">
                    ⚠️ Présentez ce billet à l’agent lors du contrôle.
                </p>

            </div>


            <!-- BOUTON IMPRIMER -->
            <div class="flex justify-center mt-8 no-print">

                <button
                    onclick="window.print()"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold"
                >
                    🖨 Imprimer mon billet
                </button>

            </div>

        </div>

    </div>

</body>
</html>