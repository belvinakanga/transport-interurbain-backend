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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            // Conversation à laquelle appartient le message
            $table->foreignId('conversation_id')
                ->constrained('conversations')
                ->cascadeOnDelete();

            // Utilisateur qui envoie le message
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Type d'expéditeur : voyageur ou admin
            $table->string('sender_type');

            // Contenu du message
            $table->text('message');

            // Indique si le message a été lu
            $table->boolean('is_read')
                ->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};