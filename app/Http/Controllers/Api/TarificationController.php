<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FraisService;

/**
 * Tarification publique.
 *
 * Permet à l'application d'afficher les frais de service **avant** l'achat,
 * sans les coder en dur de son côté : une modification du tarif se
 * répercute sans nouvelle version de l'application.
 *
 * Seuls les frais payés par le voyageur sont exposés. Leur répartition
 * entre Tokende et l'agence reste interne.
 */
class TarificationController extends Controller
{
    public function index()
    {
        return response()->json([
            'frais_service_par_billet' => FraisService::parBillet(),
            'devise' => 'FCFA',
        ]);
    }
}
