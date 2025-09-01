<?php

namespace App\Livewire;

use App\Services\XummPayment;
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

    public function toggleModal()
    {
        if ($this->showModal) {
            $this->showModal = false;
            return;
        }

        if (empty($this->destination)) {
            Log::error('Destination wallet is not configured in services.xaman.destination_wallet');
            $this->addError('destination', 'Payment cannot be initiated: Destination wallet is not configured.');
            return;
        }

        $this->showModal = true;
        $this->initiatePayment();
    }

    public function initiatePayment()
    {
        // Check if a valid payment UUID exists in the session
        $uuid = Session::get('xumm_payment_uuid');

        if ($uuid) {
            try {
                $payload = $this->xummPayment->getPayload($uuid);
                Log::info('Existing payment payload retrieved', ['uuid' => $uuid, 'payload' => (array) $payload]);

                if ($payload->response->txid) {
                    $this->paymentReceived = true;
                    $this->qr = null;
                    $this->url = null;
                    Session::forget('xumm_payment_uuid');
                    Log::info('Payment already completed for UUID: ' . $uuid, ['txid' => $payload->response->txid]);
                    return;
                }

                $this->qr = $payload->refs->qrPng;
                $this->url = $payload->next->always;
                return;
            } catch (\Throwable $e) {
                Log::warning('Old payment UUID invalid, generating new one: ' . $e->getMessage(), ['uuid' => $uuid]);
                Session::forget('xumm_payment_uuid');
            }
        }

        // Create new payment payload
        try {
            $payload = $this->xummPayment->createPaymentPayload(
                amount: $this->amount,
                destination: $this->destination,
                memo: $this->memo
            );
            Log::info('New payment payload created', [
                'uuid' => $payload->uuid,
                'amount' => $this->amount,
                'destination' => $this->destination,
                'memo' => $this->memo,
                'payload' => (array) $payload
            ]);

            // Store UUID in session
            Session::put('xumm_payment_uuid', $payload->uuid);

            $this->qr = $payload->refs->qrPng;
            $this->url = $payload->next->always;
        } catch (\Throwable $e) {
            Log::error('Failed to create payment payload: ' . $e->getMessage(), [
                'amount' => $this->amount,
                'destination' => $this->destination
            ]);
            $this->addError('payment', 'Failed to initiate payment. Please try again later.');
            $this->showModal = false;
        }
    }

    public function checkPaymentStatus()
    {
        $uuid = Session::get('xumm_payment_uuid');
        if (!$uuid) {
            Log::warning('No payment UUID found in session for status check');
            return;
        }

        try {
            $payload = $this->xummPayment->getPayload($uuid);
            Log::info('Payment status checked', ['uuid' => $uuid, 'payload' => (array) $payload]);

            if ($payload->response->txid) {
                $this->paymentReceived = true;
                $this->qr = null;
                $this->url = null;
                $this->showModal = true; // Keep modal open to show success message
                Session::forget('xumm_payment_uuid');
                Log::info('Payment completed for UUID: ' . $uuid, ['txid' => $payload->response->txid]);
                $this->dispatch('payment-success');
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
