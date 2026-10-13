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
    'montant_base',
    'frais_tokende',
    'commission_tokende',
    'part_agence',
    'reference',
    'mode_paiement',
    'statut',
    'description',
    'remboursable',
];

    /**
     * Masqué des réponses JSON.
     *
     * La répartition des frais entre Tokende et l'agence est une donnée
     * interne : le voyageur paie des « frais de service » et n'a pas à
     * savoir comment ils se partagent.
     *
     * Les vues du back-office lisent ces colonnes comme propriétés, ce que
     * $hidden n'empêche pas — seule la sérialisation est concernée.
     */
    protected $hidden = [
        'commission_tokende',
        'part_agence',
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