<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiement_agences', function (Blueprint $table) {

            $table->foreignId('abonnement_id')
                ->nullable()
                ->after('agence_id')
                ->constrained('abonnements')
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('paiement_agences', function (Blueprint $table) {

            $table->dropForeign([
                'abonnement_id'
            ]);

            $table->dropColumn('abonnement_id');

        });
    }
};