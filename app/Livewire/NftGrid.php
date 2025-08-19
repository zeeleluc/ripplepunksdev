<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Nft;

class NftGrid extends Component
{
    use WithPagination;

    public ?string $owner = null; // optional parameter

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

    public function ipfsToHttp($url)
    {
        return str_starts_with($url, 'ipfs://')
            ? 'https://ipfs.io/ipfs/' . substr($url, 7)
            : $url;
    }
}
