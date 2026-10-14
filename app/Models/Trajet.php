<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use App\Services\FraisService;
use Illuminate\Database\Eloquent\Model;

class Trajet extends Model
{
    protected $fillable = [
        'agence_id',
        'depart',
        'arrivee',
        'date_depart',
        'heure_depart',
        'duree',
        'heure_fin',
        'prix',
        'places_totales',
        'places_disponibles',
    ];

    /**
     * Champs calculés exposés dans le JSON.
     *
     * Les clients n'ont ainsi plus à deviner le format : la base contient
     * aussi bien « 08:00:00 » que « 07:30 », et la date et l'heure y sont
     * stockées séparément.
     */
    protected $appends = [
        'depart_iso',
        'date_depart_libelle',
        'heure_depart_libelle',
        'arrivee_iso',
        'heure_arrivee_libelle',
        'duree_libelle',
        'frais_service',
        'prix_total',
    ];

    /*
    |--------------------------------------------------------------------------
    | PRIX
    |--------------------------------------------------------------------------
    |
    | Le voyageur paie le prix de l'agence plus des frais de service fixes.
    | Les exposer ici permet à l'application d'afficher, avant l'achat, le
    | montant qui sera réellement débité — elle n'affichait auparavant que
    | le prix de l'agence, soit moins que la somme facturée.
    |
    | Seuls ces deux champs sont publics : la répartition des frais entre
    | Tokende et l'agence reste interne.
    |
    */

    /** Frais de service pour un billet. */
    public function getFraisServiceAttribute(): float
    {
        return FraisService::parBillet();
    }

    /** Ce qu'un billet coûte réellement au voyageur. */
    public function getPrixTotalAttribute(): float
    {
        return (float) $this->prix + FraisService::parBillet();
    }


    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /*
    |--------------------------------------------------------------------------
    | DATE ET HEURE DE DÉPART
    |--------------------------------------------------------------------------
    */

    /** Date et heure de départ réunies, au format ISO 8601. */
    public function getDepartIsoAttribute(): ?string
    {
        return $this->momentDepart()?->toIso8601String();
    }

    /** Exemple : « 12 octobre 2026 ». */
    public function getDateDepartLibelleAttribute(): ?string
    {
        $jour = $this->jourDepart();

        return $jour?->locale('fr')->isoFormat('D MMMM YYYY');
    }

    /** Exemple : « 08h00 ». */
    public function getHeureDepartLibelleAttribute(): ?string
    {
        return $this->formaterHeure($this->heure_depart);
    }

    /*
    |--------------------------------------------------------------------------
    | ARRIVÉE
    |--------------------------------------------------------------------------
    |
    | Déduite de `heure_fin` si elle est renseignée, sinon de la durée
    | ajoutée à l'heure de départ. Reste nulle si aucune des deux n'existe :
    | mieux vaut ne rien afficher qu'inventer une heure.
    |
    */

    public function getArriveeIsoAttribute(): ?string
    {
        return $this->momentArrivee()?->toIso8601String();
    }

    public function getHeureArriveeLibelleAttribute(): ?string
    {
        $arrivee = $this->momentArrivee();

        if (!$arrivee) {
            return null;
        }

        return $arrivee->format('H') . 'h' . $arrivee->format('i');
    }

    /** Exemple : « 4 h 30 ». */
    public function getDureeLibelleAttribute(): ?string
    {
        $minutes = $this->dureeEnMinutes();

        if ($minutes === null || $minutes <= 0) {
            return null;
        }

        $heures = intdiv($minutes, 60);
        $reste = $minutes % 60;

        if ($heures === 0) {
            return $reste . ' min';
        }

        return $reste === 0
            ? $heures . ' h'
            : $heures . ' h ' . str_pad((string) $reste, 2, '0', STR_PAD_LEFT);
    }

    /*
    |--------------------------------------------------------------------------
    | OUTILS INTERNES
    |--------------------------------------------------------------------------
    */

