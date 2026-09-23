<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('achats', function (Blueprint $table) {

            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('reservation_id')
                ->nullable()
                ->after('user_id')
                ->constrained('reservations')
                ->nullOnDelete();

            $table->decimal('montant', 10, 2)
                ->after('reservation_id');

            $table->text('description')
                ->nullable()
                ->after('montant');

            $table->string('reference')
                ->unique()
                ->after('description');

            $table->string('statut')
                ->default('en attente')
                ->after('reference');
        });
    }

    public function down(): void
    {
        Schema::table('achats', function (Blueprint $table) {

            $table->dropForeign(['user_id']);
            $table->dropForeign(['reservation_id']);

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