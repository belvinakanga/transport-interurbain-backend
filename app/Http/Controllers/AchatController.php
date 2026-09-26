<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Achat;
use App\Models\Billet;
use App\Models\Reservation;
use App\Models\Trajet;
use App\Models\Siege;
use App\Models\Voyageur;

use Carbon\Carbon;

class AchatController extends Controller
{
    /**
     * ============================================================
     * LISTE DES ACHATS
     * ============================================================
     */
    public function index(Request $request)
    {
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Vous devez être connecté.',
                ], 401);
            }

            return redirect()->route('login');
        }

        $user = auth()->user();

        $query = Achat::with([
            'user',

            // Achat direct
            'trajet.agence',

            // Réservation
            'reservation.user',
            'reservation.trajet.agence',
            'reservation.voyageurs',
            'reservation.sieges',

            // Billets + voyageur
            'reservation.billets.voyageur',
        ]);

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */
        if (
            $user->role === 'admin'
            && !$request->expectsJson()
        ) {
            $achats = $query
                ->latest()
                ->paginate(10)
                ->withQueryString();

            return view(
                'admin.achats',
                compact('achats')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | AGENT
        |--------------------------------------------------------------------------
        */
        if (
            $user->role === 'agent'
            && !$request->expectsJson()
        ) {
            if (!$user->agence_id) {
                abort(
                    403,
                    'Votre compte agent n’est lié à aucune agence.'
                );
            }

            $query->where(function ($q) use ($user) {

                $q->whereHas(
                    'trajet',
                    function ($trajetQuery) use ($user) {
                        $trajetQuery->where(
                            'agence_id',
                            $user->agence_id
                        );
                    }
                )

                ->orWhereHas(
                    'reservation.trajet',
                    function ($trajetQuery) use ($user) {
                        $trajetQuery->where(
                            'agence_id',
                            $user->agence_id
                        );
                    }
                );
            });

            $achats = $query
                ->latest()
                ->paginate(10)
                ->withQueryString();

            return view(
                'admin.achats',
                compact('achats')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | VOYAGEUR / API
        |--------------------------------------------------------------------------
        */
        $achats = $query
            ->where(
                'user_id',
                $user->id
            )
            ->latest()
            ->get();

        return response()->json([
            'data' => $achats,
        ]);
    }


    /**
     * ============================================================
     * CRÉER UN ACHAT
     * ============================================================
     *
     * Deux possibilités :
     *
     * 1. Paiement d'une réservation existante
     *
     * 2. Achat direct
     *
     * IMPORTANT :
     *
     * La règle des 72 heures de réservation n'est PAS appliquée
     * ici pour bloquer l'achat direct.
     *
     * Dans les 72 heures :
     *
     * - réservation impossible
     * - achat direct possible
     * - achat non remboursable
     *
     * ============================================================
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | UTILISATEUR CONNECTÉ
        |--------------------------------------------------------------------------
        */
        if (!Auth::check()) {
            return response()->json([
                'message' =>
                    'Vous devez être connecté pour effectuer un achat.',
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        |
        | Pour un achat direct, les informations voyageurs sont
        | obligatoires.
        |
        */

        $validated = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | RÉSERVATION EXISTANTE
            |--------------------------------------------------------------------------
            */
            'reservation_id' => [
                'nullable',
                'integer',
                'exists:reservations,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | ACHAT DIRECT
            |--------------------------------------------------------------------------
            */
            'trajet_id' => [
                'required_without:reservation_id',
                'integer',
                'exists:trajets,id',
            ],

            'nombre_places' => [
                'required_without:reservation_id',
                'integer',
                'min:1',
            ],

            'sieges' => [
                'required_without:reservation_id',
                'array',
                'min:1',
            ],

            'sieges.*' => [
                'required',
                'integer',
                'distinct',
                'min:1',
            ],

            /*
            |--------------------------------------------------------------------------
            | VOYAGEURS
            |--------------------------------------------------------------------------
            |
            | Obligatoire pour un achat direct.
            |
            */
            'voyageurs' => [
                'required_without:reservation_id',
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
                'min:1',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | VARIABLES
        |--------------------------------------------------------------------------
        */

        $reservation = null;
        $trajet = null;
        $nombrePlaces = 0;
        $siegesDemandes = [];

/*
|--------------------------------------------------------------------------
| TRANSACTION
|--------------------------------------------------------------------------
*/
try {

    $result = DB::transaction(
        function () use (
            $validated,
            &$reservation,
            &$trajet,
            &$nombrePlaces,
            &$siegesDemandes
        ) {


       
                    /*
                    |--------------------------------------------------------------------------
                    | ============================================================
                    | CAS 1 : PAIEMENT D'UNE RÉSERVATION EXISTANTE
                    | ============================================================
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !empty(
                            $validated['reservation_id']
                        )
                    ) {

                        /*
                        |--------------------------------------------------------------------------
                        | VERROUILLER LA RÉSERVATION
                        |--------------------------------------------------------------------------
                        */
                        $reservation =
                            Reservation::with([
                                'trajet',
                                'voyageurs',
                                'sieges',
                            ])
                            ->lockForUpdate()
                            ->find(
                                $validated['reservation_id']
                            );

                        if (!$reservation) {
                            abort(
                                404,
                                'Réservation introuvable.'
                            );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | PROPRIÉTAIRE
                        |--------------------------------------------------------------------------
                        */
                        if (
                            (int) $reservation->user_id
                            !==
                            (int) Auth::id()
                        ) {
                            abort(
                                403,
                                'Vous n’êtes pas autorisé à acheter cette réservation.'
                            );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | STATUT
                        |--------------------------------------------------------------------------
                        */
                        $statut =
                            strtolower(
                                trim(
                                    (string)
                                    $reservation->statut
                                )
                            );

                        if (
                            $statut === 'annulée'
                            ||
                            $statut === 'annulee'
                            ||
                            $statut === 'expirée'
                            ||
                            $statut === 'expiree'
                        ) {
                            abort(
                                409,
                                'Cette réservation a été annulée ou expirée et ne peut plus être achetée.'
                            );
                        }

                        if ($statut === 'payée') {
                            abort(
                                409,
                                'Cette réservation a déjà été payée.'
                            );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | DOUBLE ACHAT
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
                        | TRAJET
                        |--------------------------------------------------------------------------
                        */
                        if (!$reservation->trajet) {
                            abort(
                                404,
                                'Le trajet associé à cette réservation est introuvable.'
                            );
                        }

                        $trajet =
                            $reservation->trajet;


                        $nombrePlaces =
                            (int)
                            $reservation->nombre_places;


                        /*
                        |--------------------------------------------------------------------------
                        | SIÈGES
                        |--------------------------------------------------------------------------
                        */
                        if (
                            $reservation
                                ->sieges
                                ->count()
                            <
                            $nombrePlaces
                        ) {
                            abort(
                                409,
                                'Les sièges associés à cette réservation sont introuvables.'
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | RÉCUPÉRER LES NUMÉROS DE SIÈGES
                        |--------------------------------------------------------------------------
                        */
                        $siegesDemandes =
                            $reservation
                                ->sieges
                                ->pluck('numero_siege')
                                ->map(
                                    fn ($siege) =>
                                        (int) $siege
                                )
                                ->values()
                                ->all();
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | ============================================================
                    | CAS 2 : ACHAT DIRECT
                    | ============================================================
                    |--------------------------------------------------------------------------
                    */

                    else {

                        /*
                        |--------------------------------------------------------------------------
                        | TRAJET
                        |--------------------------------------------------------------------------
                        */
                        $trajet =
                            Trajet::where(
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


                        /*
                        |--------------------------------------------------------------------------
                        | NOMBRE DE PLACES
                        |--------------------------------------------------------------------------
                        */
                        $nombrePlaces =
                            (int)
                            $validated['nombre_places'];


                        /*
                        |--------------------------------------------------------------------------
                        | SIÈGES DEMANDÉS
                        |--------------------------------------------------------------------------
                        */
                        $siegesDemandes =
                            collect(
                                $validated['sieges']
                            )
                            ->map(
                                fn ($siege) =>
                                    (int) $siege
                            )
                            ->unique()
                            ->values()
                            ->all();


                        /*
                        |--------------------------------------------------------------------------
                        | NOMBRE DE SIÈGES
                        |--------------------------------------------------------------------------
                        */
                        if (
                            count($siegesDemandes)
                            !==
                            $nombrePlaces
                        ) {
                            abort(
                                422,
                                'Le nombre de sièges sélectionnés ne correspond pas au nombre de voyageurs.'
                            );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | NOMBRE DE VOYAGEURS
                        |--------------------------------------------------------------------------
                        */
                        if (
                            count($validated['voyageurs'])
                            !==
                            $nombrePlaces
                        ) {
                            abort(
                                422,
                                'Le nombre de voyageurs ne correspond pas au nombre de places.'
                            );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | PLACES DISPONIBLES
                        |--------------------------------------------------------------------------
                        */
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
                        $sieges =
                            Siege::where(
                                'trajet_id',
                                $trajet->id
                            )
                            ->whereIn(
                                'numero_siege',
                                $siegesDemandes
                            )
                            ->lockForUpdate()
                            ->get();


                        /*
                        |--------------------------------------------------------------------------
                        | TOUS LES SIÈGES EXISTENT
                        |--------------------------------------------------------------------------
                        */
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
                        | DISPONIBILITÉ DES SIÈGES
                        |--------------------------------------------------------------------------
                        */
                        foreach (
                            $sieges as $siege
                        ) {

                            if (
                                $siege->statut
                                !==
                                'disponible'
                            ) {
                                abort(
                                    409,
                                    'Le siège N°'
                                    .
                                    $siege->numero_siege
                                    .
                                    ' vient d’être réservé ou acheté par un autre voyageur.'
                                );
                            }
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | CRÉER LA RÉSERVATION TECHNIQUE
                        |--------------------------------------------------------------------------
                        |
                        | IMPORTANT :
                        |
                        | Cette réservation est créée directement comme
                        | payée.
                        |
                        | Elle ne passe donc PAS par la règle des 72 h.
                        |
                        */
                        $reservation =
                            Reservation::create([
                                'user_id' =>
                                    Auth::id(),

                                'trajet_id' =>
                                    $trajet->id,

                                'nombre_places' =>
                                    $nombrePlaces,

                                'statut' =>
                                    'payée',
                            ]);


                        /*
                        |--------------------------------------------------------------------------
                        | CRÉER LES VOYAGEURS DE L'ACHAT DIRECT
                        |--------------------------------------------------------------------------
                        |
                        | C'EST LA CORRECTION PRINCIPALE.
                        |
                        | Les informations saisies dans :
                        |
                        | "Informations voyageurs"
                        |
                        | sont enregistrées ici.
                        |
                        */
                        foreach (
                            $validated['voyageurs']
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


                            /*
                            |--------------------------------------------------------------------------
                            | TROUVER LE SIÈGE DU VOYAGEUR
                            |--------------------------------------------------------------------------
                            */
                            $numeroSiege =
                                (int)
                                $voyageurData['siege'];

                            $siege =
                                $sieges->firstWhere(
                                    'numero_siege',
                                    $numeroSiege
                                );

                            if (!$siege) {
                                abort(
                                    404,
                                    'Le siège N°'
                                    .
                                    $numeroSiege
                                    .
                                    ' du voyageur '
                                    .
                                    $voyageurData['prenom']
                                    .
                                    ' '
                                    .
                                    $voyageurData['nom']
                                    .
                                    ' est introuvable.'
                                );
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | ASSOCIER SIÈGE ↔ VOYAGEUR
                            |--------------------------------------------------------------------------
                            */
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
                        | DIMINUER LES PLACES DISPONIBLES
                        |--------------------------------------------------------------------------
                        */
                        $trajet->places_disponibles =
                            max(
                                0,
                                $trajet->places_disponibles
                                -
                                $nombrePlaces
                            );

                        $trajet->save();
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DATE DE DÉPART
                    |--------------------------------------------------------------------------
                    */
                    $dateDepart =
                        Carbon::parse(
                            $trajet->date_depart
                            .
                            ' '
                            .
                            $trajet->heure_depart
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | TRAJET DÉJÀ PARTI
                    |--------------------------------------------------------------------------
                    */
                    if (
                        $dateDepart->isPast()
                    ) {
                        abort(
                            422,
                            'Impossible d’acheter un billet pour un trajet déjà parti.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | RÈGLE DES 72 HEURES
                    |--------------------------------------------------------------------------
                    |
                    | Cette partie NE BLOQUE PAS l'achat.
                    |
                    | Elle détermine seulement si le remboursement
                    | est possible.
                    |
                    */
                    $limite72h =
                        now()->addHours(72);

                    $remboursable =
                        $dateDepart->greaterThan(
                            $limite72h
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | MONTANT
                    |--------------------------------------------------------------------------
                    */
                   $prixBaseParBillet = (float) $trajet->prix;

$fraisTokendeParBillet = 100;
$commissionTokendeParBillet = 80;
$partAgenceParBillet = 20;

$montantBase = $prixBaseParBillet * $nombrePlaces;

$fraisTokende = $fraisTokendeParBillet * $nombrePlaces;
$commissionTokende = $commissionTokendeParBillet * $nombrePlaces;
$partAgence = $partAgenceParBillet * $nombrePlaces;

$montant = $montantBase + $fraisTokende;
/*
|--------------------------------------------------------------------------
| RÉFÉRENCE ACHAT
|--------------------------------------------------------------------------
|
| La référence définitive sera générée à partir
| de l'identifiant réel de l'achat.
|
| Exemple :
| ACH-2026-000001
| ACH-2026-000002
| ACH-2026-000003
|
| Une référence temporaire est utilisée au moment
| de la création car la colonne "reference" est obligatoire.
|
*/

$referenceTemporaire =
    'TMP-' .
    Str::uuid()->toString();


                    /*
                    |--------------------------------------------------------------------------
                    | DESCRIPTION
                    |--------------------------------------------------------------------------
                    */
                    $description =
                        'Achat de '
                        .
                        $nombrePlaces
                        .
                        (
                            $nombrePlaces > 1
                                ? ' billets'
                                : ' billet'
                        )
                        .
                        ' '
                        .
                        $trajet->depart
                        .
                        ' → '
                        .
                        $trajet->arrivee;


                   $achat = Achat::create([
    'user_id' => Auth::id(),
    'reservation_id' => $reservation->id,
    'trajet_id' => $trajet->id,

    'montant' => $montant,
    'montant_base' => $montantBase,
    'frais_tokende' => $fraisTokende,
    'commission_tokende' => $commissionTokende,
    'part_agence' => $partAgence,

    'description' => $description,
    'reference' => 'TMP-' . Str::uuid()->toString(),
    'statut' => 'payé',
    'remboursable' => $remboursable,
]);


/*
|--------------------------------------------------------------------------
| GÉNÉRER LA RÉFÉRENCE DÉFINITIVE
|--------------------------------------------------------------------------
|
| Exemple :
| ACH-2026-000001
| ACH-2026-000002
| ACH-2026-000003
|
| On utilise l'ID réel de l'achat.
|
*/

$achat->reference =
    'ACH-' .
    $achat->created_at->format('Y') .
    '-' .
    str_pad(
        $achat->id,
        6,
        '0',
        STR_PAD_LEFT
    );

$achat->saveQuietly();

                    /*
                    |--------------------------------------------------------------------------
                    | RÉSERVATION PAYÉE
                    |--------------------------------------------------------------------------
                    */
                    $reservation->update([
                        'statut' =>
                            'payée',
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | RÉCUPÉRER LES VOYAGEURS
                    |--------------------------------------------------------------------------
                    */
                    $reservation->load([
                        'voyageurs',
                        'sieges',
                    ]);

                    $voyageurs =
                        $reservation->voyageurs;


                    /*
                    |--------------------------------------------------------------------------
                    | CRÉER LES BILLETS
                    |--------------------------------------------------------------------------
                    |
                    | UN billet par voyageur.
                    |
                    | Chaque billet reçoit son propre voyageur_id.
                    |
                    */
                    $billets = collect();


                    if (
                        $voyageurs->count() > 0
                    ) {

                        foreach (
                            $voyageurs as $voyageur
                        ) {

                            /*
                            |--------------------------------------------------------------------------
                            | ÉVITER UN DOUBLE BILLET
                            |--------------------------------------------------------------------------
                            */
                            $billetExistant =
                                Billet::where(
                                    'reservation_id',
                                    $reservation->id
                                )
                                ->where(
                                    'voyageur_id',
                                    $voyageur->id
                                )
                                ->first();

                            if ($billetExistant) {

                                $billets->push(
                                    $billetExistant
                                );

                                continue;
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | TROUVER LE SIÈGE DU VOYAGEUR
                            |--------------------------------------------------------------------------
                            */
                            $siegeVoyageur =
                                $reservation
                                    ->sieges
                                    ->firstWhere(
                                        'voyageur_id',
                                        $voyageur->id
                                    );

                                    /*
|--------------------------------------------------------------------------
| QR CODE
|--------------------------------------------------------------------------
*/

$qrCode =
    'QR-' .
    strtoupper(
        Str::random(12)
    );


/*
|--------------------------------------------------------------------------
| CRÉER LE BILLET
|--------------------------------------------------------------------------
|
| Le numéro du billet est généré automatiquement
| par le modèle Billet.
|
*/

$billet =
    new Billet();

$billet->reservation_id =
    $reservation->id;

$billet->voyageur_id =
    $voyageur->id;

$billet->qr_code =
    $qrCode;

$billet->save();

$billets->push(
    $billet
);

                        }

                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | COMPATIBILITÉ ANCIENNES RÉSERVATIONS
                        |--------------------------------------------------------------------------
                        |
                        | Une ancienne réservation peut ne pas
                        | posséder de voyageurs.
                        |
                        */
                        for (
                            $i = 0;
                            $i < $nombrePlaces;
                            $i++
                        ) {

                           
                        /*
|--------------------------------------------------------------------------
| QR CODE
|--------------------------------------------------------------------------
*/

$qrCode =
    'QR-' .
    strtoupper(
        Str::random(12)
    );


/*
|--------------------------------------------------------------------------
| CRÉER LE BILLET
|--------------------------------------------------------------------------
|
| Le numéro du billet est généré automatiquement
| par le modèle Billet.
|
*/

$billet =
    new Billet();

$billet->reservation_id =
    $reservation->id;

$billet->voyageur_id =
    null;


                            $billet->qr_code =
                                $qrCode;

                            $billet->save();

                            $billets->push(
                                $billet
                            );
                        }
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | RECHARGER TOUTES LES RELATIONS
                    |--------------------------------------------------------------------------
                    */
                    $achat->load([
                        'user',

                        'trajet.agence',

                        'reservation.user',

                        'reservation.trajet.agence',

                        'reservation.voyageurs',

                        'reservation.sieges',

                        'reservation.billets.voyageur',
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | RETOUR
                    |--------------------------------------------------------------------------
                    */
                    return [
                        'achat' =>
                            $achat,

                        'billets' => $billets->map(function ($billet) {
    $billet->load('voyageur');
    return $billet;
}),
                    ];
                }
            );


            /*
            |--------------------------------------------------------------------------
            | RÉPONSE
            |--------------------------------------------------------------------------
            */
            return response()->json([
                'message' =>
                    'Achat créé avec succès.',

                'data' =>
                    $result['achat'],

                'billets' =>
                    $result['billets'],
            ], 201);


        } catch (
            \Symfony\Component\HttpKernel\Exception\HttpException $e
        ) {

            return response()->json([
                'message' =>
                    $e->getMessage(),
            ], $e->getStatusCode());


        } catch (
            \Throwable $e
        ) {

            return response()->json([
                'message' =>
                    'Une erreur est survenue lors de l’achat.',

                'error' =>
                    $e->getMessage(),
            ], 500);
        }
    }


    /**
     * ============================================================
     * AFFICHER UN ACHAT
     * ============================================================
     */
    public function show($id)
    {
        $achat =
            Achat::with([
                'user',

                'trajet.agence',

                'reservation.trajet.agence',

                'reservation.voyageurs',

                'reservation.sieges',

                'reservation.billets.voyageur',
            ])
            ->findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | SÉCURITÉ VOYAGEUR
        |--------------------------------------------------------------------------
        */
        if (
            auth()->check()
            &&
            auth()->user()->role === 'voyageur'
            &&
            (int) $achat->user_id
            !==
            (int) auth()->id()
        ) {
            abort(
                403,
                'Vous n’êtes pas autorisé à consulter cet achat.'
            );
        }


        return response()->json(
            $achat
        );
    }


    /**
     * ============================================================
     * SUPPRIMER UN ACHAT
     * ============================================================
     */
    public function destroy($id)
    {
        if (!auth()->check()) {
            return response()->json([
                'message' =>
                    'Vous devez être connecté.',
            ], 401);
        }


        $achat =
            Achat::with([
                'trajet',
                'reservation.trajet',
            ])
            ->findOrFail($id);


        $user =
            auth()->user();


        /*
        |--------------------------------------------------------------------------
        | VOYAGEUR
        |--------------------------------------------------------------------------
        */
        if (
            $user->role === 'voyageur'
        ) {

            if (
                (int) $achat->user_id
                !==
                (int) $user->id
            ) {
                abort(
                    403,
                    'Vous ne pouvez pas supprimer cet achat.'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | AGENT
        |--------------------------------------------------------------------------
        */
        if (
            $user->role === 'agent'
            &&
            $user->agence_id
        ) {

            $appartientAgence =
                false;


            /*
            |--------------------------------------------------------------------------
            | ACHAT DIRECT
            |--------------------------------------------------------------------------
            */
            if (
                $achat->trajet
                &&
                (int)
                    $achat->trajet->agence_id
                ===
                (int)
                    $user->agence_id
            ) {
                $appartientAgence =
                    true;
            }


            /*
            |--------------------------------------------------------------------------
            | ACHAT D'UNE RÉSERVATION
            |--------------------------------------------------------------------------
            */
            if (
                $achat->reservation
                &&
                $achat->reservation->trajet
                &&
                (int)
                    $achat
                        ->reservation
                        ->trajet
                        ->agence_id
                ===
                (int)
                    $user->agence_id
            ) {
                $appartientAgence =
                    true;
            }


            if (!$appartientAgence) {
                abort(
                    403,
                    'Vous ne pouvez pas supprimer cet achat.'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | SUPPRESSION
        |--------------------------------------------------------------------------
        */
        $achat->delete();


        return response()->json([
            'message' =>
                'Achat supprimé avec succès.',
        ]);
    }
}