<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Nft;
use Illuminate\Pagination\LengthAwarePaginator;

class DuplicateNfts extends Component
{
    use WithPagination;

    public $perPage = 3;       // Duplicate groups per page
    public $nftsPerGroup = 6;  // Limit NFTs displayed per group

    public function render()
    {
        $columns = ['color', 'skin', 'type', 'total_accessories'];
        $page = request()->query('page', 1);

        // 1️⃣ Fetch only trait checksums for this page
        $currentChecksums = Nft::query()
            ->select('trait_checksum')
            ->groupBy('trait_checksum')
            ->havingRaw('COUNT(*) > 1')
            ->forPage($page, $this->perPage)
            ->pluck('trait_checksum');

        // 2️⃣ Fetch all NFTs for these checksums (select only needed columns)
        $nftsGrouped = Nft::whereIn('trait_checksum', $currentChecksums)
            ->select('id', 'nft_id', 'type', 'color', 'skin', 'trait_checksum', 'metadata')
            ->get()
            ->groupBy('trait_checksum');

        // 3️⃣ Map to groups
        $groups = collect($currentChecksums)->map(function ($checksum) use ($nftsGrouped, $columns) {
            $nfts = $nftsGrouped[$checksum] ?? collect([]);
            $sample = $nfts->first();
            return [
                'traits' => $sample ? $sample->only($columns) : [],
                'nfts' => $nfts->take($this->nftsPerGroup),
            ];
        });

        // 4️⃣ Total count of duplicate checksums (for paginator)
        $total = Nft::query()
            ->select('trait_checksum')
            ->groupBy('trait_checksum')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        $dupCombos = new LengthAwarePaginator(
            $groups,
            $total,
            $this->perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.duplicate-nfts', [
            'groups' => $groups,
            'dupCombos' => $dupCombos,
        ]);
    }
}
