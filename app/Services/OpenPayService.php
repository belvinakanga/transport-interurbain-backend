<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;

class OpenPayService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = (string) config('services.openpay.api_key', '');
        $this->baseUrl = rtrim((string) config('services.openpay.base_url', 'https://api.openpay-cg.com/v1'), '/');
    }

    /**
     * Initier un paiement via OpenPay.
     */
    public function initiatePayment(array $payload): array
    {
        $response = Http::withHeaders([
            'XO-API-KEY' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout(30)->post($this->baseUrl . '/transaction/payment', $payload);

        return $this->handleResponse($response);
    }

    /**
     * Vérifier le statut d'une transaction.
     */
    public function checkStatus(string $reference): array
    {
        $response = Http::withHeaders([
            'XO-API-KEY' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout(30)->get($this->baseUrl . '/transaction/status/' . urlencode($reference));

        return $this->handleResponse($response);
    }

    private function handleResponse(Response $response): array
    {
        $status = $response->status();
        $body = $response->json();

        if ($status >= 200 && $status < 300) {
            return [
                'success' => true,
                'status' => $status,
                'data' => $body,
            ];
        }

        // Ne jamais logger la clé API. Ne logger que des infos non sensibles.
        $errorMessage = is_array($body) ? ($body['error'] ?? $body['message'] ?? 'Erreur OpenPay') : (string) $body;
        Log::warning('OpenPay API error', [
            'status' => $status,
            'error' => $errorMessage,
        ]);

        return [
            'success' => false,
            'status' => $status,
            'error' => $errorMessage,
            'data' => $body,
        ];
    }
}
