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

    public function mount()
    {
        // Initialize latest hashes to avoid flashing old rows
        $this->latestHashes = NftSale::orderBy('accepted_at', 'desc')
            ->take(50)
            ->pluck('accepted_tx_hash')
            ->toArray();
    }

    public function render()
    {
        $sales = NftSale::orderBy('accepted_at', 'desc')
            ->paginate(250);

        $now = Carbon::now();
        $currentHashes = $sales->pluck('accepted_tx_hash')->toArray();

        // Highlight new rows accepted in last 10 seconds
        $newOnes = [];
        foreach ($sales as $sale) {
            $acceptedAt = Carbon::parse($sale->accepted_at);
            if ($acceptedAt->diffInSeconds($now) <= 1000) {
                $newOnes[] = $sale->accepted_tx_hash;
            }
        }

        $this->highlighted = $newOnes;
        $this->latestHashes = $currentHashes;

        return view('livewire.nft-sales-table', [
            'sales' => $sales,
            'highlighted' => $this->highlighted,
        ]);
    }
}
