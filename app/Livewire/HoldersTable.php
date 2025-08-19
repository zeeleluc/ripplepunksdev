<?php

namespace App\Livewire;

use App\Models\Nft;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

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
        return Nft::query()
            ->select('nft_counts.owner', 'users.name', 'nft_counts.nft_count')
            ->fromSub(function ($query) {
                $query->select('owner', DB::raw('COUNT(*) as nft_count'))
                    ->from('nfts')
                    ->where('owner', '!=', env('CTO_WALLET'))
                    ->groupBy('owner');
            }, 'nft_counts')
            ->leftJoin('users', 'users.wallet', '=', 'nft_counts.owner')
            ->orderByDesc('nft_counts.nft_count')
            ->paginate(25);
    }
}
