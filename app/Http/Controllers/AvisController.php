<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Avis;

class AvisController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Liste des avis (API)
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $avis = Avis::with('user')->get();

        return response()->json($avis);
    }

    /*
    |--------------------------------------------------------------------------
    | Ajouter un avis
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $avis = Avis::create([
            'user_id' => $request->user_id,
            'note' => $request->note,
            'commentaire' => $request->commentaire,
        ]);

        return response()->json([
            'message' => 'Avis ajouté avec succès',
            'data' => $avis
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Voir un avis (Administration)
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $avis = Avis::with('user')->findOrFail($id);

        return view('admin.show-avis', compact('avis'));
    }

    /*
    |--------------------------------------------------------------------------
    | Supprimer un avis
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $avis = Avis::findOrFail($id);

        $avis->delete();

        return redirect('/admin/avis')
            ->with('success', 'Avis supprimé avec succès.');
    }
}