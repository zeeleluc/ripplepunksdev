<?php

namespace App\Livewire;

use App\Helpers\SlackNotifier;
use App\Services\XummPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Payment extends Component
{
    public float $amount;
    public ?string $destination;
    public ?string $memo = null;
    public bool $paymentReceived = false;
    public bool $showModal = false;
    public bool $isLoading = false;
    public ?string $qrCodeUrl = null;

    protected XummPayment $xummPayment;

    public function boot(XummPayment $xummPayment)
    {
        $this->xummPayment = $xummPayment;
        $this->destination = config('services.xaman.destination_wallet');
    }

    public function mount(float $amount = 1.0, ?string $memo = null)
    {
        $this->amount = $amount;
        $this->memo = $memo;
    }

    public function initiatePayment()
    {
        Log::info('[Payment] Initiating payment', [
            'destination' => $this->destination,
            'amount' => $this->amount
        ]);
        SlackNotifier::info('[Payment] Initiating payment');

        $this->showModal = true;
        $this->isLoading = true;
        $this->paymentReceived = false;
        $this->qrCodeUrl = null;
        $this->resetErrorBag();
        $this->dispatch('modal-opened');
    }

    public function retryPayment()
    {
        Log::info('[Payment] Retrying payment', [
            'destination' => $this->destination,
            'amount' => $this->amount
        ]);
        SlackNotifier::info('[Payment] Retrying payment');

        $this->isLoading = true;
        $this->qrCodeUrl = null;
        $this->resetErrorBag();
        Session::forget('xumm_payment_uuid');
        $this->createNewPayload();
    }

    public function processPayment()
    {
        if (!Auth::check() || !Auth::user()->xumm_token) {
            Log::error('[Payment] No authenticated user or xumm_token', [
                'user' => Auth::check() ? Auth::user()->id : 'Guest'
            ]);
            SlackNotifier::error('[Payment] No authenticated user or xumm_token');
            $this->addError('payment', 'Please log in with a valid Xaman token.');
            $this->isLoading = false;
            $this->showModal = true;
            $this->dispatch('modal-error', message: 'No Xaman token available.');
            return;
        }

        if (empty($this->destination)) {
            Log::error('[Payment] Destination wallet not configured');
            SlackNotifier::error('[Payment] Destination wallet not configured');
            $this->addError('destination', 'Destination wallet not configured.');
            $this->isLoading = false;
            $this->showModal = true;
            $this->dispatch('modal-error', message: 'Destination wallet not configured.');
            return;
        }

        // Check cache for payment status
        $uuid = Session::get('xumm_payment_uuid');
        if ($uuid && Cache::has("payment:$uuid")) {
            $paymentStatus = Cache::get("payment:$uuid");
            if ($paymentStatus['validated']) {
                $this->paymentReceived = true;
                $this->isLoading = false;
                $this->qrCodeUrl = null;
                Session::forget('xumm_payment_uuid');
                Cache::forget("payment:$uuid");
                Log::info('[Payment] Payment marked successful by cache', [
                    'uuid' => $uuid,
                    'txid' => $paymentStatus['txid']
                ]);
                SlackNotifier::info('[Payment] Payment marked successful by cache: UUID=' . $uuid);
                $this->dispatch('payment-success');
                return;
            }
        }

        if ($uuid) {
            try {
                $payload = $this->xummPayment->getPayload($uuid);
                Log::info('[Payment] Existing payload retrieved', [
                    'uuid' => $uuid,
                    'txid' => $payload->response->txid ?? 'none',
                    'signed' => $payload->payloadMeta->signed ?? false,
                    'expired' => $payload->payloadMeta->expired ?? false,
                    'qrPng' => $payload->refs->qrPng ?? 'none'
                ]);
                SlackNotifier::info('[Payment] Existing payload retrieved: UUID=' . $uuid);

                if ($payload->payloadMeta->expired || !$payload->refs->qrPng) {
                    Log::warning('[Payment] Payload expired or missing QR code, creating new one', ['uuid' => $uuid]);
                    SlackNotifier::warning('[Payment] Payload expired or missing QR code: UUID=' . $uuid);
                    Session::forget('xumm_payment_uuid');
                    $this->createNewPayload();
                    return;
                }

                if ($payload->response->txid && $payload->response->hex) {
                    $submissionResult = $this->xummPayment->submitTransaction($payload->response->hex);
                    if ($submissionResult['success']) {
                        $status = $this->xummPayment->checkTransactionStatus($payload->response->txid);
                        if ($status['success'] && $status['validated']) {
                            Cache::put("payment:$uuid", [
                                'txid' => $payload->response->txid,
                                'validated' => true
                            ], now()->addMinutes(10));
                            $this->paymentReceived = true;
                            $this->isLoading = false;
                            $this->qrCodeUrl = null;
                            Session::forget('xumm_payment_uuid');
                            Log::info('[Payment] Payment validated', [
                                'uuid' => $uuid,
                                'txid' => $payload->response->txid
                            ]);
                            SlackNotifier::info('[Payment] Payment validated: UUID=' . $uuid);
                            $this->dispatch('payment-success');
                        } else {
                            Log::warning('[Payment] Transaction not validated', [
                                'uuid' => $uuid,
                                'txid' => $payload->response->txid
                            ]);
                            SlackNotifier::warning('[Payment] Transaction not validated: UUID=' . $uuid);
                            $this->addError('payment', 'Transaction submitted but not yet validated.');
                            $this->isLoading = false;
                            $this->qrCodeUrl = $payload->refs->qrPng;
                        }
                    } elseif ($submissionResult['error'] === 'tefPAST_SEQ') {
                        Log::warning('[Payment] Transaction sequence expired', [
                            'uuid' => $uuid,
                            'error' => $submissionResult['message']
                        ]);
                        SlackNotifier::warning('[Payment] Transaction sequence expired: UUID=' . $uuid);
                        Session::forget('xumm_payment_uuid');
                        $this->createNewPayload();
                    } else {
                        Log::error('[Payment] Submission failed', [
                            'uuid' => $uuid,
                            'error' => $submissionResult['message']
                        ]);
                        SlackNotifier::error('[Payment] Submission failed: UUID=' . $uuid);
                        $this->addError('payment', 'Transaction submission failed: ' . $submissionResult['message']);
                        $this->isLoading = false;
                        $this->dispatch('modal-error', message: 'Transaction submission failed.');
                    }
                    return;
                }

                Log::info('[Payment] Payload not signed, waiting for user approval', ['uuid' => $uuid]);
                SlackNotifier::info('[Payment] Payload not signed: UUID=' . $uuid);
                $this->isLoading = false;
                $this->qrCodeUrl = $payload->refs->qrPng;
                return;
            } catch (\Throwable $e) {
                Log::warning('[Payment] Invalid or incomplete payload, creating new one', [
                    'uuid' => $uuid,
                    'error' => $e->getMessage()
                ]);
                SlackNotifier::warning('[Payment] Invalid or incomplete payload: UUID=' . $uuid);
                Session::forget('xumm_payment_uuid');
            }
        }

        $this->createNewPayload();
    }

    protected function createNewPayload()
    {
        try {
            $userToken = Auth::user()->xumm_token;
            Log::info('[Payment] Creating new payload', [
                'userToken' => substr($userToken, 0, 4) . '****',
                'amount' => $this->amount,
                'destination' => $this->destination
            ]);
            SlackNotifier::info('[Payment] Creating new payload');

            if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', trim($userToken))) {
                Log::error('[Payment] Invalid user_token format', ['userToken' => substr($userToken, 0, 4) . '****']);
                SlackNotifier::error('[Payment] Invalid user_token format');
                $this->addError('payment', 'Invalid Xaman token. Please re-authenticate.');
                $this->isLoading = false;
                $this->dispatch('modal-error', message: 'Invalid Xaman token.');
                return;
            }

            $currentLedger = $this->xummPayment->getCurrentLedgerIndex();
            $ledgerBuffer = 50;
            $payload = $this->xummPayment->createPaymentPayload(
                amount: $this->amount,
                destination: $this->destination,
                memo: $this->memo,
                userToken: $userToken,
                lastLedgerSequence: $currentLedger ? $currentLedger + $ledgerBuffer : null
            );

            Log::info('[Payment] Payload created', [
                'uuid' => $payload->uuid,
                'amount' => $this->amount,
                'destination' => $this->destination,
                'pushed' => $payload->pushed,
                'qrCodeUrl' => $payload->refs->qrPng ?? 'none'
            ]);
            SlackNotifier::info('[Payment] Payload created: UUID=' . $payload->uuid);

            Session::put('xumm_payment_uuid', $payload->uuid);
            $this->isLoading = false;
            $this->qrCodeUrl = $payload->refs->qrPng;

            if (!$payload->pushed) {
                Log::warning('[Payment] Push notification failed', [
                    'userToken' => substr($userToken, 0, 4) . '****'
                ]);
                SlackNotifier::warning('[Payment] Push notification failed');
                $this->addError('payment', 'Failed to send push notification. Please scan the QR code.');
                $this->dispatch('modal-error', message: 'Push notification failed.');
            }
        } catch (\Throwable $e) {
            Log::error('[Payment] Failed to create payload: ' . $e->getMessage(), [
                'amount' => $this->amount,
                'destination' => $this->destination
            ]);
            SlackNotifier::error('[Payment] Failed to create payload');
            $this->addError('payment', 'Failed to initiate payment: ' . $e->getMessage());
            $this->isLoading = false;
            $this->dispatch('modal-error', message: 'Failed to initiate payment.');
        }
    }

    public function checkPaymentStatus()
    {
        $uuid = Session::get('xumm_payment_uuid');
        if (!$uuid) {
            Log::warning('[Payment] No UUID in session');
            SlackNotifier::warning('[Payment] No UUID in session');
            $this->addError('payment', 'No payment in progress.');
            $this->isLoading = false;
            $this->dispatch('modal-error', message: 'No payment in progress.');
            return;
        }

        // Check cache for payment status
        if (Cache::has("payment:$uuid")) {
            $paymentStatus = Cache::get("payment:$uuid");
            if ($paymentStatus['validated']) {
                $this->paymentReceived = true;
                $this->isLoading = false;
                $this->qrCodeUrl = null;
                Session::forget('xumm_payment_uuid');
                Cache::forget("payment:$uuid");
                Log::info('[Payment] Payment marked successful by cache', [
                    'uuid' => $uuid,
                    'txid' => $paymentStatus['txid']
                ]);
                SlackNotifier::info('[Payment] Payment marked successful by cache: UUID=' . $uuid);
                $this->dispatch('payment-success');
                return;
            }
        }

        try {
            $payload = $this->xummPayment->getPayload($uuid);
            Log::info('[Payment] Status checked', [
                'uuid' => $uuid,
                'txid' => $payload->response->txid ?? 'none',
                'signed' => $payload->payloadMeta->signed ?? false,
                'expired' => $payload->payloadMeta->expired ?? false,
                'qrPng' => $payload->refs->qrPng ?? 'none'
            ]);
            SlackNotifier::info('[Payment] Status checked: UUID=' . $uuid);

            if ($payload->payloadMeta->expired) {
                Log::warning('[Payment] Payload expired, creating new one', ['uuid' => $uuid]);
                SlackNotifier::warning('[Payment] Payload expired: UUID=' . $uuid);
                Session::forget('xumm_payment_uuid');
                $this->createNewPayload();
                return;
            }

            if ($payload->response->txid && $payload->response->hex) {
                $submissionResult = $this->xummPayment->submitTransaction($payload->response->hex);
                if ($submissionResult['success']) {
                    $status = $this->xummPayment->checkTransactionStatus($payload->response->txid);
                    if ($status['success'] && $status['validated']) {
                        Cache::put("payment:$uuid", [
                            'txid' => $payload->response->txid,
                            'validated' => true
                        ], now()->addMinutes(10));
                        $this->paymentReceived = true;
                        $this->isLoading = false;
                        $this->qrCodeUrl = null;
                        Session::forget('xumm_payment_uuid');
                        Log::info('[Payment] Payment validated', [
                            'uuid' => $uuid,
                            'txid' => $payload->response->txid
                        ]);
                        SlackNotifier::info('[Payment] Payment validated: UUID=' . $uuid);
                        $this->dispatch('payment-success');
                    } else {
                        Log::warning('[Payment] Transaction not validated', [
                            'uuid' => $uuid,
                            'txid' => $payload->response->txid
                        ]);
                        SlackNotifier::warning('[Payment] Transaction not validated: UUID=' . $uuid);
                        $this->addError('payment', 'Transaction submitted but not yet validated.');
                        $this->isLoading = false;
                        $this->qrCodeUrl = $payload->refs->qrPng;
                    }
                } elseif ($submissionResult['error'] === 'tefPAST_SEQ') {
                    Log::warning('[Payment] Transaction sequence expired', [
                        'uuid' => $uuid,
                        'error' => $submissionResult['message']
                    ]);
                    SlackNotifier::warning('[Payment] Transaction sequence expired: UUID=' . $uuid);
                    Session::forget('xumm_payment_uuid');
                    $this->createNewPayload();
                } else {
                    Log::error('[Payment] Submission failed', [
                        'uuid' => $uuid,
                        'error' => $submissionResult['message']
                    ]);
                    SlackNotifier::error('[Payment] Submission failed: UUID=' . $uuid);
                    $this->addError('payment', 'Transaction submission failed: ' . $submissionResult['message']);
                    $this->isLoading = false;
                    $this->dispatch('modal-error', message: 'Transaction submission failed.');
                }
            } else {
                Log::info('[Payment] Payload not signed, waiting for user approval', ['uuid' => $uuid]);
                SlackNotifier::info('[Payment] Payload not signed: UUID=' . $uuid);
                $this->isLoading = false;
                $this->qrCodeUrl = $payload->refs->qrPng;
            }
        } catch (\Throwable $e) {
            Log::error('[Payment] Error checking status: ' . $e->getMessage(), ['uuid' => $uuid]);
            SlackNotifier::error('[Payment] Error checking status: UUID=' . $uuid);
            $this->addError('payment', 'Error checking payment status: ' . $e->getMessage());
            $this->isLoading = false;
            $this->dispatch('modal-error', message: 'Error checking payment status.');
            Session::forget('xumm_payment_uuid');
            $this->createNewPayload();
        }
    }

    public function toggleModal()
    {
        $this->showModal = false;
        $this->paymentReceived = false;
        $this->isLoading = false;
        $this->qrCodeUrl = null;
        Session::forget('xumm_payment_uuid');
        Log::info('[Payment] Modal closed');
        SlackNotifier::info('[Payment] Modal closed');
        $this->dispatch('modal-closed');
    }

    public function render()
    {
        return view('livewire.payment');
    }
}
