<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Commande Inspire
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


/*
|--------------------------------------------------------------------------
| Expiration automatique des réservations
|--------------------------------------------------------------------------
|
| Toutes les minutes, Laravel vérifie les réservations non payées.
|
| Si une réservation est arrivée à 72 heures ou moins du départ :
|
| - la réservation est annulée ;
| - les sièges sont libérés ;
| - les places disponibles sont restaurées ;
| - les sièges peuvent être achetés directement.
|
|--------------------------------------------------------------------------
*/

Schedule::command('reservations:expirer')
    ->everyMinute();