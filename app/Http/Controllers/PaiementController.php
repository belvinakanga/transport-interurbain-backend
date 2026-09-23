<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Paiement;
use App\Models\Billet;
use App\Models\Reservation;
use App\Models\Achat;
use App\Models\Trajet;

class PaiementController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Liste des paiements
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $paiements = Paiement::with([
            'reservation.user',
            'reservation.trajet',
            'reservation.voyageurs',
            'reservation.billets',
        ])->latest()->get();

        return response()->json($paiements);
    }

    /*
    |--------------------------------------------------------------------------
    | Afficher le formulaire de paiement
    |--------------------------------------------------------------------------
    */
    public function create($reservation)
    {
        $reservation = Reservation::with([
            'trajet',
            'voyageurs',
            'sieges',
        ])->findOrFail($reservation);

        return view('paiements.create', compact('reservation'));
    }

    /*
    |--------------------------------------------------------------------------
    | Effectuer le paiement
    |--------------------------------------------------------------------------
    |
    | CHAÎNE :
    |
    | Paiement
    |     ↓
    | Achat
    |     ↓
    | Réservation payée
    |     ↓
    | Billet(s)
    |     ↓
    | Voyageur correspondant
    |
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'montant' => 'required|numeric|min:1',
            'mode_paiement' => 'required',
        ]);

        try {
            $result = DB::transaction(function () use ($request) {

                /*
                |--------------------------------------------------------------------------
                | 1. RÉCUPÉRER LA RÉSERVATION
                |--------------------------------------------------------------------------
                */

                $reservation = Reservation::with([
                    'trajet',
                    'voyageurs',
                    'sieges',
                ])
                ->lockForUpdate()
                ->findOrFail($request->reservation_id);


                /*
                |--------------------------------------------------------------------------
                | 2. VÉRIFIER L'UTILISATEUR CONNECTÉ
                |--------------------------------------------------------------------------
                */

                if (
                    Auth::check() &&
                    (int) $reservation->user_id !== (int) Auth::id()
                ) {
                    abort(
                        403,
                        'Vous n’êtes pas autorisé à payer cette réservation.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | 3. NE PAS PAYER DEUX FOIS
                |--------------------------------------------------------------------------
                */

                $statutReservation = strtolower(
                    trim((string) $reservation->statut)
                );

                if ($statutReservation === 'payée') {
                    abort(
                        409,
                        'Cette réservation a déjà été payée.'
                    );
                }

                if (
                    $statutReservation !== '' &&
                    $statutReservation !== 'en attente'
                ) {
                    abort(
                        409,
                        'Cette réservation ne peut plus être payée.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | 4. RÉCUPÉRER LE TRAJET
                |--------------------------------------------------------------------------
                */

                $trajet = Trajet::findOrFail(
                    $reservation->trajet_id
                );


                /*
                |--------------------------------------------------------------------------
                | 5. VÉRIFIER LES SIÈGES
                |--------------------------------------------------------------------------
                */

                $sieges = $reservation->sieges;

                if (
                    $sieges->count() !==
                    (int) $reservation->nombre_places
                ) {
                    abort(
                        409,
                        'Les sièges de cette réservation sont introuvables.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | 6. VÉRIFIER QUE LES SIÈGES APPARTIENNENT
                |    BIEN À LA RÉSERVATION ET AU TRAJET
                |--------------------------------------------------------------------------
                */

                foreach ($sieges as $siege) {

                    if (
                        (int) $siege->trajet_id !==
                        (int) $reservation->trajet_id
                    ) {
                        abort(
                            409,
                            'Un siège de cette réservation ne correspond pas au trajet.'
                        );
                    }

                    if (
                        (int) $siege->reservation_id !==
                        (int) $reservation->id
                    ) {
                        abort(
                            409,
                            'Un siège ne correspond pas à cette réservation.'
                        );
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | 7. CALCUL DU MONTANT
                |--------------------------------------------------------------------------
                */

                $montantCalcule =
                    (float) $trajet->prix *
                    (int) $reservation->nombre_places;


                /*
                |--------------------------------------------------------------------------
                | 8. VÉRIFICATION DU MONTANT
                |--------------------------------------------------------------------------
                |
                | On garde la logique existante tout en évitant
                | qu'un montant incohérent soit enregistré.
                |
                |--------------------------------------------------------------------------
                */

                if (
                    abs(
                        (float) $request->montant -
                        $montantCalcule
                    ) > 0.01
                ) {
                    abort(
                        422,
                        'Le montant du paiement ne correspond pas au montant de la réservation.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | 9. RÈGLE DES 72 HEURES
                |--------------------------------------------------------------------------
                |
                | Cette règle concerne le remboursement.
                | Elle reste inchangée :
                |
                | remboursable = départ dans PLUS de 72 heures.
                |
                |--------------------------------------------------------------------------
                */

                $dateDepart = Carbon::parse(
                    $trajet->date_depart .
                    ' ' .
                    $trajet->heure_depart
                );

                $remboursable = $dateDepart->greaterThan(
                    now()->addHours(72)
                );


                /*
                |--------------------------------------------------------------------------
                | 10. VÉRIFIER QU'IL N'EXISTE PAS DÉJÀ UN ACHAT
                |--------------------------------------------------------------------------
                */

                $achatExistant = Achat::where(
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
                | 11. CRÉER LE PAIEMENT
                |--------------------------------------------------------------------------
                */

                $paiement = Paiement::create([
                    'reservation_id' => $reservation->id,
                    'montant' => $montantCalcule,
                    'mode_paiement' => $request->mode_paiement,
                    'statut' => 'Payé',
                ]);


                /*
                |--------------------------------------------------------------------------
                | 12. CRÉER UNE RÉFÉRENCE D'ACHAT UNIQUE
                |--------------------------------------------------------------------------
                */

                $reference =
                    'INT-' .
                    now()->format('YmdHis') .
                    '-' .
                    strtoupper(
                        substr(
                            bin2hex(random_bytes(4)),
                            0,
                            6
                        )
                    );


                /*
                |--------------------------------------------------------------------------
                | 13. CRÉER L'ACHAT
                |--------------------------------------------------------------------------
                */

                $achat = Achat::create([
                    'user_id' => $reservation->user_id,
                    'reservation_id' => $reservation->id,
                    'trajet_id' => $reservation->trajet_id,

                    'montant' => $montantCalcule,

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
                | 14. CONFIRMER LA RÉSERVATION
                |--------------------------------------------------------------------------
                */

                $reservation->update([
                    'statut' => 'payée',
                ]);


                /*
                |--------------------------------------------------------------------------
                | 15. RÉCUPÉRER LES VOYAGEURS
                |--------------------------------------------------------------------------
                */

                $voyageurs = $reservation->voyageurs;


                /*
                |--------------------------------------------------------------------------
                | 16. GÉNÉRER LES BILLETS
                |--------------------------------------------------------------------------
                |
                | IMPORTANT :
                |
                | Un voyageur = un billet.
                |
                | Exemple :
                |
                | Nathan Kanga
                |      ↓
                | Billet 1
                |
                | Degrace Kanga
                |      ↓
                | Billet 2
                |
                |--------------------------------------------------------------------------
                */

                $billetsCrees = collect();


                if ($voyageurs->count() > 0) {

                    foreach ($voyageurs as $voyageur) {

                        /*
                        |--------------------------------------------------------------------------
                        | Numéro unique du billet
                        |--------------------------------------------------------------------------
                        */

                        $numeroBillet =
                            'TOK-' .
                            now()->format('YmdHis') .
                            '-' .
                            strtoupper(
                                substr(
                                    bin2hex(random_bytes(3)),
                                    0,
                                    6
                                )
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | Code du billet
                        |--------------------------------------------------------------------------
                        */

                        $qrCode =
                            'QR-' .
                            strtoupper(
                                substr(
                                    bin2hex(random_bytes(6)),
                                    0,
                                    12
                                )
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | CRÉER LE BILLET
                        |--------------------------------------------------------------------------
                        |
                        | On utilise new + save() au lieu de create()
                        | afin que voyageur_id fonctionne même si le
                        | modèle Billet n'a pas encore été ajouté au
                        | tableau $fillable.
                        |
                        |--------------------------------------------------------------------------
                        */

                        $billet = new Billet();

                        $billet->reservation_id =
                            $reservation->id;

                        $billet->voyageur_id =
                            $voyageur->id;

                        $billet->numero_billet =
                            $numeroBillet;

                        $billet->qr_code =
                            $qrCode;

                        $billet->save();


                        $billetsCrees->push($billet);
                    }

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | CAS DE COMPATIBILITÉ
                    |--------------------------------------------------------------------------
                    |
                    | Si une ancienne réservation n'a aucun voyageur
                    | enregistré, on crée quand même un billet.
                    |
                    |--------------------------------------------------------------------------
                    */

                    $numeroBillet =
                        'TOK-' .
                        now()->format('YmdHis') .
                        '-' .
                        strtoupper(
                            substr(
                                bin2hex(random_bytes(3)),
                                0,
                                6
                            )
                        );

                    $qrCode =
                        'QR-' .
                        strtoupper(
                            substr(
                                bin2hex(random_bytes(6)),
                                0,
                                12
                            )
                        );

                    $billet = new Billet();

                    $billet->reservation_id =
                        $reservation->id;

                    $billet->numero_billet =
                        $numeroBillet;

                    $billet->qr_code =
                        $qrCode;

                    $billet->save();

                    $billetsCrees->push($billet);
                }


                /*
                |--------------------------------------------------------------------------
                | 17. RECHARGER TOUTES LES RELATIONS
                |--------------------------------------------------------------------------
                |
                | C'est cette partie qui permet à /api/achats et à
                | Flutter de récupérer immédiatement les billets.
                |
                |--------------------------------------------------------------------------
                */

                $achat->load([
                    'user',

                    'reservation.user',

                    'reservation.trajet.agence',

                    'reservation.voyageurs',

                    'reservation.sieges',

                    'reservation.billets.voyageur',
                ]);


                /*
                |--------------------------------------------------------------------------
                | 18. RETOUR
                |--------------------------------------------------------------------------
                */

                return [
                    'paiement' => $paiement,

                    'achat' => $achat,

                    'billets' => $billetsCrees,
                ];
            });


            /*
            |--------------------------------------------------------------------------
            | RÉPONSE JSON
            |--------------------------------------------------------------------------
            */

            return response()->json([
                'message' =>
                    'Paiement effectué avec succès. Votre achat et vos billets ont été générés.',

                'data' => $result,

            ], 200);


        } catch (
            \Symfony\Component\HttpKernel\Exception\HttpException $e
        ) {

            return response()->json([
                'message' => $e->getMessage(),

            ], $e->getStatusCode());


        } catch (\Throwable $e) {

            return response()->json([
                'message' =>
                    'Une erreur est survenue lors du paiement de la réservation.',

                'error' => $e->getMessage(),

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Afficher un paiement
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $paiement = Paiement::with([
            'reservation.user',
            'reservation.trajet',
            'reservation.voyageurs',
            'reservation.billets',
        ])->findOrFail($id);

        return response()->json($paiement);
    }


    /*
    |--------------------------------------------------------------------------
    | Supprimer un paiement
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $paiement = Paiement::findOrFail($id);

        $paiement->delete();

        return response()->json([
            'message' => 'Paiement supprimé avec succès',
        ]);
    }
}