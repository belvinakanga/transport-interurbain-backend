<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajouter achat_id à la table sieges.
     */
    public function up(): void
    {
        Schema::table('sieges', function (Blueprint $table) {
            $table->foreignId('achat_id')
                ->nullable()
                ->after('reservation_id')
                ->constrained('achats')
                ->nullOnDelete();
        });
    }

    /**
     * Supprimer achat_id si la migration est annulée.
     */
    public function down(): void
    {
        Schema::table('sieges', function (Blueprint $table) {
            $table->dropForeign(['achat_id']);
            $table->dropColumn('achat_id');
        });
    }
};