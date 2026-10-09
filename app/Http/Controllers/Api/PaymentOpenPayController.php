<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Achat;
use App\Models\Paiement;
use App\Models\Reservation;
use App\Models\Trajet;
use App\Services\OpenPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentOpenPayController extends Controller
{
    public function __construct(
        protected OpenPayService $openPayService
    ) {
    }

    /**
     * Initier un paiement OpenPay (achat direct ou paiement réservation).
     * Authentifié via Sanctum.
     */
    public function initiate(Request $request)
    {
        $validated = $request->validate([
            // Pour paiement réservation
            'reservation_id' => 'nullable|integer|exists:reservations,id',
            // Pour achat direct
            'trajet_id' => 'nullable|integer|exists:trajets,id',
            'nombre_places' => 'nullable|integer|min:1',
            'sieges' => 'nullable|array',
            'sieges.*' => 'integer',
            'voyageurs' => 'nullable|array',
            'voyageurs.*.prenom' => 'nullable|string',
            'voyageurs.*.nom' => 'nullable|string',
            'voyageurs.*.email' => 'nullable|email',
            'voyageurs.*.telephone' => 'nullable|string',
            'voyageurs.*.siege' => 'nullable|integer',
            // OpenPay requis
            'payment_phone_number' => 'required|string',
            'provider' => 'required|in:MTN,AIRTEL',
            'customer_name' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Vous devez être connecté.'], 401);
        }

        // Recalculer montant côté serveur
        $montant = 0.0;
        $trajet = null;
        $reservation = null;

        if (!empty($validated['reservation_id'])) {
            // Paiement d'une réservation existante
            $reservation = Reservation::with(['trajet'])->findOrFail($validated['reservation_id']);

            if ((int)$reservation->user_id !== (int)$user->id) {
                return response()->json(['message' => 'Vous n’êtes pas autorisé à payer cette réservation.'], 403);
            }

            if (strtolower(trim((string)$reservation->statut)) === 'payée') {
                return response()->json(['message' => 'Cette réservation a déjà été payée.'], 409);
            }

            $trajet = $reservation->trajet;
            if (!$trajet) {
                return response()->json(['message' => 'Trajet introuvable.'], 404);
            }
            $montant = (float)$trajet->prix * (int)$reservation->nombre_places;
        } else {
            // Achat direct - recalcul minimal (on s'appuie sur logique AchatController si besoin ultérieur)
            // Ici on exige trajet_id et nombre_places pour initier
            if (empty($validated['trajet_id']) || empty($validated['nombre_places'])) {
                return response()->json(['message' => 'trajet_id et nombre_places requis pour achat direct.'], 422);
            }

            $trajet = Trajet::findOrFail($validated['trajet_id']);
            $montant = (float)$trajet->prix * (int)$validated['nombre_places'];
        }

        // Créer Paiement/Achat en pending (idempotence via transaction)
        $result = DB::transaction(function () use ($validated, $reservation, $trajet, $montant, $user) {
            // Créer/assurer achat en pending pour réservation si existe
            $achat = null;
            if ($reservation) {
                $achatExistant = Achat::where('reservation_id', $reservation->id)->lockForUpdate()->first();
                if (!$achatExistant) {
                    $referenceAchat = 'TMP-' . Str::uuid()->toString();
                    $achat = Achat::create([
                        'user_id' => $reservation->user_id,
                        'reservation_id' => $reservation->id,
                        'trajet_id' => $reservation->trajet_id,
                        'montant' => $montant,
                        'montant_base' => $montant,
                        'frais_tokende' => 0,
                        'commission_tokende' => 0,
                        'part_agence' => 0,
                        'description' => 'Paiement OpenPay en attente',
                        'reference' => $referenceAchat,
                        'statut' => 'en attente',
                        'remboursable' => true,
                    ]);
                    $achat->reference = 'ACH-' . $achat->created_at->format('Y') . '-' . str_pad($achat->id, 6, '0', STR_PAD_LEFT);
                    $achat->saveQuietly();
                } else {
                    $achat = $achatExistant;
                }
            }

            // Créer Paiement en pending
            $paiement = Paiement::create([
                'reservation_id' => $reservation?->id,
                'montant' => $montant,
                'mode_paiement' => 'mobile_money',
                'statut' => 'en attente',
                'provider' => $validated['provider'],
                'payment_phone_number' => $validated['payment_phone_number'],
            ]);

            return compact('paiement', 'achat', 'reservation');
        });

        // Préparer payload OpenPay
        $metadata = array_merge([
            'internal_reference' => $result['paiement']->id,
            'reservation_id' => $reservation?->id,
            'user_id' => $user->id,
            'achat_id' => $result['achat']?->id,
        ], $validated['metadata'] ?? []);

        $payload = [
            'amount' => (int) round($montant),
            'payment_phone_number' => $validated['payment_phone_number'],
            'provider' => $validated['provider'],
            'customer_external_id' => (string) $user->id,
            'customer' => [
                'name' => $validated['customer_name'] ?? $user->name ?? 'Client',
                'phone' => $validated['payment_phone_number'],
            ],
            'metadata' => $metadata,
        ];

        // Appel OpenPay
        $opRes = $this->openPayService->initiatePayment($payload);

        // Mettre à jour Paiement avec réponse OpenPay
        $paiement = $result['paiement'];
        $paiement->raw_response = json_encode($opRes, JSON_UNESCAPED_UNICODE);
        if ($opRes['success'] && is_array($opRes['data'])) {
            $data = $opRes['data'];
            $paiement->openpay_reference = $data['reference'] ?? null;
            $paiement->openpay_status = $data['status'] ?? 'pending';
            $paiement->statut = strtolower($data['status'] ?? 'en attente');
        } else {
            $paiement->openpay_status = 'failed';
            $paiement->statut = 'échoué';
        }
        $paiement->save();

        return response()->json([
            'message' => 'Paiement OpenPay initié',
            'openpay' => $opRes,
            'paiement' => $paiement->load(['reservation']),
        ], $opRes['success'] ? 200 : 422);
    }

    public function status(string $reference)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Vous devez être connecté.'], 401);
        }

        // Vérifier statut chez OpenPay
        $opRes = $this->openPayService->checkStatus($reference);

        // Synchroniser paiement si trouvé
        $paiement = Paiement::where('openpay_reference', $reference)->first();
        if ($paiement) {
            $paiement->status_checked_at = now();
            if ($opRes['success'] && is_array($opRes['data'])) {
                $paiement->openpay_status = $opRes['data']['status'] ?? $paiement->openpay_status;
                $paiement->statut = strtolower($opRes['data']['status'] ?? $paiement->statut);
            }
            $paiement->save();
        }

        return response()->json([
            'openpay' => $opRes,
            'paiement' => $paiement?->load(['reservation']),
        ]);
    }
}
