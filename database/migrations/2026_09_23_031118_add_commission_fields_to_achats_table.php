<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('achats', function (Blueprint $table) {
            $table->decimal('montant_base', 12, 2)->default(0)->after('montant');
            $table->decimal('frais_tokende', 12, 2)->default(0)->after('montant_base');
            $table->decimal('commission_tokende', 12, 2)->default(0)->after('frais_tokende');
            $table->decimal('part_agence', 12, 2)->default(0)->after('commission_tokende');
        });
    }

    public function down(): void
    {
        Schema::table('achats', function (Blueprint $table) {
            $table->dropColumn([
                'montant_base',
                'frais_tokende',
                'commission_tokende',
                'part_agence',
            ]);
        });
    }
};