# Stylisation du back-office web

## Le principe

Le visuel repose sur **trois couches**, dans cet ordre :

1. **Des variables CSS** (`--tk-*`) qui portent toutes les couleurs.
2. **Des classes composants** (`.tk-*`) construites avec `@apply` sur ces
   variables.
3. **Les utilitaires Tailwind** pour l'agencement (grilles, espacements).

La règle qui tient l'ensemble : **aucune couleur en dur dans une vue
Blade.** Une couleur s'écrit une fois, dans `resources/css/app.css`, et le
mode sombre devient gratuit.

Tout est dans un seul fichier : `resources/css/app.css` (884 lignes).

## Les couleurs de marque

| Couleur | Valeur | Usage |
|---|---|---|
| Bleu nuit | `#0A2A66` | titres, navigation, actions principales |
| Orange | `#FF6B00` | accent, action unique d'une page |
| Fond | `#F6F7F9` | arrière-plan des écrans |

Déclarées en double : comme variables `--tk-*` pour le CSS, et dans
`tailwind.config.js` sous les noms `navy`, `brand`, `canvas` pour les
utilitaires Tailwind.

## Les variables

Définies sur `:root` pour le mode clair, redéfinies sous `.dark` pour le
mode sombre. Changer de thème ne change que ces valeurs : aucune classe
composant n'est réécrite.

### Surfaces et texte

| Variable | Clair | Sombre |
|---|---|---|
| `--tk-canvas` | `#F6F7F9` | `#0B1220` |
| `--tk-surface` | `#FFFFFF` | `#111B2E` |
| `--tk-surface-2` | `#F8FAFC` | `#16223A` |
| `--tk-surface-3` | `#F1F5F9` | `#1D2B47` |
| `--tk-border` | `#E2E8F0` | `#2A3B5C` |
| `--tk-text` | `#1E293B` | `#E2E8F0` |
| `--tk-text-2` | `#475569` | `#B6C2D4` |
| `--tk-text-3` | `#64748B` | `#8FA0B8` |
| `--tk-heading` | `#0A2A66` | `#DCE8FF` |

### Marque et états

| Variable | Clair | Sombre |
|---|---|---|
| `--tk-primary` | `#0A2A66` | `#1B4FA8` |
| `--tk-accent` | `#FF6B00` | `#FF7A1F` |

L'orange s'éclaircit en mode sombre : à luminosité égale, un orange foncé
sur fond sombre perd son contraste.

Familles d'état : `--tk-success-*`, `--tk-warning-*`, `--tk-danger-*`,
`--tk-info-*`, chacune en trois déclinaisons `-bg` (fond), `-fg` (texte),
`-bd` (bordure).

## Le catalogue de composants

Toutes ces classes sont dans `@layer components`.

### Mise en page

| Classe | Rôle |
|---|---|
| `.tk-page` | conteneur d'une page |
| `.tk-page-head` | bandeau de titre |
| `.tk-page-title` | titre de la page |
| `.tk-card` | surface de contenu |

### Navigation latérale

| Classe | Rôle |
|---|---|
| `.tk-nav-link` | entrée du menu |
| `.tk-nav-link.is-active` | entrée courante |
| `.tk-nav-label` | intitulé de groupe |

Le menu se réduit en mode compact sur grand écran : les libellés
disparaissent, les icônes se centrent.

### Boutons

| Classe | Apparence |
|---|---|
| `.tk-btn` | base commune (hauteur, arrondi, espacement) |
| `.tk-btn-navy` | bleu nuit plein — action principale |
| `.tk-btn-accent` | orange plein — action unique et marquante |
| `.tk-btn-ghost` | contour seul — action neutre |

### Boutons icône

| Classe | Couleur | Usage |
|---|---|---|
| `.tk-icon-btn` | base | — |
| `.tk-icon-btn-navy` | bleu nuit | consulter |
| `.tk-icon-btn-brand` | orange | modifier |
| `.tk-icon-btn-red` | rouge | supprimer |
| `.tk-icon-btn-slate` | gris | suspendre (réversible) |
| `.tk-icon-btn-green` | vert | réactiver |

Le gris pour la suspension n'est pas un détail : le rouge annonce
l'irréversible, et une suspension se défait. Voir [agences.md](agences.md).

### Tableaux

`.tk-table` habille `<table>` entière — en-tête, cellules, survol des
lignes, dernière ligne sans filet. `.tk-empty` est le message affiché quand
il n'y a rien à montrer.

### Puces d'état

