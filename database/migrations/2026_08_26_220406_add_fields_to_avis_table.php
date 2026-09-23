<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Les colonnes user_id, note et commentaire
     * existent déjà dans la table avis.
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