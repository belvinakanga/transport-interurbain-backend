<?php

namespace App\Services;

use App\Models\Appareil;
use App\Models\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Envoi des alertes push par l'API FCM HTTP v1.
 *
 * L'ancienne API — `fcm.googleapis.com/fcm/send` avec une « clé serveur »
 * dans l'en-tête — a été fermée par Google. La version v1 demande un jeton
 * OAuth2 obtenu à partir d'un compte de service :
 *
 *   1. on signe un JWT avec la clé privée du compte de service ;
 *   2. Google l'échange contre un jeton d'accès valable une heure ;
 *   3. ce jeton autorise l'envoi vers `projects/{projet}/messages:send`.
 *
 * Le jeton est mis en cache : le redemander à chaque notification
 * ajouterait un aller-retour réseau pour rien.
 *
 * Aucune dépendance Composer n'est nécessaire : la signature RS256 tient en
 * un appel à openssl_sign.
 */
class PushFirebase
{
    /** Droit demandé à Google : envoyer des messages, rien d'autre. */
    private const PORTEE = 'https://www.googleapis.com/auth/firebase.messaging';

    /** Durée accordée par Google à un jeton d'accès. */
    private const DUREE_JETON = 3600;

    /** On renouvelle avant l'échéance, pour ne pas envoyer avec un jeton mort. */
    private const MARGE = 300;

    /** Clé de cache du jeton d'accès. */
    public const CLE_CACHE = 'firebase.jeton_acces';

    /** Identifiants lus une seule fois par requête. */
    private static ?array $identifiants = null;

    /**
     * Le compte de service est-il en place ?
     *
     * Faux : le push est simplement inactif, les notifications restent
     * enregistrées en base et visibles dans l'application.
     */
    public static function configure(): bool
    {
        return self::identifiants() !== null;
    }

    /** Identifiant du projet Firebase, tiré du fichier du compte de service. */
    public static function projet(): ?string
    {
        return self::identifiants()['project_id'] ?? null;
    }

    /**
     * Pousse une notification vers tous les appareils de son destinataire.
     *
     * Un appareil que Firebase ne connaît plus est oublié au passage :
     * sans cela, la liste des jetons grossit indéfiniment et chaque envoi
     * traîne des erreurs 404.
     */
    public static function envoyer(Notification $notification): void
    {
        $projet = self::projet();

        if (!$projet) {
            return;
        }

        $appareils = Appareil::where('user_id', $notification->user_id)->get();

        if ($appareils->isEmpty()) {
            return;
        }

        $jetonAcces = self::jetonAcces();

        if (!$jetonAcces) {
            return;
        }

        $url = 'https://fcm.googleapis.com/v1/projects/'
            . $projet
            . '/messages:send';

        foreach ($appareils as $appareil) {

            $reponse = Http::withToken($jetonAcces)
                ->timeout(10)
                ->post($url, [
                    'message' => self::message(
                        $notification,
                        $appareil->jeton_push
                    ),
                ]);

            if ($reponse->successful()) {
                continue;
            }

            /*
             * 404 : Firebase ne connaît plus ce jeton — application
             * désinstallée, données effacées. L'appareil est retiré.
             */
            if ($reponse->status() === 404) {

                $appareil->delete();

                Log::info('Appareil push oublié : jeton inconnu de Firebase', [
                    'appareil' => $appareil->id,
                ]);

                continue;
            }

            Log::warning('FCM a refusé l’envoi', [
                'statut' => $reponse->status(),
                'erreur' => $reponse->json('error.status')
                    ?? $reponse->json('error.message'),
                'appareil' => $appareil->id,
            ]);
        }
    }

    /**
     * Jeton d'accès OAuth2, mis en cache jusqu'à peu avant son échéance.
     */
    public static function jetonAcces(): ?string
    {
        $identifiants = self::identifiants();

        if (!$identifiants) {
            return null;
        }

        $enCache = Cache::get(self::CLE_CACHE);

        if (is_string($enCache) && $enCache !== '') {
            return $enCache;
        }

        $assertion = self::assertion($identifiants);

        if (!$assertion) {
            return null;
        }

        $reponse = Http::asForm()
            ->timeout(10)
            ->post($identifiants['token_uri'] ?? 'https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $assertion,
            ]);

        $jeton = $reponse->json('access_token');

