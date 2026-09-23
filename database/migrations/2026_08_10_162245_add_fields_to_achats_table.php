<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajouter les informations nécessaires aux achats.
     */
    public function up(): void
    {
        Schema::table('achats', function (Blueprint $table) {

            // Utilisateur qui effectue l'achat
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();

            // Réservation liée à l'achat
            $table->foreignId('reservation_id')
                ->nullable()
                ->after('user_id')
                ->constrained('reservations')
                ->nullOnDelete();

            // Montant total de l'achat
            $table->decimal('montant', 10, 2)
                ->after('reservation_id');

            // Description de l'achat
            $table->text('description')
                ->nullable()
                ->after('montant');

            // Référence unique
            $table->string('reference')
                ->unique()
                ->after('description');

            // Statut de l'achat
            $table->string('statut')
                ->default('en attente')
                ->after('reference');
        });
    }

    /**
     * Annuler les modifications.
     */
    public function down(): void
    {
        Schema::table('achats', function (Blueprint $table) {

            $table->dropForeign([
                'user_id',
            ]);

            $table->dropForeign([
                'reservation_id',
            ]);

            $table->dropColumn([
                'user_id',
                'reservation_id',
                'montant',
                'description',
                'reference',
                'statut',
            ]);
        });
    }
};