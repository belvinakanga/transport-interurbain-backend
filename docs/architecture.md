# Architecture

## Le projet en une phrase

Une application Laravel unique sert **deux publics** : l'application mobile
Flutter, via une API JSON sous `/api`, et le back-office web, via des pages
Blade rendues côté serveur.

```
                 ┌──────────────────────────┐
  Flutter  ─────▶│  routes/api.php          │
  (voyageurs)    │  jetons Sanctum          │
                 ├──────────────────────────┤
  Navigateur ───▶│  routes/web.php          │
  (admin, agent) │  sessions + Blade        │
                 └───────────┬──────────────┘
                             │
                  Contrôleurs → Modèles → SQLite
```

Les deux entrées partagent les mêmes modèles et la même base. Une règle
métier écrite dans un modèle vaut donc pour l'API comme pour le web ; une
règle écrite dans un contrôleur ne vaut que pour son côté. C'est la source
d'écart la plus fréquente dans ce code.

## Les trois rôles

La colonne `users.role` prend trois valeurs :

| Rôle | Accès | Entrée |
|---|---|---|
| `user` | voyageur : recherche, réservation, billets, avis, support | application mobile |
| `agent` | gère les trajets et réservations de **son** agence | back-office web |
| `admin` | tout, plus la gestion des agences, abonnements et commissions | back-office web |

Un `agent` est rattaché à une agence par `users.agence_id`. Si cette agence
est suspendue, le middleware `agence.active` lui ferme le back-office —
voir [agences.md](agences.md).

## Arborescence

```
app/
├── Console/Commands/
│   └── ExpirerReservations.php      réservations impayées hors délai
├── Http/
│   ├── Controllers/
│   │   ├── Api/                     consommé par Flutter
│   │   ├── Auth/                    Laravel Breeze (web)
│   │   ├── Webhook/                 retours de la passerelle de paiement
│   │   └── *.php                    back-office web
│   └── Middleware/
│       └── VerifierAgenceActive.php
├── Mail/
│   └── CodeReinitialisationMotDePasse.php
├── Models/
├── Services/
│   └── OpenPayService.php           appels HTTP à la passerelle
└── View/Components/

resources/
├── css/app.css                      socle visuel (voir stylisation.md)
├── js/app.js
└── views/
    ├── admin/                       back-office administrateur
    ├── agent/                       espace agence
    ├── auth/                        connexion, inscription, mot de passe
    ├── components/                  composants Blade réutilisables
    ├── emails/
    └── layouts/

routes/
├── api.php                          API mobile
├── web.php                          back-office
├── auth.php                         routes Breeze
└── console.php
```

## Contrôleurs

Les plus gros d'abord — la taille dit où se concentre la logique :

| Contrôleur | Lignes | Rôle |
|---|---|---|
| `AdminController` | 2550 | tout le back-office administrateur |
| `AchatController` | 1409 | achats directs (sans réservation préalable) |
| `ReservationController` | 1124 | réservations, API et web |
| `AbonnementController` | 666 | abonnements des agences |
| `PaiementController` | 627 | paiements des voyageurs |
| `PaiementAgenceController` | 368 | règlements agences → Tokende |
| `Api/PasswordResetController` | 265 | mot de passe oublié (mobile) |
| `Api/PaymentOpenPayController` | 200 | paiement mobile money |
| `Webhook/OpenPayWebhookController` | 187 | confirmation de paiement |
| `TrajetController` | 187 | trajets, API et web |

`AdminController` à 2550 lignes est le point de fragilité du projet : il
porte agences, trajets, réservations, billets, paiements, avis et
commissions. Toute reprise sérieuse commence par le découper.

## Modèles et relations

```
User ──┬── belongsTo ──▶ Agence
       ├── hasMany ────▶ Reservation
       ├── hasMany ────▶ Achat
       └── hasMany ────▶ Conversation ── hasMany ──▶ Message

Agence ─┬── hasMany ───▶ Trajet
        └── hasMany ───▶ User (ses agents)

Trajet ─┬── belongsTo ─▶ Agence
        └── hasMany ───▶ Reservation

Reservation ─┬── belongsTo ─▶ User, Trajet
             ├── hasMany ───▶ Voyageur, Paiement, Billet, Siege
             └── hasOne ────▶ Achat

Billet ─── belongsTo ──▶ Reservation, Voyageur, User, Trajet, Siege

Siege ──── belongsTo ──▶ Trajet, Reservation, Voyageur, Achat

Abonnement ─┬── belongsTo ─▶ Agence
            └── hasMany ───▶ PaiementAgence

Ville      référentiel autonome, sans relation (voir villes.md)
```

### Deux notions proches à ne pas confondre

**Réservation** et **Achat** coexistent. La réservation est le parcours
complet (choix des sièges, voyageurs, paiement, billet). L'achat est la
vente directe enregistrée au guichet. Les deux mènent à des sièges occupés
et à des billets, par des chemins différents.

## Accesseurs calculés sur `Trajet`

Le modèle `Trajet` expose six attributs qui ne sont pas des colonnes
(`$appends`) :

| Attribut | Exemple |
|---|---|
| `depart_iso` | `2026-10-20T08:00:00` |
| `date_depart_libelle` | `20 octobre 2026` |
| `heure_depart_libelle` | `08h00` |
| `arrivee_iso` | `2026-10-20T12:30:00` |
| `heure_arrivee_libelle` | `12h30` |
| `duree_libelle` | `4 h 30` |

Ils existent parce que les heures sont saisies dans des formats très
variables selon l'agence : `08:00:00`, `07:30`, `4h30`, `240`, `90 min`. La
mise en forme se fait côté serveur, une fois, plutôt que dans chaque écran
mobile. L'application mobile les lit en priorité
(`lib/utils/format_trajet.dart`).

> La colonne d'arrivée s'appelle `heure_fin`, pas `heure_arrivee`. Du code
> lisant `heure_arrivee` obtient `null` — c'était l'origine des `--:--`
> affichés dans l'application.

## Middlewares

| Alias | Classe | Effet |
|---|---|---|
| `agence.active` | `VerifierAgenceActive` | renvoie un agent d'agence suspendue vers une page d'explication |

Enregistré dans `bootstrap/app.php`, appliqué au groupe de routes protégées
de `routes/web.php`.

## Flux d'un paiement mobile

```
Flutter                  Laravel                      OpenPay
   │                        │                            │
   ├─ POST /openpay/initiate┤                            │
   │                        ├─ OpenPayService ──────────▶│
   │                        │◀──────── référence ────────┤
   │◀───── référence ───────┤                            │
   │                        │                            │
   │   (le client paie sur son téléphone)                │
   │                        │                            │
   │                        │◀── POST /webhooks/openpay ─┤
   │                        │    (met à jour le paiement)│
   ├─ GET /openpay/status/… ┤                            │
   │◀──── statut ───────────┤                            │
```

Détails dans [paiements.md](paiements.md).
