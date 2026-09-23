<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaiementAgence extends Model
{
    use HasFactory;

    protected $table = 'paiement_agences';

    protected $fillable = [
        'agence_id',
        'abonnement_id',
        'montant',
        'date_prevue',
        'date_paiement',
        'statut',
        'reference',
        'note',
        'created_by',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_prevue' => 'date',
        'date_paiement' => 'date',
    ];

    /**
     * Agence concernée.
     */
    public function agence()
    {
        return $this->belongsTo(
            Agence::class
        );
    }

    /**
     * Abonnement concerné.
     */
    public function abonnement()
    {
        return $this->belongsTo(
            Abonnement::class
        );
    }

    /**
     * Admin ayant créé le paiement.
     */
    public function createur()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}