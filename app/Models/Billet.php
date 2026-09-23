<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Billet extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Champs autorisés
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'reservation_id',
        'voyageur_id',
        'numero_billet',
        'qr_code',
    ];


    /*
    |--------------------------------------------------------------------------
    | Génération automatique du numéro du billet
    |--------------------------------------------------------------------------
    |
    | Exemple :
    | TOK-2026-000038
    | TOK-2026-000039
    | TOK-2026-000040
    |
    | Le numéro définitif est basé sur l'identifiant
    | réel du billet.
    |
    */


    protected static function booted()
    {
        /*
        |--------------------------------------------------------------------------
        | AVANT LA CRÉATION
        |--------------------------------------------------------------------------
        |
        | La colonne numero_billet est obligatoire en base.
        | On lui donne donc une valeur temporaire unique.
        |
        */

        static::creating(function ($billet) {

            if (empty($billet->numero_billet)) {

                $billet->numero_billet =
                    'TMP-' .
                    Str::uuid()->toString();
            }

        });


        /*
        |--------------------------------------------------------------------------
        | APRÈS LA CRÉATION
        |--------------------------------------------------------------------------
        |
        | Maintenant que MySQL a attribué l'ID réel,
        | on fabrique le numéro définitif.
        |
        */

        static::created(function ($billet) {

            $billet->numero_billet =
                'TOK-' .
                $billet->created_at->format('Y') .
                '-' .
                str_pad(
                    $billet->id,
                    6,
                    '0',
                    STR_PAD_LEFT
                );

            $billet->saveQuietly();

        });
    }


    /*
    |--------------------------------------------------------------------------
    | Un billet appartient à une réservation
    |--------------------------------------------------------------------------
    */

    public function reservation()
    {
        return $this->belongsTo(
            Reservation::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Un billet appartient à un voyageur
    |--------------------------------------------------------------------------
    */

    public function voyageur()
    {
        return $this->belongsTo(
            Voyageur::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Utilisateur qui a effectué la réservation
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->reservation?->user;
    }


    /*
    |--------------------------------------------------------------------------
    | Trajet du billet
    |--------------------------------------------------------------------------
    */

    public function trajet()
    {
        return $this->reservation?->trajet;
    }


    /*
    |--------------------------------------------------------------------------
    | Siège du voyageur
    |--------------------------------------------------------------------------
    */

    public function siege()
    {
        return $this->hasOne(
            Siege::class,
            'voyageur_id',
            'voyageur_id'
        );
    }
}