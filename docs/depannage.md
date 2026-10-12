# Dépannage

## Le site s'affiche sans aucune mise en forme

**Cause la plus fréquente : `public/hot`.**

Ce fichier est créé par `npm run dev`. Tant qu'il existe, toutes les pages
chargent leur CSS et leur JS depuis le serveur Vite (`127.0.0.1:5173`). Si
Vite ne tourne plus, rien ne se charge.

```bash
ls public/hot            # s'il existe, c'est le coupable
rm public/hot
npm run build
```

Vérifier ce que le serveur envoie réellement :

```bash
curl -s http://127.0.0.1:8000/login | grep -o '<link[^>]*stylesheet[^>]*>'
```

- Une adresse en `:5173` → mode développement, Vite doit tourner.
- Une adresse en `/build/assets/app-*.css` → fichiers compilés.

## Une classe Tailwind n'a aucun effet

Tailwind ne génère que les classes qu'il **trouve dans les fichiers
scannés**, au moment du build. Une classe écrite pour la première fois
n'existe pas tant que le build n'a pas tourné.

```bash
npm run build
```

Vérifier qu'elle est bien dans le bundle :

```bash
grep -o "\.ma-classe" public/build/assets/app-*.css
```

### Cas connu : les classes à opacité ne marchent pas

`bg-black/70`, `bg-white/15`, `bg-slate-950/80` ne produisent aucun fond,
même présentes dans le bundle. Contourner par un style en ligne :

```blade
<div style="background-color: rgba(2, 6, 23, 0.82);"></div>
```

Voir [stylisation.md](stylisation.md).

## Aucun e-mail ne part

`MAIL_MAILER=log`. Les messages sont écrits dans le journal.

```bash
tail -50 storage/logs/laravel.log
grep -oE "[0-9]{6}" storage/logs/laravel.log | tail -1   # code de réinitialisation
```

## Messages de validation en anglais

`APP_LOCALE=en`. Passer à `fr` dans `.env`, puis :

```bash
php artisan config:clear
```

## Les heures s'affichent `--:--`

La colonne d'arrivée s'appelle **`heure_fin`**, pas `heure_arrivee`. Du
code lisant `heure_arrivee` obtient `null`.

Utiliser les attributs calculés du modèle `Trajet` :
`heure_arrivee_libelle`, `heure_depart_libelle`, `duree_libelle`. Voir
[trajets.md](trajets.md).

## Un trajet n'apparaît plus dans l'application

`GET /api/trajets` masque les trajets des **agences suspendues**.

```bash
php artisan tinker --execute='foreach (\App\Models\Agence::all() as $a)
  echo $a->id . " " . $a->nom_agence . " actif=" . var_export($a->actif, true) . PHP_EOL;'
```

Réactiver :

```php
\App\Models\Agence::find(1)->reactiver();
```

## Un agent ne peut plus entrer dans le back-office

Il est renvoyé vers `/agence-suspendue` : son agence est désactivée. Même
vérification que ci-dessus. Voir [agences.md](agences.md).

## Une suppression d'agence est refusée

C'est voulu : l'agence porte encore des trajets, agents, abonnements ou
règlements. Le message les nomme. Désactiver au lieu de supprimer.

## Un filtre par statut ne renvoie rien

Les statuts sont enregistrés avec des casses variables (`Confirmée`,
`en attente`, `payée`). Comparer en minuscules :

```php
->whereRaw('LOWER(statut) = ?', [mb_strtolower($statut)])
```

## Un QR code de billet ne mène nulle part

`billets.qr_code` vaut `NULL` sur les billets existants : le QR encode une
URL sans identifiant. Anomalie connue, non corrigée. Voir
[reservations-billets.md](reservations-billets.md).

## Après un changement de configuration

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Vérifier l'état général

```bash
php artisan route:list --path=api
php artisan migrate:status
php artisan test
```
