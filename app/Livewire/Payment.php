<?php

namespace App\Livewire;

use App\Services\XummPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Payment extends Component
{
    public float $amount;
    public ?string $destination;
    public ?string $memo = null;
    public ?string $qr = null;
    public ?string $url = null;
    public bool $paymentReceived = false;
    public bool $showModal = false;
    public bool $isPushed = false;

    protected XummPayment $xummPayment;

    public function boot(XummPayment $xummPayment)
    {
        $this->xummPayment = $xummPayment;
        $this->destination = config('services.xaman.destination_wallet');
        Log::info('Payment component initialized', [
            'destination' => $this->destination,
            'user' => Auth::check() ? Auth::user()->toArray() : 'Guest',
            'config' => config('services.xaman')
        ]);
    }

    public function mount(float $amount = 1.0, ?string $memo = null)
    {
        $this->amount = $amount;
        $this->memo = $memo;
    }

    public function toggleModal()
    {
        if ($this->showModal) {
            $this->showModal = false;
            $this->reset(['qr', 'url', 'paymentReceived', 'isPushed']);
            return;
        }

        if (empty($this->destination)) {
            Log::error('Destination wallet is not configured in services.xaman.destination_wallet', [
                'config' => config('services.xaman')
            ]);
            $this->addError('destination', 'Payment cannot be initiated: Destination wallet is not configured.');
            return;
        }

        $this->showModal = true;
        $this->initiatePayment();
    }

    public function initiatePayment()
    {
        $uuid = Session::get('xumm_payment_uuid');

        if ($uuid) {
            try {
                $payload = $this->xummPayment->getPayload($uuid);
                Log::info('Existing payment payload retrieved', ['uuid' => $uuid, 'payload' => (array) $payload]);

                if ($payload->response->txid) {
                    $this->paymentReceived = true;
                    $this->qr = null;
                    $this->url = null;
                    $this->isPushed = false;
                    Session::forget('xumm_payment_uuid');
                    Log::info('Payment already completed for UUID: ' . $uuid, ['txid' => $payload->response->txid]);
                    $this->dispatch('payment-success');
                    $this->dispatch('refresh-component');
                    return;
                }

                $this->qr = $payload->refs->qrPng;
                $this->url = $payload->next->always;
                $this->isPushed = $payload->pushed ?? false;
                return;
            } catch (\Throwable $e) {
                Log::warning('Old payment UUID invalid, generating new one: ' . $e->getMessage(), ['uuid' => $uuid]);
                Session::forget('xumm_payment_uuid');
            }
        }

        try {
            $userToken = Auth::check() ? Auth::user()->xumm_token : null;
            $payload = $this->xummPayment->createPaymentPayload(
                amount: $this->amount,
                destination: $this->destination,
                memo: $this->memo,
                userToken: $userToken
            );
            Log::info('New payment payload created', [
                'uuid' => $payload->uuid,
                'amount' => $this->amount,
                'destination' => $this->destination,
                'memo' => $this->memo,
                'userToken' => $userToken ? 'provided' : 'none',
                'pushed' => $payload->pushed,
                'payload' => (array) $payload
            ]);

            Session::put('xumm_payment_uuid', $payload->uuid);

            $this->qr = $userToken ? null : $payload->refs->qrPng; // No QR if pushed
            $this->url = $userToken ? null : $payload->next->always; // No URL if pushed
            $this->isPushed = $payload->pushed ?? false;
        } catch (\Throwable $e) {
            Log::error('Failed to create payment payload: ' . $e->getMessage(), [
                'amount' => $this->amount,
                'destination' => $this->destination,
                'user' => Auth::check() ? Auth::user()->toArray() : 'Guest'
            ]);
            $this->addError('payment', 'Failed to initiate payment: ' . $e->getMessage());
            $this->showModal = false;
        }
    }

    public function checkPaymentStatus()
    {
        $uuid = Session::get('xumm_payment_uuid');
        if (!$uuid) {
            Log::warning('No payment UUID found in session for status check', ['session' => Session::all()]);
            return;
        }

        try {
            $payload = $this->xummPayment->getPayload($uuid);
            Log::info('Payment status checked', [
                'uuid' => $uuid,
                'txid' => $payload->response->txid ?? 'none',
                'account' => $payload->response->account ?? 'none',
                'payload' => (array) $payload
            ]);

            if ($payload->response->txid) {
                $this->paymentReceived = true;
                $this->qr = null;
                $this->url = null;
                $this->isPushed = false;
                $this->showModal = true;
                Session::forget('xumm_payment_uuid');
                Log::info('Payment completed for UUID: ' . $uuid, ['txid' => $payload->response->txid]);
                $this->dispatch('payment-success');
                $this->dispatch('refresh-component');
            }
        } catch (\Throwable $e) {
            Log::error('Error checking payment status: ' . $e->getMessage(), ['uuid' => $uuid]);
        }
    }

    public function render()
    {
        return view('livewire.payment');
    }
}
