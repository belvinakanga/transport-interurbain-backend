# Mot de passe oublié

Parcours en trois étapes, destiné à l'application mobile. L'utilisateur
reçoit un code à 6 chiffres par e-mail, le saisit, puis choisit un nouveau
mot de passe.

> Le back-office web utilise le mécanisme natif de Laravel Breeze, par lien
> cliquable. Ce document décrit le parcours **mobile**.

## Les trois appels

### 1. Demander un code

```http
POST /api/forgot-password
{ "email": "client@example.com" }
```

Réponse (toujours la même, voir plus bas) :

```json
{ "message": "Si un compte existe pour cette adresse, un code a été envoyé.",
  "validite_minutes": 15 }
```

### 2. Vérifier le code

```http
POST /api/verify-reset-code
{ "email": "client@example.com", "code": "123456" }
```

### 3. Changer le mot de passe

```http
POST /api/reset-password
{ "email": "…", "code": "123456",
  "password": "…", "password_confirmation": "…" }
```

## Les règles

`app/Http/Controllers/Api/PasswordResetController.php` :

| Règle | Valeur | Pourquoi |
|---|---|---|
| Validité du code | 15 minutes | un code qui traîne dans une boîte mail est un mot de passe en clair |
| Usage | unique | un code réutilisable annule l'intérêt de l'expiration |
| Stockage | haché | une fuite de la base ne donne pas les codes |
| Envois | 5 par heure et par adresse | empêche d'inonder une boîte mail |
| Tentatives | 10 par heure et par adresse | empêche de deviner les 10⁶ combinaisons |

À la réussite, les compteurs sont remis à zéro et **tous les jetons Sanctum
de l'utilisateur sont révoqués** : si un tiers avait obtenu un accès, le
changement de mot de passe le coupe.

## Pas d'énumération de comptes

Les trois points d'entrée renvoient **le même message, qu'un compte existe
ou non**. Autrement dit, l'écran « mot de passe oublié » ne peut pas servir
à savoir qui est inscrit.

## L'e-mail

- Classe : `app/Mail/CodeReinitialisationMotDePasse.php`
- Vue : `resources/views/emails/code-reinitialisation.blade.php`

> **`MAIL_MAILER=log` : aucun e-mail ne part.** Les codes s'écrivent dans
> `storage/logs/laravel.log`. Pratique en développement, bloquant en
> production — et les codes y restent en clair. Voir
> [securite.md](securite.md).

Pour relire un code en développement :

```bash
grep -oE "[0-9]{6}" storage/logs/laravel.log | tail -1
```

## Côté mobile

| Fichier | Rôle |
|---|---|
| `lib/screens/auth/forgot_password.dart` | saisie de l'adresse |
| `lib/screens/auth/reset_password_screen.dart` | code + nouveau mot de passe |
| `lib/services/password_reset_service.dart` | les trois appels, délai 20 s |

`PasswordResetException` porte les messages d'erreur du serveur jusqu'à
l'écran.

> `lib/screens/auth/otp_screen.dart` appartenait à l'ancien parcours. Il
> n'est plus appelé : c'est du code mort.

## Parcours testé

Demande du code, lecture dans le journal, vérification, changement, puis
connexion avec le nouveau mot de passe — vérifié de bout en bout.
