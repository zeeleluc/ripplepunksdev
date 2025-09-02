<?php

namespace App\Services;

use Xrpl\XummSdkPhp\XummSdk;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\SlackNotifier;
use WebSocket\Client as WebSocketClient;

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

        // Validate user_token format
        $usePush = false;
        if ($userToken) {
            if (!is_string($userToken) || empty(trim($userToken)) || !preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $userToken)) {
                $logMessage = '[createPaymentPayload] Invalid user_token format: ' . ($userToken ?: 'empty');
                Log::warning($logMessage);
                SlackNotifier::warning($logMessage);
            } else {
                $payloadData['custom_meta'] = [
                    'user_token' => $userToken,
                ];
                $usePush = true;
                $logMessage = '[createPaymentPayload] Using user_token: ' . $userToken;
                Log::info($logMessage);
                SlackNotifier::info($logMessage);
            }
        }

        // Check destination account for Deposit Authorization using XRPL WebSocket
        try {
            $client = new WebSocketClient('wss://xrpl.ws', ['timeout' => 10]);
            $request = [
                'id' => uniqid(),
                'command' => 'account_info',
                'account' => $destination,
                'ledger_index' => 'current',
            ];
            $client->send(json_encode($request));
            $response = json_decode($client->receive(), true);

            if (isset($response['result']['status']) && $response['result']['status'] === 'error') {
                $logMessage = '[createPaymentPayload] Failed to retrieve destination account info: ' . json_encode($response['result']);
                Log::warning($logMessage);
                SlackNotifier::warning($logMessage);
            } elseif (isset($response['result']['account_data']['Flags'])) {
                $flags = $response['result']['account_data']['Flags'];
                $depositAuth = ($flags & 0x00010000) !== 0; // lsfDepositAuth flag
                if ($depositAuth) {
                    $balance = $response['result']['account_data']['Balance'] ?? 0;
                    $balanceXrp = (int) $balance / 1000000;
                    $logMessage = '[createPaymentPayload] Destination account has Deposit Authorization enabled: ' . $destination . ', Balance: ' . $balanceXrp . ' XRP';
                    Log::warning($logMessage);
                    SlackNotifier::warning($logMessage);
                    if ($balanceXrp >= 10 && $amount <= 10) {
                        $logMessage = '[createPaymentPayload] Payment may qualify for Deposit Authorization exception (balance >= 10 XRP, amount <= 10 XRP)';
                        Log::info($logMessage);
                        SlackNotifier::info($logMessage);
                    }
                }
            } else {
                $logMessage = '[createPaymentPayload] Unexpected response format for destination account info: ' . json_encode($response);
                Log::warning($logMessage);
                SlackNotifier::warning($logMessage);
            }
            $client->close();
        } catch (\Throwable $e) {
            $logMessage = '[createPaymentPayload] Error checking destination account info: ' . $e->getMessage();
            Log::error($logMessage);
            SlackNotifier::error($logMessage);
        }

        try {
            $logMessage = '[createPaymentPayload] Creating Xumm payload: ' . json_encode([
                    'amount' => $amount,
                    'destination' => $destination,
                    'userToken' => $userToken ? $userToken : 'none',
                    'usePush' => $usePush,
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
                $errorMessage = '[createPaymentPayload] Failed to create Xumm payload: ' . $response->body();
                Log::error($errorMessage);
                SlackNotifier::error($errorMessage);
                throw new \Exception($errorMessage);
            }

            $payloadResponse = $response->json();
            $logMessage = '[createPaymentPayload] Xumm payload created: UUID=' . ($payloadResponse['uuid'] ?? 'unknown') . ', Pushed=' . ($payloadResponse['pushed'] ? 'true' : 'false');
            Log::info($logMessage, ['full_response' => $payloadResponse]);
            SlackNotifier::info($logMessage);

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
            $errorMessage = '[createPaymentPayload] Error creating Xumm payload: ' . $e->getMessage();
            Log::error($errorMessage, [
                'amount' => $amount,
                'destination' => $destination,
                'userToken' => $userToken ? $userToken : 'none',
            ]);
            SlackNotifier::error($errorMessage);
            throw $e;
        }
    }

    public function getPayload(string $uuid)
    {
        try {
            $payload = $this->sdk->getPayload($uuid);
            $logMessage = '[getPayload] Payload retrieved: UUID=' . $uuid . ', TxID=' . ($payload->response->txid ?? 'none');
            Log::info($logMessage, ['payload' => (array) $payload]);
            SlackNotifier::info($logMessage);
            return $payload;
        } catch (\Throwable $e) {
            $errorMessage = '[getPayload] Error retrieving payload: UUID=' . $uuid . ', Error=' . $e->getMessage();
            Log::error($errorMessage);
            SlackNotifier::error($errorMessage);
            throw $e;
        }
    }
}
