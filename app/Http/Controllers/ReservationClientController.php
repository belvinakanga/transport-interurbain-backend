<?php

namespace App\Http\Controllers;

use App\Models\Trajet;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationClientController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Afficher le formulaire de réservation
    |--------------------------------------------------------------------------
    */
    public function create($id)
    {
        $trajet = Trajet::findOrFail($id);

        return view('reservations.create', compact('trajet'));
    }

    /*
    |--------------------------------------------------------------------------
    | Enregistrer la réservation
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'trajet_id' => 'required',
            'nombre_places' => 'required|integer|min:1'
        ]);

        // Récupérer le trajet
        $trajet = Trajet::findOrFail($request->trajet_id);

        /*
        |--------------------------------------------------------------------------
        | RÈGLE DES 72 HEURES
        |--------------------------------------------------------------------------
        */

        $dateDepart = Carbon::parse(
            $trajet->date_depart . ' ' . $trajet->heure_depart
        );

        if (now()->diffInHours($dateDepart, false) < 72) {

            return redirect('/voyages')->with(
                'error',
                'Les réservations sont fermées à moins de 72 heures du départ.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Vérifier les places disponibles
        |--------------------------------------------------------------------------
        */

        if ($trajet->places_disponibles < $request->nombre_places) {

            return back()->with(
                'error',
                'Nombre de places insuffisant.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Création réservation
        |--------------------------------------------------------------------------
        */

        $reservation = Reservation::create([
            'user_id' => auth()->id(),
            'trajet_id' => $request->trajet_id,
            'nombre_places' => $request->nombre_places,
            'statut' => 'en attente'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Mise à jour des places
        |--------------------------------------------------------------------------
        */

        $trajet->places_disponibles =
            $trajet->places_disponibles - $request->nombre_places;

        $trajet->save();

        /*
        |--------------------------------------------------------------------------
        | Redirection paiement
        |--------------------------------------------------------------------------
        */

        return redirect(
            '/paiement/create/' . $reservation->id
        );
    }
}