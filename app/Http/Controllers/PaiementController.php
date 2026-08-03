<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paiement;
use App\Models\Billet;
use App\Models\Reservation;

class PaiementController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Liste des paiements
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        return response()->json(
            Paiement::with('reservation.user', 'reservation.trajet')->get()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Afficher le formulaire de paiement
    |--------------------------------------------------------------------------
    */
    public function create($reservation)
    {
        $reservation = Reservation::with('trajet')
            ->findOrFail($reservation);

        return view('paiements.create', compact('reservation'));
    }

    /*
    |--------------------------------------------------------------------------
    | Effectuer le paiement
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'montant' => 'required|numeric|min:1',
            'mode_paiement' => 'required'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Récupération de la réservation
        |--------------------------------------------------------------------------
        */
        $reservation = Reservation::findOrFail($request->reservation_id);

        /*
        |--------------------------------------------------------------------------
        | Création du paiement
        |--------------------------------------------------------------------------
        */
        $paiement = Paiement::create([
            'reservation_id' => $reservation->id,
            'montant' => $request->montant,
            'mode_paiement' => $request->mode_paiement,
            'statut' => 'Payé'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Confirmation de la réservation
        |--------------------------------------------------------------------------
        */
        $reservation->update([
            'statut' => 'Confirmée'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Génération du billet
        |--------------------------------------------------------------------------
        */
        Billet::create([
            'reservation_id' => $reservation->id,
            'numero_billet' => 'BIL-' . now()->format('YmdHis'),
            'qr_code' => 'QR-' . uniqid()
        ]);

        /*
        |--------------------------------------------------------------------------
        | Retour
        |--------------------------------------------------------------------------
        */
        return redirect('/dashboard')->with(
            'success',
            'Paiement effectué avec succès. Votre réservation est confirmée et votre billet a été généré.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Afficher un paiement
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        return Paiement::with('reservation')
            ->findOrFail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Supprimer un paiement
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        Paiement::destroy($id);

        return response()->json([
            'message' => 'Paiement supprimé avec succès'
        ]);
    }
}