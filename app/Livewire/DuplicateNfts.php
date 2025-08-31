<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Nft;
use Illuminate\Database\QueryException;

class DuplicateNfts extends Component
{
    public function render()
    {
        $columns = ['color', 'skin', 'type', 'total_accessories', 'owner'];

        try {
            // 1️⃣ Fetch all duplicate trait checksums
            $checksums = Nft::query()
//                ->whereIn('owner', [
//                    env('PROJECT_WALLET'),
//                    env('REWARDS_WALLET'),
//                    'rwbaCNkedtHacK8Qer3qdVZaH2fjSvBrJZ'
//                ])
                ->groupBy('trait_checksum')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('trait_checksum');

            if ($checksums->isEmpty()) {
                $group = ['traits' => [], 'nfts' => collect([])];
                return view('livewire.duplicate-nfts', compact('group'));
            }

            // 2️⃣ Fetch all NFTs for these duplicate checksums
            $nftsGrouped = Nft::whereIn('trait_checksum', $checksums)
                ->select('id', 'nft_id', 'type', 'color', 'skin', 'owner', 'trait_checksum', 'metadata')
                ->get()
                ->groupBy('trait_checksum');

            // 3️⃣ Flatten NFTs into a single collection
            $nfts = $nftsGrouped->flatMap(fn($group) => $group);

            // 4️⃣ Take the traits from the first NFT as a sample
            $sample = $nfts->first();
            $group = [
                'traits' => $sample ? $sample->only($columns) : [],
                'nfts' => $nfts,
            ];

        } catch (QueryException $e) {
            \Log::error('DuplicateNfts query failed: ' . $e->getMessage());
            $group = ['traits' => [], 'nfts' => collect([])];
        }

        return view('livewire.duplicate-nfts', compact('group'));
    }
}
