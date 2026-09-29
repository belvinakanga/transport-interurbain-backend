<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VoyageController;
use App\Http\Controllers\ReservationClientController;
use App\Http\Controllers\AgenceController;
use App\Http\Controllers\TrajetController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\BilletController;
use App\Http\Controllers\AvisController;
use App\Http\Controllers\AchatController;
use App\Http\Controllers\VoyageurController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\AbonnementController;
use App\Http\Controllers\SiegeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\PaiementAgenceController;


/*
|--------------------------------------------------------------------------
| PAGE D'ACCUEIL
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return redirect('/admin');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| ROUTES PROTÉGÉES
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    /*
|--------------------------------------------------------------------------
| ESPACE AGENT
|--------------------------------------------------------------------------
*/

Route::get(
    '/agent/dashboard',
    [AgentController::class, 'dashboard']
)->name('agent.dashboard');

Route::get(
    '/agent/paiements',
    [AgentController::class, 'paiements']
)->name('agent.paiements');

    /*
    |--------------------------------------------------------------------------
    | PROFIL
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | ADMINISTRATION - DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin',
        [AdminController::class, 'index']
    );


    /*
    |--------------------------------------------------------------------------
    | ADMIN - AGENCES
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/agences',
        [AdminController::class, 'agences']
    );

    Route::get(
        '/admin/agences/create',
        [AdminController::class, 'createAgence']
    );

    Route::post(
        '/admin/agences/store',
        [AdminController::class, 'storeAgence']
    );

    Route::get(
        '/admin/agences/{id}/edit',
        [AdminController::class, 'editAgence']
    );

    Route::put(
        '/admin/agences/{id}',
        [AdminController::class, 'updateAgence']
    );

    Route::get(
        '/admin/agences/{id}',
        [AdminController::class, 'showAgence']
    );

    Route::delete(
        '/admin/agences/{id}',
        [AdminController::class, 'destroyAgence']
    );


    /*
    |--------------------------------------------------------------------------
    | ADMIN - TRAJETS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/trajets',
        [AdminController::class, 'trajets']
    )->name('admin.trajets');

    Route::get(
        '/admin/trajets/create',
        [AdminController::class, 'createTrajet']
    );

    Route::post(
        '/admin/trajets/store',
        [AdminController::class, 'storeTrajet']
    );

    Route::get(
        '/admin/trajets/{id}/edit',
        [AdminController::class, 'editTrajet']
    );

    Route::put(
        '/admin/trajets/{id}',
        [AdminController::class, 'updateTrajet']
    );

    Route::get(
        '/admin/trajets/{id}',
        [AdminController::class, 'showTrajet']
    );

    Route::delete(
        '/admin/trajets/{id}',
        [AdminController::class, 'destroyTrajet']
    );


    /*
    |--------------------------------------------------------------------------
    | ADMIN - RÉSERVATIONS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/reservations',
        [AdminController::class, 'reservations']
    )->name('admin.reservations');


    /*
    |--------------------------------------------------------------------------
    | ADMIN - PAIEMENTS VOYAGEURS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/paiements',
        [AdminController::class, 'paiements']
    );

    Route::get(
    '/admin/commissions',
    [AdminController::class, 'commissions']
)->name('admin.commissions');


    /*
|--------------------------------------------------------------------------
| ADMIN - BILLETS
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/billets',
    [AdminController::class, 'billets']
);

Route::post(
    '/admin/billets/verifier',
    [AdminController::class, 'verifierBillet']
)->name('admin.billets.verifier');

Route::get(
    '/admin/billets/{id}',
    [BilletController::class, 'show']
);

Route::delete(
    '/admin/billets/{id}',
    [BilletController::class, 'destroy']
);


    /*
    |--------------------------------------------------------------------------
    | ADMIN - AVIS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/avis',
        [AdminController::class, 'avis']
    );

    Route::get(
        '/admin/avis/{id}',
        [AvisController::class, 'show']
    );

    Route::delete(
        '/admin/avis/{id}',
        [AvisController::class, 'destroy']
    );


    /*
|--------------------------------------------------------------------------
| ADMIN - VOYAGEURS
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/voyageurs',
    [VoyageurController::class, 'index']
)->name('voyageurs.index');

Route::get(
    '/admin/voyageurs/create',
    [VoyageurController::class, 'create']
)->name('voyageurs.create');

Route::post(
    '/admin/voyageurs',
    [VoyageurController::class, 'store']
)->name('voyageurs.store');

/*
|--------------------------------------------------------------------------
| MODIFIER UN UTILISATEUR
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/voyageurs/{id}/edit',
    [VoyageurController::class, 'edit']
)
    ->whereNumber('id')
    ->name('voyageurs.edit');

Route::put(
    '/admin/voyageurs/{id}',
    [VoyageurController::class, 'update']
)
    ->whereNumber('id')
    ->name('voyageurs.update');

/*
|--------------------------------------------------------------------------
| VOIR UN UTILISATEUR
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/voyageurs/{id}',
    [VoyageurController::class, 'show']
)
    ->whereNumber('id')
    ->name('voyageurs.show');

/*
|--------------------------------------------------------------------------
| SUPPRIMER UN UTILISATEUR
|--------------------------------------------------------------------------
*/

