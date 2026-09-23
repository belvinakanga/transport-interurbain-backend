<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajouter la durée et l'heure de fin du trajet.
     */
    public function up(): void
    {
        Schema::table('trajets', function (Blueprint $table) {
            $table->time('duree')->nullable()->after('heure_depart');
            $table->time('heure_fin')->nullable()->after('duree');
        });
    }

    /**
     * Supprimer les colonnes ajoutées.
     */
    public function down(): void
    {
        Schema::table('trajets', function (Blueprint $table) {
            $table->dropColumn([
                'duree',
                'heure_fin',
            ]);
        });
    }
};