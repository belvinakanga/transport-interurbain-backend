# Référentiel de villes

## Le problème qu'il résout

Avant, le départ et l'arrivée d'un trajet étaient des champs texte libres.
Chaque agent écrivait la ville à sa façon : `Brazzaville`, `brazzaville`,
`Brazaville`. La recherche mobile, qui compare des chaînes, ne retrouvait
plus les trajets.

Le référentiel donne **une liste commune** au formulaire web et à
l'application mobile, tout en laissant la possibilité d'ajouter une ville
inconnue.

## La table

Migration `2026_10_12_090000_create_villes_table.php`.

Elle est peuplée au moment de la migration, à partir de deux sources :

1. les sept villes déjà proposées en dur par l'application mobile —
   Brazzaville, Pointe-Noire, Dolisie, Nkayi, Oyo, Ouesso, Impfondo ;
2. toutes les villes déjà présentes dans les trajets existants, pour ne
   perdre aucune donnée.

## Le modèle

`app/Models/Ville.php` :

| Méthode | Rôle |
|---|---|
| `Ville::noms()` | liste alphabétique des noms |
| `Ville::trouverOuCreer(?string $nom)` | retrouve une ville, ou la crée |

`trouverOuCreer()` **ignore la casse et les espaces** : un agent qui saisit
`pointe-noire` récupère `Pointe-Noire` et ne crée pas de doublon. Renvoie
le nom retenu, ou `null` si la saisie est vide.

## Côté web

Le composant `resources/views/components/ville-select.blade.php` affiche
une liste déroulante avec une option « Autre… » qui révèle un champ libre.

```blade
<x-ville-select name="depart" :value="$trajet->depart ?? ''" />
```

Utilisé dans `admin/create-trajet.blade.php` et
`admin/edit-trajet.blade.php`.

Côté contrôleur, `AdminController::resoudreVilles()` traduit le choix avant
validation :

```php
private function resoudreVilles(Request $request): void
{
    foreach (['depart', 'arrivee'] as $champ) {
        $choix = trim((string) $request->input($champ, ''));
        $libre = trim((string) $request->input($champ . '_autre', ''));

        $nom = $choix === '__autre__' ? $libre : $choix;

        $request->merge([
            $champ => Ville::trouverOuCreer($nom) ?? '',
        ]);
    }
}
```

Appelée dans `storeTrajet()` **et** `updateTrajet()`.

## Côté mobile

`GET /api/villes` (publique) renvoie la liste.
`lib/services/villes_service.dart` la consomme, avec cache en mémoire et
repli sur une liste locale si l'API est injoignable.

```dart
final villes = await VillesService.charger();
VillesService.viderCache();
```

## Limite connue

La base contient **`Brazaville` (un seul z) à côté de `Brazzaville`**. La
fusion n'a pas été faite : elle modifierait un trajet existant, ce qui
dépasse l'ajout du référentiel.

Il n'existe pas encore d'écran d'administration des villes — ajout, fusion
et renommage se font en base.
