<?php

namespace App\Http\Middleware;

use App\Models\Agence;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Barre l'accès aux agents dont l'agence a été suspendue.
 *
 * Empêcher la seule création de trajets ne suffit pas : l'agent verrait
 * encore ses réservations, ses paiements et ses voyageurs. Une agence
 * suspendue perd l'accès au back-office entier, et son agent reçoit une
 * page expliquant quoi faire.
 */
class VerifierAgenceActive
{
    /**
     * Routes qui restent ouvertes malgré la suspension.
     *
     * Sans la déconnexion, l'agent resterait enfermé dans la page de
     * blocage sans pouvoir changer de compte.
     */
    private const TOUJOURS_AUTORISE = [
        'logout',
        'agence-suspendue',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Les voyageurs et l'administrateur ne sont pas concernés.
        if (!$user || $user->role !== 'agent') {
            return $next($request);
        }

        foreach (self::TOUJOURS_AUTORISE as $chemin) {
            if ($request->is($chemin) || $request->is($chemin . '/*')) {
                return $next($request);
            }
        }

        // Un agent sans agence est déjà traité ailleurs par les contrôleurs.
        if (!$user->agence_id) {
            return $next($request);
        }

        $agence = Agence::find($user->agence_id);

        if ($agence && !$agence->actif) {
            return redirect()->route('agence.suspendue');
        }

        return $next($request);
    }
}