    /** Le jour du départ, sans l'heure. */
    private function jourDepart(): ?CarbonImmutable
    {
        $valeur = $this->date_depart;

        if (empty($valeur)) {
            return null;
        }

        try {
            return CarbonImmutable::parse((string) $valeur)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    /** Date et heure de départ réunies. */
    private function momentDepart(): ?CarbonImmutable
    {
        $jour = $this->jourDepart();

        if (!$jour) {
            return null;
        }

        [$h, $m] = $this->decouperHeure($this->heure_depart);

        return $jour->setTime($h, $m);
    }

    private function momentArrivee(): ?CarbonImmutable
    {
        $depart = $this->momentDepart();

        if (!$depart) {
            return null;
        }

        // Heure de fin explicite : elle fait foi.
        if (!empty($this->heure_fin)) {
            [$h, $m] = $this->decouperHeure($this->heure_fin);

            $arrivee = $depart->setTime($h, $m);

            // Une arrivée antérieure au départ signifie le lendemain.
            return $arrivee->lessThan($depart)
                ? $arrivee->addDay()
                : $arrivee;
        }

        $minutes = $this->dureeEnMinutes();

        return $minutes !== null && $minutes > 0
            ? $depart->addMinutes($minutes)
            : null;
    }

    /**
     * Durée en minutes.
     *
     * La colonne `duree` est libre : elle peut contenir « 240 », « 4h »,
     * « 4h30 » ou « 04:30 ». On accepte ces formes plutôt que d'imposer
     * une saisie unique aux agences.
     */
    private function dureeEnMinutes(): ?int
    {
        $valeur = trim((string) ($this->duree ?? ''));

        if ($valeur === '') {
            // Pas de durée saisie : la déduire des horaires. Le formulaire
            // ne demande que l'heure d'arrivée ; la durée n'est qu'un
            // complément facultatif.
            //
            // On ne consulte momentArrivee() que si `heure_fin` est
            // renseignée : sans cela, elle rappellerait cette méthode et
            // la boucle serait infinie.
            if (empty($this->heure_fin)) {
                return null;
            }

            $depart = $this->momentDepart();
            $arrivee = $this->momentArrivee();

            if (!$depart || !$arrivee) {
                return null;
            }

            $minutes = $depart->diffInMinutes($arrivee);

            return $minutes > 0 ? (int) $minutes : null;
        }

        if (ctype_digit($valeur)) {
            return (int) $valeur;
        }

        if (preg_match('/^(\d{1,2})\s*[h:]\s*(\d{1,2})?$/i', $valeur, $m)) {
            return ((int) $m[1]) * 60 + (int) ($m[2] ?? 0);
        }

        if (preg_match('/^(\d{1,3})\s*min/i', $valeur, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    /**
     * Découpe une heure en [heures, minutes].
     *
     * Tolère « 08:00:00 », « 08:00 », « 8h30 » et « 8 ».
     *
     * @return array{0:int,1:int}
     */
    private function decouperHeure(mixed $valeur): array
    {
        $texte = trim((string) ($valeur ?? ''));

        if ($texte === '') {
            return [0, 0];
        }

        if (preg_match('/^(\d{1,2})\s*[:h]\s*(\d{1,2})?/i', $texte, $m)) {
            return [
                min(23, (int) $m[1]),
                min(59, (int) ($m[2] ?? 0)),
            ];
        }

        if (ctype_digit($texte)) {
            return [min(23, (int) $texte), 0];
        }

        return [0, 0];
    }

    /** Exemple : « 08h00 ». Nul si l'heure est absente. */
    private function formaterHeure(mixed $valeur): ?string
    {
        if (empty($valeur)) {
            return null;
        }

        [$h, $m] = $this->decouperHeure($valeur);

        return str_pad((string) $h, 2, '0', STR_PAD_LEFT)
            . 'h'
            . str_pad((string) $m, 2, '0', STR_PAD_LEFT);
    }
}
