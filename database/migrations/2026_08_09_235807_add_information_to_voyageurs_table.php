<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajouter les informations des voyageurs.
     */
    public function up(): void
    {
        Schema::table('voyageurs', function (Blueprint $table) {

            // Réservation concernée
            $table->foreignId('reservation_id')
                ->constrained('reservations')
                ->cascadeOnDelete();

            // Informations du passager
            $table->string('prenom');
            $table->string('nom');
            $table->string('telephone');
            $table->string('email')->nullable();

        });
    }

    /**
     * Annuler les modifications.
     */
    public function down(): void
    {
        Schema::table('voyageurs', function (Blueprint $table) {

            $table->dropForeign([
                'reservation_id'
            ]);

            $table->dropColumn([
                'reservation_id',
                'prenom',
                'nom',
                'telephone',
                'email',
            ]);
        });
    }
};