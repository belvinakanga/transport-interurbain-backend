<?php

namespace App\Http\Controllers;

use App\Models\Trajet;
use App\Models\PaiementAgence;

class AgentController extends Controller
{
    /**
     * ============================================================
     * TABLEAU DE BORD AGENT
     * ============================================================
     */
    public function dashboard()
    {
        $user = auth()->user();

        if ($user->role !== 'agent') {
            abort(403, 'Accès refusé.');
        }

        if (!$user->agence_id) {
            abort(
                403,
                'Votre compte agent n’est lié à aucune agence.'
            );
        }

        $agence = $user->agence;

        /*
        |--------------------------------------------------------------------------
        | Nombre de trajets
        |--------------------------------------------------------------------------
        */

        $nombreTrajets = Trajet::where(
            'agence_id',
            $agence->id
        )->count();

        /*
        |--------------------------------------------------------------------------
        | Derniers trajets
        |--------------------------------------------------------------------------
        */

        $trajets = Trajet::where(
            'agence_id',
            $agence->id
        )
        ->latest()
        ->take(5)
        ->get();

        return view(
            'agent.dashboard',
            compact(
                'agence',
                'nombreTrajets',
                'trajets'
            )
        );
    }


    /**
     * ============================================================
     * MES PAIEMENTS
     * ============================================================
     *
     * Cette page appartient exclusivement à l'Agent.
     *
     * L'Agent ne voit que les paiements de SON agence.
     */
    public function paiements()
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Vérification du rôle
        |--------------------------------------------------------------------------
        */

        if ($user->role !== 'agent') {
            abort(403, 'Accès refusé.');
        }


        /*
        |--------------------------------------------------------------------------
        | Vérification de l'agence
        |--------------------------------------------------------------------------
        */

        if (!$user->agence_id) {
            abort(
                403,
                'Votre compte agent n’est lié à aucune agence.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Agence de l'Agent
        |--------------------------------------------------------------------------
        */

        $agence = $user->agence;


        /*
        |--------------------------------------------------------------------------
        | Paiements de l'agence uniquement
        |--------------------------------------------------------------------------
        */

        $paiements = PaiementAgence::with([
            'agence',
            'abonnement',
        ])
        ->where(
            'agence_id',
            $user->agence_id
        )
        ->latest('date_prevue')
        ->paginate(10)
        ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Vue Agent
        |--------------------------------------------------------------------------
        */

        return view(
            'agent.paiements',
            compact(
                'agence',
                'paiements'
            )
        );
    }
}