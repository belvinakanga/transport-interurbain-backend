<?php

namespace App\Http\Controllers;

use App\Models\Agence;
use App\Models\Abonnement;
use App\Models\PaiementAgence;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaiementAgenceController extends Controller
{
    /**
     * Liste des paiements des agences.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['admin', 'agent'])) {
            abort(403, 'Accès refusé.');
        }

        $query = PaiementAgence::with([
            'agence',
            'abonnement',
            'createur',
        ])->latest('date_prevue');

        /*
        |--------------------------------------------------------------------------
        | AGENT
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'agent') {

            if (!$user->agence_id) {
                abort(
                    403,
                    'Votre compte agent n’est lié à aucune agence.'
                );
            }

            $query->where(
                'agence_id',
                $user->agence_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRES ADMIN
        |--------------------------------------------------------------------------
        */

        if (
            $user->role === 'admin'
            && $request->filled('agence')
        ) {
            $query->where(
                'agence_id',
                $request->agence
            );
        }

        if ($request->filled('statut')) {
            $query->where(
                'statut',
                $request->statut
            );
        }

        if ($request->filled('date')) {
            $query->whereDate(
                'date_prevue',
                $request->date
            );
        }

        $paiements = $query
            ->paginate(10)
            ->withQueryString();

        $listeAgences = $user->role === 'admin'
            ? Agence::orderBy('nom_agence')->get()
            : collect();

        return view(
            'paiements-agences.index',
            compact(
                'paiements',
                'listeAgences'
            )
        );
    }


    /**
     * Formulaire de création d'une échéance.
     */
    public function create()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Accès refusé.');
        }

        $abonnements = Abonnement::with('agence')
            ->where('statut', 'Actif')
            ->orderBy('date_fin')
            ->get();

        return view(
            'paiements-agences.create',
            compact('abonnements')
        );
    }


    /**
     * Créer une échéance de paiement.
     *
     * L'agence devra payer TOKENDE.
     */
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Accès refusé.');
        }

        $validated = $request->validate([
            'abonnement_id' => [
                'required',
                'integer',
                'exists:abonnements,id',
            ],

            'note' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $abonnement = Abonnement::with('agence')
            ->findOrFail(
                $validated['abonnement_id']
            );

        if ($abonnement->statut !== 'Actif') {
            return back()
                ->withInput()
                ->withErrors([
                    'abonnement_id' =>
                        'Cet abonnement n’est pas actif.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Éviter les doublons
        |--------------------------------------------------------------------------
        */

        $paiementExistant = PaiementAgence::where(
            'abonnement_id',
            $abonnement->id
        )->first();

        if ($paiementExistant) {
            return back()
                ->withInput()
                ->withErrors([
                    'abonnement_id' =>
                        'Un paiement existe déjà pour cet abonnement.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Créer le paiement
        |--------------------------------------------------------------------------
        */

        PaiementAgence::create([
            'agence_id' =>
                $abonnement->agence_id,

            'abonnement_id' =>
                $abonnement->id,

            'montant' =>
                $abonnement->montant,

            'date_prevue' =>
                $abonnement->date_fin,

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
                $validated['note'] ?? null,

            'created_by' =>
                auth()->id(),
        ]);

        return redirect()
            ->route('paiements-agences.index')
            ->with(
                'success',
                'Paiement de l’abonnement enregistré avec succès.'
            );
    }


    /**
     * Page de confirmation de paiement pour l'agent ou l'admin.
     */
    public function paymentForm($id)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['agent', 'admin'])) {
            abort(403, 'Accès refusé.');
        }

        if (
            $user->role === 'agent'
            && !$user->agence_id
        ) {
            abort(
                403,
                'Votre compte agent n’est lié à aucune agence.'
            );
        }

        $query = PaiementAgence::with([
            'agence',
            'abonnement',
        ])
            ->where('id', $id);

        if ($user->role === 'agent') {
            $query->where('agence_id', $user->agence_id);
        }

        $paiement = $query->firstOrFail();

        if ($paiement->statut === 'payé') {
            return redirect()
                ->route('paiements-agences.index')
                ->with(
                    'success',
                    'Ce paiement est déjà réglé.'
                );
        }

        return view(
            'paiements-agences.pay',
            compact('paiement')
        );
    }


    /**
     * Effectuer le paiement.
     *
     * Pour l'instant : simulation du paiement.
     */
    public function pay(Request $request, $id)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['agent', 'admin'])) {
            abort(403, 'Accès refusé.');
        }

        if (
            $user->role === 'agent'
            && !$user->agence_id
        ) {
            abort(
                403,
                'Votre compte agent n’est lié à aucune agence.'
            );
        }

        $query = PaiementAgence::where(
            'id',
            $id
        );

        if ($user->role === 'agent') {
            $query->where('agence_id', $user->agence_id);
        }

        $paiement = $query->firstOrFail();

        if ($paiement->statut === 'payé') {
            return redirect()
                ->route('paiements-agences.index')
                ->with(
                    'success',
                    'Ce paiement est déjà réglé.'
                );
        }

        $paiement->update([
            'statut' =>
                'payé',

            'date_paiement' =>
                now()->toDateString(),
        ]);

        return redirect()
            ->route('paiements-agences.index')
            ->with(
                'success',
                'Paiement effectué avec succès. Merci !'
            );
    }


    /**
     * Validation manuelle depuis l'Admin.
     */
    public function markAsPaid($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Accès refusé.');
        }

        $paiement =
            PaiementAgence::findOrFail($id);

        if ($paiement->statut === 'payé') {
            return back()->with(
                'success',
                'Ce paiement est déjà marqué comme payé.'
            );
        }

        $paiement->update([
            'statut' =>
                'payé',

            'date_paiement' =>
                now()->toDateString(),
        ]);

        return back()->with(
            'success',
            'Paiement de l’agence enregistré comme payé.'
        );
    }
}