        if (!$reponse->successful() || !is_string($jeton) || $jeton === '') {

            Log::warning('Jeton Firebase refusé', [
                'statut' => $reponse->status(),
                'erreur' => $reponse->json('error_description')
                    ?? $reponse->json('error'),
            ]);

            return null;
        }

        $duree = (int) ($reponse->json('expires_in') ?? self::DUREE_JETON);

        Cache::put(
            self::CLE_CACHE,
            $jeton,
            max(60, $duree - self::MARGE)
        );

        return $jeton;
    }

    /**
     * Corps d'un message FCM v1.
     *
     * `data` n'accepte que des chaînes : un entier laissé tel quel fait
     * rejeter tout le message avec INVALID_ARGUMENT.
     */
    private static function message(
        Notification $notification,
        string $jetonAppareil
    ): array {
        return [
            'token' => $jetonAppareil,

            'notification' => [
                'title' => $notification->titre,
                'body' => $notification->corps,
            ],

            'data' => self::donneesTexte(array_merge(
                $notification->donnees ?? [],
                ['notification_id' => $notification->id]
            )),

            /*
             * Pas de channel_id : l'application mobile ne déclare aucun
             * canal de notification. Nommer un canal inexistant ferait
             * ignorer l'alerte en silence sur Android 8 et au-delà ; sans
             * nom, Android retombe sur le canal créé par Firebase.
             */
            'android' => [
                'priority' => 'high',
            ],

            'apns' => [
                'payload' => [
                    'aps' => ['sound' => 'default'],
                ],
            ],
        ];
    }

    /** @param array<string,mixed> $donnees @return array<string,string> */
    private static function donneesTexte(array $donnees): array
    {
        $texte = [];

        foreach ($donnees as $cle => $valeur) {

            if ($valeur === null) {
                continue;
            }

            $texte[(string) $cle] = is_scalar($valeur)
                ? (string) $valeur
                : json_encode($valeur);
        }

        return $texte;
    }

    /**
     * JWT signé avec la clé privée du compte de service.
     */
    private static function assertion(array $identifiants): ?string
    {
        $cle = $identifiants['private_key'] ?? null;
        $emetteur = $identifiants['client_email'] ?? null;

        if (!$cle || !$emetteur) {
            return null;
        }

        $maintenant = time();

        $entete = self::base64Url(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ]));

        $charge = self::base64Url(json_encode([
            'iss' => $emetteur,
            'scope' => self::PORTEE,
            'aud' => $identifiants['token_uri'] ?? 'https://oauth2.googleapis.com/token',
            'iat' => $maintenant,
            'exp' => $maintenant + self::DUREE_JETON,
        ]));

        $signature = '';

        if (!openssl_sign(
            $entete . '.' . $charge,
            $signature,
            $cle,
            OPENSSL_ALGO_SHA256
        )) {
            Log::warning('Clé privée Firebase illisible : signature impossible');

            return null;
        }

        return $entete . '.' . $charge . '.' . self::base64Url($signature);
    }

    private static function base64Url(string $valeur): string
    {
        return rtrim(strtr(base64_encode($valeur), '+/', '-_'), '=');
    }

    /**
     * Contenu du fichier du compte de service, ou null s'il n'y en a pas.
     */
    private static function identifiants(): ?array
    {
        if (self::$identifiants !== null) {
            return self::$identifiants ?: null;
        }

        // Tableau vide = « déjà cherché, rien trouvé », pour ne pas relire
        // le disque à chaque notification.
        self::$identifiants = [];

        $chemin = config('services.firebase.compte_service');

        if (!is_string($chemin) || $chemin === '') {
            return null;
        }

        if (!str_starts_with($chemin, DIRECTORY_SEPARATOR)) {
            $chemin = base_path($chemin);
        }

        if (!is_readable($chemin)) {

            Log::warning('Compte de service Firebase introuvable', [
                'chemin' => $chemin,
            ]);

            return null;
        }

        $contenu = json_decode((string) file_get_contents($chemin), true);

        if (!is_array($contenu) || empty($contenu['project_id'])) {

            Log::warning('Compte de service Firebase illisible', [
                'chemin' => $chemin,
            ]);

            return null;
        }

        self::$identifiants = $contenu;

        return $contenu;
    }

    /** Pour les tests : force la relecture du fichier. */
    public static function oublierIdentifiants(): void
    {
        self::$identifiants = null;
    }
}
