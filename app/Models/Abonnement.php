<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Abonnement extends Model
{
    use HasFactory;

    protected $fillable = [
        'agence_id',
        'type',
        'montant',
        'date_debut',
        'date_fin',
        'statut',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    /**
     * Agence liée à l'abonnement.
     */
    public function agence()
    {
        return $this->belongsTo(
            Agence::class
        );
    }

    /**
     * Paiements liés à cet abonnement.
     */
    public function paiements()
    {
        return $this->hasMany(
            PaiementAgence::class,
            'abonnement_id'
        );
    }
}