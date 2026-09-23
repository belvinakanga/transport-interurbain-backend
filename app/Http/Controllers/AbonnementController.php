<?php

namespace App\Http\Controllers;

use App\Models\Abonnement;
use App\Models\Agence;
use App\Models\PaiementAgence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AbonnementController extends Controller
{
    /**
     * ============================================================
     * LISTE DES ABONNEMENTS
     * ============================================================
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Vérification Admin
        |--------------------------------------------------------------------------
        */

        if (!auth()->check()) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Vous devez être connecté.'
                );
        }

        if (auth()->user()->role !== 'admin') {
            abort(403, 'Accès refusé.');
        }


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $parPage = (int) $request->get('par_page', 10);

        if (!in_array($parPage, [10, 25, 50, 100])) {
            $parPage = 10;
        }


        /*
        |--------------------------------------------------------------------------
        | Requête
        |--------------------------------------------------------------------------
        */

        $abonnements = Abonnement::with('agence')

            ->when(
                $request->filled('recherche'),
                function ($query) use ($request) {

                    $recherche = $request->recherche;

                    $query->whereHas(
                        'agence',
                        function ($q) use ($recherche) {

                            $q->where(
                                'nom_agence',
                                'like',
                                '%' . $recherche . '%'
                            );

                        }
                    );

                }
            )

            ->when(
                $request->filled('statut'),
                function ($query) use ($request) {

                    $query->where(
                        'statut',
                        $request->statut
                    );

                }
            )

            ->orderByDesc('id')

            ->paginate($parPage)

            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Vue
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.abonnements',
            compact('abonnements')
        );
    }


    /**
     * ============================================================
     * FORMULAIRE DE CRÉATION
     * ============================================================
     */
    public function create()
    {
        /*
        |--------------------------------------------------------------------------
        | Vérification Admin
        |--------------------------------------------------------------------------
        */

        if (!auth()->check()) {
            return redirect()
                ->route('login');
        }

        if (auth()->user()->role !== 'admin') {
            abort(403, 'Accès refusé.');
        }


        /*
        |--------------------------------------------------------------------------
        | Récupérer les agences
        |--------------------------------------------------------------------------
        */

        $agences = Agence::orderBy(
            'nom_agence'
        )->get();


        /*
        |--------------------------------------------------------------------------
        | Vue
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.create-abonnement',
            compact('agences')
        );
    }


    /**
     * ============================================================
     * ENREGISTRER UN ABONNEMENT
     * ============================================================
     *
     * Lorsqu'un abonnement est créé :
     *
     * 1. L'abonnement est enregistré.
     * 2. Une échéance de paiement est automatiquement créée.
     *
     * Le flux devient :
     *
     * AGENCE
     *     ↓
     * PAIEMENT DE L'ABONNEMENT
     *     ↓
     * TOKENDE
     *
     * ============================================================
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Vérification Admin
        |--------------------------------------------------------------------------
        */

        if (!auth()->check()) {
            return redirect()
                ->route('login');
        }

        if (auth()->user()->role !== 'admin') {
            abort(403, 'Accès refusé.');
        }


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate(
            [

                'agence_id' => [
                    'required',
                    'integer',
                    'exists:agences,id',
                ],

                'type' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'montant' => [
                    'required',
                    'numeric',
                    'min:0',
                ],

                'date_debut' => [
                    'required',
                    'date',
                ],

                'date_fin' => [
                    'required',
                    'date',
                    'after_or_equal:date_debut',
                ],

            ],
            [

                'agence_id.required' =>
                    'Veuillez sélectionner une agence.',

                'agence_id.exists' =>
                    'L’agence sélectionnée n’existe pas.',

                'type.required' =>
                    'Veuillez renseigner le type d’abonnement.',

                'montant.required' =>
                    'Veuillez renseigner le montant.',

                'montant.numeric' =>
                    'Le montant doit être numérique.',

                'montant.min' =>
                    'Le montant ne peut pas être négatif.',

                'date_debut.required' =>
                    'Veuillez sélectionner une date de début.',

                'date_fin.required' =>
                    'Veuillez sélectionner une date de fin.',

                'date_fin.after_or_equal' =>
                    'La date de fin doit être postérieure ou égale à la date de début.',

            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Vérifier l'agence
        |--------------------------------------------------------------------------
        */

        $agence = Agence::findOrFail(
            $validated['agence_id']
        );


        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        |
        | On crée l'abonnement ET son échéance ensemble.
        |
        */

        DB::transaction(
            function () use (
                $validated,
                $agence
            ) {

                /*
                |--------------------------------------------------------------------------
                | Créer l'abonnement
                |--------------------------------------------------------------------------
                */

                $abonnement = Abonnement::create([

                    'agence_id' =>
                        $validated['agence_id'],

                    'type' =>
                        $validated['type'],

                    'montant' =>
                        $validated['montant'],

                    'date_debut' =>
                        $validated['date_debut'],

                    'date_fin' =>
                        $validated['date_fin'],

                    'statut' =>
                        'Actif',

                ]);


                /*
                |--------------------------------------------------------------------------
                | Créer automatiquement l'échéance
                |--------------------------------------------------------------------------
                */

                PaiementAgence::create([

                    'agence_id' =>
                        $validated['agence_id'],

                    'abonnement_id' =>
                        $abonnement->id,

                    'montant' =>
                        $validated['montant'],

                    'date_prevue' =>
                        $validated['date_fin'],

                    'date_paiement' =>
                        null,

                    'statut' =>
                        'en attente',

                    'reference' =>
                        'PAY-'
                        . now()->format('YmdHis')
                        . '-'
                        . strtoupper(
                            Str::random(5)
                        ),

                    'note' =>
                        'Échéance liée à l’abonnement '
                        . $validated['type']
                        . ' de l’agence '
                        . $agence->nom_agence,

                    'created_by' =>
                        auth()->id(),

                ]);

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Retour
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('admin.abonnements')
            ->with(
                'success',
                'Abonnement créé avec succès. L’échéance de paiement de l’agence a également été créée.'
            );
    }


    /*
|--------------------------------------------------------------------------
| Afficher le formulaire de modification
|--------------------------------------------------------------------------
*/
public function edit($id)
{
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->role !== 'admin') {
        abort(403, 'Accès refusé.');
    }

    $abonnement = Abonnement::findOrFail($id);

    $agences = Agence::orderBy('nom_agence')->get();

    return view(
        'admin.edit-abonnement',
        compact('abonnement', 'agences')
    );
}

/*
|--------------------------------------------------------------------------
| Modifier un abonnement
|--------------------------------------------------------------------------
*/
public function update(Request $request, $id)
{
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->role !== 'admin') {
        abort(403, 'Accès refusé.');
    }

    $abonnement = Abonnement::findOrFail($id);

    $validated = $request->validate([
        'agence_id' => 'required|integer|exists:agences,id',
        'type' => 'required|string|max:100',
        'montant' => 'required|numeric|min:0',
        'date_debut' => 'required|date',
        'date_fin' => 'required|date|after_or_equal:date_debut',
        'statut' => 'required|in:Actif,Expiré',
    ]);

    $abonnement->update([
        'agence_id' => $validated['agence_id'],
        'type' => $validated['type'],
        'montant' => $validated['montant'],
        'date_debut' => $validated['date_debut'],
        'date_fin' => $validated['date_fin'],
        'statut' => $validated['statut'],
    ]);

    return redirect()
        ->route('admin.abonnements')
        ->with('success', 'Abonnement modifié avec succès.');
}


    /**
     * ============================================================
     * AFFICHER UN ABONNEMENT
     * ============================================================
     */
    public function show($id)
    {
        /*
        |--------------------------------------------------------------------------
        | Vérification Admin
        |--------------------------------------------------------------------------
        */

        if (!auth()->check()) {
            return redirect()
                ->route('login');
        }

        if (auth()->user()->role !== 'admin') {
            abort(403, 'Accès refusé.');
        }


        /*
        |--------------------------------------------------------------------------
        | Récupération
        |--------------------------------------------------------------------------
        */

        $abonnement = Abonnement::with([
            'agence',
            'paiements',
        ])->findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Vue
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.show-abonnement',
            compact('abonnement')
        );
    }


    /**
     * ============================================================
     * SUPPRIMER UN ABONNEMENT
     * ============================================================
     */
    public function destroy($id)
    {
        /*
        |--------------------------------------------------------------------------
        | Vérification Admin
        |--------------------------------------------------------------------------
        */

        if (!auth()->check()) {
            return redirect()
                ->route('login');
        }

        if (auth()->user()->role !== 'admin') {
            abort(403, 'Accès refusé.');
        }


        /*
        |--------------------------------------------------------------------------
        | Récupérer l'abonnement
        |--------------------------------------------------------------------------
        */

        $abonnement =
            Abonnement::findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Supprimer
        |--------------------------------------------------------------------------
        |
        | Les paiements liés seront supprimés automatiquement si ta
        | clé étrangère utilise nullOnDelete/cascade selon ta migration.
        |
        */

        $abonnement->delete();


        /*
        |--------------------------------------------------------------------------
        | Retour
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('admin.abonnements')
            ->with(
                'success',
                'Abonnement supprimé avec succès.'
            );
    }
}