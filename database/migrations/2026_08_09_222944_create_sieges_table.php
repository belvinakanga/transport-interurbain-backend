<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Créer la table des sièges.
     */
    public function up(): void
    {
        Schema::create('sieges', function (Blueprint $table) {
            $table->id();

            // Le siège appartient à un trajet
            $table->foreignId('trajet_id')
                ->constrained('trajets')
                ->cascadeOnDelete();

            // Numéro du siège dans le bus
            $table->unsignedInteger('numero_siege');

            // disponible / occupe
            $table->string('statut')->default('disponible');

            // Réservation qui occupe actuellement le siège
            $table->foreignId('reservation_id')
                ->nullable()
                ->constrained('reservations')
                ->nullOnDelete();

            $table->timestamps();

            // Un même numéro de siège ne peut exister
            // qu'une seule fois pour un même trajet.
            $table->unique([
                'trajet_id',
                'numero_siege',
            ]);
        });
    }

    /**
     * Supprimer la table.
     */
    public function down(): void
    {
        Schema::dropIfExists('sieges');
    }
};