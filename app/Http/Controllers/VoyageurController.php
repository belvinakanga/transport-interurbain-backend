<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Agence;
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
        // Vérifier que la vue existe
        if (!view()->exists('admin.create-voyageur')) {
            abort(500, 'La vue admin.create-voyageur est introuvable.');
        }

        // Récupérer les agences
        $agences = Agence::orderBy('nom_agence')->get();

        return view(
            'admin.create-voyageur',
            compact('agences')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Enregistrer un nouvel utilisateur
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'email' => 'required|email|max:255|unique:users,email',

            'password' => 'required|string|min:6|confirmed',

            'role' => 'required|in:admin,agent,user',

            // Une agence est obligatoire si le rôle est agent
            'agence_id' => 'required_if:role,agent|nullable|exists:agences,id',
        ]);

        User::create([
            'name' => $validated['name'],

            'email' => $validated['email'],

            'password' => Hash::make($validated['password']),

            'role' => $validated['role'],

            // Enregistrer l'agence sélectionnée
            'agence_id' => $validated['agence_id'] ?? null,
        ]);

        return redirect()
            ->route('voyageurs.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /*
    |--------------------------------------------------------------------------
    | Voir un utilisateur
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $voyageur = User::findOrFail($id);

        return view(
            'admin.show-voyageur',
            compact('voyageur')
        );
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

    /*
    |--------------------------------------------------------------------------
    | Afficher le formulaire de modification
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $voyageur = User::findOrFail($id);

        // Récupérer les agences
        $agences = Agence::orderBy('nom_agence')->get();

        return view(
            'admin.edit-voyageur',
            compact('voyageur', 'agences')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Enregistrer les modifications
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $voyageur = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'email' => 'required|email|max:255|unique:users,email,' . $voyageur->id,

            'role' => 'required|in:admin,agent,user',

            // Une agence est obligatoire si le rôle est agent
            'agence_id' => 'required_if:role,agent|nullable|exists:agences,id',

            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $voyageur->name = $validated['name'];

        $voyageur->email = $validated['email'];

        $voyageur->role = $validated['role'];

        // Enregistrer l'agence
        $voyageur->agence_id = $validated['agence_id'] ?? null;

        // Modifier le mot de passe uniquement s'il est renseigné
        if (!empty($validated['password'])) {
            $voyageur->password = Hash::make(
                $validated['password']
            );
        }

        $voyageur->save();

        return redirect()
            ->route('voyageurs.index')
            ->with('success', 'Utilisateur modifié avec succès.');
    }
}