Route::delete(
    '/admin/voyageurs/{id}',
    [VoyageurController::class, 'destroy']
)
    ->whereNumber('id')
    ->name('voyageurs.destroy');

    /*
    |--------------------------------------------------------------------------
    | ADMIN - ABONNEMENTS
    |--------------------------------------------------------------------------
    |
    | IMPORTANT :
    | /create doit être placé avant /{id}
    |
    */

    Route::get(
        '/admin/abonnements',
        [AbonnementController::class, 'index']
    )->name('admin.abonnements');

    Route::get(
        '/admin/abonnements/create',
        [AbonnementController::class, 'create']
    )->name('admin.abonnements.create');

    Route::post(
        '/admin/abonnements',
        [AbonnementController::class, 'store']
    )->name('admin.abonnements.store');

    Route::get(
        '/admin/abonnements/{id}',
        [AbonnementController::class, 'show']
    )
        ->whereNumber('id')
        ->name('admin.abonnements.show');

    Route::delete(
        '/admin/abonnements/{id}',
        [AbonnementController::class, 'destroy']
    )
        ->whereNumber('id')
        ->name('admin.abonnements.destroy');

    Route::get(
    '/admin/abonnements/{id}/edit',
    [AbonnementController::class, 'edit']
)
    ->whereNumber('id')
    ->name('admin.abonnements.edit');

