# Notifications

## Le principe

Une notification est **enregistrée en base**, puis un push est tenté.
Jamais l'inverse.

```
Évènement (message, réponse de l'assistance…)
        │
        ▼
  Notificateur::envoyer()
        │
        ├─▶ 1. écriture en base      ← ce qui compte
        │
        └─▶ 2. push Firebase         ← peut échouer sans conséquence
```

Une alerte push qui n'arrive pas — téléphone éteint, autorisation refusée,
jeton périmé, Firebase non configuré — ne fait **pas** disparaître
l'information : elle reste consultable dans l'application et dans le
back-office.

C'est la raison d'être de la table `notifications`.

## Les tables

### `notifications`

| Colonne | Rôle |
|---|---|
| `user_id` | destinataire |
| `type` | `message`, `reservation`, `paiement`… |
| `titre`, `corps` | ce qui s'affiche |
| `donnees` | JSON : ce que l'application doit ouvrir |
| `lu_le` | horodatage de lecture, `null` si non lue |

`donnees` porte la destination :

```json
{ "ecran": "conversation", "conversation_id": "4" }
```

### `appareils`

| Colonne | Rôle |
|---|---|
| `user_id` | propriétaire |
| `jeton_push` | jeton Firebase, **unique** |
| `plateforme` | android, ios, web |

Le jeton est unique en base : quand un autre compte se connecte sur le même
téléphone, l'enregistrement **change de propriétaire** au lieu d'être
dupliqué. Sans cela, l'ancien utilisateur continuerait de recevoir les
notifications du nouveau.

## Envoyer une notification

`app/Services/Notificateur.php` — point d'entrée unique.

```php
use App\Services\Notificateur;

Notificateur::envoyer(
    $userId,
    'message',
    'Réponse de l’assistance',
    Str::limit($texte, 120),
    ['ecran' => 'conversation', 'conversation_id' => (string) $conversation->id]
);
```

L'écriture en base n'est **jamais** annulée par un échec du push : l'envoi
est enveloppé dans un `try/catch` qui se contente de journaliser.

## Ce qui déclenche une notification

| Évènement | Destinataire |
|---|---|
| Un voyageur ouvre une conversation | tous les administrateurs |
| Un voyageur écrit dans une conversation | tous les administrateurs |
| L'assistance répond | le voyageur |

Il n'existe pas d'attribution d'une conversation à une personne précise :
tous les administrateurs sont prévenus.

## L'API mobile

| Méthode | Chemin | Rôle |
|---|---|---|
| `GET` | `/api/notifications` | liste + compteur |
| `GET` | `/api/notifications/compteur` | compteur seul, pour la pastille |
| `PATCH` | `/api/notifications/{id}/lue` | marquer lue |
| `POST` | `/api/notifications/tout-lu` | tout marquer lu |
| `POST` | `/api/appareils` | déclarer le jeton push |
| `DELETE` | `/api/appareils` | oublier l'appareil |

Toutes authentifiées, toutes restreintes au demandeur.

## Côté application

| Fichier | Rôle |
|---|---|
| `lib/services/notifications_service.dart` | lit les notifications du serveur |
| `lib/services/notification_service.dart` | Firebase : autorisation, jeton, réception |
| `lib/screens/notifications/notifications_screen.dart` | la liste |

La cloche de l'accueil porte une **pastille rouge** avec le nombre de non
lues. Elle suit un `ValueNotifier` : une alerte reçue pendant que
l'application est ouverte fait bouger le compteur sans recharger l'écran.

Appuyer sur une notification la marque lue et ouvre la conversation
concernée.

### Cycle de vie du jeton push

| Moment | Action |
|---|---|
| Arrivée sur l'accueil | `enregistrerAupresDuServeur()` |
| Firebase renouvelle le jeton | enregistrement automatique |
| Déconnexion | `oublier()` |

L'enregistrement se fait **après** la connexion : avant, la requête
partirait sans jeton d'authentification.

## Côté back-office

| Route | Rôle |
|---|---|
| `GET /notifications` | la liste |
| `GET /notifications/{id}/ouvrir` | marque lue et va à la conversation |
| `POST /notifications/tout-lu` | tout marquer lu |

La cloche de l'en-tête affiche le nombre de non lues. Elle était auparavant
un bouton inerte avec une pastille allumée en permanence.

## Le push Firebase

**Il n'est pas actif.** `FIREBASE_SERVER_KEY` est vide dans `.env`.

Dans cet état, tout fonctionne **sauf** l'alerte hors application :
l'utilisateur voit ses notifications en ouvrant l'application, mais son
téléphone ne sonne pas quand elle est fermée.

### Pour l'activer

1. Console Firebase → Paramètres du projet → Cloud Messaging → clé serveur.
2. La poser dans `.env` :

```env
FIREBASE_SERVER_KEY=votre_cle
```

3. `php artisan config:clear`

`Notificateur::pushConfigure()` dit si la clé est présente.

> L'API utilisée est l'ancienne `fcm.googleapis.com/fcm/send`, qui attend
> une clé serveur. Google la remplace par HTTP v1, qui demande un compte de
> service et un jeton OAuth — à prévoir si l'ancienne est coupée.

## Vérifications

| Situation | Attendu | Obtenu |
|---|---|---|
| voyageur ouvre une conversation | admin notifié | 1 notification |
| l'assistance répond | voyageur notifié | 1 notification |
| `GET /api/notifications` | sa liste | 1 notification, 1 non lue |
| marquer lue | compteur à jour | 1 → 0 |
| un autre compte | ne voit rien | 0 notification |
| enregistrement d'appareil | accepté | HTTP 200 |
| back-office : cloche | compteur réel | pastille affichée |
| back-office : ouverture | va à la conversation | redirection + marquée lue |
