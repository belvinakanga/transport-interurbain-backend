<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'trajet_id',
        'nombre_places',
        'statut',
        'reference_reservation',
    ];

    /**
     * Génération automatique de la référence de réservation.
     */
    protected static function booted()
    {
        static::created(function ($reservation) {

            $reservation->reference_reservation =
                'RES-' .
                $reservation->created_at->format('Y') .
                '-' .
                str_pad(
                    $reservation->id,
                    6,
                    '0',
                    STR_PAD_LEFT
                );

            $reservation->saveQuietly();
        });
    }

    /**
     * Réservation appartient à un utilisateur.
     */
    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    /**
     * Réservation appartient à un trajet.
     */
    public function trajet()
    {
        return $this->belongsTo(
            Trajet::class
        );
    }

    /**
     * Réservation possède plusieurs voyageurs.
     */
    public function voyageurs()
    {
        return $this->hasMany(
            Voyageur::class
        );
    }

    /**
     * Réservation possède plusieurs paiements.
     */
    public function paiements()
    {
        return $this->hasMany(
            Paiement::class
        );
    }

    /**
     * Réservation possède plusieurs billets.
     */
    public function billets()
    {
        return $this->hasMany(
            Billet::class
        );
    }

    /**
     * Réservation possède plusieurs sièges.
     */
    public function sieges()
    {
        return $this->hasMany(
            Siege::class
        );
    }

    /**
     * Réservation possède un achat.
     */
    public function achat()
    {
        return $this->hasOne(
            Achat::class
        );
    }
}