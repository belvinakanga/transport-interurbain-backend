<?php

namespace App\Http\Controllers;

use App\Models\Abonnement;
use Illuminate\Http\Request;

class AbonnementController extends Controller
{
    /**
     * Afficher la liste des abonnements.
     */
    public function index(Request $request)
    {
        $parPage = $request->get('par_page', 10);

        $abonnements = Abonnement::with('agence')
            ->when($request->filled('recherche'), function ($query) use ($request) {
                $query->whereHas('agence', function ($q) use ($request) {
                    $q->where('nom_agence', 'like', '%' . $request->recherche . '%');
                });
            })
            ->when($request->filled('statut'), function ($query) use ($request) {
                $query->where('statut', $request->statut);
            })
            ->orderByDesc('id')
            ->paginate($parPage)
            ->withQueryString();

        return view('admin.abonnements', compact('abonnements'));
    }

    /**
     * Afficher un abonnement.
     */
    public function show($id)
    {
        $abonnement = Abonnement::with('agence')->findOrFail($id);

        return view('admin.show-abonnement', compact('abonnement'));
    }

    /**
     * Supprimer un abonnement.
     */
    public function destroy($id)
    {
        $abonnement = Abonnement::findOrFail($id);

        $abonnement->delete();

        return redirect('/admin/abonnements')
            ->with('success', 'Abonnement supprimé avec succès.');
    }
}