<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Voyageur extends Model
{
    protected $fillable = [
        'reservation_id',
        'prenom',
        'nom',
        'telephone',
        'email',
    ];

    /**
     * Le voyageur appartient à une réservation.
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(
            Reservation::class
        );
    }

    /**
     * Le voyageur possède un siège.
     */
    public function siege(): HasOne
    {
        return $this->hasOne(
            Siege::class
        );
    }
}