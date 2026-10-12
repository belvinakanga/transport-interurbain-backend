# Trajets et sièges

## Le modèle

Table `trajets` :

| Colonne | Rôle |
|---|---|
| `agence_id` | transporteur |
| `depart`, `arrivee` | villes — voir [villes.md](villes.md) |
| `date_depart` | jour du départ |
| `heure_depart` | heure du départ |
| `heure_fin` | **heure d'arrivée** |
| `duree` | durée annoncée |
| `prix` | prix du billet |
| `places_totales` | capacité du véhicule |
| `places_disponibles` | restant à vendre |

> La colonne d'arrivée s'appelle `heure_fin`. Du code qui lit
> `heure_arrivee` obtient `null` — c'était l'origine des `--:--` affichés
> dans l'application mobile.

## Formats d'heure hétérogènes

Les agences saisissent librement : `08:00:00`, `07:30`, `4h30`, `240`,
`90 min`. Plutôt que de faire gérer cette variété à chaque écran, le modèle
`Trajet` expose six attributs calculés, ajoutés à chaque réponse JSON via
`$appends` :

| Attribut | Exemple |
|---|---|
| `depart_iso` | `2026-10-20T08:00:00` |
| `date_depart_libelle` | `20 octobre 2026` |
| `heure_depart_libelle` | `08h00` |
| `arrivee_iso` | `2026-10-20T12:30:00` |
| `heure_arrivee_libelle` | `12h30` |
| `duree_libelle` | `4 h 30` |

Les clients doivent lire ces attributs, pas les colonnes brutes.

## Visibilité

`TrajetController@index` ne renvoie **que les trajets d'agences actives** :

```php
->whereHas('agence', fn ($q) => $q->where('actif', true))
```

Suspendre une agence retire donc ses trajets de la vente sans rien
supprimer. Voir [agences.md](agences.md).

## Routes

### API mobile

| Méthode | Chemin |
|---|---|
| `GET` | `/api/trajets` |
| `GET` | `/api/trajets/{id}` |
| `GET` | `/api/trajets/{id}/sieges` |

### Back-office

| Méthode | Chemin |
|---|---|
| `GET` | `/admin/trajets` |
| `GET` | `/admin/trajets/create` |
| `POST` | `/admin/trajets/store` |
| `GET` | `/admin/trajets/{id}` |
| `GET` | `/admin/trajets/{id}/edit` |
| `PUT` | `/admin/trajets/{id}` |
| `DELETE` | `/admin/trajets/{id}` |

### Création et modification

`storeTrajet()` et `updateTrajet()` appellent tous deux
`resoudreVilles()` (référentiel de villes) avant validation.

`storeTrajet()` refuse en plus la création si l'agence visée est
suspendue.

## Sièges

Table `sieges` :

| Colonne | Rôle |
|---|---|
| `trajet_id` | trajet concerné |
| `numero_siege` | numéro affiché au voyageur |
| `statut` | libre, occupé… |
| `reservation_id` | réservation qui l'occupe |
| `voyageur_id` | passager assis |
| `achat_id` | achat guichet qui l'occupe |

Un siège peut être pris par deux chemins : une **réservation** (parcours
mobile) ou un **achat** (vente au guichet). D'où les deux colonnes
distinctes.

Contrôleur : `SiegeController` — `GET /api/trajets/{id}/sieges` alimente le
plan de sélection dans l'application.
