# API mobile

Base : `/api`. Format JSON. Authentification par jeton Laravel Sanctum.

## S'authentifier

```http
POST /api/login
Content-Type: application/json

{ "email": "…", "password": "…" }
```

La réponse porte un jeton, à présenter ensuite :

```http
Authorization: Bearer <jeton>
```

Côté Flutter, le jeton est rangé dans `flutter_secure_storage` sous la clé
`auth_token`.

## Routes publiques

Aucun jeton requis.

| Méthode | Chemin | Rôle |
|---|---|---|
| `POST` | `/register` | créer un compte |
| `POST` | `/login` | se connecter |
| `GET` | `/trajets` | trajets disponibles |
| `GET` | `/trajets/{id}` | détail d'un trajet |
| `GET` | `/trajets/{id}/sieges` | plan des sièges |
| `GET` | `/agences` | agences |
| `GET` | `/agences/{id}` | détail d'une agence |
| `GET` | `/avis` | avis publiés |
| `GET` | `/avis/{id}` | détail d'un avis |
| `GET` | `/villes` | référentiel de villes |
| `GET` | `/tarification` | frais de service par billet |
| `POST` | `/forgot-password` | demander un code |
| `POST` | `/verify-reset-code` | vérifier un code |
| `POST` | `/reset-password` | changer le mot de passe |
| `POST` | `/webhooks/openpay` | retour de la passerelle |

## Routes authentifiées

Groupe `auth:sanctum`.

### Compte

| Méthode | Chemin | Rôle |
|---|---|---|
| `GET` | `/me` | profil courant |
| `PATCH` | `/profile` | modifier le profil |
| `POST` | `/logout` | révoquer le jeton |

### Réservations

| Méthode | Chemin | Rôle |
|---|---|---|
| `GET` | `/reservations` | mes réservations |
| `POST` | `/reservations` | créer |
| `GET` | `/reservations/{id}` | détail |
| `POST` | `/reservations/{id}/payer` | payer |
| `POST` | `/reservations/{id}/annuler` | annuler |
| `DELETE` | `/reservations/{id}` | supprimer |

### Achats

| Méthode | Chemin |
|---|---|
| `GET` | `/achats` |
| `POST` | `/achats` |
| `GET` | `/achats/{id}` |

### Billets

| Méthode | Chemin | Portée |
|---|---|---|
| `GET` | `/billets` | **ses propres billets** |
| `POST` | `/billets` | — |

### Paiements

| Méthode | Chemin | Portée |
|---|---|---|
| `GET` | `/paiements` | **ses propres paiements** |
| `GET` | `/paiements/{id}` | `403` si le paiement est celui d'un autre |

Ces lectures portent le nom, l'adresse e-mail et le numéro de téléphone du
payeur. Elles sont donc authentifiées **et** restreintes au demandeur : un
compte `admin` ou `agent` voit tout, un client ne voit que ses données.

La règle est dans le trait
`App\Http\Controllers\Concerns\RestreintAuProprietaire`.

### Avis

| Méthode | Chemin |
|---|---|
| `POST` | `/avis` |

### Paiement mobile money — **désactivé**

| Méthode | Chemin | Rôle |
|---|---|---|
| `POST` | `/openpay/initiate` | lancer un paiement |
| `GET` | `/openpay/status/{reference}` | consulter l'état |

> **Ces routes ne sont pas déclarées** tant que `OPENPAY_ACTIF=false` :
> elles répondent `404`, ainsi que le webhook `/webhooks/openpay`. L'achat
> passe alors par `POST /achats`, validé sans paiement réel. Voir
> [paiements.md](paiements.md).

### Support

| Méthode | Chemin |
|---|---|
| `GET` | `/support/conversations` |
| `POST` | `/support/conversations` |
| `GET` | `/support/conversations/{id}` |
| `POST` | `/support/conversations/{id}/messages` |

## Champs calculés d'un trajet

Un trajet renvoyé par l'API porte, en plus de ses colonnes, six attributs
mis en forme côté serveur :

```json
{
  "prix": 15000,
  "frais_service": 100,
  "prix_total": 15100,

  "depart_iso": "2026-10-20T08:00:00",
  "date_depart_libelle": "20 octobre 2026",
  "heure_depart_libelle": "08h00",
  "arrivee_iso": "2026-10-20T12:30:00",
  "heure_arrivee_libelle": "12h30",
  "duree_libelle": "4 h 30"
}
```

Le client doit les préférer aux colonnes brutes : celles-ci contiennent des
formats hétérogènes selon l'agence qui a saisi le trajet.

`prix_total` est ce que le voyageur paiera réellement — `prix` seul est le
tarif de l'agence, hors frais de service. Voir
[frais-de-service.md](frais-de-service.md).

## Trajets masqués

`GET /trajets` **n'expose que les trajets d'agences actives**. Suspendre une
agence retire ses trajets de la vente sans rien supprimer. Voir
[agences.md](agences.md).

## Configurer l'adresse côté Flutter

`lib/config/app_config.dart` :

| Plateforme | Adresse |
|---|---|
| Web (Chrome) | `http://127.0.0.1:8000/api` |
| Émulateur Android | `http://10.0.2.2:8000/api` |

> Plusieurs écrans redéfinissent cette adresse en dur au lieu d'utiliser
> `AppConfig`. Voir `docs/etat-des-lieux.md` du dépôt mobile.
