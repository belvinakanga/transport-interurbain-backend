<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Ville desservie par le réseau.
 *
 * Référentiel partagé : le formulaire de création de trajet et
 * l'application mobile lisent la même liste.
 */
class Ville extends Model
{
    protected $table = 'villes';

    protected $fillable = ['nom'];

    /** Liste alphabétique des noms. */
    public static function noms(): array
    {
        return static::query()
            ->orderBy('nom')
            ->pluck('nom')
            ->all();
    }

    /**
     * Retrouve une ville par son nom, ou la crée si elle est nouvelle.
     *
     * La comparaison ignore la casse et les espaces : un agent qui saisit
     * « pointe-noire » ne crée pas un doublon de « Pointe-Noire ».
     * Renvoie le nom retenu, ou `null` si la saisie est vide.
     */
    public static function trouverOuCreer(?string $nom): ?string
    {
        $propre = trim((string) $nom);

        if ($propre === '') {
            return null;
        }

        $existante = static::query()
            ->whereRaw('LOWER(nom) = ?', [mb_strtolower($propre)])
            ->first();

        if ($existante) {
            return $existante->nom;
        }

        return static::create(['nom' => $propre])->nom;
    }
}
