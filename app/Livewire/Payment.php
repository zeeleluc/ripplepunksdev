<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\XummPayment;

class Payment extends Component
{
    public $uuid;
    public $status;
    public $amount;
    public $memo;
    public $startTime;

    protected $xummPayment;

    public function mount($amount, $memo)
    {
        $this->amount = $amount;
        $this->memo = $memo;
        $this->xummPayment = app(XummPayment::class);
        $this->status = 'pending';
        $this->startTime = now()->timestamp;
    }

    public function checkPaymentStatus()
    {
        if ($this->uuid && now()->timestamp - $this->startTime < 120) { // Stop after 2 minutes
            $payload = $this->xummPayment->getPayload($this->uuid);
            if ($payload->response->txid) {
                $this->status = 'completed';
                $this->emit('paymentCompleted');
            } elseif ($payload->meta->expired) {
                $this->status = 'expired';
                $this->emit('paymentExpired');
            }
        } else {
            $this->status = 'timeout';
            $this->emit('paymentTimeout');
        }
    }

    public function render()
    {
        if (!$this->uuid) {
            $payload = $this->xummPayment->createPaymentPayload(
                $this->amount,
                config('services.xaman.destination_wallet'),
                $this->memo,
                auth()->user()->xumm_token ?? null
            );
            $this->uuid = $payload->uuid;
            return view('livewire.payment', [
                'qr' => $payload->refs->qrPng,
                'url' => $payload->next->always,
                'pushed' => $payload->pushed,
            ]);
        }
        return view('livewire.payment');
    }
}
