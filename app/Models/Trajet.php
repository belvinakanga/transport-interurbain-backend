<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trajet extends Model
{
    protected $fillable = [
        'agence_id',
        'depart',
        'arrivee',
        'date_depart',
        'heure_depart',
        'duree',
        'heure_fin',
        'prix',
        'places_totales',
        'places_disponibles',
    ];

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}