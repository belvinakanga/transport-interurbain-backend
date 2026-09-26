<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agences', function (Blueprint $table) {
            $table->string('modele_economique')->default('abonnement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agences', function (Blueprint $table) {
            $table->dropColumn('modele_economique');
        });
    }
};