<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Les attributs autorisés en insertion.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'agence_id',
    ];

    /**
     * Les attributs cachés.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts des attributs.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * L'agence à laquelle appartient l'utilisateur.
     */
    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    /**
     * Les réservations de l'utilisateur.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Les achats de l'utilisateur.
     */
    public function achats()
    {
        return $this->hasMany(Achat::class);
    }
}