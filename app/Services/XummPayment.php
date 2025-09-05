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
    protected const MAX_RETRIES = 2;
    protected const VALIDATION_RETRIES = 3;

    public function __construct()
    {
        $this->apiKey = config('services.xaman.api_key') ?: throw new \Exception('XUMM_API_KEY not configured');
        $this->apiSecret = config('services.xaman.api_secret') ?: throw new \Exception('XUMM_API_SECRET not configured');
        $this->webhookUrl = config('services.xaman.webhook_url') ?: throw new \Exception('XUMM_WEBHOOK_URL not configured');
        $this->sdk = new XummSdk($this->apiKey, $this->apiSecret);
    }

    public function createPaymentPayload(float $amount, string $destination, ?string $memo = null, ?string $userToken = null, ?int $lastLedgerSequence = null): object
    {
        $destValidation = $this->validateAccount($destination);
        if (!$destValidation['success']) {
            $error = "[createPaymentPayload] Invalid destination: {$destination}, Error: {$destValidation['message']}";
            Log::error($error);
            SlackNotifier::error($error);
            throw new \Exception($error);
        }

        $transactionBody = [
            'TransactionType' => 'Payment',
            'Destination' => $destination,
            'Amount' => (string) ($amount * 1000000),
        ];

        if ($memo) {
            $transactionBody['Memos'] = [
                ['Memo' => ['MemoData' => bin2hex($memo)]],
            ];
        }

        if ($lastLedgerSequence) {
            $transactionBody['LastLedgerSequence'] = $lastLedgerSequence;
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

        $usePush = false;
        if ($userToken && is_string($userToken) && preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', trim($userToken))) {
            $payloadData['custom_meta'] = ['user_token' => $userToken];
            $usePush = true;
            Log::info("[createPaymentPayload] Using user_token: " . substr($userToken, 0, 4) . '****');
            SlackNotifier::info("[createPaymentPayload] Using user_token");
        } elseif ($userToken) {
            Log::warning("[createPaymentPayload] Invalid user_token format: " . ($userToken ?: 'empty'));
            SlackNotifier::warning("[createPaymentPayload] Invalid user_token format");
        }

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
            $client->close();

            if (isset($response['result']['status']) && $response['result']['status'] === 'error') {
                Log::warning("[createPaymentPayload] Failed to retrieve account info: " . json_encode($response['result']));
                SlackNotifier::warning("[createPaymentPayload] Failed to retrieve account info");
            } elseif (isset($response['result']['account_data']['Flags'])) {
                $flags = $response['result']['account_data']['Flags'];
                if ($flags & 0x00010000) {
                    $balance = $response['result']['account_data']['Balance'] ?? 0;
                    $balanceXrp = (int) $balance / 1000000;
                    Log::warning("[createPaymentPayload] Deposit Authorization enabled for {$destination}, Balance: {$balanceXrp} XRP");
                    SlackNotifier::warning("[createPaymentPayload] Deposit Authorization enabled");
                    if ($balanceXrp >= 10 && $amount <= 10) {
                        Log::info("[createPaymentPayload] Payment qualifies for Deposit Authorization exception");
                        SlackNotifier::info("[createPaymentPayload] Payment qualifies for Deposit Authorization exception");
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error("[createPaymentPayload] Error checking account info: {$e->getMessage()}");
            SlackNotifier::error("[createPaymentPayload] Error checking account info");
        }

        for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
            try {
                Log::info("[createPaymentPayload] Attempt {$attempt} - Payload data", [
                    'amount' => $amount,
                    'destination' => $destination,
                    'userToken' => $userToken ? substr($userToken, 0, 4) . '****' : 'none',
                    'usePush' => $usePush,
                    'lastLedgerSequence' => $lastLedgerSequence ?? 'none',
                    'payloadData' => $payloadData
                ]);
                SlackNotifier::info("[createPaymentPayload] Attempt {$attempt} - Creating payload");

                $response = Http::withHeaders([
                    'X-API-Key' => $this->apiKey,
                    'X-API-Secret' => $this->apiSecret,
                    'Content-Type' => 'application/json',
                ])->post('https://xumm.app/api/v1/platform/payload', $payloadData);

                if ($response->failed()) {
                    $error = "[createPaymentPayload] Attempt {$attempt} - Failed: " . $response->body();
                    Log::error($error);
                    SlackNotifier::error($error);
                    throw new \Exception($error);
                }

                $payloadResponse = $response->json();
                Log::info("[createPaymentPayload] Attempt {$attempt} - Raw response", ['response' => $payloadResponse]);
                SlackNotifier::info("[createPaymentPayload] Attempt {$attempt} - Response received");

                if (!isset($payloadResponse['refs']['qr_png'])) {
                    $error = "[createPaymentPayload] Attempt {$attempt} - Missing QR code URL";
                    Log::error($error, ['response' => $payloadResponse]);
                    SlackNotifier::error($error);
                    if ($attempt < self::MAX_RETRIES) {
                        sleep(1);
                        continue;
                    }
                    throw new \Exception('Failed to generate QR code after ' . self::MAX_RETRIES . ' attempts');
                }

                Log::info("[createPaymentPayload] Payload created: UUID={$payloadResponse['uuid']}, Pushed=" . ($payloadResponse['pushed'] ? 'true' : 'false'), ['response' => $payloadResponse]);
                SlackNotifier::info("[createPaymentPayload] Payload created: UUID={$payloadResponse['uuid']}");

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
                    'txjson' => $transactionBody,
                ];
            } catch (\Throwable $e) {
                Log::error("[createPaymentPayload] Attempt {$attempt} - Error: {$e->getMessage()}", [
                    'amount' => $amount,
                    'destination' => $destination,
                    'userToken' => $userToken ? substr($userToken, 0, 4) . '****' : 'none',
                ]);
                SlackNotifier::error("[createPaymentPayload] Attempt {$attempt} - Error");
                if ($attempt < self::MAX_RETRIES) {
                    sleep(1);
                    continue;
                }
                throw $e;
            }
        }
    }

    public function getPayload(string $uuid): object
    {
        try {
            $payload = $this->sdk->getPayload($uuid);
            Log::info("[getPayload] Retrieved: UUID={$uuid}, TxID=" . ($payload->response->txid ?? 'none'), [
                'signed' => $payload->payloadMeta->signed ?? false,
                'qrPng' => $payload->refs->qrPng ?? 'none'
            ]);
            SlackNotifier::info("[getPayload] Retrieved: UUID={$uuid}");
            return $payload;
        } catch (\Throwable $e) {
            Log::error("[getPayload] Error: UUID={$uuid}, {$e->getMessage()}");
            SlackNotifier::error("[getPayload] Error: UUID={$uuid}");
            throw $e;
        }
    }

    public function submitTransaction(string $signedTxBlob, string $xrplNode = 'wss://xrplcluster.com'): array
    {
        for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
            try {
                Log::info("[submitTransaction] Attempt {$attempt} - Submitting transaction");
                SlackNotifier::info("[submitTransaction] Attempt {$attempt} - Submitting");

                $client = new WebSocketClient($xrplNode, ['timeout' => 10]);
                $request = [
                    'id' => uniqid(),
                    'command' => 'submit',
                    'tx_blob' => $signedTxBlob,
                ];
                $client->send(json_encode($request));
                $response = json_decode($client->receive(), true);
                $client->close();

                if (isset($response['result']['engine_result']) && $response['result']['engine_result'] === 'tesSUCCESS') {
                    Log::info("[submitTransaction] Success: TxID=" . ($response['result']['tx_json']['hash'] ?? 'unknown'), ['response' => $response]);
                    SlackNotifier::info("[submitTransaction] Success");
                    return [
                        'success' => true,
                        'txid' => $response['result']['tx_json']['hash'] ?? null,
                        'engine_result' => $response['result']['engine_result'],
                        'engine_result_message' => $response['result']['engine_result_message'] ?? '',
                    ];
                }

                Log::error("[submitTransaction] Attempt {$attempt} - Failed: " . json_encode($response));
                SlackNotifier::error("[submitTransaction] Attempt {$attempt} - Failed");
                if ($attempt < self::MAX_RETRIES) {
                    sleep(1);
                    continue;
                }
                return [
                    'success' => false,
                    'error' => $response['result']['engine_result'] ?? 'unknown_error',
                    'message' => $response['result']['engine_result_message'] ?? json_encode($response),
                ];
            } catch (\Throwable $e) {
                Log::error("[submitTransaction] Attempt {$attempt} - Error: {$e->getMessage()}");
                SlackNotifier::error("[submitTransaction] Attempt {$attempt} - Error");
                if ($attempt < self::MAX_RETRIES) {
                    sleep(1);
                    continue;
                }
                return [
                    'success' => false,
                    'error' => 'submission_error',
                    'message' => $e->getMessage(),
                ];
            }
        }
    }

    public function checkTransactionStatus(string $txid, string $xrplNode = 'wss://xrplcluster.com'): array
    {
        for ($attempt = 1; $attempt <= self::VALIDATION_RETRIES; $attempt++) {
            try {
                Log::info("[checkTransactionStatus] Attempt {$attempt} - Checking TxID={$txid}");
                SlackNotifier::info("[checkTransactionStatus] Attempt {$attempt} - Checking");

                $client = new WebSocketClient($xrplNode, ['timeout' => 10]);
                $request = [
                    'id' => uniqid(),
                    'command' => 'tx',
                    'transaction' => $txid,
                    'binary' => false,
                ];
                $client->send(json_encode($request));
                $response = json_decode($client->receive(), true);
                $client->close();

                if (isset($response['result']['validated']) && $response['result']['validated']) {
                    Log::info("[checkTransactionStatus] Validated: TxID={$txid}", ['response' => $response]);
                    SlackNotifier::info("[checkTransactionStatus] Validated");
                    return [
                        'success' => true,
                        'validated' => true,
                        'meta' => $response['result']['meta'] ?? [],
                    ];
                }

                Log::warning("[checkTransactionStatus] Not validated: TxID={$txid}, Attempt={$attempt}", ['response' => $response]);
                SlackNotifier::warning("[checkTransactionStatus] Not validated: Attempt {$attempt}");
                if ($attempt < self::VALIDATION_RETRIES) {
                    sleep(2); // Wait 2 seconds before retrying
                    continue;
                }
                return [
                    'success' => false,
                    'validated' => false,
                    'message' => $response['result']['status'] ?? 'Transaction not found',
                ];
            } catch (\Throwable $e) {
                Log::error("[checkTransactionStatus] Attempt {$attempt} - Error: {$e->getMessage()}");
                SlackNotifier::error("[checkTransactionStatus] Attempt {$attempt} - Error");
                if ($attempt < self::VALIDATION_RETRIES) {
                    sleep(2);
                    continue;
                }
                return [
                    'success' => false,
                    'validated' => false,
                    'message' => $e->getMessage(),
                ];
            }
        }
    }

    public function validateAccount(string $account, string $xrplNode = 'wss://xrplcluster.com'): array
    {
        try {
            $client = new WebSocketClient($xrplNode, ['timeout' => 10]);
            $request = [
                'id' => uniqid(),
                'command' => 'account_info',
                'account' => $account,
                'ledger_index' => 'current',
            ];
            $client->send(json_encode($request));
            $response = json_decode($client->receive(), true);
            $client->close();

            if (isset($response['result']['account_data'])) {
                $balance = $response['result']['account_data']['Balance'] ?? 0;
                $balanceXrp = (int) $balance / 1000000;
                Log::info("[validateAccount] Validated: {$account}, Balance: {$balanceXrp} XRP");
                SlackNotifier::info("[validateAccount] Validated");
                return [
                    'success' => true,
                    'balance' => $balanceXrp,
                    'flags' => $response['result']['account_data']['Flags'] ?? 0,
                ];
            }

            Log::warning("[validateAccount] Not found: {$account}");
            SlackNotifier::warning("[validateAccount] Not found");
            return [
                'success' => false,
                'message' => $response['result']['error_message'] ?? 'Account not found',
            ];
        } catch (\Throwable $e) {
            Log::error("[validateAccount] Error: {$account}, {$e->getMessage()}");
            SlackNotifier::error("[validateAccount] Error");
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function getCurrentLedgerIndex(string $xrplNode = 'wss://xrplcluster.com'): ?int
    {
        try {
            $client = new WebSocketClient($xrplNode, ['timeout' => 10]);
            $request = [
                'id' => uniqid(),
                'command' => 'ledger_current',
            ];
            $client->send(json_encode($request));
            $response = json_decode($client->receive(), true);
            $client->close();

            if (isset($response['result']['ledger_current_index'])) {
                Log::info("[getCurrentLedgerIndex] Ledger index: {$response['result']['ledger_current_index']}");
                SlackNotifier::info("[getCurrentLedgerIndex] Ledger index retrieved");
                return $response['result']['ledger_current_index'];
            }

            Log::warning("[getCurrentLedgerIndex] No ledger index found");
            SlackNotifier::warning("[getCurrentLedgerIndex] No ledger index");
            return null;
        } catch (\Throwable $e) {
            Log::error("[getCurrentLedgerIndex] Error: {$e->getMessage()}");
            SlackNotifier::error("[getCurrentLedgerIndex] Error");
            return null;
        }
    }
}
