# Frais de service et commission

## Le principe

Le voyageur paie **le prix de l'agence plus des frais de service fixes**.
Ces frais se répartissent ensuite entre Tokende et l'agence.

```
prix affiché par l'agence       15 000 FCFA
+ frais de service                 100 FCFA   ← payé par le voyageur
───────────────────────────────────────────
= débité au voyageur            15 100 FCFA

Les 100 FCFA se partagent :
  commission Tokende                80 FCFA   ← interne
  part reversée à l'agence          20 FCFA   ← interne
```

**Les frais sont fixes, pas proportionnels** : 100 FCFA par billet quel que
soit le prix du trajet.

## Le tarif

`config/tokende.php`, alimenté par `.env` :

```env
TOKENDE_FRAIS_PAR_BILLET=100
TOKENDE_COMMISSION_PAR_BILLET=80
TOKENDE_PART_AGENCE_PAR_BILLET=20
```

> `commission` + `part_agence` doivent totaliser `par_billet`.

Changer le tarif ne demande aucune modification de code, ni côté serveur
ni côté application : celle-ci le lit sur l'API.

## Le calcul

`app/Services/FraisService.php` — **source unique**. Tout chemin de vente
passe par là.

```php
use App\Services\FraisService;

FraisService::parBillet();                    // 100.0

FraisService::ventiler(15000, 2);
// [
//     'montant_base'       => 30000.0,
//     'frais_tokende'      => 200.0,
//     'commission_tokende' => 160.0,
//     'part_agence'        => 40.0,
//     'montant'            => 30200.0,
// ]
```

### Pourquoi un service plutôt qu'un calcul sur place

Le montant était auparavant écrit en dur au milieu d'`AchatController`
(`$fraisTokendeParBillet = 100;`). Deux conséquences mesurées :

- **le parcours « réservation » ne facturait aucun frais** — une
  réservation payée ne rapportait rien à Tokende ;
- **le parcours OpenPay non plus** (`'frais_tokende' => 0`).

Un seul point de calcul rend cette divergence impossible.

## Les quatre chemins de vente

| Chemin | Contrôleur | État |
|---|---|---|
| Achat direct | `AchatController@store` | applique les frais |
| Réservation payée | `ReservationController@payer` | **corrigé** — n'en appliquait aucun |
| OpenPay, initiation | `Api\PaymentOpenPayController@initiate` | **corrigé** — posait 0 |
| OpenPay, webhook | `Webhook\OpenPayWebhookController` | **corrigé** — posait 0 |

> Les deux chemins OpenPay sont actuellement débranchés
> (`OPENPAY_ACTIF=false`). Ils ont été alignés pour que le défaut ne
> revienne pas au rebranchement, mais **n'ont pas pu être testés de bout en
> bout** dans cet état. Voir [paiements.md](paiements.md).

## Ce que l'API expose

### Sur chaque trajet

`GET /api/trajets` et `GET /api/trajets/{id}` portent deux attributs
calculés, au même titre que les libellés de date :

```json
{
  "prix": 15000,
  "frais_service": 100,
  "prix_total": 15100
}
```

L'application affiche ainsi le montant réellement débité **avant** l'achat.

### Le tarif seul

```http
GET /api/tarification        (publique)

{ "frais_service_par_billet": 100, "devise": "FCFA" }
```

Permet d'annoncer les frais sans charger de trajet.

### À l'achat

`POST /api/achats` renvoie `montant`, `montant_base` et `frais_tokende`.

**`commission_tokende` et `part_agence` sont masqués** (`$hidden` sur le
modèle `Achat`) : la répartition est interne et ne regarde pas le voyageur.

Les vues du back-office les lisent comme propriétés, ce que `$hidden`
n'empêche pas — seule la sérialisation JSON est concernée.

## Vocabulaire

| Interlocuteur | Terme |
|---|---|
| Voyageur | **frais de service** |
| Back-office | commission Tokende, part agence |

Ne jamais afficher « commission » au client, ni le partage 80/20 : il paie
100, la manière dont ils se répartissent ne le concerne pas.

## Côté application

| Fichier | Rôle |
|---|---|
| `lib/services/tarif_service.dart` | lit le tarif sur l'API, cache, repli à 100 |
| `lib/widgets/tk/tk_montant_detail.dart` | affiche le détail des montants |

Le détail remplace le total sec au récapitulatif et au paiement :

```
Billets           2 × 15 000 FCFA      30 000 FCFA
Frais de service  2 × 100 FCFA            200 FCFA
──────────────────────────────────────────────────
Total à payer                          30 200 FCFA
```

## Vérifications

| Situation | Attendu | Obtenu |
|---|---|---|
| `GET /api/trajets` | frais annoncés | `frais_service=100`, `prix_total=15100` |
| `GET /api/tarification` | tarif seul | `{"frais_service_par_billet":100}` |
| achat direct, 2 billets à 15 000 | 30 200 | base 30 000 + frais 200 |
| réservation payée, 2 billets | 30 200 | base 30 000 + frais 200 |
| réponse d'achat | sans répartition | `montant`, `montant_base`, `frais_tokende` |
