<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Cette migration est conservée uniquement pour
     * correspondre à l'état actuel de la base de données.
     *
     * Les colonnes duree et heure_fin existent déjà
     * dans la table trajets.
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