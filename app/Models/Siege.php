<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Siege extends Model
{
    protected $fillable = [
        'trajet_id',
        'numero_siege',
        'statut',
        'reservation_id',
        'voyageur_id',
        'achat_id',
    ];

    /**
     * Le siège appartient à un trajet.
     */
    public function trajet(): BelongsTo
    {
        return $this->belongsTo(
            Trajet::class
        );
    }

    /**
     * Le siège appartient éventuellement à une réservation.
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(
            Reservation::class
        );
    }

    /**
     * Le siège appartient éventuellement à un voyageur.
     */
    public function voyageur(): BelongsTo
    {
        return $this->belongsTo(
            Voyageur::class
        );
    }

    /**
     * Le siège appartient éventuellement à un achat direct.
     */
    public function achat(): BelongsTo
    {
        return $this->belongsTo(
            Achat::class
        );
    }
}