<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use App\Models\Billet;
use App\Models\Paiement;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Réservations de l'utilisateur
        $reservations = Reservation::with('trajet')
            ->where('user_id', $user->id)
            ->get();

        // IDs des réservations
        $reservationIds = $reservations->pluck('id');

        // Billets liés aux réservations
        $billets = Billet::whereIn(
            'reservation_id',
            $reservationIds
        )->get();

        // Paiements liés aux réservations
        $paiements = Paiement::whereIn(
            'reservation_id',
            $reservationIds
        )->get();

        return view('dashboard', compact(
            'user',
            'reservations',
            'billets',
            'paiements'
        ));
    }
}