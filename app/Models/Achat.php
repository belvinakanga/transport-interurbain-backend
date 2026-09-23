<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trajet_id',
        'reservation_id',
        'montant',
        'reference',
        'mode_paiement',
        'statut',
        'description',
        'remboursable',
    ];

    // ==========================
    // Relations
    // ==========================

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function trajet()
    {
        return $this->belongsTo(
            Trajet::class
        );
    }

    public function reservation()
    {
        return $this->belongsTo(
            Reservation::class
        );
    }
}