<?php

namespace App\Services;

use App\Models\Appareil;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Point d'entrée unique pour notifier un utilisateur.
 *
 * Deux étages, volontairement séparés :
 *
 * 1. **La notification est enregistrée en base.** C'est ce qui compte :
 *    elle reste consultable dans l'application quoi qu'il arrive.
 * 2. **Un push est tenté** vers les appareils connus. S'il échoue — pas de
 *    clé Firebase, jeton expiré, réseau coupé — la notification existe
 *    quand même et l'étape 1 n'est jamais annulée.
 *
 * Cet ordre est le point important : une notification perdue parce que le
 * téléphone était éteint serait invisible pour toujours.
 */
class Notificateur
{
    /**
     * Notifie un utilisateur.
     *
     * @param array<string,mixed> $donnees ce que l'application doit ouvrir
     */
    public static function envoyer(
        int|User $destinataire,
        string $type,
        string $titre,
        string $corps,
        array $donnees = []
    ): ?Notification {
        $userId = $destinataire instanceof User
            ? $destinataire->id
            : $destinataire;

        if (!$userId) {
            return null;
        }

        $notification = Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'titre' => $titre,
            'corps' => $corps,
            'donnees' => $donnees ?: null,
        ]);

        // Le push ne doit jamais faire échouer l'enregistrement.
        try {
            self::pousser($notification);
        } catch (\Throwable $e) {
            Log::warning('Push non envoyé : ' . $e->getMessage());
        }

        return $notification;
    }

    /** Le push est-il configuré ? */
    public static function pushConfigure(): bool
    {
        return (bool) config('services.firebase.cle_serveur');
    }

    /**
     * Envoie la notification aux appareils de l'utilisateur.
     *
     * Sans clé Firebase, la méthode ne fait rien : l'application reste
     * fonctionnelle, seule l'alerte hors application manque.
     */
    private static function pousser(Notification $notification): void
    {
        if (!self::pushConfigure()) {
            return;
        }

        $jetons = Appareil::where('user_id', $notification->user_id)
            ->pluck('jeton_push')
            ->all();

        if (empty($jetons)) {
            return;
        }

        $reponse = Http::withHeaders([
            'Authorization' => 'key=' . config('services.firebase.cle_serveur'),
            'Content-Type' => 'application/json',
        ])->timeout(10)->post('https://fcm.googleapis.com/fcm/send', [
            'registration_ids' => $jetons,
            'notification' => [
                'title' => $notification->titre,
                'body' => $notification->corps,
            ],
            'data' => array_merge(
                $notification->donnees ?? [],
                ['notification_id' => (string) $notification->id]
            ),
        ]);

        if ($reponse->failed()) {
            Log::warning('FCM a refusé l’envoi', [
                'statut' => $reponse->status(),
            ]);
        }
    }
}
