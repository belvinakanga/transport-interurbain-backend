<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agence extends Model
{
    protected $fillable = [
        'nom_agence',
        'ville',
        'adresse',
        'telephone',
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
}