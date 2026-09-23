<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajouter les colonnes manquantes à la table abonnements.
     */
    public function up(): void
    {
        Schema::table('abonnements', function (Blueprint $table) {

            if (!Schema::hasColumn('abonnements', 'agence_id')) {
                $table->foreignId('agence_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('agences')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('abonnements', 'type')) {
                $table->string('type')
                    ->nullable()
                    ->after('agence_id');
            }

            if (!Schema::hasColumn('abonnements', 'montant')) {
                $table->decimal('montant', 10, 2)
                    ->nullable()
                    ->after('type');
            }

            if (!Schema::hasColumn('abonnements', 'date_debut')) {
                $table->date('date_debut')
                    ->nullable()
                    ->after('montant');
            }

            if (!Schema::hasColumn('abonnements', 'date_fin')) {
                $table->date('date_fin')
                    ->nullable()
                    ->after('date_debut');
            }

            if (!Schema::hasColumn('abonnements', 'statut')) {
                $table->string('statut')
                    ->default('Actif')
                    ->after('date_fin');
            }

        });
    }

    /**
     * Annuler les modifications.
     */
    public function down(): void
    {
        Schema::table('abonnements', function (Blueprint $table) {

            if (Schema::hasColumn('abonnements', 'agence_id')) {
                $table->dropForeign(['agence_id']);
                $table->dropColumn('agence_id');
            }

            if (Schema::hasColumn('abonnements', 'type')) {
                $table->dropColumn('type');
            }

            if (Schema::hasColumn('abonnements', 'montant')) {
                $table->dropColumn('montant');
            }

            if (Schema::hasColumn('abonnements', 'date_debut')) {
                $table->dropColumn('date_debut');
            }

            if (Schema::hasColumn('abonnements', 'date_fin')) {
                $table->dropColumn('date_fin');
            }

            if (Schema::hasColumn('abonnements', 'statut')) {
                $table->dropColumn('statut');
            }

        });
    }
};