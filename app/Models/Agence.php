<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Agence extends Model
{
    protected $fillable = [
        'nom_agence',
        'ville',
        'adresse',
        'telephone',
        'modele_economique',
        'actif',
        'desactivee_le',
        'motif_desactivation',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'desactivee_le' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function trajets()
    {
        return $this->hasMany(Trajet::class);
    }

    public function agents()
    {
        return $this->hasMany(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Portées
    |--------------------------------------------------------------------------
    */

    /** Agences en service, seules visibles des voyageurs. */
    public function scopeActives(Builder $query): Builder
    {
        return $query->where('actif', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Suspension
    |--------------------------------------------------------------------------
    */

    public function desactiver(?string $motif = null): void
    {
        $this->forceFill([
            'actif' => false,
            'desactivee_le' => now(),
            'motif_desactivation' => $motif,
        ])->save();
    }

    public function reactiver(): void
    {
        $this->forceFill([
            'actif' => true,
            'desactivee_le' => null,
            'motif_desactivation' => null,
        ])->save();
    }

    /*
    |--------------------------------------------------------------------------
    | Suppression
    |--------------------------------------------------------------------------
    */

    /**
     * Éléments qui rattachent encore des données à cette agence.
     *
     * Aucune contrainte de clé étrangère n'existe en base : supprimer une
     * agence qui porte des trajets ou des règlements laisserait des
     * références orphelines, sans erreur visible. On compte donc avant.
     *
     * @return array<string,int> libellé => nombre, uniquement si non nul
     */
    public function attachesBloquantes(): array
    {
        $compteurs = [
            'trajet' => Trajet::where('agence_id', $this->id)->count(),
            'agent' => User::where('agence_id', $this->id)->count(),
            'abonnement' => Abonnement::where('agence_id', $this->id)->count(),
            'règlement' => PaiementAgence::where('agence_id', $this->id)->count(),
        ];

        return array_filter($compteurs, fn ($n) => $n > 0);
    }

    /** Vrai lorsque la suppression ne laisserait aucune donnée orpheline. */
    public function peutEtreSupprimee(): bool
    {
        return $this->attachesBloquantes() === [];
    }
}
