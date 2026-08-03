<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trajet;

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
            'agence_id' => 'required',
            'depart' => 'required',
            'arrivee' => 'required',
            'date_depart' => 'required',
            'heure_depart' => 'required',
            'prix' => 'required|numeric',
            'places_totales' => 'required|integer|min:1'
        ]);

        $trajet = Trajet::create([
            'agence_id' => $request->agence_id,
            'depart' => $request->depart,
            'arrivee' => $request->arrivee,
            'date_depart' => $request->date_depart,
            'heure_depart' => $request->heure_depart,
            'prix' => $request->prix,
            'places_totales' => $request->places_totales,
            'places_disponibles' => $request->places_totales,
        ]);

        return response()->json([
            'message' => 'Trajet ajouté avec succès',
            'data' => $trajet
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

        $trajet->update([
            'agence_id' => $request->agence_id,
            'depart' => $request->depart,
            'arrivee' => $request->arrivee,
            'date_depart' => $request->date_depart,
            'heure_depart' => $request->heure_depart,
            'prix' => $request->prix,
        ]);

        return response()->json([
            'message' => 'Trajet modifié avec succès',
            'data' => $trajet
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