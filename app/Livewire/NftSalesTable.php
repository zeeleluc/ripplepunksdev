<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\NftSale;
use Carbon\Carbon;

class NftSalesTable extends Component
{
    use WithPagination;

    public $latestHashes = [];
    public $highlighted = [];

    public $totalXrp = 0;
    public $totalUsd = 0;
    public $marketplaceCounts = [];

    public function mount()
    {
        $this->latestHashes = NftSale::latestHashes(50);
        $this->refreshStats();
    }

    public function refreshStats()
    {
        $this->totalXrp = NftSale::totalXrpLast24h();
        $this->totalUsd = NftSale::totalUsdLast24h();
        $this->marketplaceCounts = NftSale::marketplaceCountsLast24h();
    }

    public function render()
    {
        $sales = NftSale::where('amount', '>', 1)
            ->where('accepted_at', '>=', now()->subDay())
            ->orderBy('accepted_at', 'desc')
            ->paginate(50);

        $now = Carbon::now();
        $currentHashes = $sales->pluck('accepted_tx_hash')->toArray();

        // Highlight new rows accepted in last 10 seconds
        $newOnes = [];
        foreach ($sales as $sale) {
            if ($sale->accepted_at->diffInSeconds($now) <= 10) {
                $newOnes[] = $sale->accepted_tx_hash;
            }
        }

        $this->highlighted = $newOnes;
        $this->latestHashes = $currentHashes;

        // Refresh 24h stats
        $this->refreshStats();

        return view('livewire.nft-sales-table', [
            'sales' => $sales,
            'highlighted' => $this->highlighted,
        ]);
    }
}
