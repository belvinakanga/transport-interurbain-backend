<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Models\Reservation;
use App\Models\Trajet;
use App\Models\Siege;

use Carbon\Carbon;

class ExpirerReservations extends Command
{
    /**
     * Nom de la commande.
     */
    protected $signature = 'reservations:expirer';

    /**
     * Description de la commande.
     */
    protected $description =
        'Annule automatiquement les réservations non payées arrivées dans les 72 heures avant le départ';

    /**
     * Exécution de la commande.
     */
    public function handle()
    {
        $this->info(
            'Vérification des réservations non payées...'
        );

        /*
        |--------------------------------------------------------------------------
        | RÉCUPÉRER LES RÉSERVATIONS EN ATTENTE
        |--------------------------------------------------------------------------
        */

        $reservations = Reservation::where(
            'statut',
            'en attente'
        )->get();

        $nombreExpirees = 0;

        foreach ($reservations as $reservation) {

            /*
            |--------------------------------------------------------------------------
            | RÉCUPÉRER LE TRAJET
            |--------------------------------------------------------------------------
            */

            $trajet = Trajet::find(
                $reservation->trajet_id
            );

            if (!$trajet) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | DATE ET HEURE DU DÉPART
            |--------------------------------------------------------------------------
            */

            $dateDepart = Carbon::parse(
                $trajet->date_depart
                . ' '
                . $trajet->heure_depart
            );


            /*
            |--------------------------------------------------------------------------
            | CALCUL DU TEMPS RESTANT
            |--------------------------------------------------------------------------
            */

            $heuresRestantes = now()->diffInHours(
                $dateDepart,
                false
            );


            /*
            |--------------------------------------------------------------------------
            | SI LE DÉPART EST DANS 72 H OU MOINS
            |--------------------------------------------------------------------------
            */

           if ($heuresRestantes <= 72) {

                DB::transaction(function () use (
                    $reservation,
                    $trajet
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | VERROUILLER LA RÉSERVATION
                    |--------------------------------------------------------------------------
                    */

                    $reservationVerrouillee =
                        Reservation::where(
                            'id',
                            $reservation->id
                        )
                        ->lockForUpdate()
                        ->first();

                    if (!$reservationVerrouillee) {
                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | VÉRIFIER QU'ELLE EST TOUJOURS EN ATTENTE
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $reservationVerrouillee->statut
                        !==
                        'en attente'
                    ) {
                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | LIBÉRER LES SIÈGES
                    |--------------------------------------------------------------------------
                    */

                    Siege::where(
                        'reservation_id',
                        $reservationVerrouillee->id
                    )->update([

                        'statut' =>
                            'disponible',

                        'reservation_id' =>
                            null,

                        'voyageur_id' =>
                            null,
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | RESTAURER LES PLACES DISPONIBLES
                    |--------------------------------------------------------------------------
                    */

                    $trajet->places_disponibles =
                        $trajet->places_disponibles
                        +
                        $reservationVerrouillee->nombre_places;


                    /*
                    |--------------------------------------------------------------------------
                    | NE PAS DÉPASSER LE NOMBRE TOTAL
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $trajet->places_disponibles
                        >
                        $trajet->places_totales
                    ) {

                        $trajet->places_disponibles =
                            $trajet->places_totales;
                    }


                    $trajet->save();


                    /*
                    |--------------------------------------------------------------------------
                    | ANNULER LA RÉSERVATION
                    |--------------------------------------------------------------------------
                    */

                    $reservationVerrouillee->statut =
                        'annulée';

                    $reservationVerrouillee->save();


                    /*
                    |--------------------------------------------------------------------------
                    | MESSAGE TERMINAL
                    |--------------------------------------------------------------------------
                    */

                    $this->info(
                        'Réservation #'
                        . $reservationVerrouillee->id
                        . ' annulée automatiquement.'
                    );

                    $this->info(
                        '→ '
                        . $reservationVerrouillee->nombre_places
                        . ' siège(s) libéré(s).'
                    );
                });


                $nombreExpirees++;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | RÉSULTAT
        |--------------------------------------------------------------------------
        */

        $this->info(
            'Vérification terminée.'
        );

        $this->info(
            $nombreExpirees
            . ' réservation(s) traitée(s).'
        );


        return Command::SUCCESS;
    }
}