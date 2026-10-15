<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

/**
 * Notifications du back-office.
 *
 * Mêmes notifications que côté mobile : un message de voyageur prévient
 * l'équipe ici, et le voyageur dans son application.
 */
class NotificationWebController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $this->siennes()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /** Ouvre la notification et va là où elle pointe. */
    public function ouvrir($id)
    {
        $notification = $this->siennes()->findOrFail($id);

        $notification->marquerLue();

        $conversation = $notification->donnees['conversation_id'] ?? null;

        return $conversation
            ? redirect()->route('admin.support.show', $conversation)
            : back();
    }

    public function toutMarquerLu()
    {
        $this->siennes()->nonLues()->update(['lu_le' => now()]);

        return back()->with('success', 'Toutes les notifications sont lues.');
    }

    private function siennes()
    {
        return Notification::where('user_id', auth()->id());
    }
}
