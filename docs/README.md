# Documentation — Tokende backend

Application Laravel servant à la fois l'API de l'application mobile et le
back-office web (administration et agences).

Tokende est une plateforme de réservation de transport interurbain en
République du Congo.

## Par où commencer

| Vous voulez… | Document |
|---|---|
| installer et lancer le projet | [installation.md](installation.md) |
| comprendre l'organisation du code | [architecture.md](architecture.md) |
| reprendre l'habillage visuel | [stylisation.md](stylisation.md) |
| appeler l'API | [api.md](api.md) |

## Fonctionnalités

| Sujet | Document |
|---|---|
| Comptes, rôles et connexion | [authentification.md](authentification.md) |
| Mot de passe oublié | [reinitialisation-mot-de-passe.md](reinitialisation-mot-de-passe.md) |
| Agences : création, suspension, suppression | [agences.md](agences.md) |
| Référentiel de villes | [villes.md](villes.md) |
| Trajets et sièges | [trajets.md](trajets.md) |
| Réservations, achats et billets | [reservations-billets.md](reservations-billets.md) |
| Paiements, abonnements, commissions | [paiements.md](paiements.md) |
| Support et conversations | [support.md](support.md) |

## Transversal

| Sujet | Document |
|---|---|
| Schéma et migrations | [base-de-donnees.md](base-de-donnees.md) |
| Points de sécurité **à traiter** | [securite.md](securite.md) |
| Pannes courantes | [depannage.md](depannage.md) |

## Le dépôt mobile

L'application Flutter vit dans `transport-interurbain-tokende-mobile`, avec
sa propre documentation dans `docs/`. Les deux dépôts se rejoignent sur un
seul contrat : l'API décrite dans [api.md](api.md).

---

Documentation rédigée le 12 octobre 2026 à partir du code tel qu'il existe,
pas d'une intention. Quand quelque chose ne fonctionne pas, c'est écrit.
