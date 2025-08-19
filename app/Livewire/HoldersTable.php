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
        return Holder::query()
            ->where('wallet', '!=', env('CTO_WALLET'))
            ->orderByDesc('holdings')
            ->paginate(25);
    }
}
