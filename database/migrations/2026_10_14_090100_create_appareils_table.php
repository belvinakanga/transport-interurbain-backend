<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Appareils enregistrés pour recevoir les notifications push.
 *
 * Un même compte peut être connecté sur plusieurs téléphones ; chacun a son
 * jeton Firebase. Le jeton est unique : il change d'appareil quand
 * l'utilisateur se déconnecte puis qu'un autre se connecte.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appareils', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->index();

            $table->string('jeton_push', 255)->unique();

            /** android, ios, web */
            $table->string('plateforme', 20)->nullable();

            $table->timestamp('vu_le')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appareils');
    }
};
