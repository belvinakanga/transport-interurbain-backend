<?php

namespace App\Http\Controllers;

use App\Models\Agence;
use Illuminate\Http\Request;

class AgenceController extends Controller
{
    /**
     * ============================================================
     * LISTE DES AGENCES
     * ============================================================
     */
    public function index()
    {
        $agences = Agence::latest()->get();

        return response()->json($agences);
    }


    /**
     * ============================================================
     * CRÉER UNE AGENCE
     * ============================================================
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_agence' => [
                'required',
                'string',
                'max:255',
            ],

            'ville' => [
                'required',
                'string',
                'max:255',
            ],

            'adresse' => [
                'required',
                'string',
                'max:255',
            ],

            'telephone' => [
                'nullable',
                'string',
                'max:50',
            ],
        ]);

        $agence = Agence::create($validated);

        return response()->json([
            'message' => 'Agence créée avec succès.',
            'data' => $agence,
        ], 201);
    }


    /**
     * ============================================================
     * AFFICHER UNE AGENCE
     * ============================================================
     */
    public function show($id)
    {
        $agence = Agence::findOrFail($id);

        return response()->json($agence);
    }


    /**
     * ============================================================
     * SUPPRIMER UNE AGENCE
     * ============================================================
     */
    public function destroy($id)
    {
        $agence = Agence::findOrFail($id);

        $agence->delete();

        return response()->json([
            'message' => 'Agence supprimée avec succès.',
        ]);
    }
}