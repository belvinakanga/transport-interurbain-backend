<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trajet;
use Carbon\Carbon;

class TrajetController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Afficher tous les trajets
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        return response()->json(
            Trajet::with('agence')->get()
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Ajouter un trajet
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'agence_id' => 'required|exists:agences,id',
            'depart' => 'required|string',
            'arrivee' => 'required|string',
            'date_depart' => 'required|date',
            'heure_depart' => 'required',
            'duree' => 'required|date_format:H:i',
            'prix' => 'required|numeric|min:0',
            'places_totales' => 'required|integer|min:1',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Calcul automatique de l'heure de fin
        |--------------------------------------------------------------------------
        |
        | Exemple :
        | départ = 08:00
        | durée  = 10:00
        | fin    = 18:00
        |
        */

        $heureDepart = Carbon::createFromFormat(
            'H:i',
            $request->heure_depart
        );

        $duree = Carbon::createFromFormat(
            'H:i',
            $request->duree
        );

        $heureFin = $heureDepart->copy()
            ->addHours($duree->hour)
            ->addMinutes($duree->minute);

        $trajet = Trajet::create([
            'agence_id' => $request->agence_id,
            'depart' => $request->depart,
            'arrivee' => $request->arrivee,
            'date_depart' => $request->date_depart,
            'heure_depart' => $request->heure_depart,
            'duree' => $request->duree,
            'heure_fin' => $heureFin->format('H:i:s'),
            'prix' => $request->prix,
            'places_totales' => $request->places_totales,
            'places_disponibles' => $request->places_totales,
        ]);

        return response()->json([
            'message' => 'Trajet ajouté avec succès',
            'data' => $trajet,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Afficher un trajet
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        return response()->json(
            Trajet::with('agence')
                ->findOrFail($id)
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Modifier un trajet
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $trajet = Trajet::findOrFail($id);

        $request->validate([
            'agence_id' => 'required|exists:agences,id',
            'depart' => 'required|string',
            'arrivee' => 'required|string',
            'date_depart' => 'required|date',
            'heure_depart' => 'required',
            'duree' => 'required|date_format:H:i',
            'prix' => 'required|numeric|min:0',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Recalcul automatique de l'heure de fin
        |--------------------------------------------------------------------------
        */

        $heureDepart = Carbon::createFromFormat(
            'H:i',
            $request->heure_depart
        );

        $duree = Carbon::createFromFormat(
            'H:i',
            $request->duree
        );

        $heureFin = $heureDepart->copy()
            ->addHours($duree->hour)
            ->addMinutes($duree->minute);

        $trajet->update([
            'agence_id' => $request->agence_id,
            'depart' => $request->depart,
            'arrivee' => $request->arrivee,
            'date_depart' => $request->date_depart,
            'heure_depart' => $request->heure_depart,
            'duree' => $request->duree,
            'heure_fin' => $heureFin->format('H:i:s'),
            'prix' => $request->prix,
        ]);

        return response()->json([
            'message' => 'Trajet modifié avec succès',
            'data' => $trajet,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Supprimer un trajet
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $trajet = Trajet::findOrFail($id);

        $trajet->delete();

        return redirect('/admin/trajets')
            ->with(
                'success',
                'Trajet supprimé avec succès.'
            );
    }
}