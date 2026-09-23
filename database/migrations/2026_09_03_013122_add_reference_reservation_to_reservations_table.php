<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('reference_reservation')
                ->nullable()
                ->unique()
                ->after('id');
        });

        // Générer les références des anciennes réservations
        DB::table('reservations')
            ->orderBy('id')
            ->get()
            ->each(function ($reservation) {

                $annee = Carbon::parse(
                    $reservation->created_at
                )->format('Y');

                DB::table('reservations')
                    ->where('id', $reservation->id)
                    ->update([
                        'reference_reservation' =>
                            'RES-' .
                            $annee .
                            '-' .
                            str_pad(
                                $reservation->id,
                                6,
                                '0',
                                STR_PAD_LEFT
                            )
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropUnique([
                'reference_reservation'
            ]);

            $table->dropColumn(
                'reference_reservation'
            );
        });
    }
};