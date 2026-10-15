<?php

namespace App\Console\Commands;

use App\Models\Appareil;
use App\Models\User;
use App\Services\Notificateur;
use App\Services\PushFirebase;
use Illuminate\Console\Command;

/**
 * Vérifie la chaîne de push de bout en bout.
 *
 * Sans destinataire, la commande se contente de contrôler la
 * configuration : compte de service lisible, jeton d'accès accordé par
 * Google, appareils connus. C'est le diagnostic à lancer en premier quand
 * « les notifications n'arrivent pas ».
 */
class TesterPush extends Command
{
    protected $signature = 'push:tester
        {user? : identifiant ou e-mail du destinataire du message de test}
        {--titre=Test Tokende}
        {--corps=Si vous lisez ceci, les notifications push fonctionnent.}';

    protected $description =
        'Contrôle la configuration Firebase et, si un utilisateur est donné, lui envoie une notification de test';

    public function handle(): int
    {
        /*
        |--------------------------------------------------------------------------
        | COMPTE DE SERVICE
        |--------------------------------------------------------------------------
        */

        if (!PushFirebase::configure()) {

            $this->error('Compte de service Firebase absent ou illisible.');

            $this->line(
                'Attendu : '
                . config('services.firebase.compte_service')
            );

            return Command::FAILURE;
        }

        $this->info('Projet Firebase : ' . PushFirebase::projet());


        /*
        |--------------------------------------------------------------------------
        | JETON D'ACCÈS
        |--------------------------------------------------------------------------
        |
        | C'est l'étape qui échoue quand la clé privée a été révoquée ou que
        | l'horloge de la machine est fausse : Google refuse un JWT dont la
        | date de création est dans le futur.
        |
        */

        $jeton = PushFirebase::jetonAcces();

        if (!$jeton) {

            $this->error(
                'Google a refusé le jeton d’accès. Voir storage/logs pour le motif.'
            );

            return Command::FAILURE;
        }

        $this->info('Jeton d’accès obtenu (' . strlen($jeton) . ' caractères).');


        /*
        |--------------------------------------------------------------------------
        | APPAREILS ENREGISTRÉS
        |--------------------------------------------------------------------------
        */

        $total = Appareil::count();

        $this->line($total . ' appareil(s) enregistré(s) au total.');

        if ($total === 0) {
            $this->warn(
                'Aucun appareil : l’application mobile doit s’être connectée '
                . 'au moins une fois pour déclarer son jeton.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ENVOI DE TEST
        |--------------------------------------------------------------------------
        */

        $cible = $this->argument('user');

        if (!$cible) {

            $this->info('Configuration valide. Aucun envoi demandé.');

            return Command::SUCCESS;
        }

        $utilisateur = is_numeric($cible)
            ? User::find((int) $cible)
            : User::where('email', $cible)->first();

        if (!$utilisateur) {

            $this->error('Utilisateur introuvable : ' . $cible);

            return Command::FAILURE;
        }

        $appareils = Appareil::where('user_id', $utilisateur->id)->count();

        if ($appareils === 0) {
            $this->warn(
                $utilisateur->email
                . ' n’a aucun appareil : la notification sera enregistrée '
                . 'en base, mais aucun push ne partira.'
            );
        }

        Notificateur::envoyer(
            $utilisateur,
            'info',
            (string) $this->option('titre'),
            (string) $this->option('corps'),
            ['ecran' => 'notifications']
        );

        $this->info(
            'Notification envoyée à '
            . $utilisateur->email
            . ' (' . $appareils . ' appareil(s)).'
        );

        return Command::SUCCESS;
    }
}
