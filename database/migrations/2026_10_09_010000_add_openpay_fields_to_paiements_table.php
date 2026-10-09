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
        Schema::table('paiements', function (Blueprint $table) {
            if (!Schema::hasColumn('paiements', 'openpay_reference')) {
                $table->string('openpay_reference')->nullable()->unique()->after('statut');
            }
            if (!Schema::hasColumn('paiements', 'provider')) {
                $table->string('provider')->nullable()->after('mode_paiement');
            }
            if (!Schema::hasColumn('paiements', 'payment_phone_number')) {
                $table->string('payment_phone_number')->nullable()->after('provider');
            }
            if (!Schema::hasColumn('paiements', 'openpay_status')) {
                $table->string('openpay_status')->nullable()->after('openpay_reference');
            }
            if (!Schema::hasColumn('paiements', 'status_checked_at')) {
                $table->timestamp('status_checked_at')->nullable()->after('openpay_status');
            }
            if (!Schema::hasColumn('paiements', 'raw_response')) {
                $table->longText('raw_response')->nullable()->after('status_checked_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            if (Schema::hasColumn('paiements', 'raw_response')) {
                $table->dropColumn('raw_response');
            }
            if (Schema::hasColumn('paiements', 'status_checked_at')) {
                $table->dropColumn('status_checked_at');
            }
            if (Schema::hasColumn('paiements', 'openpay_status')) {
                $table->dropColumn('openpay_status');
            }
            if (Schema::hasColumn('paiements', 'openpay_reference')) {
                $table->dropUnique(['openpay_reference']);
                $table->dropColumn('openpay_reference');
            }
            if (Schema::hasColumn('paiements', 'payment_phone_number')) {
                $table->dropColumn('payment_phone_number');
            }
            if (Schema::hasColumn('paiements', 'provider')) {
                $table->dropColumn('provider');
            }
        });
    }
};
