<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use App\Models\Trajet;
use Carbon\Carbon;

class ExpirerReservations extends Command
{
    /**
     * Nom de la commande
     */
    protected $signature = 'reservations:expirer';

    /**
     * Description de la commande
     */
    protected $description = 'Expire automatiquement les réservations non payées à moins de 72 heures du départ';

    /**
     * Exécution de la commande
     */
    public function handle()
    {
        // Récupérer les réservations en attente
        $reservations = Reservation::where(
            'statut',
            'en attente'
        )->get();

        foreach ($reservations as $reservation) {

            $trajet = Trajet::find(
                $reservation->trajet_id
            );

            if (!$trajet) {
                continue;
            }

            // Date et heure du départ
            $dateDepart = Carbon::parse(
                $trajet->date_depart . ' ' . $trajet->heure_depart
            );

            // Si moins de 72 heures avant le départ
            if (now()->diffInHours($dateDepart, false) < 72) {

                // Réservation expirée
                $reservation->statut = 'expirée';

                $reservation->save();

                // Remettre les places disponibles
                $trajet->places_disponibles =
                    $trajet->places_disponibles +
                    $reservation->nombre_places;

                $trajet->save();

                $this->info(
                    'Réservation #' .
                    $reservation->id .
                    ' expirée.'
                );
            }
        }

        $this->info(
            'Vérification des réservations terminée.'
        );

        return Command::SUCCESS;
    }
}