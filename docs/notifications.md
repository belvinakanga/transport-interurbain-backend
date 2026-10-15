# Notifications

## Le principe

Une notification est **enregistrée en base**, puis un push est tenté.
Jamais l'inverse.

```
Évènement direct                 Tâche planifiée
message, paiement, suppression   rappels de voyage, relances
        │                                │
        │                                ▼
        │                      la clé existe déjà ? on s'arrête
        │                                │
        └────────────┬───────────────────┘
                     ▼
         1. écriture en base      ← ce qui compte
                     │
                     ▼
         2. push Firebase         ← peut échouer sans conséquence
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
| `type` | famille : `message`, `voyage_rappel`, `billet_emis`… voir plus bas |
| `cle` | identifiant de l'évènement, **unique**, `null` pour les envois humains |
| `titre`, `corps` | ce qui s'affiche |
| `donnees` | JSON : ce que l'application doit ouvrir |
| `lu_le` | horodatage de lecture, `null` si non lue |

`cle` existe pour les notifications automatiques. La tâche planifiée repasse
toutes les quinze minutes ; sans ce garde-fou, le même rappel repartirait à
chaque passage. La clé décrit l'évènement — `voyage-rappel-7` — et c'est
l'index unique de la base, pas une condition écrite à la main, qui refuse le
doublon. Conséquence utile : la commande peut être relancée sans rien
dupliquer.

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

Pour tout ce qui est automatique, `envoyerUneFois()` prend une clé en plus
et ne fait rien si cette clé existe déjà :

```php
Notificateur::envoyerUneFois(
    $reservation->user_id,
    'voyage-rappel-' . $reservation->id,
    'voyage_rappel',
    'Votre voyage approche',
    'Brazzaville → Oyo demain à 06:30. Siège 15.',
    ['ecran' => 'voyages', 'reservation_id' => $reservation->id]
);
```

Elle renvoie `null` quand la notification existait déjà, ce qui permet de
compter ce qui est réellement parti.

L'écriture en base n'est **jamais** annulée par un échec du push : l'envoi
est enveloppé dans un `try/catch` qui se contente de journaliser.

Les appels placés dans un contrôleur le sont **hors transaction** et dans un
`try/catch` : un paiement encaissé ne doit pas se transformer en échec parce
qu'une notification n'est pas partie.

## Ce qui déclenche une notification

### Assistance

| Évènement | Destinataire | `type` |
|---|---|---|
| Un voyageur ouvre une conversation | tous les administrateurs | `message` |
| Un voyageur écrit dans une conversation | tous les administrateurs | `message` |
| L'assistance répond | le voyageur | `message` |

Il n'existe pas d'attribution d'une conversation à une personne précise :
tous les administrateurs sont prévenus.

### Réservations et voyages

| Évènement | Quand | Destinataire | `type` |
|---|---|---|---|
| Réservation enregistrée | à la création | le voyageur | `reservation_enregistree` |
| Billets émis | au paiement, et à l'achat direct | le voyageur | `billet_emis` |
| Réservation impayée | le lendemain de la réservation | le voyageur | `reservation_attente` |
| Sièges bientôt libérés | 24 h avant la libération | le voyageur | `reservation_derniere_chance` |
| Réservation annulée | à la libération des sièges | le voyageur | `reservation_expiree` |
| Voyage proche | moins de 28 h avant le départ | le voyageur | `voyage_rappel` |
| Départ imminent | moins de 3 h avant le départ | le voyageur | `voyage_imminent` |
| Horaire modifié | quand l'agence change date ou heure | les voyageurs du trajet | `trajet_modifie` |
| Voyage annulé | à la suppression du trajet | les voyageurs du trajet | `trajet_supprime` |
| Nouvelle vente | à l'achat direct | les agents de l'agence | `vente_agence` |

Les quatre rappels — impayée, dernière chance, voyage proche, départ
imminent — viennent de la commande `notifications:rappels`. Les autres sont
déclenchés par l'action elle-même.

#### Le calendrier d'une réservation impayée

`reservations:expirer` annule les réservations non payées à **72 heures** du
départ. Les relances encadrent ce moment, de sorte que le voyageur est
prévenu avant de perdre ses sièges, et non après :

```
création          +20 h                    J-96 h    J-72 h              départ
   |----------------|------------------------|---------|-------------------|
   |         reservation_attente    reservation_derniere_chance            |
   |                                                  ↑                    |
   |                                         sièges remis en vente         |
   |                                         reservation_expiree           |
