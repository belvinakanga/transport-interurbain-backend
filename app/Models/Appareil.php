<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Appareil enregistré pour le push.
 *
 * Le jeton est unique en base : quand un autre compte se connecte sur le
 * même téléphone, l'enregistrement change de propriétaire au lieu d'être
 * dupliqué — sans quoi l'ancien utilisateur continuerait de recevoir les
 * notifications du nouveau.
 */
class Appareil extends Model
{
    protected $table = 'appareils';

    protected $fillable = [
        'user_id',
        'jeton_push',
        'plateforme',
        'vu_le',
    ];

    protected $casts = ['vu_le' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Enregistre ou réattribue un jeton. */
    public static function enregistrer(
        int $userId,
        string $jeton,
        ?string $plateforme = null
    ): self {
        return self::updateOrCreate(
            ['jeton_push' => $jeton],
            [
                'user_id' => $userId,
                'plateforme' => $plateforme,
                'vu_le' => now(),
            ]
        );
    }
}
