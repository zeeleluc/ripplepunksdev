<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Nft;
use Illuminate\Database\QueryException;

class DuplicateNfts extends Component
{
    public function render()
    {
        try {
            $duplicateChecksums = Nft::query()
                ->select('trait_checksum')
                ->groupBy('trait_checksum')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('trait_checksum');

            if ($duplicateChecksums->isEmpty()) {
                $nftsGrouped = collect();
            } else {
                $nftsGrouped = Nft::whereIn('trait_checksum', $duplicateChecksums)
                    ->get()
                    ->groupBy('trait_checksum');
            }

        } catch (QueryException $e) {
            \Log::error('DuplicateNfts query failed: ' . $e->getMessage());
            $nftsGrouped = collect();
        }

        return view('livewire.duplicate-nfts', compact('nftsGrouped'));
    }
}
