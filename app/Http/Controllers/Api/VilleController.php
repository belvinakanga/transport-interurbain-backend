<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ville;

/**
 * Référentiel des villes, lu par l'application mobile.
 */
class VilleController extends Controller
{
    /**
     * Liste alphabétique des villes desservies.
     *
     * Publique comme `/trajets` et `/agences` : c'est une donnée de
     * référence, nécessaire avant même d'ouvrir un compte.
     */
    public function index()
    {
        return response()->json([
            'data' => Ville::noms(),
        ]);
    }
}
