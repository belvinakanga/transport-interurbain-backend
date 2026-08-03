<?php

namespace App\Http\Controllers;

use App\Models\Trajet;
use Carbon\Carbon;

class VoyageController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Liste des trajets disponibles
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $trajets = Trajet::all();

        foreach ($trajets as $trajet) {

            $dateDepart = Carbon::parse(
                $trajet->date_depart . ' ' . $trajet->heure_depart
            );

            // Le trajet est réservable uniquement si le départ est dans plus de 72 heures
            $trajet->reservationOuverte =
                now()->diffInHours($dateDepart, false) >= 72;
        }

        return view(
            'voyages.index',
            compact('trajets')
        );
    }
}