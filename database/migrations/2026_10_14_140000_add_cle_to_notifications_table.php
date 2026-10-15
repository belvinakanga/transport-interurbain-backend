<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Clé d'unicité des notifications automatiques.
 *
 * Les rappels sont produits par une tâche planifiée qui repasse toutes les
 * quinze minutes. Sans garde-fou, le même rappel partirait à chaque passage.
 *
 * La clé décrit l'évènement, pas le moment de l'envoi — par exemple
 * « voyage-rappel-veille-7 ». L'index unique fait le reste : un deuxième
 * envoi est refusé par la base, et non par une condition qu'on pourrait
 * oublier d'écrire.
 *
 * Elle reste nullable : les notifications déclenchées par une action
 * humaine — un message du support — n'ont pas à être dédoublonnées.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('cle', 120)->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropUnique(['cle']);
            $table->dropColumn('cle');
        });
    }
};
