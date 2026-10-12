<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billet;
use App\Http\Controllers\Concerns\RestreintAuProprietaire;

class BilletController extends Controller
{
    use RestreintAuProprietaire;

    /*
    |--------------------------------------------------------------------------
    | Liste de tous les billets (API)
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $billets = Billet::with([
    'voyageur',
    'reservation.user',
    'reservation.trajet.agence'
])
        // Un billet porte le nom et l'adresse e-mail de son titulaire :
        // un client ne voit que les siens. Le back-office voit tout.
        ->when(
            ! $this->consulteToutLeMonde(),
            fn ($requete) => $requete->whereHas(
                'reservation',
                fn ($reservation) => $reservation->where('user_id', auth()->id())
            )
        )
        ->latest()->get();

        return response()->json($billets);
    }

    /*
    |--------------------------------------------------------------------------
    | Mes billets
    |--------------------------------------------------------------------------
    */
    public function mesBillets()
    {
        $billets = Billet::with([
    'voyageur',
    'reservation.user',
    'reservation.trajet.agence'
])
        ->whereHas('reservation', function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->latest()
        ->get();

        return view('billets.index', compact('billets'));
    }

    /*
    |--------------------------------------------------------------------------
    | Créer un billet
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id'
        ]);

        $billet = Billet::create([
    'reservation_id' => $request->reservation_id,
    'qr_code' => 'QR-' . strtoupper(
        substr(
            bin2hex(random_bytes(8)),
            0,
            12
        )
    ),
]);

        return response()->json([
            'message' => 'Billet créé avec succès',
            'data' => $billet
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Afficher un billet
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $billet = Billet::with([
    'voyageur',
    'reservation.user',
    'reservation.trajet.agence'
])->findOrFail($id);

        return view('admin.show-billet', compact('billet'));
    }

    /*
    |--------------------------------------------------------------------------
    | Supprimer un billet
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $billet = Billet::findOrFail($id);

        $billet->delete();

        return redirect('/admin/billets')
            ->with('success', 'Billet supprimé avec succès.');
    }

    public function publicTicket($qr_code)
{
    $billet = Billet::with([
        'voyageur',
        'reservation.user',
        'reservation.trajet.agence',
        'siege'
    ])
    ->where('qr_code', $qr_code)
    ->firstOrFail();

    return view('billets.public', compact('billet'));
}
}