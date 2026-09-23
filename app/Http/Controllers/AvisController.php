<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Avis;

class AvisController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTE DES AVIS
    |--------------------------------------------------------------------------
    |
    | GET /api/avis
    |
    */

    public function index()
    {
        $avis = Avis::with('user')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $avis,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | AJOUTER UN AVIS
    |--------------------------------------------------------------------------
    |
    | POST /api/avis
    |
    */

    public function store(Request $request)
    {
        /*
        |----------------------------------------------------------------------
        | Vérifier les données reçues
        |----------------------------------------------------------------------
        */

        $validated = $request->validate([
            'note' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],

            'commentaire' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);


        /*
        |----------------------------------------------------------------------
        | Récupérer automatiquement l'utilisateur connecté
        |----------------------------------------------------------------------
        */

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié.',
            ], 401);
        }


        /*
        |----------------------------------------------------------------------
        | Créer l'avis
        |----------------------------------------------------------------------
        */

        $avis = Avis::create([
            'user_id' => $user->id,
            'note' => $validated['note'],
            'commentaire' => $validated['commentaire'] ?? null,
        ]);


        /*
        |----------------------------------------------------------------------
        | Charger l'utilisateur
        |----------------------------------------------------------------------
        */

        $avis->load('user');


        /*
        |----------------------------------------------------------------------
        | Retourner l'avis créé
        |----------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'message' => 'Avis ajouté avec succès.',
            'data' => $avis,
        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | AFFICHER UN AVIS
    |--------------------------------------------------------------------------
    |
    | API :
    | GET /api/avis/{id}
    |
    | ADMIN :
    | GET /admin/avis/{id}
    |
    */

    public function show(Request $request, $id)
    {
        $avis = Avis::with('user')
            ->findOrFail($id);


        /*
        |----------------------------------------------------------------------
        | Si la demande vient de l'API
        |----------------------------------------------------------------------
        */

        if ($request->is('api/*') || $request->expectsJson()) {

            return response()->json([
                'success' => true,
                'data' => $avis,
            ]);
        }


        /*
        |----------------------------------------------------------------------
        | Si la demande vient de l'administration
        |----------------------------------------------------------------------
        */

        return view('admin.show-avis', compact('avis'));
    }


    /*
    |--------------------------------------------------------------------------
    | SUPPRIMER UN AVIS
    |--------------------------------------------------------------------------
    |
    | DELETE /admin/avis/{id}
    |
    */

    public function destroy($id)
    {
        $avis = Avis::findOrFail($id);

        $avis->delete();

        return redirect('/admin/avis')
            ->with(
                'success',
                'Avis supprimé avec succès.'
            );
    }
}