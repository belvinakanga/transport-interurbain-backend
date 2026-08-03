<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VoyageurController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Liste des utilisateurs
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $recherche = $request->input('recherche');
        $role = $request->input('role');
        $parPage = $request->input('par_page', 10);

        $query = User::query();

        // Recherche par nom ou email
        if (!empty($recherche)) {
            $query->where(function ($q) use ($recherche) {
                $q->where('name', 'like', "%{$recherche}%")
                  ->orWhere('email', 'like', "%{$recherche}%");
            });
        }

        // Filtre par rôle
        if (!empty($role)) {
            $query->where('role', $role);
        }

        $voyageurs = $query
            ->latest()
            ->paginate($parPage)
            ->withQueryString();

        return view('admin.voyageurs', compact('voyageurs'));
    }

    /*
    |--------------------------------------------------------------------------
    | Afficher le formulaire d'ajout
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        // Vérifie que la vue existe
        if (!view()->exists('admin.create-voyageur')) {
            abort(500, 'La vue admin.create-voyageur est introuvable.');
        }

        return view('admin.create-voyageur');
    }

    /*
    |--------------------------------------------------------------------------
    | Enregistrer un nouvel utilisateur
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,agence,user',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()
            ->route('voyageurs.index')
            ->with('success', 'Utilisateur ajouté avec succès.');
    }

    /*
    |--------------------------------------------------------------------------
    | Voir un utilisateur
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $voyageur = User::findOrFail($id);

        return view('admin.show-voyageur', compact('voyageur'));
    }

    /*
    |--------------------------------------------------------------------------
    | Supprimer un utilisateur
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $voyageur = User::findOrFail($id);

        $voyageur->delete();

        return redirect()
            ->route('voyageurs.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}