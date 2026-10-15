# Réservations, achats et billets

## Deux chemins vers un siège

C'est la distinction la plus importante du modèle métier.

| | Réservation | Achat |
|---|---|---|
| Origine | application mobile | guichet de l'agence |
| Parcours | sièges → voyageurs → paiement → billet | vente directe |
| Table | `reservations` | `achats` |
| Contrôleur | `ReservationController` (1124 lignes) | `AchatController` (1409 lignes) |

Les deux occupent des sièges et produisent des billets. La table `sieges`
porte donc **deux colonnes de rattachement** : `reservation_id` et
`achat_id`.

## Réservation

Table `reservations` :

| Colonne | Rôle |
|---|---|
| `user_id` | client |
| `trajet_id` | trajet |
| `nombre_places` | nombre de sièges |
| `statut` | état |
| `reference_reservation` | référence lisible, `RES-2026-000001` |
| `type` | `reservation` ou achat |

### Statuts

Les statuts sont enregistrés **avec des casses variables** selon le code
qui les écrit : `Confirmée`, `en attente`, `payée`. En base aujourd'hui :
`Confirmée` et `en attente`.

Cette variété a des conséquences réelles :

- côté web, le filtre de `AdminController::reservations` compare en
  minuscules (`whereRaw('LOWER(statut) = ?')`) ;
- côté mobile, `TkChip.statut()` normalise avant de choisir la couleur,
  pour qu'un statut ne finisse jamais en puce grise par accident ;
- `Confirmée` est traité comme payé (`estHonoree`), ce qui débloque
  « Voir mon billet » et « Donner mon avis ».

**Normaliser ces valeurs en base serait le vrai correctif.** En attendant,
tout nouveau code doit comparer en minuscules.

### Routes API

| Méthode | Chemin |
|---|---|
| `GET` | `/api/reservations` |
| `POST` | `/api/reservations` |
| `GET` | `/api/reservations/{id}` |
| `POST` | `/api/reservations/{id}/payer` |
| `POST` | `/api/reservations/{id}/annuler` |
| `DELETE` | `/api/reservations/{id}` |

### Expiration automatique

```bash
php artisan reservations:expirer
```

Libère les réservations non payées hors délai et rend les sièges. À
brancher sur le planificateur.

Le voyageur est prévenu : relance le lendemain de sa réservation, dernier
avertissement 24 heures avant la libération, puis message d'annulation quand
les sièges repartent en vente. Voir `notifications.md`.

## Voyageurs

Table `voyageurs` : les passagers d'une réservation, chacun relié à un
siège. Une réservation de trois places porte trois voyageurs.

## Billets

Table `billets` :

| Colonne | Rôle |
|---|---|
| `reservation_id` | réservation d'origine |
| `voyageur_id` | passager |
| `numero_billet` | référence lisible, `TOK-2026-000001` |
| `qr_code` | identifiant encodé dans le QR |

### Page publique

```
GET /billet/{qr_code}      nom : billet.public
```

Page sans authentification, destinée au contrôle à l'embarquement : le
contrôleur scanne le QR, le téléphone ouvre l'URL.

Le QR code de l'application mobile encode cette adresse, construite par
`AppConfig.urlBillet(qrCode)`.

### Anomalie : `qr_code` vaut `null`

Le seul billet existant en base, `TOK-2026-000001`, a `qr_code = NULL`.

Conséquence : le QR encode `…/billet/` sans identifiant, et la page
publique ne trouve rien. **Le billet est inscannable.**

La génération existe pourtant à quatre endroits
(`BilletController:60`, `AchatController:1074` et `:1136`,
`PaiementController:454` et `:506`), sous la forme
`'QR-' . strtoupper(...)`. Le billet en base a été créé par un chemin qui
ne l'a pas renseignée.

**À corriger côté Laravel** : garantir le remplissage à la création, et
rattraper les billets existants.

## Côté mobile

Le billet est rendu par un composant partagé, `TkBilletSheet`, utilisé à
la fois par « Mes voyages » et par la carte « prochain départ » de
l'accueil — pour qu'il soit identique d'où qu'on l'ouvre.

Un bouton **Imprimer** produit un PDF A4 (`BilletPdf`) avec QR vectoriel,
donc scannable même à petite taille. Détails dans la documentation du dépôt
mobile.
