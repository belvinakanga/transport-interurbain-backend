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

/*
|--------------------------------------------------------------------------
| Rappels de voyage et relances de paiement
|--------------------------------------------------------------------------
|
| Toutes les quinze minutes :
|
| - rappel la veille du départ ;
| - rappel moins de trois heures avant le départ ;
| - relance des réservations non payées ;
| - dernier avertissement avant la libération des sièges.
|
| La commande se repose sur la clé d'unicité des notifications pour ne
| jamais envoyer deux fois le même rappel. Elle est donc sans danger à
| relancer à la main.
|
|--------------------------------------------------------------------------
*/

Schedule::command('notifications:rappels')
    ->everyFifteenMinutes()
    ->withoutOverlapping();
