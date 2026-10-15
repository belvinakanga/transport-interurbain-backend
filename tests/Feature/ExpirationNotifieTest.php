<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Reservation;
use App\Models\Siege;
use App\Models\Trajet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * L'annulation automatique d'une réservation impayée doit se dire.
 *
 * Avant, les sièges disparaissaient sans un mot : le voyageur retrouvait sa
 * réservation annulée sans jamais avoir été prévenu.
 */
class ExpirationNotifieTest extends TestCase
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

    private function reservation(string $depart, string $statut): Reservation
    {
        $user = User::factory()->create();

        $quand = Carbon::parse($depart);

        $trajet = Trajet::create([
            'agence_id' => 1,
            'depart' => 'Dolisie',
            'arrivee' => 'Nkayi',
            'date_depart' => $quand->format('Y-m-d'),
            'heure_depart' => $quand->format('H:i'),
            'prix' => 6500,
            'places_totales' => 20,
            'places_disponibles' => 18,
        ]);

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'trajet_id' => $trajet->id,
            'nombre_places' => 2,
            'statut' => $statut,
            'type' => 'reservation',
        ]);

        foreach ([1, 2] as $numero) {
            Siege::create([
                'trajet_id' => $trajet->id,
                'numero_siege' => $numero,
                'statut' => 'occupe',
                'reservation_id' => $reservation->id,
            ]);
        }

        return $reservation;
    }

    public function test_previent_le_voyageur_de_l_annulation(): void
    {
        // Départ dans 48 heures : en deçà du seuil de 72 heures.
        $reservation = $this->reservation('2026-06-12 08:00', 'en attente');

        $this->artisan('reservations:expirer')->assertSuccessful();

        $this->assertSame('annulée', $reservation->fresh()->statut);

        $notification = Notification::first();

        $this->assertNotNull($notification);
        $this->assertSame('reservation_expiree', $notification->type);
        $this->assertStringContainsString(
            '2 places ont été remises en vente',
            $notification->corps
        );
    }

    public function test_ne_previent_pas_une_reservation_payee(): void
    {
        $this->reservation('2026-06-12 08:00', 'payée');

        $this->artisan('reservations:expirer')->assertSuccessful();

        $this->assertSame(0, Notification::count());
    }

    public function test_les_sieges_sont_bien_liberes(): void
    {
        $reservation = $this->reservation('2026-06-12 08:00', 'en attente');

        $this->artisan('reservations:expirer')->assertSuccessful();

        $this->assertSame(
            0,
            Siege::where('reservation_id', $reservation->id)->count()
        );

        $this->assertSame(
            20,
            $reservation->trajet->fresh()->places_disponibles
        );
    }
}
