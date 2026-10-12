# Sécurité — points à traiter

Ce document liste des faiblesses **constatées sur le code**, avec la preuve
et le correctif. Chacune porte son état.

---

## 1. Données clients exposées sans authentification — **CORRIGÉ**

*Corrigé le 12 octobre 2026.*

### Ce qui se passait

Trois routes de lecture étaient déclarées **hors** du groupe `auth:sanctum` :

```bash
$ curl -s -o /dev/null -w "%{http_code}\n" http://127.0.0.1:8000/api/billets
200
$ curl -s -o /dev/null -w "%{http_code}\n" http://127.0.0.1:8000/api/paiements
200
```

Sans le moindre jeton, et pour **tous** les clients :

```json
// GET /api/billets
{"numero_billet":"TOK-2026-000001",
 "reservation":{"user":{"name":"Segn Bamba","email":"test@example.com"}}}

// GET /api/paiements
{"montant":15000,"payment_phone_number":"242068216025",
 "provider":"MTN","openpay_reference":"PTXN26282E71F77F40A"}
```

Aggravant : `BilletController@index` et `PaiementController@index` ne
filtraient pas par utilisateur. Le filtrage existait bien pour les billets,
mais dans `mesBillets()` — **une autre méthode**. Déplacer les routes
n'aurait donc pas suffi.

### Ce qui a été fait

**1. Les trois routes sont passées dans le groupe `auth:sanctum`**
(`routes/api.php`).

**2. Les listes sont restreintes au demandeur.** La règle est dans un trait
partagé, pour qu'elle ne puisse plus diverger d'un contrôleur à l'autre :

```php
// app/Http/Controllers/Concerns/RestreintAuProprietaire.php
protected function consulteToutLeMonde(): bool
{
    $role = auth()->user()?->role;

    return in_array($role, ['admin', 'agent'], true);
}
```

```php
->when(
    ! $this->consulteToutLeMonde(),
    fn ($requete) => $requete->whereHas(
        'reservation',
        fn ($reservation) => $reservation->where('user_id', auth()->id())
    )
)
```

Un compte `admin` ou `agent` continue de tout voir — ces mêmes méthodes
servent le back-office web.

**3. `PaiementController@show` refuse le paiement d'autrui** :

```php
if (! $this->consulteToutLeMonde()
    && $paiement->reservation?->user_id !== auth()->id()) {
    abort(403, "Ce paiement ne vous appartient pas.");
}
```

Un paiement sans réservation rattachée — la colonne est nullable — n'est
réclamable par personne : refusé par défaut.

### Vérifications

| Situation | Attendu | Obtenu |
|---|---|---|
| sans jeton → `/api/billets` | refus | `401` |
| sans jeton → `/api/paiements` | refus | `401` |
| sans jeton → `/api/paiements/{id}` | refus | `401` |
| client propriétaire → `/api/billets` | ses billets | 1 sur 1 |
| autre client → `/api/billets` | rien | 0 |
| autre client → `/api/paiements` | rien | 0 sur 6 |
| client → paiement d'autrui | refus | `403` |
| admin → `/api/billets`, `/api/paiements` | tout | 1 et 6 |
| admin → paiement quelconque | autorisé | `200` |

Routes publiques inchangées : `/api/trajets`, `/api/villes`,
`/api/agences`, `/api/avis` répondent toujours `200`.

Back-office web inchangé : `/admin/billets`, `/admin/paiements`,
`/mes-billets`, `/billets`, `/paiements` répondent `200` pour
l'administrateur.

L'application mobile n'appelait ni `/billets` ni `/paiements` : aucun écran
n'est affecté.

---

## 2. `APP_DEBUG=true`

Les traces d'exception complètes sont renvoyées au client : chemins de
fichiers, extraits de code, valeurs de configuration.

**Correctif :** `APP_DEBUG=false` hors développement.

---

## 3. Aucun e-mail ne part

`MAIL_MAILER=log`. Les codes de réinitialisation de mot de passe sont
écrits dans `storage/logs/laravel.log` au lieu d'être envoyés.

Double conséquence : la fonctionnalité ne marche pas pour un vrai
utilisateur, et **les codes s'accumulent en clair dans un fichier de
journal**.

**Correctif :** configurer un vrai transport SMTP.

---

## 4. Pas de contraintes de clés étrangères

Les tables ne déclarent pas de contraintes référentielles. Supprimer une
ligne référencée ailleurs laisse des enregistrements orphelins, sans
erreur.

C'est ce qui rendait `destroyAgence()` dangereux avant son durcissement :
supprimer une agence orphelinait ses trajets, agents, abonnements et
règlements en silence. Le garde-fou est maintenant en place pour les
agences — voir [agences.md](agences.md) — **mais pas pour les autres
entités**.

**Correctif :** soit ajouter les contraintes en base, soit appliquer le
même schéma de vérification (`attachesBloquantes()` / `peutEtreSupprimee()`)
aux autres suppressions.

---

## Ce qui est en place

Pour être juste, le code fait aussi des choses correctes :

- **Lecture des billets et paiements** : authentifiée et restreinte au
  propriétaire des données.
- **Réinitialisation de mot de passe** : code haché en base, validité
  15 minutes, usage unique, limitation du nombre d'envois et de tentatives,
  pas d'énumération de comptes, révocation des jetons Sanctum après
  changement. Voir [reinitialisation-mot-de-passe.md](reinitialisation-mot-de-passe.md).
- **Suppression d'agence** : refusée tant qu'un objet y est rattaché.
- **Actions d'agence** : réservées à l'administrateur, un agent reçoit
  `403`.
- **Agents d'agence suspendue** : back-office fermé par middleware.
