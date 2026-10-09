<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Achat;
use App\Models\Billet;
use App\Models\Paiement;
use App\Models\Reservation;
use App\Models\Trajet;
use App\Models\Siege;
use App\Models\Voyageur;
use App\Services\OpenPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OpenPayWebhookController extends Controller
{
    public function __construct(protected OpenPayService $openPayService)
    {
    }

    /**
     * Callback OpenPay (public). Doit répondre HTTP 200 rapidement.
     */
    public function handle(Request $request)
    {
        // Accepter payload JSON
        $payload = $request->json()->all();

        // Répondre immédiatement 200
        $response = response()->json(['success' => true]);

        // Traiter en arrière-plan logique si souhaité, mais ici synchrone simple (réponse 200 déjà envoyée? non ici; on renvoie après traitement minimal)
        // OpenPay exige HTTP 200 dans 10s. Traiter rapidement.
        $this->processWebhook($payload);

        return $response;
    }

    protected function processWebhook(array $payload): void
    {
        $reference = $payload['reference'] ?? null;
        $status = strtolower((string) ($payload['status'] ?? ''));
        $amount = (float) ($payload['amount'] ?? 0);
        $metadata = $payload['metadata'] ?? [];

        if (!$reference) {
            Log::warning('OpenPay webhook sans reference');
            return;
        }

        DB::transaction(function () use ($reference, $status, $amount, $payload, $metadata) {
            $paiement = Paiement::with(['reservation.trajet', 'reservation.voyageurs', 'reservation.sieges'])
                ->where('openpay_reference', $reference)
                ->lockForUpdate()
                ->first();

            if (!$paiement) {
                // Paiement inconnu - tenter vérification statut pour réconciliation
                try {
                    $opRes = $this->openPayService->checkStatus($reference);
                    if ($opRes['success'] && is_array($opRes['data'])) {
                        $data = $opRes['data'];
                        $paiement = Paiement::where('openpay_reference', $data['reference'] ?? $reference)->lockForUpdate()->first();
                        if ($paiement) {
                            $paiement->openpay_status = $data['status'] ?? $paiement->openpay_status;
                        }
                    }
                } catch (\Throwable $e) {
                    // Ignorer
                }
                if (!$paiement) {
                    return; // ne pas traiter
                }
            }

            // Mettre à jour statut OpenPay
            $paiement->openpay_status = $status;
            $paiement->status_checked_at = now();
            $paiement->raw_response = json_encode(array_merge(['webhook' => true], $payload), JSON_UNESCAPED_UNICODE);
            $paiement->save();

            // Seulement success déclenche confirmation
            if ($status !== 'success') {
                $paiement->statut = $status;
                $paiement->save();
                return;
            }

            // Vérifier montant si possible (arrondi)
            $expected = (float) $paiement->montant;
            if ($expected > 0 && abs($expected - $amount) > 1.0) {
                Log::warning('OpenPay webhook montant incoherent', [
                    'reference' => $reference,
                    'expected' => $expected,
                    'received' => $amount,
                ]);
                // Ne pas marquer comme payé automatiquement - laisser réconciliation
                return;
            }

            $reservation = $paiement->reservation;
            if (!$reservation) {
                return;
            }

            $trajet = $reservation->trajet;
            if (!$trajet) {
                $trajet = Trajet::find($reservation->trajet_id);
            }

            // Éviter double traitement
            if (strtolower(trim((string)$reservation->statut)) === 'payée') {
                $paiement->statut = 'payé';
                $paiement->save();
                return;
            }

            // S'assurer Achat existe et marqué payé
            $achat = Achat::where('reservation_id', $reservation->id)->lockForUpdate()->first();
            if (!$achat) {
                $referenceAchat = 'ACH-' . now()->format('Y') . '-' . str_pad((int)$reservation->id, 6, '0', STR_PAD_LEFT);
                $achat = Achat::create([
                    'user_id' => $reservation->user_id,
                    'reservation_id' => $reservation->id,
                    'trajet_id' => $reservation->trajet_id,
                    'montant' => $expected > 0 ? $expected : $amount,
                    'montant_base' => $expected > 0 ? $expected : $amount,
                    'frais_tokende' => 0,
                    'commission_tokende' => 0,
                    'part_agence' => 0,
                    'description' => 'Paiement OpenPay confirmé',
                    'reference' => $referenceAchat,
                    'statut' => 'payé',
                    'remboursable' => true,
                ]);
            } else {
                $achat->statut = 'payé';
                $achat->save();
            }

            // Marquer réservation payée
            $reservation->statut = 'payée';
            $reservation->save();

            // Générer billets 1 seule fois
            $voyageurs = $reservation->voyageurs()->get();
            if ($voyageurs->isEmpty()) {
                // Compat ancien
                for ($i = 0; $i < (int)$reservation->nombre_places; $i++) {
                    $exists = Billet::where('reservation_id', $reservation->id)->whereNull('voyageur_id')->exists();
                    if ($exists) {
                        break;
                    }
                    Billet::create([
                        'reservation_id' => $reservation->id,
                        'voyageur_id' => null,
                        'qr_code' => 'QR-' . strtoupper(Str::random(12)),
                    ]);
                    break;
                }
            } else {
                foreach ($voyageurs as $voyageur) {
                    $billetExistant = Billet::where('reservation_id', $reservation->id)
                        ->where('voyageur_id', $voyageur->id)
                        ->first();
                    if ($billetExistant) {
                        continue;
                    }
                    Billet::create([
                        'reservation_id' => $reservation->id,
                        'voyageur_id' => $voyageur->id,
                        'qr_code' => 'QR-' . strtoupper(Str::random(12)),
                    ]);
                }
            }

            // Marquer paiement succès
            $paiement->statut = 'payé';
            $paiement->save();
        });
    }
}
