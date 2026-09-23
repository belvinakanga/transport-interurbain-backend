<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('achats', function (Blueprint $table) {
            $table->foreignId('trajet_id')
                  ->nullable()
                  ->after('reservation_id')
                  ->constrained('trajets')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('achats', function (Blueprint $table) {
            $table->dropForeign(['trajet_id']);
            $table->dropColumn('trajet_id');
        });
    }
};