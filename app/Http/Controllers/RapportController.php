<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Agence;
use App\Models\Trajet;
use App\Models\Reservation;
use App\Models\Billet;
use App\Models\Paiement;
use App\Models\Avis;
use App\Models\Achat;
use App\Models\Abonnement;
use App\Models\Voyageur;

class RapportController extends Controller
{
    public function index()
    {
        return view('admin.rapports', [
            'users' => User::count(),
            'agences' => Agence::count(),
            'trajets' => Trajet::count(),
            'voyageurs' => Voyageur::count(),
            'reservations' => Reservation::count(),
            'billets' => Billet::count(),
            'paiements' => Paiement::count(),
            'avis' => Avis::count(),
            'achats' => Achat::count(),
            'abonnements' => Abonnement::count(),
        ]);
    }
}