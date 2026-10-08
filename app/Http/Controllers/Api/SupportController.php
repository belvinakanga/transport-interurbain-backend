<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    /**
     * Liste des conversations du voyageur connecté.
     */
   public function conversations(Request $request)
{
    $conversations = Conversation::with([
        'messages' => function ($query) {
            $query->latest()->limit(1);
        },
    ])
    ->withCount([
        'messages as unread_messages_count' => function ($query) {
            $query
                ->where('sender_type', 'admin')
                ->where('is_read', false);
        },
    ])
    ->where('user_id', $request->user()->id)
    ->latest('updated_at')
    ->get();

    return response()->json([
        'success' => true,
        'conversations' => $conversations,
    ]);
}
    /**
     * Créer une nouvelle conversation avec son premier message.
     */
    public function storeConversation(Request $request)
    {
        $validated = $request->validate([
            'subject' => [
                'required',
                'string',
                'max:255',
            ],
            'message' => [
                'required',
                'string',
            ],
        ]);

        $conversation = Conversation::create([
            'user_id' => $request->user()->id,
            'subject' => $validated['subject'],
            'status' => 'open',
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $request->user()->id,
            'sender_type' => 'voyageur',
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        $conversation->load('messages.user');

        return response()->json([
            'success' => true,
            'message' => 'Conversation créée avec succès.',
            'conversation' => $conversation,
        ], 201);
    }

    /**
     * Afficher une conversation du voyageur connecté.
     */
    public function showConversation(
    Request $request,
    $id
) {
    $conversation = Conversation::with([
        'messages.user',
    ])
    ->where('user_id', $request->user()->id)
    ->findOrFail($id);

    // Les messages envoyés par l'admin sont considérés
    // comme lus lorsque le voyageur ouvre la conversation.
    Message::where('conversation_id', $conversation->id)
        ->where('sender_type', 'admin')
        ->where('is_read', false)
        ->update([
            'is_read' => true,
        ]);

    $conversation->load([
        'messages.user',
    ]);

    return response()->json([
        'success' => true,
        'conversation' => $conversation,
    ]);
}

    /**
     * Envoyer un nouveau message dans une conversation.
     */
    public function sendMessage(
        Request $request,
        $id
    ) {
        $validated = $request->validate([
            'message' => [
                'required',
                'string',
            ],
        ]);

        $conversation = Conversation::where(
            'user_id',
            $request->user()->id
        )->findOrFail($id);

        if ($conversation->status === 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Cette conversation est fermée.',
            ], 422);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $request->user()->id,
            'sender_type' => 'voyageur',
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        $message->load('user');

        return response()->json([
            'success' => true,
            'message' => $message,
        ], 201);
    }
}