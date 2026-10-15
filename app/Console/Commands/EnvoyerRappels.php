<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Services\Notificateur;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Rappels automatiques liés aux voyages et aux réservations impayées.
 *
 * La commande repasse régulièrement et renvoie l'ensemble des rappels dus.
 * Elle ne tient aucun registre de ce qu'elle a déjà fait : c'est la clé
 * d'unicité de chaque notification qui l'en empêche. Conséquence utile,
 * elle peut être relancée à la main sans rien dupliquer.
 *
 * Quatre rappels :
 *
 * 1. Voyage la veille — pour les réservations payées.
 * 2. Voyage imminent — moins de trois heures avant le départ.
 * 3. Réservation impayée — la journée suivant sa création.
 * 4. Dernier avertissement — avant la libération automatique des sièges.
 *
 * Les points 3 et 4 encadrent `reservations:expirer`, qui annule les
 * réservations non payées à 72 heures du départ : le voyageur est prévenu
 * avant de perdre ses sièges, pas après.
 */
class EnvoyerRappels extends Command
{
    protected $signature = 'notifications:rappels';

    protected $description =
        'Envoie les rappels de voyage et les relances de réservation impayée';

    /** Heures avant le départ en deçà desquelles le voyage est « proche ». */
    private const FENETRE_VEILLE = 28;

    /** Heures avant le départ en deçà desquelles le départ est imminent. */
    private const FENETRE_IMMINENT = 3;

    /** Heures après la création avant de relancer une réservation impayée. */
    private const DELAI_RELANCE = 20;

    /**
     * Heures avant le départ où les sièges d'une réservation impayée sont
     * libérés. Doit rester aligné sur `reservations:expirer`.
     */
    private const EXPIRATION = 72;

    public function handle(): int
    {
        $envoyes = 0;

        $envoyes += $this->rappelsDeVoyage();
        $envoyes += $this->relancesImpayees();

        $this->info($envoyes . ' rappel(s) envoyé(s).');

        return Command::SUCCESS;
    }

    /*
    |--------------------------------------------------------------------------
    | RAPPELS DE VOYAGE
    |--------------------------------------------------------------------------
    |
    | Concerne les réservations payées — réservation classique comme achat
    | direct — dont le départ n'a pas encore eu lieu.
    |
    */

    private function rappelsDeVoyage(): int
    {
        $envoyes = 0;

        $reservations = Reservation::with(['trajet', 'sieges'])
            ->whereRaw('lower(trim(statut)) in (?, ?)', ['payée', 'confirmée'])
            ->get();

        foreach ($reservations as $reservation) {

            $depart = $this->depart($reservation);

            if (!$depart) {
                continue;
            }

            $heures = $this->heuresAvant($depart);

            // Voyage passé, ou encore trop loin pour en parler.
            if ($heures <= 0 || $heures > self::FENETRE_VEILLE) {
                continue;
            }

            $trajet = $reservation->trajet;
            $sieges = $this->sieges($reservation);

            if ($heures <= self::FENETRE_IMMINENT) {

                $envoye = Notificateur::envoyerUneFois(
                    $reservation->user_id,
                    'voyage-imminent-' . $reservation->id,
                    'voyage_imminent',
                    'Départ imminent',
                    'Votre car '
                    . $trajet->depart . ' → ' . $trajet->arrivee
                    . ' part à ' . $this->heure($depart) . '.'
                    . $sieges
                    . ' Présentez-vous au point de départ.',
                    $this->lienVoyage($reservation)
                );

            } else {

                $envoye = Notificateur::envoyerUneFois(
                    $reservation->user_id,
                    'voyage-rappel-' . $reservation->id,
                    'voyage_rappel',
                    'Votre voyage approche',
                    $trajet->depart . ' → ' . $trajet->arrivee
                    . ' ' . $this->quand($depart) . '.'
                    . $sieges
                    . ' Pensez à votre pièce d’identité.',
                    $this->lienVoyage($reservation)
                );
            }

            if ($envoye) {
                $envoyes++;

                $this->line(
                    '→ Réservation #' . $reservation->id
                    . ' : rappel de voyage.'
                );
            }
        }

        return $envoyes;
    }

    /*
    |--------------------------------------------------------------------------
    | RELANCES DES RÉSERVATIONS IMPAYÉES
    |--------------------------------------------------------------------------
    |
    | Une réservation en attente immobilise des sièges. Deux relances :
    | une le lendemain, une juste avant la libération automatique.
    |
    */

