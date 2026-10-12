<?php

namespace App\Http\Controllers\Concerns;

/**
 * Restriction des lectures au propriétaire des données.
 *
 * Les méthodes de liste de ce projet servent deux publics : l'API mobile,
 * où chaque client ne doit voir que ses propres billets et paiements, et
 * le back-office, où l'administration doit tout voir.
 *
 * Mettre la règle ici plutôt que de la recopier dans chaque contrôleur
 * évite qu'elle diverge — c'est exactement ce qui s'était produit pour les
 * billets, filtrés dans `mesBillets()` mais pas dans `index()`.
 */
trait RestreintAuProprietaire
{
    /**
     * L'utilisateur courant consulte-t-il au titre du back-office ?
     *
     * Un visiteur sans session n'est jamais dans ce cas : la valeur par
     * défaut est la plus restrictive.
     */
    protected function consulteToutLeMonde(): bool
    {
        $role = auth()->user()?->role;

        return in_array($role, ['admin', 'agent'], true);
    }
}
