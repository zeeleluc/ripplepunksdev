<?php

namespace App\Services;

use Xrpl\XummSdkPhp\XummSdk;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XummPayment
{
    protected XummSdk $sdk;
    protected string $apiKey;
    protected string $apiSecret;
    protected string $webhookUrl;

    public function __construct()
    {
        $this->sdk = new XummSdk(
            config('services.xaman.api_key'),
            config('services.xaman.api_secret')
        );
        $this->apiKey = config('services.xaman.api_key');
        $this->apiSecret = config('services.xaman.api_secret');
        $this->webhookUrl = config('services.xaman.webhook_url');
    }

    public function createPaymentPayload(float $amount, string $destination, ?string $memo = null, ?string $userToken = null)
    {
        $transactionBody = [
            'TransactionType' => 'Payment',
            'Destination' => $destination,
            'Amount' => (string) ($amount * 1000000), // Convert XRP to drops
        ];

        if ($memo) {
            $transactionBody['Memos'] = [
                [
                    'Memo' => [
                        'MemoData' => bin2hex($memo),
                    ],
                ],
            ];
        }

        $payloadData = [
            'txjson' => $transactionBody,
            'options' => [
                'submit' => false,
                'return_url' => [
                    'web' => $this->webhookUrl,
                    'app' => null,
                ],
            ],
        ];

        // Include user_token in custom_meta for authenticated users
        if ($userToken) {
            $payloadData['custom_meta'] = [
                'user_token' => $userToken,
            ];
        }

        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'X-API-Secret' => $this->apiSecret,
                'Content-Type' => 'application/json',
            ])->post('https://xumm.app/api/v1/platform/payload', $payloadData);

            if ($response->failed()) {
                throw new \Exception('Failed to create payload: ' . $response->body());
            }

            $payloadResponse = $response->json();
            Log::info('Raw Xumm API payload response', ['response' => $payloadResponse]);

            // Return a simplified payload object compatible with existing code
            return (object) [
                'uuid' => $payloadResponse['uuid'],
                'next' => (object) [
                    'always' => $payloadResponse['next']['always'],
                    'noPushMessageReceived' => $payloadResponse['next']['no_push_msg_received'] ?? null,
                ],
                'refs' => (object) [
                    'qrPng' => $payloadResponse['refs']['qr_png'],
                    'qrMatrix' => $payloadResponse['refs']['qr_matrix'],
                    'websocketStatus' => $payloadResponse['refs']['websocket_status'],
                    'qrUriQualityOptions' => $payloadResponse['refs']['qr_uri_quality_opts'] ?? ['m', 'q', 'h'],
                ],
                'pushed' => $payloadResponse['pushed'] ?? false,
            ];
        } catch (\Throwable $e) {
            Log::error('Error creating Xumm payload via API', [
                'error' => $e->getMessage(),
                'amount' => $amount,
                'destination' => $destination,
                'userToken' => $userToken ? 'provided' : 'none',
            ]);
            throw $e;
        }
    }

    public function getPayload(string $uuid)
    {
        return $this->sdk->getPayload($uuid);
    }
}
