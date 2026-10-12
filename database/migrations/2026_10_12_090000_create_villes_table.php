<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Référentiel des villes desservies.
 *
 * Jusqu'ici la liste vivait en dur dans l'application mobile et les
 * agents saisissaient les villes en texte libre côté web : les deux
 * pouvaient diverger, et une faute de frappe créait une ville fantôme.
 */
return new class extends Migration
{
    /** Villes proposées par l'application mobile avant cette table. */
    private const VILLES_INITIALES = [
        'Brazzaville',
        'Pointe-Noire',
        'Dolisie',
        'Nkayi',
        'Oyo',
        'Ouesso',
        'Impfondo',
    ];

    public function up(): void
    {
        Schema::create('villes', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->timestamps();
        });

        $maintenant = now();

        $noms = collect(self::VILLES_INITIALES);

        // Récupérer aussi les villes déjà saisies dans les trajets, pour
        // qu'aucune destination existante ne disparaisse du référentiel.
        if (Schema::hasTable('trajets')) {
            $existantes = DB::table('trajets')
                ->select('depart as nom')
                ->union(DB::table('trajets')->select('arrivee as nom'))
                ->pluck('nom');

            $noms = $noms->concat($existantes);
        }

        $lignes = $noms
            ->map(fn ($nom) => trim((string) $nom))
            ->filter(fn ($nom) => $nom !== '')
            // Insensible à la casse : « nkayi » et « Nkayi » sont la même ville.
            ->unique(fn ($nom) => mb_strtolower($nom))
            ->sort(fn ($a, $b) => strcoll($a, $b))
            ->map(fn ($nom) => [
                'nom' => $nom,
                'created_at' => $maintenant,
                'updated_at' => $maintenant,
            ])
            ->values()
            ->all();

        if ($lignes !== []) {
            DB::table('villes')->insert($lignes);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('villes');
    }
};
