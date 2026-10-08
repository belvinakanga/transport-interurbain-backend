<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\AgenceController;
use App\Http\Controllers\AchatController;
use App\Http\Controllers\AvisController;
use App\Http\Controllers\BilletController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SiegeController;
use App\Http\Controllers\TrajetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\SupportController;


/*
|--------------------------------------------------------------------------
| API TOKENDE
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| AUTHENTIFICATION
|--------------------------------------------------------------------------
*/

Route::post(
    '/register',
    [AuthController::class, 'register']
);

Route::post(
    '/login',
    [AuthController::class, 'login']
);


/*
|--------------------------------------------------------------------------
| ROUTES PUBLIQUES
|--------------------------------------------------------------------------
|
| Ces routes sont accessibles sans authentification.
|
*/


/*
|--------------------------------------------------------------------------
| TRAJETS
|--------------------------------------------------------------------------
*/

Route::get(
    '/trajets',
    [TrajetController::class, 'index']
);

Route::get(
    '/trajets/{id}',
    [TrajetController::class, 'show']
);


/*
|--------------------------------------------------------------------------
| SIÈGES D'UN TRAJET
|--------------------------------------------------------------------------
*/

Route::get(
    '/trajets/{id}/sieges',
    [SiegeController::class, 'index']
);


/*
|--------------------------------------------------------------------------
| AGENCES
|--------------------------------------------------------------------------
*/

Route::get(
    '/agences',
    [AgenceController::class, 'index']
);

Route::get(
    '/agences/{id}',
    [AgenceController::class, 'show']
);


/*
|--------------------------------------------------------------------------
| AVIS PUBLICS
|--------------------------------------------------------------------------
*/

Route::get(
    '/avis',
    [AvisController::class, 'index']
);

Route::get(
    '/avis/{id}',
    [AvisController::class, 'show']
);


/*
|--------------------------------------------------------------------------
| BILLETS - LECTURE
|--------------------------------------------------------------------------
*/

Route::get(
    '/billets',
    [BilletController::class, 'index']
);


/*
|--------------------------------------------------------------------------
| PAIEMENTS - LECTURE
|--------------------------------------------------------------------------
*/

Route::get(
    '/paiements',
    [PaiementController::class, 'index']
);

Route::get(
    '/paiements/{id}',
    [PaiementController::class, 'show']
);


/*
|--------------------------------------------------------------------------
| ROUTES PROTÉGÉES - SANCTUM
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | UTILISATEUR CONNECTÉ
    |--------------------------------------------------------------------------
    */

    // Récupérer les informations de l'utilisateur connecté
    Route::get(
        '/me',
        [AuthController::class, 'me']
    );

    // Modifier les informations du profil
    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    );


    /*
    |--------------------------------------------------------------------------
    | DÉCONNEXION
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/logout',
        [AuthController::class, 'logout']
    );



        /*
    |--------------------------------------------------------------------------
    | SUPPORT - CONVERSATIONS
    |--------------------------------------------------------------------------
    */

    // Liste des conversations du voyageur connecté
    Route::get(
        '/support/conversations',
        [SupportController::class, 'conversations']
    );

    // Créer une nouvelle conversation
    Route::post(
        '/support/conversations',
        [SupportController::class, 'storeConversation']
    );

    // Afficher une conversation et ses messages
    Route::get(
        '/support/conversations/{id}',
        [SupportController::class, 'showConversation']
    );

    // Envoyer un message dans une conversation
    Route::post(
        '/support/conversations/{id}/messages',
        [SupportController::class, 'sendMessage']
    );

    /*
    |--------------------------------------------------------------------------
    | RÉSERVATIONS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/reservations',
        [ReservationController::class, 'index']
    );

    Route::post(
        '/reservations',
        [ReservationController::class, 'store']
    );

    Route::get(
        '/reservations/{id}',
        [ReservationController::class, 'show']
    );

    Route::post(
        '/reservations/{id}/payer',
        [ReservationController::class, 'payer']
    );

    Route::post(
        '/reservations/{id}/annuler',
        [ReservationController::class, 'annuler']
    );

    Route::delete(
        '/reservations/{id}',
        [ReservationController::class, 'destroy']
    );


    /*
    |--------------------------------------------------------------------------
    | ACHATS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/achats',
        [AchatController::class, 'index']
    );

    Route::post(
        '/achats',
        [AchatController::class, 'store']
    );

    Route::get(
        '/achats/{id}',
        [AchatController::class, 'show']
    );


    /*
    |--------------------------------------------------------------------------
    | AVIS
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/avis',
        [AvisController::class, 'store']
    );


    /*
    |--------------------------------------------------------------------------
    | BILLETS
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/billets',
        [BilletController::class, 'store']
    );

});