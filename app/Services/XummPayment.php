<?php

namespace App\Services;

use Xrpl\XummSdkPhp\XummSdk;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\SlackNotifier;

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
                    'app' => null, // Null is correct for web-based apps
                ],
            ],
        ];

        // Include user_token in custom_meta for authenticated users
        if ($userToken) {
            // Validate userToken format (UUID-like)
            if (!is_string($userToken) || empty(trim($userToken)) || !preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $userToken)) {
                $logMessage = 'Invalid user_token format: ' . ($userToken ? substr($userToken, 0, 8) . '...' : 'empty');
                Log::warning($logMessage);
                SlackNotifier::warning($logMessage);
            } else {
                $payloadData['custom_meta'] = [
                    'user_token' => $userToken,
                ];
            }
        }

        // Check destination account for Deposit Authorization
        try {
            $accountInfo = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'X-API-Secret' => $this->apiSecret,
                'Content-Type' => 'application/json',
            ])->post('https://xumm.app/api/v1/platform/xrpl/account-info', [
                'account' => $destination,
            ]);

            if ($accountInfo->successful()) {
                $accountData = $accountInfo->json();
                $flags = $accountData['account_data']['Flags'] ?? 0;
                $depositAuth = ($flags & 0x00010000) !== 0; // lsfDepositAuth flag
                if ($depositAuth) {
                    $logMessage = 'Destination account has Deposit Authorization enabled: ' . $destination;
                    Log::warning($logMessage);
                    SlackNotifier::warning($logMessage);
                }
            } else {
                $logMessage = 'Failed to check destination account info: ' . $accountInfo->body();
                Log::warning($logMessage);
                SlackNotifier::warning($logMessage);
            }
        } catch (\Throwable $e) {
            $logMessage = 'Error checking destination account info: ' . $e->getMessage();
            Log::error($logMessage);
            SlackNotifier::error($logMessage);
        }

        try {
            $logMessage = 'Creating Xumm payload: ' . json_encode([
                    'amount' => $amount,
                    'destination' => $destination,
                    'userToken' => $userToken ? 'provided (' . substr($userToken, 0, 8) . '...)' : 'none',
                    'payloadData' => $payloadData,
                ], JSON_PRETTY_PRINT);
            Log::info($logMessage);
            SlackNotifier::info($logMessage);

            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'X-API-Secret' => $this->apiSecret,
                'Content-Type' => 'application/json',
            ])->post('https://xumm.app/api/v1/platform/payload', $payloadData);

            if ($response->failed()) {
                $errorMessage = 'Failed to create Xumm payload: ' . $response->body();
                Log::error($errorMessage);
                SlackNotifier::error($errorMessage);
                throw new \Exception($errorMessage);
            }

            $payloadResponse = $response->json();
            $logMessage = 'Xumm payload created: UUID=' . ($payloadResponse['uuid'] ?? 'unknown') . ', Pushed=' . ($payloadResponse['pushed'] ? 'true' : 'false');
            Log::info($logMessage, ['full_response' => $payloadResponse]);
            SlackNotifier::info($logMessage);

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
            $errorMessage = 'Error creating Xumm payload: ' . $e->getMessage();
            Log::error($errorMessage, [
                'amount' => $amount,
                'destination' => $destination,
                'userToken' => $userToken ? 'provided (' . substr($userToken, 0, 8) . '...)' : 'none',
            ]);
            SlackNotifier::error($errorMessage);
            throw $e;
        }
    }

    public function getPayload(string $uuid)
    {
        try {
            $payload = $this->sdk->getPayload($uuid);
            $logMessage = 'Payload retrieved: UUID=' . $uuid . ', TxID=' . ($payload->response->txid ?? 'none');
            Log::info($logMessage, ['payload' => (array) $payload]);
            SlackNotifier::info($logMessage);
            return $payload;
        } catch (\Throwable $e) {
            $errorMessage = 'Error retrieving payload: UUID=' . $uuid . ', Error=' . $e->getMessage();
            Log::error($errorMessage);
            SlackNotifier::error($errorMessage);
            throw $e;
        }
    }
}
