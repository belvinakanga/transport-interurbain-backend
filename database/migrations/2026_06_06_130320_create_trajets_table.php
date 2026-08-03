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
        Schema::create('trajets', function (Blueprint $table) {

            $table->id();

            // ID de l'agence
            $table->unsignedBigInteger('agence_id');

            // Ville de départ
            $table->string('depart');

            // Ville d'arrivée
            $table->string('arrivee');

            // Date du voyage
            $table->date('date_depart');

            // Heure du départ
            $table->time('heure_depart');

            // Prix du billet
            $table->decimal('prix', 10, 2);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trajets');
    }
};