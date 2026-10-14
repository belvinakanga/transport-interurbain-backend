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

`duree_libelle` se calcule dans cet ordre :

1. la colonne `duree` si elle est renseignée ;
2. sinon l'écart entre `heure_depart` et `heure_fin`.

Les deux sens sont donc couverts : saisir l'heure d'arrivée suffit à
obtenir la durée, et saisir la durée suffit à obtenir l'heure d'arrivée.

Deux autres attributs portent le prix réellement payé :

| Attribut | Exemple |
|---|---|
| `frais_service` | `100` |
| `prix_total` | `15100` |

`prix` est le tarif de l'agence ; `prix_total` ce que le voyageur débourse.
Voir [frais-de-service.md](frais-de-service.md).

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

Le formulaire porte sept champs obligatoires — agence, villes de départ et
d'arrivée, date, heure de départ, prix, places — et deux facultatifs :

| Champ | Rôle |
|---|---|
| `heure_fin` | heure d'arrivée ; **la durée s'en déduit** |
| `duree` | durée annoncée, à ne remplir que pour annoncer autre chose que l'écart entre les deux horaires |

Une arrivée antérieure au départ est comprise comme **le lendemain** :
`22h00 → 05h30` donne une durée de 7 h 30 et une arrivée datée du jour
suivant.

`storeTrajet()` et `updateTrajet()` appellent tous deux
`resoudreVilles()` (référentiel de villes) avant validation.

`storeTrajet()` refuse en plus la création si l'agence visée est
suspendue.

> Jusqu'à l'ajout de ces deux champs, le formulaire ne permettait pas de
> saisir l'heure d'arrivée : tout trajet créé depuis l'interface affichait
> « — » à l'arrivée et aucune durée dans l'application.

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
