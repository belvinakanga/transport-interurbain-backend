<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation;
use App\Models\Trajet;
use App\Models\Voyageur;
use App\Models\Siege;
use App\Models\Achat;
use App\Models\Billet;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | MES RÉSERVATIONS
    |--------------------------------------------------------------------------
    */

    public function index()
{
    if (!auth()->check()) {
        return response()->json([
            'message' => 'Vous devez être connecté.',
        ], 401);
    }

    $reservations = Reservation::with([
    'trajet.agence',
    'sieges',
    'user',
    'voyageurs',
])->where('user_id', auth()->id())
  ->latest()
  ->get();

    return response()->json([
        'data' => $reservations,
    ]);
}

    /*
    |--------------------------------------------------------------------------
    | CRÉER UNE RÉSERVATION
    |--------------------------------------------------------------------------
    |
    | Une réservation est possible uniquement si le départ
    | est à PLUS DE 72 heures.
    |
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trajet_id' => [
                'required',
                'integer',
                'exists:trajets,id',
            ],

            'nombre_places' => [
                'required',
                'integer',
                'min:1',
            ],

            'sieges' => [
                'required',
                'array',
                'min:1',
            ],

            'sieges.*' => [
                'required',
                'integer',
                'distinct',
            ],

            'voyageurs' => [
                'required',
                'array',
                'min:1',
            ],

            'voyageurs.*.prenom' => [
                'required',
                'string',
                'max:100',
            ],

            'voyageurs.*.nom' => [
                'required',
                'string',
                'max:100',
            ],

            'voyageurs.*.email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'voyageurs.*.telephone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'voyageurs.*.siege' => [
                'required',
                'integer',
            ],
        ]);

        if (!auth()->check()) {
            return response()->json([
                'message' =>
                    'Vous devez être connecté pour effectuer une réservation.',
            ], 401);
        }

        $trajet = Trajet::find(
            $validated['trajet_id']
        );

        if (!$trajet) {
            return response()->json([
                'message' => 'Trajet introuvable.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | RÈGLE DES 72 HEURES
        |--------------------------------------------------------------------------
        */

        $dateDepart = Carbon::parse(
            $trajet->date_depart . ' ' . $trajet->heure_depart
        );

        $limiteReservation = now()->addHours(72);

        if ($dateDepart->lessThanOrEqualTo($limiteReservation)) {
            return response()->json([
                'message' =>
                    'La réservation n’est plus possible. Le départ est prévu dans 72 heures ou moins.',
                'date_depart' =>
                    $dateDepart->toDateTimeString(),
                'reservation_possible' => false,
            ], 422);
        }

        $nombrePlaces =
            (int) $validated['nombre_places'];

        $siegesDemandes =
            $validated['sieges'];

        $voyageurs =
            $validated['voyageurs'];

        if (count($siegesDemandes) !== $nombrePlaces) {
            return response()->json([
                'message' =>
                    'Le nombre de sièges sélectionnés ne correspond pas au nombre de places.',
            ], 422);
        }

        if (count($voyageurs) !== $nombrePlaces) {
            return response()->json([
                'message' =>
                    'Le nombre de voyageurs ne correspond pas au nombre de places.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | VÉRIFIER LES SIÈGES DES VOYAGEURS
        |--------------------------------------------------------------------------
        */

        $siegesVoyageurs = collect($voyageurs)
            ->pluck('siege')
            ->map(fn ($siege) => (int) $siege)
            ->values()
            ->all();

        if (
            count(array_unique($siegesVoyageurs))
            !==
            $nombrePlaces
        ) {
            return response()->json([
                'message' =>
                    'Chaque voyageur doit avoir un siège différent.',
            ], 422);
        }

        $siegesDemandesNormalises = collect(
            $siegesDemandes
        )
            ->map(fn ($siege) => (int) $siege)
            ->sort()
            ->values()
            ->all();

        $siegesVoyageursNormalises = collect(
            $siegesVoyageurs
        )
            ->sort()
            ->values()
            ->all();

        if (
            $siegesDemandesNormalises
            !==
            $siegesVoyageursNormalises
        ) {
            return response()->json([
                'message' =>
                    'Les sièges sélectionnés ne correspondent pas aux sièges des voyageurs.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        try {

            $result = DB::transaction(
                function () use (
                    $validated,
                    $nombrePlaces,
                    $siegesDemandes,
                    $voyageurs
                ) {

                    $trajet = Trajet::where(
                        'id',
                        $validated['trajet_id']
                    )
                        ->lockForUpdate()
                        ->first();

                    if (!$trajet) {
                        abort(
                            404,
                            'Trajet introuvable.'
                        );
                    }

                    if (
                        $trajet->places_disponibles
                        <
                        $nombrePlaces
                    ) {
                        abort(
                            409,
                            'Il ne reste pas suffisamment de places disponibles pour ce trajet.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | RÉCUPÉRER LES SIÈGES
                    |--------------------------------------------------------------------------
                    */

                    $sieges = Siege::where(
                        'trajet_id',
                        $trajet->id
                    )
                        ->whereIn(
                            'numero_siege',
                            $siegesDemandes
                        )
                        ->lockForUpdate()
                        ->get();

                    if (
                        $sieges->count()
                        !==
                        $nombrePlaces
                    ) {
                        abort(
                            404,
                            'Un ou plusieurs sièges sélectionnés n’existent pas pour ce trajet.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | VÉRIFIER DISPONIBILITÉ
                    |--------------------------------------------------------------------------
                    */

                    foreach ($sieges as $siege) {

                        if (
                            $siege->statut
                            !==
                            'disponible'
                        ) {
                            abort(
                                409,
                                'Le siège ' .
                                $siege->numero_siege .
                                ' vient d’être réservé par un autre voyageur.'
                            );
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CRÉER RÉSERVATION
                    |--------------------------------------------------------------------------
                    */

                    $reservation =
                        Reservation::create([
                            'user_id' =>
                                auth()->id(),

                            'trajet_id' =>
                                $trajet->id,

                            'nombre_places' =>
                                $nombrePlaces,

                            'statut' =>
                                'en attente',
                        ]);

                    /*
                    |--------------------------------------------------------------------------
                    | CRÉER VOYAGEURS ET OCCUPER SIÈGES
                    |--------------------------------------------------------------------------
                    */

                    foreach (
                        $voyageurs
                        as $voyageurData
                    ) {

                        $voyageur =
                            Voyageur::create([
                                'reservation_id' =>
                                    $reservation->id,

                                'prenom' =>
                                    $voyageurData['prenom'],

                                'nom' =>
                                    $voyageurData['nom'],

                                'email' =>
                                    $voyageurData['email']
                                    ?? null,

                                'telephone' =>
                                    $voyageurData['telephone']
                                    ?? null,
                            ]);

                        $siege =
                            $sieges->firstWhere(
                                'numero_siege',
                                (int) $voyageurData['siege']
                            );

                        if (!$siege) {
                            abort(
                                404,
                                'Le siège du voyageur est introuvable.'
                            );
                        }

                        $siege->statut =
                            'occupe';

                        $siege->reservation_id =
                            $reservation->id;

                        $siege->voyageur_id =
                            $voyageur->id;

                        $siege->save();
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | DIMINUER PLACES
                    |--------------------------------------------------------------------------
                    */

                    $trajet->places_disponibles =
                        $trajet->places_disponibles
                        -
                        $nombrePlaces;

                    $trajet->save();

                    /*
                    |--------------------------------------------------------------------------
                    | CHARGER LES DONNÉES COMPLÈTES
                    |--------------------------------------------------------------------------
                    |
                    | IMPORTANT :
                    | On charge également trajet.agence afin que Flutter
                    | puisse afficher le nom de l'agence immédiatement.
                    |
                    */

                    return $reservation->load([
                        'user',
                        'trajet.agence',
                        'voyageurs',
                        'sieges',
                        'achat',
                    ]);
                }
            );

            return response()->json([
                'message' =>
                    'Réservation créée avec succès.',

                'data' =>
                    $result,

            ], 201);

        } catch (
            \Symfony\Component\HttpKernel\Exception\HttpException $e
        ) {

            return response()->json([
                'message' =>
                    $e->getMessage(),

            ], $e->getStatusCode());

        } catch (\Throwable $e) {

            return response()->json([
                'message' =>
                    'Une erreur est survenue lors de la réservation.',

                'error' =>
                    $e->getMessage(),

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | AFFICHER UNE RÉSERVATION
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Vous devez être connecté.',
            ], 401);
        }

        $reservation =
            Reservation::with([
                'user',
                'trajet.agence',
                'voyageurs',
                'sieges',
                'achat',
                'paiements',
                'billets',
            ])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return response()->json(
            $reservation
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PAYER UNE RÉSERVATION
    |--------------------------------------------------------------------------
    |
    | IMPORTANT :
    | Cette méthode est différente de l'achat direct.
    |
    | Le siège est déjà occupé par la réservation.
    | On ne doit donc PAS vérifier que le siège est
    | "disponible".
    |
    */

    public function payer($id)
    {
        if (!auth()->check()) {
            return response()->json([
                'message' =>
                    'Vous devez être connecté pour payer cette réservation.',
            ], 401);
        }

        try {

            $result = DB::transaction(
                function () use ($id) {

                    /*
                    |--------------------------------------------------------------------------
                    | RÉCUPÉRER LA RÉSERVATION DU VOYAGEUR
                    |--------------------------------------------------------------------------
                    */

                    $reservation =
                        Reservation::where(
                            'id',
                            $id
                        )
                        ->where(
                            'user_id',
                            auth()->id()
                        )
                        ->lockForUpdate()
                        ->first();

                    if (!$reservation) {
                        abort(
                            404,
                            'Réservation introuvable ou vous n’êtes pas autorisé à la payer.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | VÉRIFIER STATUT
                    |--------------------------------------------------------------------------
                    */

                    if (
                        strtolower(
                            trim($reservation->statut)
                        )
                        !==
                        'en attente'
                    ) {

                        if (
                            strtolower(
                                trim($reservation->statut)
                            )
                            ===
                            'payée'
                        ) {
                            abort(
                                409,
                                'Cette réservation a déjà été payée.'
                            );
                        }

                        abort(
                            409,
                            'Cette réservation ne peut plus être payée.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | TRAJET
                    |--------------------------------------------------------------------------
                    */

                    $trajet =
                        Trajet::findOrFail(
                            $reservation->trajet_id
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | SIÈGES DE LA RÉSERVATION
                    |--------------------------------------------------------------------------
                    */

                    $sieges =
                        Siege::where(
                            'reservation_id',
                            $reservation->id
                        )
                        ->lockForUpdate()
                        ->get();

                    if (
                        $sieges->count()
                        !==
                        (int) $reservation->nombre_places
                    ) {
                        abort(
                            409,
                            'Les sièges de cette réservation sont introuvables.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | VÉRIFIER QUE LES SIÈGES APPARTIENNENT
                    | BIEN À CETTE RÉSERVATION
                    |--------------------------------------------------------------------------
                    */

                    foreach ($sieges as $siege) {

                        if (
                            (int) $siege->trajet_id
                            !==
                            (int) $reservation->trajet_id
                        ) {
                            abort(
                                409,
                                'Un siège de cette réservation ne correspond pas au trajet.'
                            );
                        }

                        if (
                            $siege->reservation_id
                            !=
                            $reservation->id
                        ) {
                            abort(
                                409,
                                'Un siège ne correspond pas à cette réservation.'
                            );
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CALCUL DU MONTANT
                    |--------------------------------------------------------------------------
                    */

                    $montant =
                        (float) $trajet->prix
                        *
                        (int) $reservation->nombre_places;

                    /*
                    |--------------------------------------------------------------------------
                    | RÈGLE DES 72 HEURES POUR LE REMBOURSEMENT
                    |--------------------------------------------------------------------------
                    */

                    $dateDepart =
                        Carbon::parse(
                            $trajet->date_depart .
                            ' ' .
                            $trajet->heure_depart
                        );

                    $remboursable =
                        $dateDepart->greaterThan(
                            now()->addHours(72)
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | ÉVITER UN DOUBLE ACHAT
                    |--------------------------------------------------------------------------
                    */

                    $achatExistant =
                        Achat::where(
                            'reservation_id',
                            $reservation->id
                        )
                        ->lockForUpdate()
                        ->first();

                    if ($achatExistant) {
                        abort(
                            409,
                            'Cette réservation possède déjà un achat.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | RÉFÉRENCE
                    |--------------------------------------------------------------------------
                    */

                    $reference =
                        'INT-' .
                        now()->format(
                            'YmdHis'
                        ) .
                        '-' .
                        strtoupper(
                            substr(
                                bin2hex(
                                    random_bytes(4)
                                ),
                                0,
                                6
                            )
                        );
 /*
|--------------------------------------------------------------------------
| CRÉER L'ACHAT
|--------------------------------------------------------------------------
*/

$achat = Achat::create([
    'user_id' => auth()->id(),

    'reservation_id' => $reservation->id,

    // ✅ AJOUT IMPORTANT
    'trajet_id' => $reservation->trajet_id,

    'montant' => $montant,

    'description' =>
        'Achat de ' .
        $reservation->nombre_places .
        ' billet(s) ' .
        $trajet->depart .
        ' → ' .
        $trajet->arrivee,

    'reference' => $reference,

    'statut' => 'payé',

    'remboursable' => $remboursable,
]);

                   
                    /*
                    |--------------------------------------------------------------------------
                    | METTRE À JOUR LA RÉSERVATION
                    |--------------------------------------------------------------------------
                    */

                    $reservation->statut =
                        'payée';

                    $reservation->save();

                    /*
|--------------------------------------------------------------------------
| CRÉER AUTOMATIQUEMENT LE BILLET
|--------------------------------------------------------------------------
|
| Le billet est créé uniquement après confirmation
| du paiement.
|
*/

/*
|--------------------------------------------------------------------------
| CRÉER UN BILLET POUR CHAQUE VOYAGEUR
|--------------------------------------------------------------------------
|
| Chaque passager saisi dans "Informations voyageurs"
| reçoit son propre billet.
|
| IMPORTANT :
| La règle des 72 heures n'est pas modifiée ici.
|
*/

$reservation->load([
    'voyageurs',
    'sieges',
]);

foreach ($reservation->voyageurs as $voyageur) {

    // Éviter de créer deux fois le même billet
    $billetExistant = Billet::where(
        'reservation_id',
        $reservation->id
    )
        ->where(
            'voyageur_id',
            $voyageur->id
        )
        ->first();

    if ($billetExistant) {
        continue;
    }

    /*
    |--------------------------------------------------------------------------
    | CRÉER LE BILLET DU VOYAGEUR
    |--------------------------------------------------------------------------
    */

    Billet::create([
    'reservation_id' => $reservation->id,
    'voyageur_id' => $voyageur->id,
    'qr_code' => 'QR-' . strtoupper(
        substr(
            bin2hex(random_bytes(8)),
            0,
            12
        )
    ),
]);
}
                    
                    /*
                    |--------------------------------------------------------------------------
                    | RETOURNER LES DONNÉES
                    |--------------------------------------------------------------------------
                    */

                    return $achat->load([
    'user',
    'reservation',
    'reservation.trajet.agence',
    'reservation.voyageurs',
    'reservation.sieges',
    'reservation.billets.voyageur',
]);
                }
            );

            return response()->json([
                'message' =>
                    'Paiement de la réservation confirmé avec succès.',

                'data' =>
                    $result,

            ], 200);

        } catch (
            \Symfony\Component\HttpKernel\Exception\HttpException $e
        ) {

            return response()->json([
                'message' =>
                    $e->getMessage(),

            ], $e->getStatusCode());

        } catch (\Throwable $e) {

            return response()->json([
                'message' =>
                    'Une erreur est survenue lors du paiement de la réservation.',

                'error' =>
                    $e->getMessage(),

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ANNULER UNE RÉSERVATION
    |--------------------------------------------------------------------------
    */

    public function annuler($id)
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Vous devez être connecté.',
            ], 401);
        }

        $reservation =
            Reservation::with([
                'trajet',
                'sieges',
            ])
            ->where(
                'user_id',
                auth()->id()
            )
            ->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | NE PAS ANNULER UNE RÉSERVATION DÉJÀ PAYÉE
        |--------------------------------------------------------------------------
        */

        if (
            strtolower(
                trim($reservation->statut)
            )
            ===
            'payée'
        ) {
            return response()->json([
                'message' =>
                    'Cette réservation est déjà payée. Elle ne peut plus être annulée comme une réservation en attente.',
            ], 409);
        }

        /*
        |--------------------------------------------------------------------------
        | DÉJÀ ANNULÉE
        |--------------------------------------------------------------------------
        */

        if (
            $reservation->statut
            ===
            'annulée'
        ) {

            return response()->json([
                'message' =>
                    'Cette réservation est déjà annulée.',
            ], 409);
        }

        $trajet =
            Trajet::findOrFail(
                $reservation->trajet_id
            );

        /*
        |--------------------------------------------------------------------------
        | RÈGLE DES 72 HEURES
        |--------------------------------------------------------------------------
        */

        $dateDepart =
            Carbon::parse(
                $trajet->date_depart .
                ' ' .
                $trajet->heure_depart
            );

        if (
            $dateDepart->lessThanOrEqualTo(
                now()->addHours(72)
            )
        ) {

            return response()->json([
                'message' =>
                    'Impossible d’annuler une réservation dans les 72 heures avant le départ.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $reservation,
                $trajet
            ) {

                foreach (
                    $reservation->sieges
                    as $siege
                ) {

                    $siege->statut =
                        'disponible';

                    $siege->reservation_id =
                        null;

                    $siege->voyageur_id =
                        null;

                    $siege->save();
                }

                $trajet->places_disponibles =
                    $trajet->places_disponibles
                    +
                    $reservation->nombre_places;

                if (
                    $trajet->places_disponibles
                    >
                    $trajet->places_totales
                ) {
                    $trajet->places_disponibles =
                        $trajet->places_totales;
                }

                $trajet->save();

                $reservation->statut =
                    'annulée';

                $reservation->save();
            }
        );

        return response()->json([
            'message' =>
                'Réservation annulée avec succès.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SUPPRIMER UNE RÉSERVATION
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Vous devez être connecté.',
            ], 401);
        }

        $reservation =
            Reservation::where(
                'user_id',
                auth()->id()
            )
            ->findOrFail($id);

        DB::transaction(
            function () use (
                $reservation
            ) {

                $trajet =
                    Trajet::find(
                        $reservation->trajet_id
                    );

                /*
                |--------------------------------------------------------------------------
                | NE RESTAURER LES PLACES QUE SI
                | LA RÉSERVATION N'ÉTAIT PAS PAYÉE
                |--------------------------------------------------------------------------
                */

                if (
                    strtolower(
                        trim($reservation->statut)
                    )
                    !==
                    'payée'
                ) {

                    if ($trajet) {

                        $trajet->places_disponibles =
                            $trajet->places_disponibles
                            +
                            $reservation->nombre_places;

                        if (
                            $trajet->places_disponibles
                            >
                            $trajet->places_totales
                        ) {
                            $trajet->places_disponibles =
                                $trajet->places_totales;
                        }

                        $trajet->save();
                    }

                    Siege::where(
                        'reservation_id',
                        $reservation->id
                    )->update([
                        'statut' =>
                            'disponible',

                        'reservation_id' =>
                            null,

                        'voyageur_id' =>
                            null,
                    ]);
                }

                $reservation->delete();
            }
        );

        return response()->json([
            'message' =>
                'Réservation supprimée avec succès.',
        ]);
    }
}