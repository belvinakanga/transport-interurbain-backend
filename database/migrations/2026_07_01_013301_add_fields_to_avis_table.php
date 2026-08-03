<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('avis', function (Blueprint $table) {

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()
                  ->cascadeOnDelete();

            $table->tinyInteger('note')->default(5);

            $table->text('commentaire')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('avis', function (Blueprint $table) {

            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'note',
                'commentaire'
            ]);

        });
    }
};