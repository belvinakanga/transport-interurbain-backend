<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = [
        'reservation_id',
        'montant',
        'mode_paiement',
        'statut',
        'provider',
        'payment_phone_number',
        'openpay_reference',
        'openpay_status',
        'status_checked_at',
        'raw_response',
    ];

    protected $casts = [
        'status_checked_at' => 'datetime',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}