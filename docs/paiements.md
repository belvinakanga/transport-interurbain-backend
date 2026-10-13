# Paiements, abonnements et commissions

Trois flux d'argent distincts, à ne pas confondre.

| Flux | De → vers | Table |
|---|---|---|
| Paiement d'un billet | voyageur → Tokende | `paiements` |
| Abonnement | agence → Tokende | `abonnements` |
| Règlement d'agence | agence ↔ Tokende | `paiement_agences` |

---

## 1. Paiement d'un billet

> ### ⚠ OpenPay est désactivé
>
> **`OPENPAY_ACTIF=false` dans `.env`.** Les routes OpenPay et son webhook
> **ne sont pas déclarés** : ils répondent `404`. Un achat est validé
> directement par `AchatController@store`, qui pose `statut => 'payé'`
> **sans paiement réel**. C'est le mode voulu pendant les tests
> fonctionnels.
>
> Rien n'a été supprimé — `OpenPayService`, `PaymentOpenPayController` et
> `OpenPayWebhookController` sont intacts.
>
> **Pour rebrancher le paiement en ligne :**
>
> ```bash
> # backend : .env
> OPENPAY_ACTIF=true
> php artisan config:clear
> ```
>
> ```dart
> // mobile : lib/config/app_config.dart
> static const bool paiementEnLigneActif = true;
> ```
>
> **Les deux drapeaux doivent rester accordés.** Si le mobile croit
> OpenPay actif alors que le backend ne déclare plus les routes, l'achat
> en mobile money échoue sur un `404`.

### Mobile money via OpenPay

`app/Services/OpenPayService.php` appelle une passerelle externe.

| Méthode | Appel |
|---|---|
| `initiatePayment(array)` | `POST {base}/transaction/payment` |
| `checkStatus(string)` | `GET {base}/transaction/status/{ref}` |

En-tête d'authentification : `XO-API-KEY`. Délai : 30 secondes.

Configuration (`.env`) :

| Clé | Rôle |
|---|---|
| `OPENPAY_API_KEY` | clé d'API |
| `OPENPAY_BASE_URL` | racine, défaut `https://api.openpay-cg.com/v1` |
| `OPENPAY_CALLBACK_URL` | adresse de retour |

### Le parcours

```
Flutter                  Laravel                      OpenPay
   ├─ POST /openpay/initiate ─▶ OpenPayService ──────────▶│
   │◀──────── référence ───────────────────────────────── │
   │                                                       │
   │   (le client valide sur son téléphone)                │
   │                                                       │
   │                 ◀── POST /webhooks/openpay ───────────┤
   ├─ GET /openpay/status/{ref} ─▶ statut                  │
```

| Méthode | Chemin | Authentification |
|---|---|---|
| `POST` | `/api/openpay/initiate` | jeton |
| `GET` | `/api/openpay/status/{reference}` | jeton |
| `POST` | `/api/webhooks/openpay` | **publique** |

Le webhook est forcément public — c'est OpenPay qui appelle.

> Ces trois routes ne sont déclarées que si `OPENPAY_ACTIF=true`. En mode
> test, elles répondent `404`.

### Table `paiements`

| Colonne | Rôle |
|---|---|
| `reservation_id` | réservation payée (nullable) |
| `montant`, `mode_paiement`, `statut` | montant et état |
| `openpay_reference` | référence de transaction |
| `provider` | opérateur, `MTN`… |
| `payment_phone_number` | numéro du payeur |
| `openpay_status`, `status_checked_at` | dernier état connu |
| `raw_response` | réponse brute de la passerelle |

> `GET /api/paiements` est **publique** et renvoie tout cela pour tous les
> clients, numéro de téléphone compris. Voir [securite.md](securite.md).

---

## 2. Abonnements

Table `abonnements` : `agence_id`, `type`, `montant`, `date_debut`,
`date_fin`, `statut`.

Contrôleur `AbonnementController` (666 lignes), routes sous
`/admin/abonnements`.

La colonne `agences.modele_economique` (défaut `abonnement`) indique
comment l'agence rémunère Tokende : forfait d'abonnement ou commission.

---

## 3. Règlements d'agence

Table `paiement_agences` : `agence_id`, `abonnement_id`, `montant`,
`date_prevue`, `date_paiement`, `statut`, `reference`, `note`,
`created_by`.

| Méthode | Chemin | Nom |
|---|---|---|
| `GET` | `/paiements-agences` | `paiements-agences.index` |
| `POST` | `/paiements-agences` | `paiements-agences.store` |
| `GET` | `/paiements-agences/create` | `paiements-agences.create` |
| `GET` | `/paiements-agences/{id}/pay` | `paiements-agences.pay.form` |
| `POST` | `/paiements-agences/{id}/pay` | `paiements-agences.pay` |
| `PATCH` | `/paiements-agences/{id}/paid` | `paiements-agences.paid` |

Les agents voient les leurs via `/agent/paiements`.

---

## Commissions

Le détail du calcul est dans [frais-de-service.md](frais-de-service.md).

`GET /admin/commissions` (`AdminController@commissions`). Les champs de
commission sont portés par la table `achats` (migration
`2026_09_23_031118_add_commission_fields_to_achats_table.php`).

---

## Le chantier ouvert : à qui revient l'argent

**En l'état, OpenPay encaisse sur un compte Tokende unique.** Or les fonds
d'un billet appartiennent à l'agence qui opère le trajet ; Tokende ne doit
percevoir que sa commission.

Trois pistes ont été étudiées :

| Piste | Verdict |
|---|---|
| Simiz | aucun corridor en production |
| CinetPay | custodial — les fonds restent chez l'intermédiaire |
| MTN MoMo | pas de division du paiement à la source |

**Direction retenue :** un compte marchand par agence, la commission
Tokende étant perçue dans un flux séparé — crédit prépayé, pré-autorisation
ou abonnement.

**Chantier suspendu à la demande du client.** Rien n'est engagé côté code.
