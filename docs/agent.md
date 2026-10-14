# Espace agent

Un agent gère **sa seule agence**. Il entre par `/agent/dashboard` et
partage ensuite les vues du back-office, filtrées sur son agence.

## Accès

| Route | Vue | Portée |
|---|---|---|
| `/agent/dashboard` | `agent.dashboard` | son agence |
| `/admin/trajets` | partagée | ses trajets |
| `/admin/reservations` | partagée | ses réservations |
| `/achats` | partagée | ses achats |
| `/agent/paiements` | `agent.paiements` | ses règlements |

Si son agence est suspendue, le middleware `agence.active` lui ferme tout —
voir [agences.md](agences.md).

## Réservations et achats ne se mélangent pas

C'est la règle structurante de cet espace :

> **Une réservation payée reste une réservation.** Seul l'achat direct, fait
> au guichet, compte comme un achat.

| Compteur | Compte |
|---|---|
| Réservations | `Reservation` de l'agence dont `type != 'achat_direct'` |
| Achats | `Achat` dont la réservation a `type = 'achat_direct'` |

Le comptage était auparavant écrit **dans la vue Blade**, et une réservation
payée s'y retrouvait comptée des deux côtés. Il est maintenant dans
`AgentController@dashboard`, aligné sur le filtre déjà appliqué par
`AchatController@index`.

## Recettes

Le tableau de bord affiche quatre montants, sur les ventes **payées** :

| Bloc | Contenu |
|---|---|
| Billets | ce que l'agence encaisse sur ses billets |
| Votre part des frais | sa part des frais de service (20 FCFA/billet) |
| Commission Tokende | la part de Tokende (80 FCFA/billet) |
| Total encaissé | billets + frais |

Les montants par billet sont lus dans `config/tokende.php` : changer le
tarif met la page à jour sans toucher au code.

## Numéro de siège

Affiché dans les deux listes :

- **Achats** — déjà présent, par voyageur.
- **Réservations** — colonne « Siège », ajoutée ; liste les numéros de la
  réservation, ou `—` si aucun siège n'y est rattaché.

## Ventilation visible dans les listes

### Réservations

La cellule « Montant » porte trois lignes :

```
30 200 FCFA
30 000 + 200 de frais
vous 40 · Tokende 160
```

Le total inclut les frais : la vue affichait auparavant `prix × places`,
soit moins que la somme réellement due.

### Achats

La colonne « Part agence » détaille `billets X · part frais Y`, et une
colonne « Commission Tokende » a été ajoutée à côté.

## Colonne « Agence » masquée pour l'agent

Elle est toujours la sienne, et le tableau des réservations manquait de
largeur. Elle reste affichée pour l'administrateur, qui voit toutes les
agences.

Le `colspan` de la ligne vide suit le rôle.

## Largeur des tableaux

Les deux listes dépassent 1280 px et défilent horizontalement
(`overflow-x-auto`). Les colonnes de droite — dont « Statut » — peuvent
demander un défilement sur un écran étroit.