```

La première relance n'est envoyée que si le départ laisse encore le temps de
payer. Passé 72 heures, c'est l'annulation qui parle.

#### Une suppression de trajet

Le message est envoyé **avant** la suppression : après, il n'y aurait plus de
quoi l'écrire. Les réservations, elles, ne sont pas supprimées avec le
trajet — elles restent en base sans trajet à afficher, et la notification est
la seule trace que le voyageur recevra.

> `destroyTrajet` ne vérifie **ni** le rôle **ni** l'agence : un agent peut
> supprimer le trajet d'une autre agence, alors que `updateTrajet` le lui
> refuse. Le contrôle manquant n'a pas été ajouté ici, pour ne pas toucher à
> la logique existante.

#### Un changement d'horaire

La clé contient le nouvel horaire : `trajet-horaire-10-2026-10-18T07:15`.
Deux modifications successives donnent donc bien deux messages, mais
réenregistrer le formulaire sans rien changer n'en envoie aucun. Le prix et
l'itinéraire ne déclenchent rien : ils ne changent pas le rendez-vous du
voyageur.

### La commande des rappels

```bash
php artisan notifications:rappels
```

Planifiée toutes les quinze minutes dans `routes/console.php`, avec
`withoutOverlapping()`. Sans danger à relancer à la main.

Les seuils sont des constantes en tête de
`app/Console/Commands/EnvoyerRappels.php` :

| Constante | Valeur | Rôle |
|---|---|---|
| `FENETRE_VEILLE` | 28 h | en deçà, le voyage est « proche » |
| `FENETRE_IMMINENT` | 3 h | en deçà, le départ est imminent |
| `DELAI_RELANCE` | 20 h | après la réservation, avant de relancer |
| `EXPIRATION` | 72 h | **doit rester aligné** sur `reservations:expirer` |

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

Appuyer sur une notification la marque lue, puis ouvre ce que `donnees.ecran`
désigne : la conversation pour un message, **Mes voyages** pour un rappel ou
une réservation. Les billets d'un achat direct ouvrent l'onglet « Achats »,
tout le reste l'onglet « Réservations ». Une notification sans destination
reste une simple information : l'appui la marque lue, et rien de plus.

L'icône et sa couleur viennent du `type` : vert pour ce qui est acquis — un
billet, une vente —, ambre pour ce qui attend ou change, rouge pour ce qui
est perdu, orange pour le reste.

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

Les pastilles d'icône suivent la même grammaire de couleur que l'application,
avec les variables `--tk-success-*`, `--tk-warning-*` et `--tk-danger-*` —
les seules à avoir une déclinaison en mode sombre. La bordure n'est pas
décorative : sans elle, la pastille d'une ligne non lue se confondrait avec
le fond crème de cette ligne, qui est exactement la même teinte.

## Le push Firebase

**Il est configuré.** Projet `tokende-e535e`, API **FCM HTTP v1**.

### Comment l'autorisation fonctionne

L'ancienne API — `fcm.googleapis.com/fcm/send` avec une « clé serveur »
dans l'en-tête — a été fermée par Google. La version v1 demande un jeton
OAuth2, obtenu à partir du compte de service :

```
compte de service (JSON)
        │
        │  on signe un JWT avec sa clé privée (RS256)
        ▼
oauth2.googleapis.com/token
        │
        │  jeton d'accès, valable 1 heure (mis en cache)
        ▼
fcm.googleapis.com/v1/projects/tokende-e535e/messages:send
        │
        │  un appel par appareil
        ▼
   le téléphone sonne
