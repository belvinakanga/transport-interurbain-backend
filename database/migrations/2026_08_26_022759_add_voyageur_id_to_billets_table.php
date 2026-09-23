<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajouter le voyageur au billet.
     */
    public function up(): void
    {
        Schema::table('billets', function (Blueprint $table) {
            $table->foreignId('voyageur_id')
                ->nullable()
                ->after('reservation_id')
                ->constrained('voyageurs')
                ->nullOnDelete();
        });
    }

    /**
     * Annuler la modification.
     */
    public function down(): void
    {
        Schema::table('billets', function (Blueprint $table) {
            $table->dropForeign(['voyageur_id']);
            $table->dropColumn('voyageur_id');
        });
    }
};