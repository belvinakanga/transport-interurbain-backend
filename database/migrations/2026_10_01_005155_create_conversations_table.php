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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            // Voyageur propriétaire de la conversation
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Sujet de la conversation
            $table->string('subject');

            // Statut de la conversation
            $table->string('status')
                ->default('open');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};