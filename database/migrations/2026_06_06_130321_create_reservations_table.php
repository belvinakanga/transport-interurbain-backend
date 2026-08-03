<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {

            $table->id();


            // Utilisateur qui fait la réservation
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();


            // Trajet réservé
            $table->foreignId('trajet_id')
                  ->constrained()
                  ->cascadeOnDelete();


            // Nombre de places
            $table->integer('nombre_places');


            // Etat réservation
            $table->string('statut')
                  ->default('en attente');


            $table->timestamps();

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};