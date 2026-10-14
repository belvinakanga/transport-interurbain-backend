<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appareil;
use App\Models\Notification;
use Illuminate\Http\Request;

/**
 * Notifications de l'utilisateur connecté.
 *
 * Chaque route ne travaille que sur ses propres notifications : le
 * filtrage par utilisateur est posé une fois, dans une méthode privée.
 */
class NotificationController extends Controller
{
    /** Liste, les plus récentes d'abord. */
    public function index(Request $request)
    {
        $notifications = $this->siennes($request)
            ->latest()
            ->limit(100)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'non_lues' => $this->siennes($request)->nonLues()->count(),
        ]);
    }

    /** Compteur seul : utilisé par la pastille de l'application. */
    public function compteur(Request $request)
    {
        return response()->json([
            'non_lues' => $this->siennes($request)->nonLues()->count(),
        ]);
    }

    /** Marque une notification comme lue. */
    public function marquerLue(Request $request, $id)
    {
        $notification = $this->siennes($request)->findOrFail($id);

        $notification->marquerLue();

        return response()->json([
            'notification' => $notification,
            'non_lues' => $this->siennes($request)->nonLues()->count(),
        ]);
    }

    /** Marque tout comme lu. */
    public function toutMarquerLu(Request $request)
    {
        $this->siennes($request)->nonLues()->update(['lu_le' => now()]);

        return response()->json(['non_lues' => 0]);
    }

    /**
     * Enregistre le jeton push de l'appareil.
     *
     * Appelé à chaque démarrage de l'application : le jeton Firebase peut
     * changer sans prévenir, et un jeton périmé ne reçoit plus rien.
     */
    public function enregistrerAppareil(Request $request)
    {
        $valide = $request->validate([
            'jeton_push' => 'required|string|max:255',
            'plateforme' => 'nullable|string|max:20',
        ]);

        Appareil::enregistrer(
            $request->user()->id,
            $valide['jeton_push'],
            $valide['plateforme'] ?? null
        );

        return response()->json(['enregistre' => true]);
    }

    /** Oublie l'appareil : à la déconnexion. */
    public function oublierAppareil(Request $request)
    {
        $valide = $request->validate([
            'jeton_push' => 'required|string|max:255',
        ]);

        Appareil::where('jeton_push', $valide['jeton_push'])
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json(['oublie' => true]);
    }

    private function siennes(Request $request)
    {
        return Notification::where('user_id', $request->user()->id);
    }
}
