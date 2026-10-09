<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Cette migration est conservée uniquement pour
     * correspondre à l'état actuel de la base de données.
     *
     * Les colonnes user_id, reservation_id, montant,
     * description, reference et statut existent déjà
     * dans la table achats (ajoutées par
     * 2026_08_10_162245_add_fields_to_achats_table).
     */
    public function up(): void
    {
        //
    }

    /**
     * Aucun changement à annuler.
     */
    public function down(): void
    {
        //
    }
};
