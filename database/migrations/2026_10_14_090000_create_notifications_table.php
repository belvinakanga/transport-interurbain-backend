<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Notifications destinées à un utilisateur.
 *
 * Stockées en base plutôt que simplement poussées : une notification push
 * qui n'arrive pas — téléphone éteint, autorisation refusée, jeton expiré —
 * serait perdue. Ici elle reste consultable dans l'application.
 *
 * L'envoi push, quand il est configuré, vient s'ajouter par-dessus.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->index();

            /** Famille de la notification : message, reservation, paiement… */
            $table->string('type', 40)->index();

            $table->string('titre');
            $table->text('corps');

            /**
             * Ce que l'application doit ouvrir à l'appui.
             *
             * Exemple : {"ecran":"conversation","conversation_id":4}
             */
            $table->json('donnees')->nullable();

            $table->timestamp('lu_le')->nullable();

            $table->timestamps();

            // La liste d'un utilisateur se lit toujours triée par date.
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