Route::put(
    '/admin/abonnements/{id}',
    [AbonnementController::class, 'update']
)
    ->whereNumber('id')
    ->name('admin.abonnements.update');    


    /*
    |--------------------------------------------------------------------------
    | ADMIN / AGENT - PAIEMENTS DES AGENCES
    |--------------------------------------------------------------------------
    |
    | L'Admin crée l'échéance.
    | L'Agent consulte et paie son échéance.
    |
    */

    Route::get(
        '/paiements-agences',
        [PaiementAgenceController::class, 'index']
    )->name('paiements-agences.index');

    /*
     * Ancienne page de création manuelle.
     * On la conserve pour l'instant afin de ne rien casser,
     * mais avec notre nouveau fonctionnement l'Abonnement
     * crée automatiquement l'échéance.
     */
    Route::get(
        '/paiements-agences/create',
        [PaiementAgenceController::class, 'create']
    )->name('paiements-agences.create');

    Route::post(
        '/paiements-agences',
        [PaiementAgenceController::class, 'store']
    )->name('paiements-agences.store');

    /*
     * Page de confirmation de paiement côté Agent.
     */
    Route::get(
        '/paiements-agences/{id}/pay',
        [PaiementAgenceController::class, 'paymentForm']
    )
        ->whereNumber('id')
        ->name('paiements-agences.pay.form');

    /*
     * Effectuer le paiement côté Agent.
     */
    Route::post(
        '/paiements-agences/{id}/pay',
        [PaiementAgenceController::class, 'pay']
    )
        ->whereNumber('id')
        ->name('paiements-agences.pay');

    /*
     * Validation manuelle côté Admin.
     */
    Route::patch(
        '/paiements-agences/{id}/paid',
        [PaiementAgenceController::class, 'markAsPaid']
    )
        ->whereNumber('id')
        ->name('paiements-agences.paid');


    /*
    |--------------------------------------------------------------------------
    | ESPACE VOYAGEUR
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/voyages',
        [VoyageController::class, 'index']
    );

    Route::get(
        '/reservation/create/{id}',
        [ReservationClientController::class, 'create']
    );

    Route::post(
        '/reservation/store',
        [ReservationClientController::class, 'store']
    );

    Route::put(
        '/reservations/{id}/annuler',
        [ReservationController::class, 'annuler']
    );


    /*
    |--------------------------------------------------------------------------
    | PAIEMENT CLIENT
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/paiement/create/{reservation}',
        [PaiementController::class, 'create']
    );

    Route::post(
        '/paiement/store',
        [PaiementController::class, 'store']
    );


    /*
    |--------------------------------------------------------------------------
    | MES BILLETS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/mes-billets',
        [BilletController::class, 'mesBillets']
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

    Route::post(
        '/agences',
        [AgenceController::class, 'store']
    );

    Route::get(
        '/agences/{id}',
        [AgenceController::class, 'show']
    );

    Route::delete(
        '/agences/{id}',
        [AgenceController::class, 'destroy']
    );


    /*
    |--------------------------------------------------------------------------
    | TRAJETS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/trajets',
        [TrajetController::class, 'index']
    );

    Route::post(
        '/trajets',
        [TrajetController::class, 'store']
    );

    Route::get(
        '/trajets/{id}',
        [TrajetController::class, 'show']
    );

    Route::put(
        '/trajets/{id}',
        [TrajetController::class, 'update']
    );

    Route::delete(
        '/trajets/{id}',
        [TrajetController::class, 'destroy']
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

    Route::delete(
        '/reservations/{id}',
        [ReservationController::class, 'destroy']
    );


    /*
    |--------------------------------------------------------------------------
    | PAIEMENTS
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

    Route::delete(
        '/paiements/{id}',
        [PaiementController::class, 'destroy']
    );


    /*
    |--------------------------------------------------------------------------
    | BILLETS
    |--------------------------------------------------------------------------
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
    | AVIS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/avis',
        [AvisController::class, 'index']
    );

    Route::post(
        '/avis',
        [AvisController::class, 'store']
    );

    Route::get(
        '/avis/{id}',
        [AvisController::class, 'show']
    );

    Route::delete(
        '/avis/{id}',
        [AvisController::class, 'destroy']
    );


    /*
    |--------------------------------------------------------------------------
    | ACHATS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/achats',
        [AchatController::class, 'index']
    )->name('achats.index');

    Route::post(
        '/achats',
        [AchatController::class, 'store']
    )->name('achats.store');

    Route::get(
        '/achats/{id}',
        [AchatController::class, 'show']
    )->name('achats.show');

    Route::delete(
        '/achats/{id}',
        [AchatController::class, 'destroy']
    )->name('achats.destroy');


    /*
    |--------------------------------------------------------------------------
    | ESPACE AGENT
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/agent/dashboard',
        [AgentController::class, 'dashboard']
    )->name('agent.dashboard');

});

/*
|--------------------------------------------------------------------------
| BILLET PUBLIC
|--------------------------------------------------------------------------
*/

Route::get(
    '/billet/{qr_code}',
    [BilletController::class, 'publicTicket']
)->name('billet.public');

/*
|--------------------------------------------------------------------------
| API - AUTHENTIFICATION
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

Route::middleware('auth:sanctum')->group(function () {

    Route::get(
        '/me',
        [AuthController::class, 'me']
    );

    Route::post(
        '/logout',
        [AuthController::class, 'logout']
    );


    /*
    |--------------------------------------------------------------------------
    | API SIÈGES
    |--------------------------------------------------------------------------
    */

});


/*
|--------------------------------------------------------------------------
| AUTHENTIFICATION WEB
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';