<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajouter le voyageur associé au siège.
     */
    public function up(): void
    {
        Schema::table('sieges', function (Blueprint $table) {

            $table->foreignId('voyageur_id')
                ->nullable()
                ->after('reservation_id')
                ->constrained('voyageurs')
                ->nullOnDelete();
        });
    }

    /**
     * Supprimer le lien avec le voyageur.
     */
    public function down(): void
    {
        Schema::table('sieges', function (Blueprint $table) {

            $table->dropForeign([
                'voyageur_id'
            ]);

            $table->dropColumn('voyageur_id');
        });
    }
};