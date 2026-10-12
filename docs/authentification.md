# Comptes, rôles et connexion

Deux mécanismes coexistent, selon le client.

| Client | Mécanisme | Routes |
|---|---|---|
| Back-office web | sessions Laravel Breeze | `routes/auth.php` |
| Application mobile | jetons Sanctum | `routes/api.php` |

## Les rôles

Colonne `users.role` :

| Rôle | Peut | Entrée |
|---|---|---|
| `user` | réserver, payer, consulter ses billets, noter, écrire au support | mobile |
| `agent` | gérer trajets et réservations de son agence | web |
| `admin` | tout, plus agences, abonnements, commissions | web |

Un `agent` est lié à une agence par `users.agence_id`.

## Connexion web

Laravel Breeze, session classique.

| Méthode | Chemin | Rôle |
|---|---|---|
| `GET` | `/login` | formulaire |
| `POST` | `/login` | connexion |
| `POST` | `/logout` | déconnexion |
| `GET` | `/register` | inscription |
| `GET` | `/forgot-password` | demande de lien |
| `GET` | `/reset-password/{token}` | nouveau mot de passe |

Après connexion, `/dashboard` redirige vers `/admin`.

### Agent d'agence suspendue

Le middleware `agence.active` l'intercepte et l'envoie sur
`/agence-suspendue`. La déconnexion reste accessible. Voir
[agences.md](agences.md).

## Connexion mobile

```http
POST /api/login
{ "email": "…", "password": "…" }
```

Renvoie un jeton Sanctum, à présenter ensuite en `Authorization: Bearer`.

| Méthode | Chemin | Rôle |
|---|---|---|
| `POST` | `/api/register` | créer un compte |
| `POST` | `/api/login` | obtenir un jeton |
| `GET` | `/api/me` | profil courant |
| `PATCH` | `/api/profile` | modifier le profil |
| `POST` | `/api/logout` | révoquer le jeton |

Contrôleur : `app/Http/Controllers/Api/AuthController.php`.

### Stockage côté Flutter

Le jeton va dans `flutter_secure_storage` sous la clé `auth_token` —
chiffré par le système, pas dans les préférences en clair.

Les informations de profil (`user_name`, `user_email`, `user_id`,
`user_role`) sont conservées à côté pour éviter un appel réseau à chaque
ouverture d'écran.

## Mot de passe oublié

Deux parcours distincts :

- **Web** : lien cliquable, mécanisme natif de Breeze.
- **Mobile** : code à 6 chiffres, voir
  [reinitialisation-mot-de-passe.md](reinitialisation-mot-de-passe.md).

## Vérification d'adresse e-mail

Les routes Breeze existent (`/verify-email`,
`/email/verification-notification`), mais aucun e-mail ne part tant que
`MAIL_MAILER=log`.

## Contrôle d'accès dans le code

Il n'y a **pas de système de permissions centralisé**. Les contrôles sont
écrits à la main, méthode par méthode, sous deux formes :

```php
// 1. Réservé à l'administrateur — abort(403) sinon
private function exigerAdmin(): void
```

Appelé trois fois seulement, par les actions d'agence (activer, désactiver,
supprimer).

```php
// 2. Ouvert aux deux rôles du back-office
if (!in_array($user->role, ['admin', 'agent'])) { … }
```

Répété dans plusieurs méthodes, avec ensuite des branchements
`$user->role === 'admin'` pour restreindre certaines parties.

`AdminController` compte 26 méthodes publiques. **Le contrôle d'accès n'est
pas uniforme entre elles** : il faut le vérifier méthode par méthode avant
d'ouvrir le back-office à de vrais agents. Un middleware de rôle, appliqué
au groupe de routes, remplacerait utilement ces vérifications dispersées.
