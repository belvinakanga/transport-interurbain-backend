<?php

namespace Tests\Feature;

use App\Models\Appareil;
use App\Models\Notification;
use App\Models\User;
use App\Services\Notificateur;
use App\Services\PushFirebase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Envoi des alertes push par l'API FCM HTTP v1.
 *
 * Rien ici ne sort sur le réseau : un compte de service est fabriqué de
 * toutes pièces, avec une vraie paire de clés RSA pour que la signature du
 * JWT soit réellement vérifiable, et les réponses de Google sont simulées.
 */
class PushFirebaseTest extends TestCase
{
    use RefreshDatabase;

    private string $fichier;

    private string $clePublique;

    protected function setUp(): void
    {
        parent::setUp();

        $paire = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        openssl_pkey_export($paire, $clePrivee);

        $this->clePublique = openssl_pkey_get_details($paire)['key'];

        $this->fichier = tempnam(sys_get_temp_dir(), 'firebase-') . '.json';

        file_put_contents($this->fichier, json_encode([
            'type' => 'service_account',
            'project_id' => 'tokende-essai',
            'private_key' => $clePrivee,
            'client_email' => 'robot@tokende-essai.iam.gserviceaccount.com',
            'token_uri' => 'https://oauth2.googleapis.com/token',
        ]));

        config(['services.firebase.compte_service' => $this->fichier]);

        PushFirebase::oublierIdentifiants();
    }

    protected function tearDown(): void
    {
        @unlink($this->fichier);

        PushFirebase::oublierIdentifiants();

        parent::tearDown();
    }

