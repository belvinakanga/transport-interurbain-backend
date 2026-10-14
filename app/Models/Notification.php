<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Notification destinée à un utilisateur.
 *
 * Elle survit à l'échec de l'envoi push : l'application la retrouve dans sa
 * liste même si le téléphone était éteint.
 */
class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'titre',
        'corps',
        'donnees',
        'lu_le',
    ];

    protected $casts = [
        'donnees' => 'array',
        'lu_le' => 'datetime',
    ];

    protected $appends = ['lue'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Non lues d'abord : c'est le seul filtre dont l'application a besoin. */
    public function scopeNonLues(Builder $query): Builder
    {
        return $query->whereNull('lu_le');
    }

    public function getLueAttribute(): bool
    {
        return $this->lu_le !== null;
    }

    public function marquerLue(): void
    {
        if ($this->lu_le === null) {
            $this->lu_le = now();
            $this->save();
        }
    }
}
