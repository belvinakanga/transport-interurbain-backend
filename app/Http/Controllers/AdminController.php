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

class AdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Tableau de bord administrateur
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Accès refusé');
        }

        /*
        |--------------------------------------------------------------------------
        | Statistiques générales
        |--------------------------------------------------------------------------
        */

        $nombreUtilisateurs = User::count();
        $nombreAgences = Agence::count();
        $nombreTrajets = Trajet::count();
        $nombreReservations = Reservation::count();
        $nombrePaiements = Paiement::count();
        $nombreBillets = Billet::count();
        $nombreAchats = Achat::count();
        $nombreAvis = Avis::count();

        /*
        |--------------------------------------------------------------------------
        | Nouvelles statistiques
        |--------------------------------------------------------------------------
        */

        $revenuTotal = Paiement::where('statut', 'Payé')
            ->sum('montant');

        $reservationsAujourdhui = Reservation::whereDate(
            'created_at',
            today()
        )->count();

        $paiementsAujourdhui = Paiement::whereDate(
            'created_at',
            today()
        )->count();

        $billetsAujourdhui = Billet::whereDate(
            'created_at',
            today()
        )->count();

        /*
        |--------------------------------------------------------------------------
        | Dernières activités
        |--------------------------------------------------------------------------
        */

        $dernieresReservations = Reservation::with(
            'user',
            'trajet'
        )
        ->latest()
        ->take(5)
        ->get();

        $derniersPaiements = Paiement::with(
            'reservation.user',
            'reservation.trajet'
        )
        ->latest()
        ->take(5)
        ->get();

        $derniersTrajets = Trajet::with('agence')
            ->latest()
            ->take(5)
            ->get();
        $derniersAvis = Avis::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'nombreUtilisateurs',
            'nombreAgences',
            'nombreTrajets',
            'nombreReservations',
            'nombrePaiements',
            'nombreBillets',
            'nombreAchats',
            'revenuTotal',
            'reservationsAujourdhui',
            'paiementsAujourdhui',
            'billetsAujourdhui',
            'dernieresReservations',
            'derniersPaiements',
            'derniersTrajets' ,
            'nombreAvis',
            'derniersAvis',

            
            
    
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Liste des trajets
    |--------------------------------------------------------------------------
    */
    public function trajets(Request $request)
{
    if (auth()->user()->role !== 'admin') {
        abort(403, 'Accès refusé');
    }

    $recherche = $request->recherche;
    $agence = $request->agence;
    $depart = $request->depart;
    $tri = $request->tri ?? 'recent';
    $parPage = $request->par_page ?? 10;

    $query = Trajet::with('agence');

    // Recherche
    if ($recherche) {
        $query->where(function ($q) use ($recherche) {
            $q->where('depart', 'like', "%{$recherche}%")
              ->orWhere('arrivee', 'like', "%{$recherche}%");
        });
    }

    // Filtre agence
    if ($agence) {
        $query->where('agence_id', $agence);
    }

    // Filtre ville de départ
    if ($depart) {
        $query->where('depart', $depart);
    }

    // Tri
    switch ($tri) {

        case 'depart':
            $query->orderBy('depart');
            break;

        case 'arrivee':
            $query->orderBy('arrivee');
            break;

        case 'prix':
            $query->orderBy('prix');
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

    $listeAgences = Agence::orderBy('nom_agence')->get();

    $listeDeparts = Trajet::select('depart')
        ->distinct()
        ->orderBy('depart')
        ->pluck('depart');

    return view('admin.trajets', compact(
        'trajets',
        'listeAgences',
        'listeDeparts'
    ));
}
    

    /*
    |--------------------------------------------------------------------------
    | Formulaire ajout trajet
    |--------------------------------------------------------------------------
    */
    public function createTrajet()
    {
        $agences = Agence::all();

        return view(
            'admin.create-trajet',
            compact('agences')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Enregistrer trajet
    |--------------------------------------------------------------------------
    */
    public function storeTrajet(Request $request)
    {
        $request->validate([
            'agence_id' => 'required',
            'depart' => 'required',
            'arrivee' => 'required',
            'date_depart' => 'required',
            'heure_depart' => 'required',
            'prix' => 'required',
            'places_totales' => 'required|integer|min:1',
        ]);

        Trajet::create([
            'agence_id' => $request->agence_id,
            'depart' => $request->depart,
            'arrivee' => $request->arrivee,
            'date_depart' => $request->date_depart,
            'heure_depart' => $request->heure_depart,
            'prix' => str_replace([' ', '.'], '', $request->prix),
            'places_totales' => $request->places_totales,
            'places_disponibles' => $request->places_totales,
        ]);

        return redirect('/admin/trajets')
            ->with(
                'success',
                'Trajet ajouté avec succès.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Formulaire modification trajet
    |--------------------------------------------------------------------------
    */
    public function editTrajet($id)
    {
        $trajet = Trajet::findOrFail($id);

        $agences = Agence::all();

        return view(
            'admin.edit-trajet',
            compact('trajet', 'agences')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Mise à jour trajet
    |--------------------------------------------------------------------------
    */
    public function updateTrajet(Request $request, $id)
{
    $request->validate([
        'agence_id' => 'required',
        'depart' => 'required',
        'arrivee' => 'required',
        'date_depart' => 'required',
        'heure_depart' => 'required',
        'prix' => 'required',
    ]);

    $trajet = Trajet::findOrFail($id);

    $trajet->update([
        'agence_id' => $request->agence_id,
        'depart' => $request->depart,
        'arrivee' => $request->arrivee,
        'date_depart' => $request->date_depart,
        'heure_depart' => $request->heure_depart,
        'prix' => str_replace([' ', '.'], '', $request->prix),
    ]);

    return redirect('/admin/trajets')
        ->with('success', 'Trajet modifié avec succès.');
}

    /*
    |--------------------------------------------------------------------------
    | Liste des réservations
    |--------------------------------------------------------------------------
    */
    public function reservations(Request $request)
{
    $recherche = $request->recherche;
    $statut = $request->statut;
    $date = $request->date;
    $parPage = $request->par_page ?? 10;

    $query = Reservation::with([
        'user',
        'trajet.agence'
    ]);

    // Recherche par nom du voyageur
    if ($recherche) {
        $query->whereHas('user', function ($q) use ($recherche) {
            $q->where('name', 'like', "%{$recherche}%");
        });
    }

    // Filtre par statut
    if ($statut) {
        $query->where('statut', $statut);
    }

    // Filtre par date
    if ($date) {
        $query->whereDate('created_at', $date);
    }

    $reservations = $query
        ->latest()
        ->paginate($parPage)
        ->withQueryString();

    return view(
        'admin.reservations',
        compact('reservations')
    );
}

    /*
    |--------------------------------------------------------------------------
    | Liste des agences
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

    // Recherche
    if ($recherche) {
        $query->where(function ($q) use ($recherche) {
            $q->where('nom_agence', 'like', "%{$recherche}%")
              ->orWhere('telephone', 'like', "%{$recherche}%");
        });
    }

    // Filtre par agence
    if ($agence) {
        $query->where('id', $agence);
    }

    // Filtre par ville
    if ($ville) {
        $query->where('ville', $ville);
    }

    // Tri
    switch ($tri) {

        case 'nom_asc':
            $query->orderBy('nom_agence', 'asc');
            break;

        case 'nom_desc':
            $query->orderBy('nom_agence', 'desc');
            break;

        case 'ville':
            $query->orderBy('ville', 'asc');
            break;

        case 'ancien':
            $query->oldest();
            break;

        default:
            $query->latest();
            break;
    }

    $agences = $query
        ->paginate($parPage)
        ->withQueryString();

    // Pour les listes déroulantes
    $listeAgences = Agence::orderBy('nom_agence')->get();

    $listeVilles = Agence::select('ville')
        ->distinct()
        ->orderBy('ville')
        ->pluck('ville');

    return view('admin.agences', compact(
        'agences',
        'listeAgences',
        'listeVilles'
    ));
}
    

    /*
    |--------------------------------------------------------------------------
    | Liste des paiements
    |--------------------------------------------------------------------------
    */
    public function paiements(Request $request)
{
    $recherche = $request->recherche;
    $statut = $request->statut;
    $date = $request->date;
    $parPage = $request->par_page ?? 10;

    $query = Paiement::with([
        'reservation.user',
        'reservation.trajet.agence'
    ]);

    // Recherche par nom du voyageur
    if ($recherche) {
        $query->whereHas('reservation.user', function ($q) use ($recherche) {
            $q->where('name', 'like', "%{$recherche}%");
        });
    }

    // Filtre par statut
    if ($statut) {
        $query->where('statut', $statut);
    }

    // Filtre par date
    if ($date) {
        $query->whereDate('created_at', $date);
    }

    $paiements = $query
        ->latest()
        ->paginate($parPage)
        ->withQueryString();

    return view(
        'admin.paiements',
        compact('paiements')
    );
}
    /*
|--------------------------------------------------------------------------
| Formulaire ajout agence
|--------------------------------------------------------------------------
*/
public function createAgence()
{
    return view('admin.create-agence');
}

/*
|--------------------------------------------------------------------------
| Enregistrer une agence
|--------------------------------------------------------------------------
*/
public function storeAgence(Request $request)
{
    $request->validate([
        'nom_agence' => 'required|string|max:255',
        'ville' => 'required|string|max:255',
        'adresse' => 'required|string|max:255',
        'telephone' => 'required|string|max:50',
    ]);

    Agence::create([
        'nom_agence' => $request->nom_agence,
        'ville' => $request->ville,
        'adresse' => $request->adresse,
        'telephone' => $request->telephone,
    ]);

    return redirect('/admin/agences')
        ->with('success', 'Agence ajoutée avec succès.');
}

/*
|--------------------------------------------------------------------------
| Formulaire modification agence
|--------------------------------------------------------------------------
*/
public function editAgence($id)
{
    $agence = Agence::findOrFail($id);

    return view('admin.edit-agence', compact('agence'));
}

/*
|--------------------------------------------------------------------------
| Mise à jour agence
|--------------------------------------------------------------------------
*/
public function updateAgence(Request $request, $id)
{
    $request->validate([
        'nom_agence' => 'required|string|max:255',
        'ville' => 'required|string|max:255',
        'adresse' => 'required|string|max:255',
        'telephone' => 'required|string|max:50',
    ]);

    $agence = Agence::findOrFail($id);

    $agence->update([
        'nom_agence' => $request->nom_agence,
        'ville' => $request->ville,
        'adresse' => $request->adresse,
        'telephone' => $request->telephone,
    ]);

    return redirect('/admin/agences')
        ->with('success', 'Agence modifiée avec succès.');
}

/*
|--------------------------------------------------------------------------
| Supprimer une agence
|--------------------------------------------------------------------------
*/
public function destroyAgence($id)
{
    $agence = Agence::findOrFail($id);

    $agence->delete();

    return redirect('/admin/agences')
        ->with('success', 'Agence supprimée avec succès.');
}
/*
|--------------------------------------------------------------------------
| Liste des billets
|--------------------------------------------------------------------------
*/

public function billets(Request $request)
{
    $recherche = $request->recherche;
    $agence = $request->agence;
    $date = $request->date;
    $parPage = $request->par_page ?? 10;

    $query = Billet::with([
        'reservation.user',
        'reservation.trajet.agence'
    ]);

    // Recherche par numéro de billet ou voyageur
    if ($recherche) {
        $query->where(function ($q) use ($recherche) {

            $q->where('numero_billet', 'like', "%{$recherche}%")

            ->orWhereHas('reservation.user', function ($user) use ($recherche) {

                $user->where('name', 'like', "%{$recherche}%");

            });

        });
    }

    // Filtre agence
    if ($agence) {

        $query->whereHas('reservation.trajet.agence', function ($q) use ($agence) {

            $q->where('id', $agence);

        });

    }

    // Filtre date
    if ($date) {

        $query->whereDate('created_at', $date);

    }

    $billets = $query
        ->latest()
        ->paginate($parPage)
        ->withQueryString();

    $listeAgences = Agence::orderBy('nom_agence')->get();

    return view('admin.billets', compact(
        'billets',
        'listeAgences'
    ));
}

/*
|--------------------------------------------------------------------------
| Liste des avis
|--------------------------------------------------------------------------
*/

public function avis(Request $request)
{
    $recherche = $request->recherche;
    $note = $request->note;
    $date = $request->date;
    $parPage = $request->par_page ?? 10;

    $query = Avis::with('user');

    // Recherche
    if ($recherche) {

        $query->whereHas('user', function ($q) use ($recherche) {

            $q->where('name', 'like', "%{$recherche}%");

        });

    }

    // Filtre par note
    if ($note) {

        $query->where('note', $note);

    }

    // Filtre par date
    if ($date) {

        $query->whereDate('created_at', $date);

    }

    $avis = $query
        ->latest()
        ->paginate($parPage)
        ->withQueryString();

    return view('admin.avis', compact('avis'));
}
/*
|--------------------------------------------------------------------------
| Liste des voyageurs
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| Liste des utilisateurs
|--------------------------------------------------------------------------
*/
public function voyageurs(Request $request)
{
    $recherche = $request->recherche;
    $role = $request->role;
    $parPage = $request->par_page ?? 10;

    $query = User::query();

    // Recherche
    if ($recherche) {

        $query->where(function ($q) use ($recherche) {

            $q->where('name', 'like', "%{$recherche}%")
              ->orWhere('email', 'like', "%{$recherche}%");

        });

    }

    // Filtre rôle
    if ($role) {

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
| Liste des abonnements
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| Liste des abonnements
|--------------------------------------------------------------------------
*/

public function abonnements(Request $request)
{
    $recherche = $request->recherche;
    $statut = $request->statut;
    $parPage = $request->par_page ?? 10;

    $query = Abonnement::with('agence');

    // Recherche par agence

    if ($recherche) {

        $query->whereHas('agence', function ($q) use ($recherche) {

            $q->where('nom_agence', 'like', "%{$recherche}%");

        });

    }

    // Filtre statut

    if ($statut) {

        $query->where('statut', $statut);

    }

    $abonnements = $query
        ->latest()
        ->paginate($parPage)
        ->withQueryString();

    return view(
        'admin.abonnements',
        compact('abonnements')
    );
}


/*
|--------------------------------------------------------------------------
| Liste des commissions
|--------------------------------------------------------------------------
*/
public function commissions()
{
    $paiements = Paiement::with([
        'reservation.trajet.agence'
    ])->latest()->get();

    return view('admin.commissions', compact('paiements'));
}
public function parametres()
{
    return view('admin.parametres');
}
/*
|--------------------------------------------------------------------------
| Afficher une agence
|--------------------------------------------------------------------------
*/

public function showAgence($id)
{
    $agence = Agence::findOrFail($id);

    return view('admin.show-agence', compact('agence'));
}
public function showTrajet($id)
{
    $trajet = Trajet::with('agence')->findOrFail($id);

    return view('admin.show-trajet', compact('trajet'));
}
public function destroyTrajet($id)
{
    $trajet = Trajet::findOrFail($id);

    $trajet->delete();

    return redirect('/admin/trajets')
        ->with('success', 'Trajet supprimé avec succès.');
}
}
