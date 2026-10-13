<?php

namespace App\Services;

/**
 * Calcul des frais de service appliqués à une vente.
 *
 * Source unique du tarif. Avant, le montant était écrit en dur au milieu
 * d'AchatController, si bien que le parcours « réservation » n'appliquait
 * aucun frais et que l'application mobile affichait un total différent de
 * celui facturé.
 *
 * Toute vente — achat direct comme réservation payée — passe par ici.
 */
class FraisService
{
    /** Frais facturés au voyageur pour un billet. */
    public static function parBillet(): float
    {
        return (float) config('tokende.frais_service.par_billet', 0);
    }

    /**
     * Ventilation complète d'une vente.
     *
     * @return array{
     *     montant_base: float,
     *     frais_tokende: float,
     *     commission_tokende: float,
     *     part_agence: float,
     *     montant: float
     * }
     */
    public static function ventiler(float $prixUnitaire, int $nombreBillets): array
    {
        $billets = max(0, $nombreBillets);

        $base  = $prixUnitaire * $billets;
        $frais = self::parBillet() * $billets;

        return [
            'montant_base'       => $base,
            'frais_tokende'      => $frais,

            // Répartition interne des frais. Jamais montrée au voyageur.
            'commission_tokende' => (float) config('tokende.frais_service.commission', 0) * $billets,
            'part_agence'        => (float) config('tokende.frais_service.part_agence', 0) * $billets,

            'montant'            => $base + $frais,
        ];
    }
}