    private function relancesImpayees(): int
    {
        $envoyes = 0;

        $reservations = Reservation::with(['trajet', 'sieges'])
            ->whereRaw('lower(trim(statut)) = ?', ['en attente'])
            ->get();

        foreach ($reservations as $reservation) {

            $depart = $this->depart($reservation);

            if (!$depart) {
                continue;
            }

            $heures = $this->heuresAvant($depart);

            if ($heures <= 0) {
                continue;
            }

            $trajet = $reservation->trajet;

            $itineraire = $trajet->depart . ' → ' . $trajet->arrivee;

            /*
            |--------------------------------------------------------------------------
            | DERNIER AVERTISSEMENT
            |--------------------------------------------------------------------------
            |
            | La journée qui précède la libération des sièges.
            |
            */

            if (
                $heures <= self::EXPIRATION + 24
                && $heures > self::EXPIRATION
            ) {

                $restantes = (int) ceil($heures - self::EXPIRATION);

                $envoye = Notificateur::envoyerUneFois(
                    $reservation->user_id,
                    'reservation-derniere-chance-' . $reservation->id,
                    'reservation_derniere_chance',
                    'Vos sièges vont être libérés',
                    'Réservation ' . $reservation->reference_reservation
                    . ' — ' . $itineraire . '. '
                    . 'Sans paiement sous '
                    . $restantes . ' heure' . ($restantes > 1 ? 's' : '')
                    . ', vos '
                    . $reservation->nombre_places
                    . ' place' . ($reservation->nombre_places > 1 ? 's' : '')
                    . ' seront remises en vente.',
                    $this->lienVoyage($reservation)
                );

                if ($envoye) {
                    $envoyes++;

                    $this->line(
                        '→ Réservation #' . $reservation->id
                        . ' : dernier avertissement.'
                    );
                }

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | PREMIÈRE RELANCE
            |--------------------------------------------------------------------------
            |
            | Le lendemain de la réservation, si le départ laisse encore le
            | temps de payer.
            |
            */

            if (
                $heures > self::EXPIRATION
                && $reservation->created_at
                && $reservation->created_at->diffInHours(now()) >= self::DELAI_RELANCE
            ) {

                $envoye = Notificateur::envoyerUneFois(
                    $reservation->user_id,
                    'reservation-attente-' . $reservation->id,
                    'reservation_attente',
                    'Réservation en attente de paiement',
                    'Réservation ' . $reservation->reference_reservation
                    . ' — ' . $itineraire
                    . ' ' . $this->quand($depart) . '. '
                    . 'Vos places ne sont tenues que jusqu’à 72 heures '
                    . 'avant le départ.',
                    $this->lienVoyage($reservation)
                );

                if ($envoye) {
                    $envoyes++;

                    $this->line(
                        '→ Réservation #' . $reservation->id
                        . ' : relance de paiement.'
                    );
                }
            }
        }

        return $envoyes;
    }

    /*
    |--------------------------------------------------------------------------
    | OUTILS
    |--------------------------------------------------------------------------
    */

    /** Date et heure du départ, ou null si le trajet a disparu. */
    private function depart(Reservation $reservation): ?Carbon
    {
        $trajet = $reservation->trajet;

        if (!$trajet || !$trajet->date_depart) {
            return null;
        }

        try {
            return Carbon::parse(
                Carbon::parse($trajet->date_depart)->format('Y-m-d')
                . ' '
                . ($trajet->heure_depart ?: '00:00')
            );
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Heures restantes avant le départ, négatives s'il est passé. */
    private function heuresAvant(Carbon $depart): float
    {
        // Carbon rend déjà un flottant : 30.5 pour trente heures et demie.
        return now()->diffInHours($depart, false);
    }

    /** « demain à 06:30 », « aujourd’hui à 14:00 », « le 21/10 à 05:00 ». */
    private function quand(Carbon $depart): string
    {
        if ($depart->isToday()) {
            return 'aujourd’hui à ' . $this->heure($depart);
        }

        if ($depart->isTomorrow()) {
            return 'demain à ' . $this->heure($depart);
        }

        return 'le ' . $depart->format('d/m') . ' à ' . $this->heure($depart);
    }

    private function heure(Carbon $depart): string
    {
        return $depart->format('H:i');
    }

    /** « Siège 12. » — vide si les sièges ne sont pas numérotés. */
    private function sieges(Reservation $reservation): string
    {
        $numeros = $reservation->sieges
            ->pluck('numero_siege')
            ->filter()
            ->values();

        if ($numeros->isEmpty()) {
            return '';
        }

        return ' Siège'
            . ($numeros->count() > 1 ? 's ' : ' ')
            . $numeros->implode(', ')
            . '.';
    }

    /** Ce que l'application ouvre à l'appui sur la notification. */
    private function lienVoyage(Reservation $reservation): array
    {
        return [
            'ecran' => 'voyages',
            'reservation_id' => $reservation->id,
        ];
    }
}
