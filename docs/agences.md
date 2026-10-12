# Agences

Une agence est un transporteur. Elle porte des trajets, emploie des agents,
souscrit un abonnement et règle des commissions à Tokende.

## Cycle de vie

```
   créée ──▶ active ◀──────────────┐
               │                   │
               │ désactiver        │ réactiver
               ▼                   │
          suspendue ───────────────┘
               │
               │ (si plus rien n'y est rattaché)
               ▼
          supprimée
```

## Les colonnes de statut

Migration `2026_10_12_100000_add_actif_to_agences_table.php` :

| Colonne | Type | Rôle |
|---|---|---|
| `actif` | booléen, défaut `true` | l'agence est-elle en service |
| `desactivee_le` | horodatage, nullable | quand elle a été suspendue |
| `motif_desactivation` | texte, nullable | pourquoi — facultatif |

## L'API du modèle

`app/Models/Agence.php` :

| Méthode | Effet |
|---|---|
| `scopeActives()` | filtre sur `actif = true` |
| `desactiver(?string $motif)` | passe `actif` à faux, horodate, garde le motif |
| `reactiver()` | repasse à vrai, efface horodatage et motif |
| `attachesBloquantes()` | liste ce qui empêche la suppression |
| `peutEtreSupprimee()` | vrai si rien n'est rattaché |

```php
$agence->desactiver('Règlement de mai non parvenu');
$agence->reactiver();

if ($agence->peutEtreSupprimee()) {
    $agence->delete();
}
```

## Ce que la suspension change réellement

Ce n'est pas un simple drapeau d'affichage.

### 1. Les trajets sortent de la vente

`TrajetController@index` filtre sur l'agence :

```php
->whereHas('agence', fn ($q) => $q->where('actif', true))
```

`GET /api/trajets` cesse donc de renvoyer les trajets de l'agence. Rien
n'est supprimé : la réactivation les ramène tous.

### 2. Les agents perdent le back-office

Le middleware `VerifierAgenceActive` (alias `agence.active`) intercepte
chaque requête. Un agent dont l'agence est suspendue est renvoyé vers
`/agence-suspendue`, une page qui explique la situation, affiche le motif
s'il y en a un, donne les coordonnées de l'administration et propose la
déconnexion.

```php
// app/Http/Middleware/VerifierAgenceActive.php
private const TOUJOURS_AUTORISE = ['logout', 'agence-suspendue'];
```

**La déconnexion reste ouverte** : sans cette exception, l'agent serait
enfermé dans la page de blocage sans pouvoir en sortir.

L'administrateur n'est jamais concerné — il doit pouvoir réactiver depuis
cette même interface.

### 3. Plus de création de trajet

`AdminController@storeTrajet` refuse si l'agence visée est suspendue.

## Suppression

`destroyAgence()` refuse tant qu'un objet est rattaché, et nomme les
obstacles :

> Suppression impossible : cette agence porte encore 1 trajet, 1 agent,
> 1 abonnement, 1 règlement. Désactivez-la pour la retirer de la vente sans
> perdre son historique.

Ce garde-fou existe parce que **la base ne déclare aucune contrainte de clé
étrangère** : la suppression brute d'origine orphelinait silencieusement
trajets, agents, abonnements et règlements, et des réservations clients
pointaient vers une agence disparue.

Une agence réellement vide se supprime normalement.

## Routes

| Méthode | Chemin | Nom |
|---|---|---|
| `GET` | `/admin/agences` | — |
| `GET` | `/admin/agences/create` | — |
| `POST` | `/admin/agences/store` | — |
| `GET` | `/admin/agences/{id}` | — |
| `GET` | `/admin/agences/{id}/edit` | — |
| `PUT` | `/admin/agences/{id}` | — |
| `PATCH` | `/admin/agences/{id}/activer` | `admin.agences.activer` |
| `PATCH` | `/admin/agences/{id}/desactiver` | `admin.agences.desactiver` |
| `DELETE` | `/admin/agences/{id}` | — |

Les trois actions de statut passent par `exigerAdmin()` : un agent reçoit
`403`.

## Interface

Dans `admin/agences.blade.php`, le statut s'affiche **sous le nom de
l'agence**, pas dans une colonne dédiée :

```
Test
⏸ Désactivée          Brazza    Congo    +242…
```

Le tableau compte déjà huit colonnes et déborde à 1280 px ; une neuvième
poussait les boutons d'action hors de l'écran. Le badge n'apparaît que pour
les agences suspendues — une agence active n'a pas besoin d'être annoncée
comme telle. Le motif, s'il existe, est en infobulle.

Les boutons d'action utilisent `.tk-icon-btn-slate` (suspendre) et
`.tk-icon-btn-green` (réactiver). **Le gris, pas le rouge** : l'action est
réversible, le rouge reste réservé à la suppression.

## Modèle économique

`agences.modele_economique`, défaut `abonnement`. Voir
[paiements.md](paiements.md).
