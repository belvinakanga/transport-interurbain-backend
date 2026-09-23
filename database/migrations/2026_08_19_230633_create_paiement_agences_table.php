<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiement_agences', function (Blueprint $table) {

            $table->id();

            $table->foreignId('agence_id')
                ->constrained('agences')
                ->cascadeOnDelete();

            $table->decimal('montant', 12, 2);

            $table->date('date_prevue');

            $table->date('date_paiement')->nullable();

            $table->enum('statut', [
                'en attente',
                'payé',
                'en retard',
                'annulé',
            ])->default('en attente');

            $table->string('reference')->nullable()->unique();

            $table->text('note')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiement_agences');
    }
};