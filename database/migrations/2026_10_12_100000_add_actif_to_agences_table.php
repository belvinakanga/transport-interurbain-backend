<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Permet de suspendre une agence sans supprimer son historique.
 *
 * Une agence porte des trajets, des réservations, des agents et des
 * règlements : la supprimer détruirait des données comptables. La
 * désactivation la retire de la vente tout en conservant le passé.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agences', function (Blueprint $table) {
            $table->boolean('actif')
                ->default(true)
                ->after('modele_economique');

            // Trace de la dernière suspension, utile en support.
            $table->timestamp('desactivee_le')
                ->nullable()
                ->after('actif');

            $table->string('motif_desactivation')
                ->nullable()
                ->after('desactivee_le');
        });
    }

    public function down(): void
    {
        Schema::table('agences', function (Blueprint $table) {
            $table->dropColumn([
                'actif',
                'desactivee_le',
                'motif_desactivation',
            ]);
        });
    }
};
