<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Abonnement extends Model
{
    protected $fillable = [
        'agence_id',
        'type',
        'montant',
        'date_debut',
        'date_fin',
        'statut',
    ];

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }
}