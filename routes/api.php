<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\VilleController;
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
| VILLES - RÉFÉRENTIEL
|--------------------------------------------------------------------------
|
| Donnée de référence publique, comme les trajets et les agences :
| l'application mobile en a besoin pour son sélecteur de ville.
|
*/

Route::get(
    '/villes',
    [VilleController::class, 'index']
);


/*
|--------------------------------------------------------------------------
| MOT DE PASSE OUBLIÉ
|--------------------------------------------------------------------------
|
| Publiques par nécessité : l'utilisateur ne peut pas être authentifié
| puisqu'il a perdu son mot de passe. La protection repose sur le code
| envoyé par e-mail et sur la limitation du nombre de tentatives.
|
*/

Route::post(
    '/forgot-password',
    [PasswordResetController::class, 'demanderCode']
);

Route::post(
    '/verify-reset-code',
    [PasswordResetController::class, 'verifierCode']
);

Route::post(
    '/reset-password',
    [PasswordResetController::class, 'reinitialiser']
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
    |
    | La lecture est authentifiée : un billet porte le nom et l'adresse
    | e-mail de son titulaire. Le contrôleur restreint en plus la liste au
    | demandeur — voir BilletController::index().
    |
    */

    Route::get(
        '/billets',
        [BilletController::class, 'index']
    );

    Route::post(
        '/billets',
        [BilletController::class, 'store']
    );


    /*
    |--------------------------------------------------------------------------
    | PAIEMENTS - LECTURE
    |--------------------------------------------------------------------------
    |
    | Un paiement porte le montant et le numéro de téléphone du payeur :
    | ces routes ne peuvent pas être publiques. Le contrôleur restreint en
    | plus au demandeur.
    |
    */

    Route::get(
        '/paiements',
        [PaiementController::class, 'index']
    );

    Route::get(
        '/paiements/{id}',
        [PaiementController::class, 'show']
    );

});

/*
|--------------------------------------------------------------------------
| OPENPAY - PAIEMENT MOBILE MONEY
|--------------------------------------------------------------------------
|
| Désactivé pendant les tests fonctionnels : l'achat est alors validé
| directement par AchatController, sans paiement réel.
|
| Rien n'est supprimé — les contrôleurs et le service restent en place.
| Pour rebrancher le paiement en ligne : OPENPAY_ACTIF=true dans .env,
| puis `php artisan config:clear`.
|
*/

use App\Http\Controllers\Api\PaymentOpenPayController;
use App\Http\Controllers\Webhook\OpenPayWebhookController;

if (config('services.openpay.actif')) {

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/openpay/initiate', [PaymentOpenPayController::class, 'initiate']);
        Route::get('/openpay/status/{reference}', [PaymentOpenPayController::class, 'status']);
    });

    // Webhook public (callback OpenPay)
    Route::post('/webhooks/openpay', [OpenPayWebhookController::class, 'handle'])->name('webhooks.openpay');
}
