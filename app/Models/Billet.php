<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billet extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Champs autorisés
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'reservation_id',
        'numero_billet',
        'qr_code'
    ];

    /*
    |--------------------------------------------------------------------------
    | Un billet appartient à une réservation
    |--------------------------------------------------------------------------
    */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accesseur : utilisateur du billet
    |--------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->reservation?->user;
    }

    /*
    |--------------------------------------------------------------------------
    | Accesseur : trajet du billet
    |--------------------------------------------------------------------------
    */
    public function trajet()
    {
        return $this->reservation?->trajet;
    }
}