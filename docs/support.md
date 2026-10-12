# Support et avis

## Conversations

Messagerie entre un voyageur et l'administration.

| Table | Colonnes principales |
|---|---|
| `conversations` | `user_id`, sujet, statut |
| `messages` | `conversation_id`, `user_id`, contenu |

Relations : `User` → `Conversation` → `Message`.

### API mobile

| Méthode | Chemin |
|---|---|
| `GET` | `/api/support/conversations` |
| `POST` | `/api/support/conversations` |
| `GET` | `/api/support/conversations/{id}` |
| `POST` | `/api/support/conversations/{id}/messages` |

Contrôleur : `Api/SupportController`.

### Back-office

| Méthode | Chemin | Nom |
|---|---|---|
| `GET` | `/admin/support` | `admin.support.index` |
| `GET` | `/admin/support/{id}` | `admin.support.show` |
| `POST` | `/admin/support/{id}/reply` | `admin.support.reply` |
| `POST` | `/admin/support/{id}/close` | `admin.support.close` |
| `POST` | `/admin/support/{id}/reopen` | `admin.support.reopen` |

Contrôleur : `AdminSupportController`.

### Côté mobile

| Fichier | Rôle |
|---|---|
| `lib/support/support_screen.dart` | liste des conversations |
| `lib/support/conversation_screen.dart` | fil d'une conversation |
| `lib/support/nouvelle_conversation_screen.dart` | ouvrir un sujet |
| `lib/services/support_service.dart` | appels API |

Le raccourci « Assistance » de l'accueil ouvre `HelpSupportScreen`.

> `support_service.dart` pointe encore sur `http://192.168.1.84:8000/api`,
> une adresse morte, au lieu d'utiliser `AppConfig`.

---

## Avis

Notes et commentaires laissés par les voyageurs après un trajet honoré.

| Méthode | Chemin | Authentification |
|---|---|---|
| `GET` | `/api/avis` | publique |
| `GET` | `/api/avis/{id}` | publique |
| `POST` | `/api/avis` | jeton |

Back-office : `/admin/avis`, consultation et suppression.

Le bouton « Donner mon avis » n'apparaît dans l'application que si la
réservation est honorée — statut `payée` ou `Confirmée`.

Écran mobile : `lib/screens/avis/avis_screen.dart`.