| Classe | Couleur |
|---|---|
| `.tk-badge-green` | validé, payé, actif |
| `.tk-badge-orange` | en attente |
| `.tk-badge-red` | annulé, refusé |
| `.tk-badge-slate` | neutre, suspendu |
| `.tk-badge-navy` | information |

### Formulaires

`.tk-input`, `.tk-form-label`, `.tk-label`.

### Encarts et indicateurs

`.tk-alert`, `.tk-alert-success`, `.tk-alert-danger`, `.tk-kpi`,
`.tk-kpi-value`, `.tk-kpi-icon`, `.tk-stat-navy`, `.tk-stat-brand`,
`.tk-stat-tint`.

### Le logo

`.tk-logo-tuile` porte le logo transparent sur une tuile blanche. Elle
existe parce que `.dark .bg-white` repeint tous les fonds blancs en mode
sombre, ce qui rendait le logo illisible. Cette classe échappe à cette
surcharge.

## Le mode sombre

Stratégie `class` (`darkMode: 'class'` dans `tailwind.config.js`). Un script
dans `layouts/guest.blade.php` pose la classe `dark` sur `<html>` avant le
premier rendu, d'après `localStorage` puis `prefers-color-scheme`. Poser la
classe avant le rendu évite le flash blanc.

La bascule est exposée par `window.tkToggleTheme()`.

Une section de `app.css` (lignes 531 et suivantes) **surcharge les
utilitaires Tailwind** en mode sombre :

```css
.dark .bg-white      { background: var(--tk-surface); }
.dark .bg-gray-50,
.dark .bg-slate-50   { background: var(--tk-surface-2); }
.dark .bg-orange-50  { … }
```

C'est ce qui permet aux vues écrites en Tailwind clair de basculer sans
être réécrites. Mais c'est aussi un piège : **une classe `bg-*` posée dans
une vue peut être repeinte sans prévenir.** Pour un fond qui doit rester
tel quel, créez une classe `.tk-*` dédiée.

## La chaîne de compilation

```
resources/css/app.css
   └─▶ PostCSS (postcss.config.js)
         ├─ tailwindcss 3.4.19
         └─ autoprefixer
            └─▶ Vite 8 ──▶ public/build/assets/app-[hash].css
```

Tailwind lit les classes utilisées dans :

```js
content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
]
```

**Conséquence pratique :** une classe Tailwind écrite pour la première fois
dans une vue n'existe pas dans le CSS tant que le build n'a pas tourné.

```bash
npm run build     # après avoir ajouté des classes Tailwind nouvelles
```

En développement, `npm run dev` recompile tout seul.

## Deux anomalies connues

### Les classes à opacité ne s'appliquent pas

`bg-black/70`, `bg-white/15`, `bg-slate-950/80` **ne produisent aucun fond**,
alors que la règle CSS correspondante est bien générée et présente dans le
bundle (`.bg-white\/15{background-color:#ffffff26}`). La cause n'a pas été
identifiée.

Conséquence visible : le voile sombre de la page de connexion ne s'applique
pas, et la photo de fond reste en pleine luminosité sous la carte.

**Contournement employé** dans `agence-suspendue.blade.php` : un style en
ligne, qui ne dépend d'aucune compilation.

```blade
<div class="absolute inset-0" style="background-color: rgba(2, 6, 23, 0.82);"></div>
```

### Deux versions de Tailwind cohabitent

`package.json` déclare `tailwindcss` en `^3.1.0` (3.4.19 installé) **et**
`@tailwindcss/vite` en `^4.0.0` (4.3.0 installé). Le plugin v4 n'est
référencé nulle part dans `vite.config.js` — la compilation passe bien par
PostCSS et Tailwind 3. C'est une dépendance morte, à retirer. Elle est le
premier suspect pour l'anomalie ci-dessus.

## Ajouter un composant

1. Ouvrir `resources/css/app.css`, section `@layer components`.
2. Écrire la classe avec `@apply` et des variables `--tk-*`, jamais de
   couleur en dur.
3. Vérifier le rendu dans les deux thèmes.
4. `npm run build`.

```css
.tk-mon-composant {
    @apply inline-flex items-center gap-2 px-3 py-2 rounded-lg;
    background: var(--tk-surface-2);
    color: var(--tk-text);
    border: 1px solid var(--tk-border);
}
```

## Polices et icônes

| Ressource | Source |
|---|---|
| Figtree | Bunny Fonts (`fonts.bunny.net`) |
| Font Awesome 7 | paquet npm `@fortawesome/fontawesome-free` |
| Chart.js 4 | paquet npm, graphiques du tableau de bord |
| Alpine.js 3 | interactions légères des vues |