```

Le jeton est gardé en cache jusqu'à cinq minutes avant son échéance : le
redemander à chaque notification ajouterait un aller-retour réseau pour
rien.

Aucune dépendance Composer n'a été ajoutée : la signature RS256 tient en un
appel à `openssl_sign`.

### Où se trouve le fichier

| | |
|---|---|
| Fichier | `storage/app/private/firebase/compte-service.json` |
| Variable | `FIREBASE_CREDENTIALS` (chemin relatif à la racine) |
| Droits | `600` |
| Git | exclu — `storage/app/private/.gitignore` ignore tout son contenu |

Le fichier se télécharge depuis la console Firebase → Paramètres du projet →
**Comptes de service** → « Générer une nouvelle clé privée ».

> **Ce fichier est un secret au même titre qu'un mot de passe.** Il permet
> d'envoyer des notifications au nom du projet. Il ne doit jamais entrer
> dans Git, ni dans une image Docker publique, ni circuler par message.

### Le code

| Fichier | Rôle |
|---|---|
| `app/Services/PushFirebase.php` | JWT, jeton d'accès, envoi v1, purge des jetons morts |
| `app/Services/Notificateur.php` | aiguille vers v1 dès qu'un compte de service existe |
| `app/Console/Commands/TesterPush.php` | diagnostic et envoi de test |

`Notificateur::pousser()` n'a pas été réécrit : une condition en tête
délègue à `PushFirebase` et l'ancien chemin reste en dessous, pour une
installation encore sur `FIREBASE_SERVER_KEY`. Le compte de service a la
priorité.

### Les valeurs de `data` sont des chaînes

FCM v1 **rejette tout le message** si une valeur de `data` n'est pas une
chaîne. `reservation_id` valant 7 part donc en `"7"`. Côté Dart, les
accesseurs de `NotificationTokende` acceptaient déjà les deux formes.

### Les jetons morts s'oublient

Un appareil dont Firebase répond **404** — application désinstallée,
données effacées — est supprimé de la table `appareils`. Sans cela la liste
grossit indéfiniment et chaque envoi traîne des erreurs.

Une **400** (`INVALID_ARGUMENT`) ne supprime rien : elle vient souvent du
message, pas de l'appareil.

### Diagnostic

```bash
# Configuration seulement : fichier lisible, Google accorde-t-il un jeton ?
php artisan push:tester

# Et un envoi réel
php artisan push:tester test@example.com
php artisan push:tester 2 --titre="Essai" --corps="Message de test"
```

Sortie attendue sans destinataire :

```
Projet Firebase : tokende-e535e
Jeton d'accès obtenu (1024 caractères).
0 appareil(s) enregistré(s) au total.
```

Les deux échecs à connaître :

| Symptôme | Cause habituelle |
|---|---|
| « Google a refusé le jeton d'accès » | clé privée révoquée, ou horloge de la machine fausse — un JWT daté dans le futur est rejeté |
| « Compte de service Firebase introuvable » | `FIREBASE_CREDENTIALS` pointe à côté, ou le fichier n'a pas suivi le déploiement |

### Ce qui reste à vérifier sur un vrai téléphone

La chaîne est prouvée jusqu'à Firebase : Google accorde le jeton et accepte
les appels authentifiés. L'arrivée sur l'écran demande un appareil réel,
car la table `appareils` est vide — aucune installation mobile ne s'est
encore connectée.

Deux points à surveiller à ce moment-là :

1. **Le canal Android.** Le message ne nomme aucun `channel_id` : en nommer
   un qui n'existe pas ferait ignorer l'alerte en silence sur Android 8 et
   au-delà. L'application ne déclare aucun canal, Android retombe donc sur
   celui que crée Firebase. Pour grouper les alertes Tokende, il faudra
   créer le canal côté Dart **et** le nommer ici.
2. **Le web.** `firebase_messaging` sur le web réclame une clé VAPID passée
   à `getToken(vapidKey:)`, plus un service worker. L'application ne les a
   pas : la version web n'obtiendra pas de jeton. Les builds Android et iOS
   n'en ont pas besoin.

### Les tests ne sortent pas sur le réseau

`phpunit.xml` vide `FIREBASE_CREDENTIALS` : sans cela, chaque notification
enregistrée par un test partirait vers Google.

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
| réservation créée | voyageur notifié, date limite exacte | « tenues jusqu'au 15/10/2026 à 06:30 » |
| réservation payée | billets annoncés | 1 notification, référence incluse |
| achat direct | voyageur **et** agent notifiés | 2 notifications |
| horaire modifié | les voyageurs du trajet prévenus | 2 réservations, 2 notifications |
| même horaire réenregistré | silence | 0 notification |
| `notifications:rappels` relancée | pas de doublon | 1 notification après 3 passages |
| voyage à plus de 28 h | pas de rappel | 0 notification |
| voyage passé | pas de rappel | 0 notification |
| `push:tester` sans compte de service | échec explicite | « Compte de service absent ou illisible » |
| `push:tester` avec le compte de service | jeton accordé par Google | jeton de 1024 caractères |
| envoi vers un jeton factice | refus de Firebase, appareil conservé | HTTP 400 `INVALID_ARGUMENT` |

Automatisé :

| Fichier | Couvre |
|---|---|
| `tests/Feature/RappelsNotificationsTest.php` | les quatre rappels, horloge simulée — 8 cas |
| `tests/Feature/ExpirationNotifieTest.php` | l'annulation automatique et la libération des sièges — 3 cas |
| `tests/Feature/PushFirebaseTest.php` | signature du JWT, cache du jeton, envoi v1, purge des jetons morts — 11 cas |
