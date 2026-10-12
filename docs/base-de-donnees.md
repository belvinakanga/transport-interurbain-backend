# Base de données

## Moteur

SQLite par défaut : `DB_CONNECTION=sqlite`, fichier
`database/database.sqlite`.

```bash
touch database/database.sqlite
php artisan migrate
```

Sessions, cache et files d'attente passent aussi par la base
(`SESSION_DRIVER=database`, `CACHE_STORE=database`,
`QUEUE_CONNECTION=database`).

## Pas de contraintes référentielles

**Les tables ne déclarent aucune clé étrangère.** Supprimer une ligne
référencée ailleurs laisse des orphelins, sans erreur ni avertissement.

C'est une caractéristique structurante du projet : chaque suppression doit
vérifier ses rattachements **dans le code**. Le modèle `Agence` montre le
schéma à suivre :

```php
$agence->attachesBloquantes();   // ['1 trajet', '1 agent', …]
$agence->peutEtreSupprimee();    // bool
```

Les autres entités n'ont pas encore cette protection. Voir
[securite.md](securite.md).

## Tables

| Table | Rôle |
|---|---|
| `users` | comptes, avec `role` et `agence_id` |
| `agences` | transporteurs |
| `villes` | référentiel des villes desservies |
| `trajets` | offres de voyage |
| `sieges` | places d'un trajet |
| `reservations` | parcours mobile |
| `voyageurs` | passagers d'une réservation |
| `achats` | ventes au guichet |
| `billets` | titres de transport |
| `paiements` | paiements des voyageurs |
| `abonnements` | abonnements des agences |
| `paiement_agences` | règlements agences ↔ Tokende |
| `avis` | notes et commentaires |
| `conversations`, `messages` | support |
| `personal_access_tokens` | jetons Sanctum |

## Migrations

44 migrations. L'historique se lit en trois temps :

1. **Juin 2026** — socle : trajets, voyageurs, réservations, achats,
   billets, paiements, avis, agences.
2. **Juillet–septembre 2026** — enrichissements successifs : rôles, sièges,
   abonnements, règlements, commissions, conversations.
3. **Octobre 2026** — OpenPay, référentiel de villes, statut des agences.

### Les plus récentes

| Migration | Apport |
|---|---|
| `2026_10_09_010000_add_openpay_fields_to_paiements_table` | référence, opérateur, numéro, statut, réponse brute |
| `2026_10_09_010100_make_reservation_id_nullable_in_paiements` | un paiement peut précéder sa réservation |
| `2026_10_12_090000_create_villes_table` | référentiel + peuplement |
| `2026_10_12_100000_add_actif_to_agences_table` | `actif`, `desactivee_le`, `motif_desactivation` |

### Doublon

`2026_08_26_230357_add_duree_to_trajets_table` et
`2026_08_26_231620_add_duree_to_trajets_table` portent le même nom à une
minute d'intervalle. À vérifier avant toute remise à plat.

## Seeders

```bash
php artisan db:seed
```

| Seeder | Effet |
|---|---|
| `DatabaseSeeder` | crée un utilisateur de test |
| `AgenceSeeder` | peuple les agences |

Les villes sont peuplées **par la migration**, pas par un seeder : elles
dérivent des trajets existants, ce qu'un seeder rejouable ne doit pas
faire.

## Repartir de zéro

```bash
php artisan migrate:fresh --seed
```

> Efface toutes les données.
