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
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| ROUTES PROTÉGÉES
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PROFIL
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | ADMINISTRATION
    |--------------------------------------------------------------------------
    */

    Route::get('/admin', [AdminController::class, 'index']);
    

   /*
|--------------------------------------------------------------------------
| AGENCES
|--------------------------------------------------------------------------
*/

Route::get('/admin/agences', [AdminController::class, 'agences']);

Route::get('/admin/agences/create', [AdminController::class, 'createAgence']);

Route::post('/admin/agences/store', [AdminController::class, 'storeAgence']);

/*
|--------------------------------------------------------------------------
| Voir une agence
|--------------------------------------------------------------------------
*/

Route::get('/admin/agences/{id}', [AdminController::class, 'showAgence']);

/*
|--------------------------------------------------------------------------
| Modifier une agence
|--------------------------------------------------------------------------
*/

Route::get('/admin/agences/{id}/edit', [AdminController::class, 'editAgence']);

Route::put('/admin/agences/{id}', [AdminController::class, 'updateAgence']);

/*
|--------------------------------------------------------------------------
| Supprimer une agence
|--------------------------------------------------------------------------
*/

Route::delete('/admin/agences/{id}', [AdminController::class, 'destroyAgence']);
    /*
    |--------------------------------------------------------------------------
    | TRAJETS
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/trajets', [AdminController::class, 'trajets']);
    Route::get('/admin/trajets/create', [AdminController::class, 'createTrajet']);
    Route::get('/admin/trajets/{id}', [AdminController::class, 'showTrajet']);
    Route::post('/admin/trajets/store', [AdminController::class, 'storeTrajet']);
    Route::get('/admin/trajets/{id}/edit', [AdminController::class, 'editTrajet']);
    Route::put('/admin/trajets/{id}', [AdminController::class, 'updateTrajet']);
    Route::get('/admin/trajets', [AdminController::class, 'trajets']);
Route::get('/admin/trajets/create', [AdminController::class, 'createTrajet']);
Route::post('/admin/trajets/store', [AdminController::class, 'storeTrajet']);
Route::get('/admin/trajets/{id}', [AdminController::class, 'showTrajet']);
Route::get('/admin/trajets/{id}/edit', [AdminController::class, 'editTrajet']);
Route::put('/admin/trajets/{id}', [AdminController::class, 'updateTrajet']);
Route::delete('/admin/trajets/{id}', [AdminController::class, 'destroyTrajet']);

    /*
    |--------------------------------------------------------------------------
    | RESERVATIONS
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/reservations', [AdminController::class, 'reservations']);

    /*
    |--------------------------------------------------------------------------
    | PAIEMENTS
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/paiements', [AdminController::class, 'paiements']);

    /*
    |--------------------------------------------------------------------------
    | BILLETS ADMIN
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/billets', [AdminController::class, 'billets']);
    Route::get('/admin/billets/{id}', [BilletController::class, 'show']);
    Route::delete('/admin/billets/{id}', [BilletController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | ESPACE VOYAGEUR
    |--------------------------------------------------------------------------
    */

    Route::get('/voyages', [VoyageController::class, 'index']);

    Route::get('/reservation/create/{id}', [ReservationClientController::class, 'create']);
    Route::post('/reservation/store', [ReservationClientController::class, 'store']);

    Route::put('/reservations/{id}/annuler', [ReservationController::class, 'annuler']);

    /*
    |--------------------------------------------------------------------------
    | PAIEMENT CLIENT
    |--------------------------------------------------------------------------
    */

    Route::get('/paiement/create/{reservation}', [PaiementController::class, 'create']);
    Route::post('/paiement/store', [PaiementController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | MES BILLETS
    |--------------------------------------------------------------------------
    */

    Route::get('/mes-billets', [BilletController::class, 'mesBillets']);

    /*
    |--------------------------------------------------------------------------
    | API AGENCES
    |--------------------------------------------------------------------------
    */

    Route::get('/agences', [AgenceController::class, 'index']);
    Route::post('/agences', [AgenceController::class, 'store']);
    Route::get('/agences/{id}', [AgenceController::class, 'show']);
    Route::delete('/agences/{id}', [AgenceController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | API TRAJETS
    |--------------------------------------------------------------------------
    */

    Route::get('/trajets', [TrajetController::class, 'index']);
    Route::post('/trajets', [TrajetController::class, 'store']);
    Route::get('/trajets/{id}', [TrajetController::class, 'show']);
    Route::put('/trajets/{id}', [TrajetController::class, 'update']);
    Route::delete('/trajets/{id}', [TrajetController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | API RESERVATIONS
    |--------------------------------------------------------------------------
    */

    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/reservations/{id}', [ReservationController::class, 'show']);
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | API PAIEMENTS
    |--------------------------------------------------------------------------
    */

    Route::get('/paiements', [PaiementController::class, 'index']);
    Route::get('/paiements/{id}', [PaiementController::class, 'show']);
    Route::delete('/paiements/{id}', [PaiementController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | API BILLETS
    |--------------------------------------------------------------------------
    */

    Route::get('/billets', [BilletController::class, 'index']);
    Route::post('/billets', [BilletController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | API AVIS
    |--------------------------------------------------------------------------
    */

    Route::get('/avis', [AvisController::class, 'index']);
    Route::post('/avis', [AvisController::class, 'store']);
    Route::get('/avis/{id}', [AvisController::class, 'show']);
    Route::delete('/avis/{id}', [AvisController::class, 'destroy']);
    Route::get('/admin/avis', [AdminController::class, 'avis']);
    Route::get('/admin/billets', [AdminController::class, 'billets']);

    /*
    |--------------------------------------------------------------------------
    | API ACHATS
    |--------------------------------------------------------------------------
    */

    Route::get('/achats', [AchatController::class, 'index']);
    Route::post('/achats', [AchatController::class, 'store']);
    Route::get('/achats/{id}', [AchatController::class, 'show']);
    Route::delete('/achats/{id}', [AchatController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| ADMIN - AVIS
|--------------------------------------------------------------------------
*/

Route::get('/admin/avis', [AdminController::class, 'avis']);
Route::get('/admin/avis/{id}', [AvisController::class, 'show']);
Route::delete('/admin/avis/{id}', [AvisController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| ADMIN - VOYAGEURS
|--------------------------------------------------------------------------
*/

Route::get('/admin/voyageurs', [VoyageurController::class, 'index']);
Route::get('/admin/voyageurs/{id}', [VoyageurController::class, 'show']);
Route::delete('/admin/voyageurs/{id}', [VoyageurController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| ADMIN - VOYAGEURS
|--------------------------------------------------------------------------
*/

Route::get('/admin/voyageurs', [VoyageurController::class, 'index'])
    ->name('voyageurs.index');

Route::get('/admin/voyageurs/create', [VoyageurController::class, 'create'])
    ->name('voyageurs.create');

Route::post('/admin/voyageurs', [VoyageurController::class, 'store'])
    ->name('voyageurs.store');

Route::get('/admin/voyageurs/{id}', [VoyageurController::class, 'show'])
    ->whereNumber('id')
    ->name('voyageurs.show');

Route::delete('/admin/voyageurs/{id}', [VoyageurController::class, 'destroy'])
    ->whereNumber('id')
    ->name('voyageurs.destroy');

    /*
|--------------------------------------------------------------------------
| ADMIN - ABONNEMENTS
|--------------------------------------------------------------------------
*/

Route::get('/admin/abonnements', [AbonnementController::class, 'index']);
Route::get('/admin/abonnements/{id}', [AbonnementController::class, 'show']);
Route::delete('/admin/abonnements/{id}', [AbonnementController::class, 'destroy']);


    require __DIR__.'/auth.php';