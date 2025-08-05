<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Giveaway;

class GiveawayForm extends Component
{
    public $submitted = false;
    public $type;
    public $wallet;

    protected $rules = [
        'wallet' => 'required|string',
    ];

    public function mount()
    {
        if (session()->has('giveaway_submitted_' . $this->type)) {
            $this->submitted = true;
        }
    }

    public function submit()
    {
        $this->validate();

        if (!$this->isValidXrpAddress($this->wallet)) {
            $this->addError('wallet', 'The wallet address is not a valid XRP address.');
            return;
        }

        // Check if this wallet already submitted for this type
        $alreadyExists = Giveaway::where('type', $this->type)
            ->where('wallet', $this->wallet)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('claimed_at')
                        ->whereNull('received_giveaway_at')
                        ->whereNull('declined_at');
                })->orWhere(function ($q) {
                    $q->whereNotNull('claimed_at')
                        ->whereNotNull('received_giveaway_at');
                });
            })
            ->exists();

        if ($alreadyExists) {
            $this->addError('wallet', 'This wallet has already claimed this giveaway.');
            return;
        }

        // Check session lock
        if (session()->has('giveaway_submitted_' . $this->type)) {
            $this->addError('wallet', 'You have already submitted.');
            return;
        }

        Giveaway::create([
            'type' => $this->type,
            'wallet' => $this->wallet,
            'claimed_at' => now(),
        ]);

        session()->put('giveaway_submitted_' . $this->type, true);
        $this->submitted = true;

        $this->dispatch('giveawayAdded', $this->type);
        $this->reset('wallet');
    }

    private function isValidXrpAddress($address): bool
    {
        return preg_match('/^r[1-9A-HJ-NP-Za-km-z]{24,34}$/', $address);
    }

    public function render()
    {
        return view('livewire.giveaway-form');
    }
}