    /** Réponses de Google : jeton accordé, envoi accepté. */
    private function googleRepond(int $statutEnvoi = 200, array $corps = []): void
    {
        Http::fake([
            'oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'jeton-accorde',
                'expires_in' => 3600,
            ]),

            'fcm.googleapis.com/*' => Http::response(
                $corps ?: ['name' => 'projects/tokende-essai/messages/1'],
                $statutEnvoi
            ),
        ]);
    }

    private function appareil(User $user, string $jeton = 'jeton-appareil'): Appareil
    {
        return Appareil::enregistrer($user->id, $jeton, 'android');
    }

    private function notification(User $user): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => 'voyage_rappel',
            'titre' => 'Départ demain',
            'corps' => 'Brazzaville → Pointe-Noire, 06:30.',
            'donnees' => ['ecran' => 'voyages', 'reservation_id' => 7],
        ]);
    }

    public function test_sans_fichier_le_push_est_inactif(): void
    {
        config(['services.firebase.compte_service' => '/introuvable.json']);

        PushFirebase::oublierIdentifiants();

        $this->assertFalse(PushFirebase::configure());
        $this->assertNull(PushFirebase::projet());
    }

    public function test_le_projet_vient_du_compte_de_service(): void
    {
        $this->assertTrue(PushFirebase::configure());
        $this->assertSame('tokende-essai', PushFirebase::projet());
    }

    public function test_le_jwt_est_signe_avec_la_cle_du_compte_de_service(): void
    {
        $this->googleRepond();

        $this->assertSame('jeton-accorde', PushFirebase::jetonAcces());

        Http::assertSent(function ($requete) {

            if (!str_contains($requete->url(), 'oauth2.googleapis.com')) {
                return false;
            }

            $donnees = $requete->data();

            $this->assertSame(
                'urn:ietf:params:oauth:grant-type:jwt-bearer',
                $donnees['grant_type']
            );

            [$entete, $charge, $signature] = explode('.', $donnees['assertion']);

            $revendications = json_decode(
                base64_decode(strtr($charge, '-_', '+/')),
                true
            );

            $this->assertSame(
                'robot@tokende-essai.iam.gserviceaccount.com',
                $revendications['iss']
            );

            $this->assertSame(
                'https://www.googleapis.com/auth/firebase.messaging',
                $revendications['scope']
            );

            $this->assertSame(
                'https://oauth2.googleapis.com/token',
                $revendications['aud']
            );

            // La signature doit tenir face à la clé publique : c'est tout
            // ce que Google vérifie de son côté.
            $this->assertSame(1, openssl_verify(
                $entete . '.' . $charge,
                base64_decode(strtr($signature, '-_', '+/')),
                $this->clePublique,
                OPENSSL_ALGO_SHA256
            ));

            return true;
        });
    }

    public function test_le_jeton_dacces_est_mis_en_cache(): void
    {
        $this->googleRepond();

        PushFirebase::jetonAcces();
        PushFirebase::jetonAcces();

        $this->assertSame('jeton-accorde', Cache::get(PushFirebase::CLE_CACHE));

        // Un seul aller-retour, pas deux.
        Http::assertSentCount(1);
    }

    public function test_lenvoi_cible_le_projet_et_porte_le_jeton(): void
    {
        $this->googleRepond();

        $user = User::factory()->create();
        $this->appareil($user);

        PushFirebase::envoyer($this->notification($user));

        Http::assertSent(function ($requete) {

            if (!str_contains($requete->url(), 'fcm.googleapis.com')) {
                return false;
            }

            $this->assertSame(
                'https://fcm.googleapis.com/v1/projects/tokende-essai/messages:send',
                $requete->url()
            );

            $this->assertSame(
                'Bearer jeton-accorde',
                $requete->header('Authorization')[0]
            );

            $message = $requete->data()['message'];

            $this->assertSame('jeton-appareil', $message['token']);
            $this->assertSame('Départ demain', $message['notification']['title']);

            return true;
        });
    }

    public function test_les_donnees_sont_envoyees_en_chaines(): void
    {
        $this->googleRepond();

        $user = User::factory()->create();
        $this->appareil($user);

        $notification = $this->notification($user);

        PushFirebase::envoyer($notification);

        Http::assertSent(function ($requete) use ($notification) {

            if (!str_contains($requete->url(), 'fcm.googleapis.com')) {
                return false;
            }

            $donnees = $requete->data()['message']['data'];

            // FCM v1 rejette tout le message si une valeur de `data` n'est
            // pas une chaîne : l'entier 7 devient « 7 ».
            foreach ($donnees as $valeur) {
                $this->assertIsString($valeur);
            }

            $this->assertSame('7', $donnees['reservation_id']);
            $this->assertSame('voyages', $donnees['ecran']);

            $this->assertSame(
                (string) $notification->id,
                $donnees['notification_id']
            );

            return true;
        });
    }

    public function test_un_appareil_inconnu_de_firebase_est_oublie(): void
    {
        $this->googleRepond(404, ['error' => ['status' => 'NOT_FOUND']]);

        $user = User::factory()->create();
        $appareil = $this->appareil($user);

        PushFirebase::envoyer($this->notification($user));

        $this->assertDatabaseMissing('appareils', ['id' => $appareil->id]);
    }

    public function test_une_autre_erreur_ne_supprime_pas_lappareil(): void
    {
        $this->googleRepond(400, ['error' => ['status' => 'INVALID_ARGUMENT']]);

        $user = User::factory()->create();
        $appareil = $this->appareil($user);

        PushFirebase::envoyer($this->notification($user));

        // Un message mal formé n'est pas la faute de l'appareil.
        $this->assertDatabaseHas('appareils', ['id' => $appareil->id]);
    }

    public function test_chaque_appareil_recoit_son_message(): void
    {
        $this->googleRepond();

        $user = User::factory()->create();
        $this->appareil($user, 'telephone');
        $this->appareil($user, 'tablette');

        PushFirebase::envoyer($this->notification($user));

        // Un jeton d'accès, puis un envoi par appareil.
        Http::assertSentCount(3);
    }

    public function test_le_notificateur_passe_par_lapi_v1(): void
    {
        $this->googleRepond();

        $user = User::factory()->create();
        $this->appareil($user);

        Notificateur::envoyer(
            $user,
            'billet_emis',
            'Billet émis',
            'Votre billet est disponible.',
            ['ecran' => 'billets']
        );

        $this->assertTrue(Notificateur::pushConfigure());

        Http::assertSent(
            fn ($requete) => str_contains($requete->url(), '/messages:send')
        );

        // L'ancienne API ne doit plus être appelée.
        Http::assertNotSent(
            fn ($requete) => str_contains($requete->url(), '/fcm/send')
        );
    }

    public function test_la_notification_est_enregistree_meme_si_le_push_echoue(): void
    {
        Http::fake([
            'oauth2.googleapis.com/token' => Http::response(
                ['error' => 'invalid_grant'],
                400
            ),
        ]);

        $user = User::factory()->create();
        $this->appareil($user);

        $notification = Notificateur::envoyer(
            $user,
            'info',
            'Message',
            'Corps du message.'
        );

        // C'est le point entier du module : la base d'abord.
        $this->assertNotNull($notification);
        $this->assertDatabaseHas('notifications', ['id' => $notification->id]);
    }
}
