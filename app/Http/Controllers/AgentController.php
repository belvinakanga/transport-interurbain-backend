<?php

namespace App\Http\Controllers;

use App\Models\Achat;
use App\Models\Reservation;
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

        /*
        |--------------------------------------------------------------------------
        | Réservations et achats, comptés séparément
        |--------------------------------------------------------------------------
        |
        | Une réservation payée reste une réservation : elle ne bascule pas
        | du côté des achats. Seul l'achat direct, fait au guichet, compte
        | comme un achat.
        |
        | Les compteurs étaient auparavant calculés dans la vue, et une
        | réservation payée s'y retrouvait comptée des deux côtés.
        |
        */

        $nombreReservations = Reservation::where('type', '!=', 'achat_direct')
            ->whereHas(
                'trajet',
                fn ($q) => $q->where('agence_id', $agence->id)
            )
            ->count();

        $achatsDirects = Achat::whereHas(
            'reservation',
            fn ($q) => $q->where('type', 'achat_direct')
        )
            ->whereHas(
                'reservation.trajet',
                fn ($q) => $q->where('agence_id', $agence->id)
            );

        $nombreAchats = (clone $achatsDirects)->count();

        /*
        |--------------------------------------------------------------------------
        | Recettes de l'agence
        |--------------------------------------------------------------------------
        |
        | Affichées pour éviter à l'agence de refaire le calcul à la main :
        | ce qui lui revient sur les billets, et sa part des frais de service.
        |
        */

        $ventesAgence = Achat::where('statut', 'payé')
            ->whereHas(
                'trajet',
                fn ($q) => $q->where('agence_id', $agence->id)
            )
            ->get();

        $recettes = [
            'billets'      => (float) $ventesAgence->sum('montant_base'),
            'part_agence'  => (float) $ventesAgence->sum('part_agence'),
            'commission'   => (float) $ventesAgence->sum('commission_tokende'),
            'encaisse'     => (float) $ventesAgence->sum('montant'),
        ];

        return view(
            'agent.dashboard',
            compact(
                'agence',
                'nombreTrajets',
                'trajets',
                'nombreReservations',
                'nombreAchats',
                'recettes'
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