# Installation et lancement

## Ce qu'il faut avoir

| Outil | Version du poste de référence | Contrainte réelle |
|---|---|---|
| PHP | 8.5.4 | `^8.3` (déclaré dans `composer.json`) |
| Composer | 2.9.5 | — |
| Node | 24.4.0 | — |
| npm | 11.4.2 | — |

Extensions PHP : celles qu'exige Laravel 13 (`pdo_sqlite`, `mbstring`,
`openssl`, `tokenizer`, `xml`, `ctype`, `json`, `curl`, `fileinfo`).

## Première installation

Un script Composer fait tout :

```bash
composer run setup
```

Il enchaîne : `composer install`, copie de `.env.example` vers `.env` si
absent, `php artisan key:generate`, `php artisan migrate --force`,
`npm install --ignore-scripts`, `npm run build`.

### À la main, si le script échoue

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite      # la base par défaut est un fichier SQLite
php artisan migrate
npm install
npm run build
```

### Jeu de données de départ

```bash
php artisan db:seed
```

`DatabaseSeeder` crée un utilisateur de test ; `AgenceSeeder` peuple la table
des agences. Les villes sont peuplées par la migration elle-même — voir
[villes.md](villes.md).

## Lancer en développement

### Tout d'un coup

```bash
composer run dev
```

Démarre quatre processus en parallèle (`concurrently`) :

| Nom | Commande | Rôle |
|---|---|---|
| `server` | `php artisan serve` | HTTP sur `127.0.0.1:8000` |
| `queue` | `php artisan queue:listen` | file d'attente (envoi des e-mails) |
| `logs` | `php artisan pail` | journal en direct |
| `vite` | `npm run dev` | compilation CSS/JS à la volée |

Les quatre s'arrêtent ensemble (`--kill-others`).

### Séparément

```bash
php artisan serve        # API + back-office
npm run dev              # assets en mode développement
```

## Le piège des assets : `public/hot`

`npm run dev` crée le fichier `public/hot`. Tant qu'il existe, **toutes les
pages chargent leur CSS et leur JS depuis le serveur Vite** (`127.0.0.1:5173`)
au lieu des fichiers compilés.

Conséquence : si vous arrêtez Vite sans supprimer ce fichier, le site perd
toute sa mise en forme et vous verrez du HTML brut.

```bash
rm public/hot      # repasse sur les fichiers de public/build
npm run build      # (re)génère ces fichiers
```

En production, `public/hot` ne doit jamais exister.

## Tests

```bash
composer run test          # vide la configuration puis lance Pest
php artisan test
php artisan test --filter=NomDuTest
```

La suite tourne avec Pest 4. Par défaut les tests utilisent la base
configurée dans `phpunit.xml` ; pour les faire porter sur la base de
développement :

```bash
DB_DATABASE=$(pwd)/database/database.sqlite php artisan test
```

> Attention : cela écrit dans vos vraies données. À réserver aux
> vérifications ponctuelles, et à nettoyer derrière soi.

## Qualité de code

```bash
./vendor/bin/pint          # formatage (Laravel Pint)
```

## Configuration

Toutes les clés attendues sont dans `.env.example`. Les plus structurantes :

| Clé | Rôle |
|---|---|
| `APP_URL` | racine utilisée par `asset()` et les liens des e-mails |
| `APP_LOCALE` | langue des messages de validation |
| `DB_CONNECTION` | `sqlite` par défaut |
| `MAIL_MAILER` | transport des e-mails |
| `OPENPAY_ACTIF` | **branche ou débranche le paiement en ligne** |
| `OPENPAY_API_KEY`, `OPENPAY_BASE_URL`, `OPENPAY_CALLBACK_URL` | passerelle de paiement |

### Mode test : achat validé sans paiement

`OPENPAY_ACTIF=false` (valeur actuelle) retire les routes OpenPay et son
webhook. Un achat est alors validé directement, sans paiement réel. Le
drapeau mobile `AppConfig.paiementEnLigneActif` doit rester accordé. Voir
[paiements.md](paiements.md).

### Valeurs actuelles qui poseront problème en production

| Clé | Valeur actuelle | Pourquoi c'est un problème |
|---|---|---|
| `MAIL_MAILER` | `log` | **aucun e-mail ne part.** Les codes de réinitialisation finissent dans `storage/logs/laravel.log` |
| `APP_LOCALE` | `en` | messages de validation en anglais dans une interface française |
| `APP_URL` | `http://localhost` | liens d'e-mails et `asset()` pointent au mauvais endroit |
| `APP_DEBUG` | `true` | expose les traces d'erreur |

Ces quatre points sont connus et non corrigés.

## Tâche planifiée

```bash
php artisan reservations:expirer
```

Libère les réservations non payées dont le délai est dépassé. À brancher sur
le planificateur de Laravel ou sur une tâche cron.
