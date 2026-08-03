<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Trajet;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Afficher toutes les réservations
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        return response()->json(
            Reservation::with(['user', 'trajet'])->get()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Ajouter une réservation
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'trajet_id' => 'required',
            'nombre_places' => 'required|integer|min:1',
        ]);

        $reservation = Reservation::create([
            'user_id' => auth()->id(),
            'trajet_id' => $request->trajet_id,
            'nombre_places' => $request->nombre_places,
            'statut' => 'en attente',
        ]);

        return response()->json([
            'message' => 'Réservation créée avec succès',
            'data' => $reservation
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Afficher une réservation
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        return response()->json(
            Reservation::with(['user', 'trajet'])
                ->findOrFail($id)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Annuler une réservation
    |--------------------------------------------------------------------------
    */
    public function annuler($id)
    {
        $reservation = Reservation::findOrFail($id);

        // Déjà annulée
        if ($reservation->statut === 'annulée') {

            return redirect('/dashboard')
                ->with(
                    'error',
                    'Cette réservation est déjà annulée.'
                );
        }

        // Récupération du trajet
        $trajet = Trajet::findOrFail(
            $reservation->trajet_id
        );

        /*
        |--------------------------------------------------------------------------
        | RÈGLE DES 72 HEURES
        |--------------------------------------------------------------------------
        */

        $dateDepart = Carbon::parse(
            $trajet->date_depart . ' ' . $trajet->heure_depart
        );

        if (now()->diffInHours($dateDepart, false) < 72) {

            return redirect('/dashboard')
                ->with(
                    'error',
                    'Impossible d\'annuler une réservation moins de 72 heures avant le départ.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Remettre les places disponibles
        |--------------------------------------------------------------------------
        */

        $trajet->places_disponibles =
            $trajet->places_disponibles +
            $reservation->nombre_places;

        $trajet->save();

        /*
        |--------------------------------------------------------------------------
        | Mise à jour du statut
        |--------------------------------------------------------------------------
        */

        $reservation->statut = 'annulée';

        $reservation->save();

        return redirect('/dashboard')
            ->with(
                'success',
                'Réservation annulée avec succès.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Supprimer une réservation
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);

        $reservation->delete();

        return response()->json([
            'message' => 'Réservation supprimée avec succès'
        ]);
    }
}