<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Agence;
use App\Models\Trajet;
use App\Models\Reservation;
use App\Models\Paiement;
use App\Models\Billet;
use App\Models\Achat;
use App\Models\Avis;
use App\Models\Abonnement;
use App\Models\PaiementAgence;

class AdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | TABLEAU DE BORD
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = auth()->user();

        if (!in_array($user->role, ['admin', 'agent'])) {
            abort(403, 'Accès refusé');
        }

        if ($user->role === 'agent') {
            return redirect()->route('agent.dashboard');
        }

        $nombreUtilisateurs = User::count();

        $nombreAgences = Agence::count();

        $nombreTrajets = Trajet::count();

        $nombreReservations = Reservation::count();

        $nombrePaiements = Paiement::count();

        $nombreBillets = Billet::count();

        $nombreAchats = Achat::count();

        $nombreAvis = Avis::count();

        $revenuTotal = Paiement::where(
            'statut',
            'Payé'
        )->sum('montant');

        $reservationsAujourdhui =
            Reservation::whereDate(
                'created_at',
                today()
            )->count();

        $paiementsAujourdhui =
            Paiement::whereDate(
                'created_at',
                today()
            )->count();

        $billetsAujourdhui =
            Billet::whereDate(
                'created_at',
                today()
            )->count();

        $dernieresReservations =
            Reservation::with(
                'user',
                'trajet'
            )
            ->latest()
            ->take(5)
            ->get();

        $derniersPaiements =
            Paiement::with(
                'reservation.user',
                'reservation.trajet'
            )
            ->latest()
            ->take(5)
            ->get();

        $derniersTrajets =
            Trajet::with('agence')
            ->latest()
            ->take(5)
            ->get();

        $derniersAvis =
            Avis::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view(
            'admin.dashboard',
            compact(
                'nombreUtilisateurs',
                'nombreAgences',
                'nombreTrajets',
                'nombreReservations',
                'nombrePaiements',
                'nombreBillets',
                'nombreAchats',
                'nombreAvis',
                'revenuTotal',
                'reservationsAujourdhui',
                'paiementsAujourdhui',
                'billetsAujourdhui',
                'dernieresReservations',
                'derniersPaiements',
                'derniersTrajets',
                'derniersAvis'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LISTE DES TRAJETS
    |--------------------------------------------------------------------------
    */

    public function trajets(Request $request)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['admin', 'agent'])) {
            abort(403, 'Accès refusé');
        }

        $recherche = $request->recherche;

        $agence = $request->agence;

        $depart = $request->depart;

        $tri = $request->tri ?? 'recent';

        $parPage = $request->par_page ?? 10;

        $query = Trajet::with('agence');

        /*
        |--------------------------------------------------------------------------
        | RESTRICTION AGENT
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'agent') {

            if (!$user->agence_id) {
                abort(
                    403,
                    'Votre compte agent n\'est lié à aucune agence.'
                );
            }

            $query->where(
                'agence_id',
                $user->agence_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | RECHERCHE
        |--------------------------------------------------------------------------
        */

        if ($recherche) {

            $query->where(
                function ($q) use ($recherche) {

                    $q->where(
                        'depart',
                        'like',
                        "%{$recherche}%"
                    )
                    ->orWhere(
                        'arrivee',
                        'like',
                        "%{$recherche}%"
                    );

                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRE AGENCE
        |--------------------------------------------------------------------------
        */

        if (
            $user->role === 'admin'
            && $agence
        ) {

            $query->where(
                'agence_id',
                $agence
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRE DÉPART
        |--------------------------------------------------------------------------
        */

        if ($depart) {

            $query->where(
                'depart',
                $depart
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TRI
        |--------------------------------------------------------------------------
        */

        switch ($tri) {

            case 'depart':

                $query->orderBy(
                    'depart'
                );

                break;

            case 'arrivee':

                $query->orderBy(
                    'arrivee'
                );

                break;

            case 'prix':

                $query->orderBy(
                    'prix'
                );

                break;

            case 'ancien':

                $query->oldest();

                break;

            default:

                $query->latest();

                break;
        }


        $trajets = $query
            ->paginate($parPage)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | LISTES POUR FILTRES
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'admin') {

            $listeAgences =
                Agence::orderBy(
                    'nom_agence'
                )->get();

        } else {

            $listeAgences =
                Agence::where(
                    'id',
                    $user->agence_id
                )->get();
        }


        $listeDeparts =
            Trajet::when(
                $user->role === 'agent',
                function ($q) use ($user) {

                    $q->where(
                        'agence_id',
                        $user->agence_id
                    );

                }
            )
            ->select('depart')
            ->distinct()
            ->orderBy('depart')
            ->pluck('depart');


        return view(
            'admin.trajets',
            compact(
                'trajets',
                'listeAgences',
                'listeDeparts'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VÉRIFIER SI UNE AGENCE A UN PAIEMENT EN RETARD
    |--------------------------------------------------------------------------
    |
    | Une agence est considérée comme bloquée si :
    |
    | - le paiement est encore "en attente"
    | - la date prévue est dépassée
    |
    | Un paiement déjà "payé" ne bloque jamais l'agence.
    |
    */

    private function agenceBloqueeParPaiement($agenceId): bool
    {
        return PaiementAgence::where(
            'agence_id',
            $agenceId
        )
        ->where(
            'statut',
            'en attente'
        )
        ->whereDate(
            'date_prevue',
            '<',
            today()
        )
        ->exists();
    }


    /*
    |--------------------------------------------------------------------------
    | FORMULAIRE AJOUT TRAJET
    |--------------------------------------------------------------------------
    */

    public function createTrajet()
    {
        $user = auth()->user();

        if (!in_array($user->role, ['admin', 'agent'])) {
            abort(403, 'Accès refusé.');
        }


        /*
        |--------------------------------------------------------------------------
        | AGENT
        |--------------------------------------------------------------------------
        |
        | L'Agent ne peut créer des trajets que pour son agence.
        |
        | Si le paiement est en retard, le formulaire est bloqué.
        |
        */

        if ($user->role === 'agent') {

            if (!$user->agence_id) {

                abort(
                    403,
                    'Votre compte agent n’est lié à aucune agence.'
                );
            }


            if (
                $this->agenceBloqueeParPaiement(
                    $user->agence_id
                )
            ) {

                return redirect()
                    ->route('agent.dashboard')
                    ->with(
                        'error',
                        'Votre agence a un paiement en retard. '
                        . 'Veuillez régulariser votre situation avant de créer un nouveau trajet.'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | L'Agent ne voit que son agence
            |--------------------------------------------------------------------------
            */

            $agences = Agence::where(
                'id',
                $user->agence_id
            )->get();

        } else {

            /*
            |--------------------------------------------------------------------------
            | ADMIN
            |--------------------------------------------------------------------------
            |
            | L'Admin peut créer un trajet pour n'importe quelle agence.
            | Le contrôle définitif sera effectué lors de l'enregistrement.
            |
            */

            $agences = Agence::orderBy(
                'nom_agence'
            )->get();
        }


        return view(
            'admin.create-trajet',
            compact('agences')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ENREGISTRER TRAJET
    |--------------------------------------------------------------------------
    */

    public function storeTrajet(Request $request)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['admin', 'agent'])) {
            abort(403, 'Accès refusé.');
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'agence_id' =>
                'required|integer|exists:agences,id',

            'depart' =>
                'required|string|max:255',

            'arrivee' =>
                'required|string|max:255',

            'date_depart' =>
                'required|date',

            'heure_depart' =>
                'required',

            'prix' =>
                'required',

            'places_totales' =>
                'required|integer|min:1',
        ]);


        /*
        |--------------------------------------------------------------------------
        | AGENT
        |--------------------------------------------------------------------------
        |
        | L'Agent ne peut jamais créer un trajet pour une autre agence.
        |
        */

        if ($user->role === 'agent') {

            if (!$user->agence_id) {

                abort(
                    403,
                    'Votre compte agent n’est lié à aucune agence.'
                );
            }


            if (
                (int) $request->agence_id
                !==
                (int) $user->agence_id
            ) {

                abort(
                    403,
                    'Vous ne pouvez créer un trajet que pour votre agence.'
                );
            }

        }


        /*
        |--------------------------------------------------------------------------
        | VÉRIFICATION DU PAIEMENT EN RETARD
        |--------------------------------------------------------------------------
        |
        | Cette vérification est faite côté serveur.
        |
        | Elle empêche toute création de trajet tant que l'agence
        | possède une échéance dépassée et non payée.
        |
        */

        $agenceId = $request->agence_id;


        if (
            $this->agenceBloqueeParPaiement(
                $agenceId
            )
        ) {

            /*
            |--------------------------------------------------------------------------
            | ADMIN
            |--------------------------------------------------------------------------
            */

            if ($user->role === 'admin') {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Impossible de créer ce trajet : '
                        . 'l’agence sélectionnée a un paiement en retard.'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | AGENT
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route('agent.dashboard')
                ->with(
                    'error',
                    'Votre agence a un paiement en retard. '
                    . 'Veuillez régulariser votre situation avant de créer un nouveau trajet.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CRÉATION DU TRAJET
        |--------------------------------------------------------------------------
        */

        Trajet::create([

            'agence_id' =>
                $request->agence_id,

            'depart' =>
                $request->depart,

            'arrivee' =>
                $request->arrivee,

            'date_depart' =>
                $request->date_depart,

            'heure_depart' =>
                $request->heure_depart,

            'prix' =>
                str_replace(
                    [' ', '.'],
                    '',
                    $request->prix
                ),

            'places_totales' =>
                $request->places_totales,

            'places_disponibles' =>
                $request->places_totales,

        ]);


        /*
        |--------------------------------------------------------------------------
        | REDIRECTION
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'agent') {

            return redirect()
                ->route('agent.dashboard')
                ->with(
                    'success',
                    'Trajet ajouté avec succès.'
                );
        }


        return redirect(
            '/admin/trajets'
        )
        ->with(
            'success',
            'Trajet ajouté avec succès.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FORMULAIRE MODIFICATION TRAJET
    |--------------------------------------------------------------------------
    */

    public function editTrajet($id)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['admin', 'agent'])) {
            abort(403, 'Accès refusé.');
        }

        $trajet = Trajet::findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | L'Agent ne peut modifier que son trajet
        |--------------------------------------------------------------------------
        */

        if (
            $user->role === 'agent'
            &&
            (int) $trajet->agence_id
            !==
            (int) $user->agence_id
        ) {

            abort(
                403,
                'Vous ne pouvez modifier que les trajets de votre agence.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Vérification paiement en retard
        |--------------------------------------------------------------------------
        |
        | Si l'agence est bloquée, on ne permet pas de modifier
        | le trajet non plus.
        |
        */

        if (
            $this->agenceBloqueeParPaiement(
                $trajet->agence_id
            )
        ) {

            if ($user->role === 'agent') {

                return redirect()
                    ->route('agent.dashboard')
                    ->with(
                        'error',
                        'Votre agence a un paiement en retard. '
                        . 'La modification des trajets est temporairement bloquée.'
                    );
            }

            return back()->with(
                'error',
                'Cette agence a un paiement en retard. '
                . 'La modification du trajet est temporairement bloquée.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Agences
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'agent') {

            $agences =
                Agence::where(
                    'id',
                    $user->agence_id
                )->get();

        } else {

            $agences =
                Agence::orderBy(
                    'nom_agence'
                )->get();

        }


        return view(
            'admin.edit-trajet',
            compact(
                'trajet',
                'agences'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MISE À JOUR TRAJET
    |--------------------------------------------------------------------------
    */

    public function updateTrajet(
        Request $request,
        $id
    )
    {
        $user = auth()->user();

        if (!in_array($user->role, ['admin', 'agent'])) {
            abort(403, 'Accès refusé.');
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'agence_id' =>
                'required|integer|exists:agences,id',

            'depart' =>
                'required|string|max:255',

            'arrivee' =>
                'required|string|max:255',

            'date_depart' =>
                'required|date',

            'heure_depart' =>
                'required',

            'prix' =>
                'required',
        ]);


        $trajet =
            Trajet::findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | AGENT : seulement son agence
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'agent') {

            if (
                (int) $trajet->agence_id
                !==
                (int) $user->agence_id
            ) {

                abort(
                    403,
                    'Vous ne pouvez modifier que les trajets de votre agence.'
                );
            }


            if (
                (int) $request->agence_id
                !==
                (int) $user->agence_id
            ) {

                abort(
                    403,
                    'Vous ne pouvez pas affecter le trajet à une autre agence.'
                );
            }

        }


        /*
        |--------------------------------------------------------------------------
        | Vérifier le paiement
        |--------------------------------------------------------------------------
        */

        if (
            $this->agenceBloqueeParPaiement(
                $request->agence_id
            )
        ) {

            if ($user->role === 'admin') {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Impossible de modifier ce trajet : '
                        . 'l’agence possède un paiement en retard.'
                    );
            }


            return redirect()
                ->route('agent.dashboard')
                ->with(
                    'error',
                    'Votre agence a un paiement en retard. '
                    . 'Veuillez régulariser votre situation avant de modifier vos trajets.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Mise à jour
        |--------------------------------------------------------------------------
        */

        $trajet->update([

            'agence_id' =>
                $request->agence_id,

            'depart' =>
                $request->depart,

            'arrivee' =>
                $request->arrivee,

            'date_depart' =>
                $request->date_depart,

            'heure_depart' =>
                $request->heure_depart,

            'prix' =>
                str_replace(
                    [' ', '.'],
                    '',
                    $request->prix
                ),

        ]);


        /*
        |--------------------------------------------------------------------------
        | Redirection
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'agent') {

            return redirect()
                ->route('agent.dashboard')
                ->with(
                    'success',
                    'Trajet modifié avec succès.'
                );
        }


        return redirect(
            '/admin/trajets'
        )
        ->with(
            'success',
            'Trajet modifié avec succès.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LISTE DES RÉSERVATIONS
    |--------------------------------------------------------------------------
    */

    public function reservations(Request $request)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['admin', 'agent'])) {
            abort(403, 'Accès refusé');
        }

        $recherche = $request->recherche;

        $statut = $request->statut;

        $date = $request->date;

        $parPage = $request->par_page ?? 10;

       $query = Reservation::with([
    'user',
    'trajet.agence',
    'voyageurs',
    'billets'
]);

        /*
        |--------------------------------------------------------------------------
        | RESTRICTION AGENT
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'agent') {

            if (!$user->agence_id) {
                abort(
                    403,
                    'Votre compte agent n’est lié à aucune agence.'
                );
            }

            $query->whereHas(
                'trajet',
                function ($q) use ($user) {

                    $q->where(
                        'agence_id',
                        $user->agence_id
                    );

                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | RECHERCHE
        |--------------------------------------------------------------------------
        */

        if ($recherche) {

            $query->whereHas(
                'user',
                function ($q) use ($recherche) {

                    $q->where(
                        'name',
                        'like',
                        "%{$recherche}%"
                    );

                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | STATUT
        |--------------------------------------------------------------------------
        */

        if ($statut) {

            $query->where(
                'statut',
                $statut
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DATE
        |--------------------------------------------------------------------------
        */

        if ($date) {

            $query->whereDate(
                'created_at',
                $date
            );
        }


        $reservations =
            $query
            ->latest()
            ->paginate($parPage)
            ->withQueryString();


        return view(
            'admin.reservations',
            compact(
                'reservations'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LISTE DES AGENCES
    |--------------------------------------------------------------------------
    */

    public function agences(Request $request)
    {
        $recherche = $request->recherche;

        $agence = $request->agence;

        $ville = $request->ville;

        $tri = $request->tri ?? 'recent';

        $parPage = $request->par_page ?? 10;

        $query = Agence::query();


        /*
        |--------------------------------------------------------------------------
        | RECHERCHE
        |--------------------------------------------------------------------------
        */

        if ($recherche) {

            $query->where(
                function ($q) use ($recherche) {

                    $q->where(
                        'nom_agence',
                        'like',
                        "%{$recherche}%"
                    )

                    ->orWhere(
                        'telephone',
                        'like',
                        "%{$recherche}%"
                    );

                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRE AGENCE
        |--------------------------------------------------------------------------
        */

        if ($agence) {

            $query->where(
                'id',
                $agence
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRE VILLE
        |--------------------------------------------------------------------------
        */

        if ($ville) {

            $query->where(
                'ville',
                $ville
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TRI
        |--------------------------------------------------------------------------
        */

        switch ($tri) {

            case 'nom_asc':

                $query->orderBy(
                    'nom_agence',
                    'asc'
                );

                break;

            case 'nom_desc':

                $query->orderBy(
                    'nom_agence',
                    'desc'
                );

                break;

            case 'ville':

                $query->orderBy(
                    'ville',
                    'asc'
                );

                break;

            case 'ancien':

                $query->oldest();

                break;

            default:

                $query->latest();

                break;
        }


        $agences =
            $query
            ->paginate($parPage)
            ->withQueryString();


        $listeAgences =
            Agence::orderBy(
                'nom_agence'
            )->get();


        $listeVilles =
            Agence::select('ville')
            ->distinct()
            ->orderBy('ville')
            ->pluck('ville');


        return view(
            'admin.agences',
            compact(
                'agences',
                'listeAgences',
                'listeVilles'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LISTE DES PAIEMENTS VOYAGEURS
    |--------------------------------------------------------------------------
    */

    public function paiements(Request $request)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['admin', 'agent'])) {
            abort(403, 'Accès refusé');
        }

        $recherche = $request->recherche;

        $statut = $request->statut;

        $date = $request->date;

        $parPage = $request->par_page ?? 10;

        $query = Paiement::with([
            'reservation.user',
            'reservation.trajet.agence'
        ]);


        /*
        |--------------------------------------------------------------------------
        | RESTRICTION AGENT
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'agent') {

            if (!$user->agence_id) {

                abort(
                    403,
                    'Votre compte agent n’est lié à aucune agence.'
                );
            }

            $query->whereHas(
                'reservation.trajet',
                function ($q) use ($user) {

                    $q->where(
                        'agence_id',
                        $user->agence_id
                    );

                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | RECHERCHE
        |--------------------------------------------------------------------------
        */

        if ($recherche) {

            $query->whereHas(
                'reservation.user',
                function ($q) use ($recherche) {

                    $q->where(
                        'name',
                        'like',
                        "%{$recherche}%"
                    );

                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | STATUT
        |--------------------------------------------------------------------------
        */

        if ($statut) {

            $query->where(
                'statut',
                $statut
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DATE
        |--------------------------------------------------------------------------
        */

        if ($date) {

            $query->whereDate(
                'created_at',
                $date
            );
        }


        $paiements =
            $query
            ->latest()
            ->paginate($parPage)
            ->withQueryString();


        if ($user->role === 'agent') {

            return view(
                'agent.paiements-voyageurs',
                compact('paiements')
            );
        }


        return view(
            'admin.paiements',
            compact('paiements')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FORMULAIRE AJOUT AGENCE
    |--------------------------------------------------------------------------
    */

    public function createAgence()
    {
        return view(
            'admin.create-agence'
        );
    }


    /*
|--------------------------------------------------------------------------
| ENREGISTRER UNE AGENCE
|--------------------------------------------------------------------------
*/

public function storeAgence(Request $request)
{
    $request->validate([
        'nom_agence' =>
            'required|string|max:255',

        'ville' =>
            'required|string|max:255',

        'adresse' =>
            'required|string|max:255',

        'telephone' =>
            'required|string|max:50',

        'modele_economique' =>
            'required|in:commission,abonnement',
    ]);


    Agence::create([

        'nom_agence' =>
            $request->nom_agence,

        'ville' =>
            $request->ville,

        'adresse' =>
            $request->adresse,

        'telephone' =>
            $request->telephone,

        'modele_economique' =>
            $request->modele_economique,

    ]);


    return redirect(
        '/admin/agences'
    )
    ->with(
        'success',
        'Agence ajoutée avec succès.'
    );
}

    /*
    |--------------------------------------------------------------------------
    | MODIFICATION AGENCE
    |--------------------------------------------------------------------------
    */

    public function editAgence($id)
    {
        $agence =
            Agence::findOrFail($id);

        return view(
            'admin.edit-agence',
            compact('agence')
        );
    }


   /*
|--------------------------------------------------------------------------
| MISE À JOUR AGENCE
|--------------------------------------------------------------------------
*/

public function updateAgence(
    Request $request,
    $id
)
{
    $request->validate([
        'nom_agence' =>
            'required|string|max:255',

        'ville' =>
            'required|string|max:255',

        'adresse' =>
            'required|string|max:255',

        'telephone' =>
            'required|string|max:50',

        'modele_economique' =>
            'required|in:commission,abonnement',
    ]);


    $agence =
        Agence::findOrFail($id);


    /*
    |--------------------------------------------------------------------------
    | VÉRIFICATION DU CHANGEMENT DE MODÈLE ÉCONOMIQUE
    |--------------------------------------------------------------------------
    */

    if (
        $agence->modele_economique !==
        $request->modele_economique
    ) {

        /*
        |--------------------------------------------------------------------------
        | ABONNEMENT ACTIF → COMMISSION INTERDITE
        |--------------------------------------------------------------------------
        */

        if (
            $agence->modele_economique === 'abonnement'
            &&
            $request->modele_economique === 'commission'
        ) {

            $abonnementActif =
                Abonnement::where(
                    'agence_id',
                    $agence->id
                )
                ->where(
                    'statut',
                    'Actif'
                )
                ->exists();


            if ($abonnementActif) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Impossible de passer cette agence en commission : un abonnement est actuellement actif.'
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | COMMISSION AVEC VENTES → ABONNEMENT INTERDIT
        |--------------------------------------------------------------------------
        */

        if (
            $agence->modele_economique === 'commission'
            &&
            $request->modele_economique === 'abonnement'
        ) {

            $achatsPayes =
                Achat::whereHas(
                    'trajet',
                    function ($query) use ($agence) {

                        $query->where(
                            'agence_id',
                            $agence->id
                        );

                    }
                )
                ->where(
                    'statut',
                    'payé'
                )
                ->exists();


            if ($achatsPayes) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Impossible de passer cette agence en abonnement : des billets ont déjà été vendus avec le modèle de commission.'
                    );
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | MISE À JOUR
    |--------------------------------------------------------------------------
    */

    $agence->update([

        'nom_agence' =>
            $request->nom_agence,

        'ville' =>
            $request->ville,

        'adresse' =>
            $request->adresse,

        'telephone' =>
            $request->telephone,

        'modele_economique' =>
            $request->modele_economique,

    ]);


    return redirect(
        '/admin/agences'
    )
    ->with(
        'success',
        'Agence modifiée avec succès.'
    );
}

    /*
    |--------------------------------------------------------------------------
    | SUPPRIMER AGENCE
    |--------------------------------------------------------------------------
    */

    public function destroyAgence($id)
    {
        $agence =
            Agence::findOrFail($id);


        $agence->delete();


        return redirect(
            '/admin/agences'
        )
        ->with(
            'success',
            'Agence supprimée avec succès.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LISTE DES BILLETS
    |--------------------------------------------------------------------------
    */

    public function billets(Request $request)
{
    $user = auth()->user();

    if (!in_array($user->role, ['admin', 'agent'])) {
        abort(403, 'Accès refusé');
    }

    $recherche = $request->recherche;
    $agence = $request->agence;
    $date = $request->date;
    $parPage = $request->par_page ?? 10;

    $query = Billet::with([
        'reservation.user',
        'reservation.trajet.agence'
    ]);
    $billets =
    $query
    ->latest()
    ->paginate($parPage)
    ->withQueryString();

$listeAgences =
    Agence::orderBy(
        'nom_agence'
    )->get();

return view(
    'admin.billets',
    compact(
        'billets',
        'listeAgences'
    )
);
        /*
        |--------------------------------------------------------------------------
        | RESTRICTION AGENT
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'agent') {

            if (!$user->agence_id) {

                abort(
                    403,
                    'Votre compte agent n’est lié à aucune agence.'
                );
            }

            $query->whereHas(
                'reservation.trajet',
                function ($q) use ($user) {

                    $q->where(
                        'agence_id',
                        $user->agence_id
                    );

                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | RECHERCHE
        |--------------------------------------------------------------------------
        */

        if ($recherche) {

            $query->where(
                function ($q) use ($recherche) {

                    $q->where(
                        'numero_billet',
                        'like',
                        "%{$recherche}%"
                    )

                    ->orWhereHas(
                        'reservation.user',
                        function ($user) use ($recherche) {

                            $user->where(
                                'name',
                                'like',
                                "%{$recherche}%"
                            );

                        }
                    );

                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRE AGENCE
        |--------------------------------------------------------------------------
        */

        if (
            $user->role === 'admin'
            && $agence
        ) {

            $query->whereHas(
                'reservation.trajet.agence',
                function ($q) use ($agence) {

                    $q->where(
                        'id',
                        $agence
                    );

                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DATE
        |--------------------------------------------------------------------------
        */

        if ($date) {

            $query->whereDate(
                'created_at',
                $date
            );
        }


        $billets =
            $query
            ->latest()
            ->paginate($parPage)
            ->withQueryString();


        $listeAgences =
            Agence::orderBy(
                'nom_agence'
            )->get();


        if ($user->role === 'agent') {

           return view(
    'admin.billets',
    compact(
        'billets',
        'listeAgences'
    )
);
        }


        return view(
            'admin.billets',
            compact(
                'billets',
                'listeAgences'
            )
        );
    }

    


    /*
    |--------------------------------------------------------------------------
    | LISTE DES AVIS
    |--------------------------------------------------------------------------
    */

    public function avis(Request $request)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['admin', 'agent'])) {
            abort(403, 'Accès refusé');
        }

        $recherche = $request->recherche;

        $note = $request->note;

        $date = $request->date;

        $parPage = $request->par_page ?? 10;

        $query = Avis::with('user');


        /*
        |--------------------------------------------------------------------------
        | RESTRICTION AGENT
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'agent') {

            if (!$user->agence_id) {

                abort(
                    403,
                    'Votre compte agent n’est lié à aucune agence.'
                );
            }

            $query->whereHas(
                'reservation.trajet',
                function ($q) use ($user) {

                    $q->where(
                        'agence_id',
                        $user->agence_id
                    );

                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | RECHERCHE
        |--------------------------------------------------------------------------
        */

        if ($recherche) {

            $query->whereHas(
                'user',
                function ($q) use ($recherche) {

                    $q->where(
                        'name',
                        'like',
                        "%{$recherche}%"
                    );

                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | NOTE
        |--------------------------------------------------------------------------
        */

        if ($note) {

            $query->where(
                'note',
                $note
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DATE
        |--------------------------------------------------------------------------
        */

        if ($date) {

            $query->whereDate(
                'created_at',
                $date
            );
        }


        $avis =
            $query
            ->latest()
            ->paginate($parPage)
            ->withQueryString();


        return view(
            'admin.avis',
            compact('avis')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LISTE DES VOYAGEURS / UTILISATEURS
    |--------------------------------------------------------------------------
    */

    public function voyageurs(Request $request)
    {
        $recherche = $request->recherche;

        $role = $request->role;

        $parPage = $request->par_page ?? 10;

        $query = User::query();


        /*
        |--------------------------------------------------------------------------
        | RECHERCHE
        |--------------------------------------------------------------------------
        */

        if ($recherche) {

            $query->where(
                function ($q) use ($recherche) {

                    $q->where(
                        'name',
                        'like',
                        "%{$recherche}%"
                    )

                    ->orWhere(
                        'email',
                        'like',
                        "%{$recherche}%"
                    );

                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRE RÔLE
        |--------------------------------------------------------------------------
        */

        if ($role) {

            $query->where(
                'role',
                $role
            );
        }


        $voyageurs =
            $query
            ->latest()
            ->paginate($parPage)
            ->withQueryString();


        return view(
            'admin.voyageurs',
            compact('voyageurs')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LISTE DES ABONNEMENTS
    |--------------------------------------------------------------------------
    */

    public function abonnements(Request $request)
{
    $recherche = $request->recherche;

    $statut = $request->statut;

    $parPage = $request->par_page ?? 10;


    /*
    |--------------------------------------------------------------------------
    | AGENCES DU MODÈLE ABONNEMENT
    |--------------------------------------------------------------------------
    */

    $agencesAbonnement =
        Agence::where(
            'modele_economique',
            'abonnement'
        )
        ->when(
            $recherche,
            function ($query) use ($recherche) {

                $query->where(
                    'nom_agence',
                    'like',
                    "%{$recherche}%"
                );

            }
        )
        ->orderBy(
            'nom_agence'
        )
        ->get();


    /*
    |--------------------------------------------------------------------------
    | ABONNEMENTS EXISTANTS
    |--------------------------------------------------------------------------
    */

    $abonnementsExistants =
        Abonnement::with(
            'agence'
        )
        ->when(
            $recherche,
            function ($query) use ($recherche) {

                $query->whereHas(
                    'agence',
                    function ($q) use ($recherche) {

                        $q->where(
                            'nom_agence',
                            'like',
                            "%{$recherche}%"
                        );

                    }
                );

            }
        )
        ->when(
            $statut,
            function ($query) use ($statut) {

                $query->where(
                    'statut',
                    $statut
                );

            }
        )
        ->latest()
        ->get();


    /*
    |--------------------------------------------------------------------------
    | AJOUTER LES AGENCES SANS ABONNEMENT
    |--------------------------------------------------------------------------
    */

    foreach ($agencesAbonnement as $agence) {

        $aUnAbonnement =
            $abonnementsExistants->contains(
                function ($abonnement) use ($agence) {

                    return
                        $abonnement->agence_id ===
                        $agence->id;

                }
            );


        if (!$aUnAbonnement) {

            $abonnementsExistants->push(
                (object) [
                    'id' => null,

                    'agence_id' =>
                        $agence->id,

                    'agence' =>
                        $agence,

                    'type' =>
                        'Abonnement mensuel',

                    'montant' =>
                        null,

                    'date_debut' =>
                        null,

                    'date_fin' =>
                        null,

                    'statut' =>
                        'En attente',
                ]
            );

        }
    }


    /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */

    $total =
        $abonnementsExistants->count();

    $page =
        request()->get(
            'page',
            1
        );

    $items =
        $abonnementsExistants
        ->forPage(
            $page,
            $parPage
        )
        ->values();


    $abonnements =
        new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $parPage,
            $page,
            [
                'path' =>
                    request()->url(),

                'query' =>
                    request()->query(),
            ]
        );


    return view(
        'admin.abonnements',
        compact('abonnements')
    );
}

    /*
    |--------------------------------------------------------------------------
    | PARAMÈTRES
    |--------------------------------------------------------------------------
    */

    public function parametres()
    {
        return view(
            'admin.parametres'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | AFFICHER UNE AGENCE
    |--------------------------------------------------------------------------
    */

    public function showAgence($id)
{
    $agence = Agence::with('agents')->findOrFail($id);

    return view(
        'admin.show-agence',
        compact('agence')
    );
}


    /*
    |--------------------------------------------------------------------------
    | AFFICHER UN TRAJET
    |--------------------------------------------------------------------------
    */

    public function showTrajet($id)
    {
        $trajet =
            Trajet::with('agence')
            ->findOrFail($id);


        return view(
            'admin.show-trajet',
            compact('trajet')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SUPPRIMER UN TRAJET
    |--------------------------------------------------------------------------
    */

    public function destroyTrajet($id)
    {
        $trajet =
            Trajet::findOrFail($id);


        $trajet->delete();


        return redirect(
            '/admin/trajets'
        )
        ->with(
            'success',
            'Trajet supprimé avec succès.'
        );
    }
    /*
|--------------------------------------------------------------------------
| VÉRIFIER UN BILLET
|--------------------------------------------------------------------------
*/

public function verifierBillet(Request $request)
{
    $user = auth()->user();

    if (!in_array($user->role, ['admin', 'agent'])) {
        abort(403, 'Accès refusé');
    }

    $request->validate([
        'numero_billet' => 'required|string'
    ]);

    $billet = Billet::with([
        'voyageur',
        'reservation.user',
        'reservation.trajet.agence',
        'siege'
    ])
    ->where('numero_billet', $request->numero_billet)
    ->first();

    if (!$billet) {
        return back()->with(
            'error',
            'Billet invalide ou introuvable.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Vérification de l'agence pour l'agent
    |--------------------------------------------------------------------------
    */

    if ($user->role === 'agent') {

        if (!$user->agence_id) {
            abort(
                403,
                'Votre compte agent n’est lié à aucune agence.'
            );
        }

        $agenceBillet =
            $billet->reservation?->trajet?->agence_id;

        if (
            (int) $agenceBillet
            !==
            (int) $user->agence_id
        ) {
            return back()->with(
                'error',
                'Ce billet ne appartient pas à votre agence.'
            );
        }
    }

    return back()->with(
        'billet_verifie',
        $billet
    );
}

public function commissions()
{
    // On récupère uniquement les agences
    // qui ont choisi le modèle COMMISSION
    $agences = Agence::where(
        'modele_economique',
        'commission'
    )
    ->orderBy('nom_agence')
    ->get();

    // On récupère uniquement les achats payés
    // appartenant à des agences en mode COMMISSION
    $achats = Achat::with([
        'trajet.agence',
        'reservation'
    ])
    ->where('statut', 'payé')
    ->whereHas(
        'trajet.agence',
        function ($query) {
            $query->where(
                'modele_economique',
                'commission'
            );
        }
    )
    ->get()
    ->groupBy(function ($achat) {
        return $achat->trajet?->agence_id;
    });

    // On prépare les données pour la page
    $commissions = $agences
        ->map(function ($agence) use ($achats) {

            $achatsAgence = $achats->get(
                $agence->id,
                collect()
            );

            $nombreBillets = $achatsAgence->sum(
                function ($achat) {
                    return $achat->reservation?->nombre_places ?? 0;
                }
            );

            return [
                'agence' => $agence,

                'nombre_billets' => $nombreBillets,

                'frais_generes' => $achatsAgence->sum(
                    'frais_tokende'
                ),

                'part_tokende' => $achatsAgence->sum(
                    'commission_tokende'
                ),

                'part_agence' => $achatsAgence->sum(
                    'part_agence'
                ),
            ];
        })
        ->values();

    return view(
        'admin.commissions',
        compact('commissions')
    );
}
}