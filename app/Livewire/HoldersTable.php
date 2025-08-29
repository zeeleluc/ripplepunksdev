<?php

namespace App\Livewire;

use App\Models\Holder;
use Livewire\Component;
use Livewire\WithPagination;

class HoldersTable extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.holders-table', [
            'holders' => $this->holders,
            'tiers' => config('badges.tiers'),
        ]);
    }

    public function getHoldersProperty()
    {
        $excludedWallets = [
            env('CTO_WALLET'),
            env('PROJECT_WALLET'),
            env('REWARDS_WALLET'),
        ];

        return Holder::query()
            ->whereNotIn('wallet', $excludedWallets)
            ->orderByDesc('holdings')
            ->paginate(25);
    }
}
