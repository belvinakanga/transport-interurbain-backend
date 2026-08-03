<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trajets', function (Blueprint $table) {

            // Nombre total de places
            $table->integer('places_totales')
                  ->default(40);

            // Nombre de places encore disponibles
            $table->integer('places_disponibles')
                  ->default(40);

        });
    }

    public function down(): void
    {
        Schema::table('trajets', function (Blueprint $table) {

            $table->dropColumn('places_totales');
            $table->dropColumn('places_disponibles');

        });
    }
};