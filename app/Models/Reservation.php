<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'trajet_id',
        'nombre_places',
        'statut'
    ];

    // Réservation appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Réservation appartient à un trajet
    public function trajet()
    {
        return $this->belongsTo(Trajet::class);
    }

    // Réservation possède plusieurs paiements
    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    // Réservation possède plusieurs billets
    public function billets()
    {
        return $this->hasMany(Billet::class);
    }
}