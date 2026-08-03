<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agence;

class AgenceController extends Controller
{
    // AFFICHER toutes les agences
    public function index()
    {
        return response()->json(Agence::all());
    }

    // AJOUTER une agence
    public function store(Request $request)
    {
        $agence = Agence::create([
            'nom' => $request->nom,
            'adresse' => $request->adresse,
            'telephone' => $request->telephone,
        ]);

        return response()->json([
            'message' => 'Agence ajoutée avec succès',
            'data' => $agence
        ]);
    }

    // AFFICHER une seule agence
    public function show($id)
    {
        return Agence::findOrFail($id);
    }

    // SUPPRIMER une agence
    public function destroy($id)
    {
        $agence = Agence::findOrFail($id);
        $agence->delete();

        return response()->json([
            'message' => 'Agence supprimée avec succès'
        ]);
    }
}