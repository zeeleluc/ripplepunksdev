<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Nft;
use Illuminate\Support\Facades\Storage;

class NftGrid extends Component
{
    use WithPagination;

    public ?string $owner = null;

    public function mount($owner = null)
    {
        $this->owner = $owner;
    }

    public function render()
    {
        $query = Nft::query()->orderBy('nft_id', 'desc');

        if ($this->owner) {
            $query->where('owner', $this->owner);
        }

        $nfts = $query->paginate(4);

        return view('livewire.nft-grid', [
            'nfts' => $nfts,
        ]);
    }

    public function getImageUrl($nft)
    {
        $path = "ogs/{$nft->nft_id}.png";

        if (Storage::disk('spaces')->exists($path)) {
            return Storage::disk('spaces')->url($path);
        }

        // Return a placeholder image if the file doesn't exist
        return asset('images/nft-placeholder.png');
    }
}
