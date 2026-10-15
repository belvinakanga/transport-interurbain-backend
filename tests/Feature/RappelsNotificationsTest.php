<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Reservation;
use App\Models\Trajet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Rappels automatiques : ce que la commande envoie, et quand.
 *
 * Les quatre cas sont séparés parce qu'ils se déclenchent sur des fenêtres
 * différentes et que c'est précisément là que se cachent les erreurs.
 */
class RappelsNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-06-10 08:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    /** Crée un voyageur, un trajet au départ donné, et sa réservation. */
    private function reservation(
        string $depart,
        string $statut,
        ?string $creeLe = null
    ): Reservation {
        $user = User::factory()->create();

        $quand = Carbon::parse($depart);

        $trajet = Trajet::create([
            'agence_id' => 1,
            'depart' => 'Brazzaville',
            'arrivee' => 'Pointe-Noire',
            'date_depart' => $quand->format('Y-m-d'),
            'heure_depart' => $quand->format('H:i'),
            'prix' => 25000,
            'places_totales' => 40,
            'places_disponibles' => 38,
        ]);

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'trajet_id' => $trajet->id,
            'nombre_places' => 2,
            'statut' => $statut,
            'type' => 'reservation',
        ]);

        if ($creeLe) {
            $reservation->created_at = Carbon::parse($creeLe);
            $reservation->saveQuietly();
        }

        return $reservation;
    }

    private function types(): array
    {
        return Notification::pluck('type')->all();
    }

    public function test_rappelle_le_voyage_la_veille(): void
    {
        $this->reservation('2026-06-11 06:30', 'payée');

        $this->artisan('notifications:rappels')->assertSuccessful();

        $this->assertSame(['voyage_rappel'], $this->types());

        $this->assertStringContainsString(
            'demain à 06:30',
            Notification::first()->corps
        );
    }

    public function test_rappelle_le_depart_imminent(): void
    {
        $this->reservation('2026-06-10 10:00', 'payée');

        $this->artisan('notifications:rappels')->assertSuccessful();

        $this->assertSame(['voyage_imminent'], $this->types());
    }

    public function test_ne_rappelle_pas_un_voyage_encore_loin(): void
    {
        $this->reservation('2026-06-20 06:30', 'payée');

        $this->artisan('notifications:rappels')->assertSuccessful();

        $this->assertSame([], $this->types());
    }

    public function test_ne_rappelle_pas_un_voyage_passe(): void
    {
        $this->reservation('2026-06-09 06:30', 'payée');

        $this->artisan('notifications:rappels')->assertSuccessful();

        $this->assertSame([], $this->types());
    }

    public function test_relance_une_reservation_impayee_du_lendemain(): void
    {
        // Départ dans dix jours, réservée il y a deux jours.
        $this->reservation(
            '2026-06-20 06:30',
            'en attente',
            '2026-06-08 08:00:00'
        );

        $this->artisan('notifications:rappels')->assertSuccessful();

        $this->assertSame(['reservation_attente'], $this->types());
    }

    public function test_ne_relance_pas_une_reservation_du_jour(): void
    {
        $this->reservation(
            '2026-06-20 06:30',
            'en attente',
            '2026-06-10 06:00:00'
        );

        $this->artisan('notifications:rappels')->assertSuccessful();

        $this->assertSame([], $this->types());
    }

    public function test_avertit_avant_la_liberation_des_sieges(): void
    {
        // 86 heures avant le départ : les sièges tombent à 72 heures.
        $this->reservation(
            '2026-06-13 22:00',
            'en attente',
            '2026-06-01 08:00:00'
        );

        $this->artisan('notifications:rappels')->assertSuccessful();

        $this->assertSame(['reservation_derniere_chance'], $this->types());

        $this->assertStringContainsString(
            '14 heures',
            Notification::first()->corps
        );
    }

    public function test_n_envoie_jamais_deux_fois_le_meme_rappel(): void
    {
        $this->reservation('2026-06-11 06:30', 'payée');

        $this->artisan('notifications:rappels')->assertSuccessful();
        $this->artisan('notifications:rappels')->assertSuccessful();
        $this->artisan('notifications:rappels')->assertSuccessful();

        $this->assertSame(1, Notification::count());
    }
}
