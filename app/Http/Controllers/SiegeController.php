<?php

namespace App\Http\Controllers;

use App\Models\Siege;
use App\Models\Trajet;
use Illuminate\Http\JsonResponse;

class SiegeController extends Controller
{
    /**
     * ============================================================
     * AFFICHER LES SIÈGES D'UN TRAJET
     * ============================================================
     *
     * GET /api/trajets/{id}/sieges
     */
    public function index($id): JsonResponse
    {
        $trajet = Trajet::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Créer les sièges manquants
        |--------------------------------------------------------------------------
        |
        | Exemple :
        | places_totales = 31
        |
        | Laravel crée automatiquement :
        | siège 1 jusqu'au siège 31.
        |
        */

        $this->createMissingSeats($trajet);

        /*
        |--------------------------------------------------------------------------
        | Récupérer les sièges
        |--------------------------------------------------------------------------
        */

        $sieges = Siege::where('trajet_id', $trajet->id)
            ->orderBy('numero_siege')
            ->get([
                'id',
                'trajet_id',
                'numero_siege',
                'statut',
                'reservation_id',
            ]);

        return response()->json([
            'trajet_id' => $trajet->id,
            'places_totales' => $trajet->places_totales,
            'places_disponibles' => $trajet->places_disponibles,
            'sieges' => $sieges,
        ]);
    }

    /**
     * ============================================================
     * CRÉER LES SIÈGES MANQUANTS
     * ============================================================
     */
    private function createMissingSeats(Trajet $trajet): void
    {
        $existingSeats = Siege::where(
            'trajet_id',
            $trajet->id
        )
            ->pluck('numero_siege')
            ->toArray();

        for (
            $numero = 1;
            $numero <= $trajet->places_totales;
            $numero++
        ) {
            if (!in_array($numero, $existingSeats)) {
                Siege::create([
                    'trajet_id' => $trajet->id,
                    'numero_siege' => $numero,
                    'statut' => 'disponible',
                    'reservation_id' => null,
                ]);
            }
        }
    }
}