<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class AdminSupportController extends Controller
{
    /**
     * Vérifier que l'utilisateur connecté est un administrateur.
     */
    private function authorizeAdmin(): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Accès refusé.');
        }
    }

    /**
     * Liste des conversations de support.
     */
    public function index()
    {
        $this->authorizeAdmin();

        $conversations = Conversation::with([
            'user',
            'messages' => function ($query) {
                $query->latest()->limit(1);
            },
        ])
        ->withCount([
            'messages',
            'messages as unread_messages_count' => function ($query) {
                $query
                    ->where('sender_type', 'voyageur')
                    ->where('is_read', false);
            },
        ])
        ->latest('updated_at')
        ->paginate(15);

        return view(
            'admin.support.index',
            compact('conversations')
        );
    }

    /**
     * Afficher une conversation complète.
     */
    public function show($id)
    {
        $this->authorizeAdmin();

        $conversation = Conversation::with([
            'user',
            'messages.user',
        ])->findOrFail($id);

        // Les messages du voyageur sont considérés
        // comme lus lorsque l'admin ouvre la conversation.
        Message::where('conversation_id', $conversation->id)
            ->where('sender_type', 'voyageur')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
            ]);

        $conversation->load([
            'user',
            'messages.user',
        ]);

        return view(
            'admin.support.show',
            compact('conversation')
        );
    }

    /**
     * Répondre à une conversation.
     */
    public function reply(
        Request $request,
        $id
    ) {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'message' => [
                'required',
                'string',
            ],
        ]);

        $conversation = Conversation::findOrFail($id);

        if ($conversation->status === 'closed') {
            return back()->with(
                'error',
                'Cette conversation est fermée.'
            );
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => auth()->id(),
            'sender_type' => 'admin',
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        $conversation->touch();

        return redirect()
            ->route(
                'admin.support.show',
                $conversation->id
            )
            ->with(
                'success',
                'Réponse envoyée avec succès.'
            );
    }

    /**
     * Fermer une conversation.
     */
    public function close($id)
    {
        $this->authorizeAdmin();

        $conversation = Conversation::findOrFail($id);

        $conversation->update([
            'status' => 'closed',
        ]);

        return redirect()
            ->route(
                'admin.support.show',
                $conversation->id
            )
            ->with(
                'success',
                'Conversation fermée avec succès.'
            );
    }


    public function reopen($id)
{
    $this->authorizeAdmin();

    $conversation = Conversation::findOrFail($id);

    $conversation->update([
        'status' => 'open',
    ]);

    return redirect()
        ->route(
            'admin.support.show',
            $conversation->id
        )
        ->with(
            'success',
            'Conversation réouverte avec succès.'
        );
}
}