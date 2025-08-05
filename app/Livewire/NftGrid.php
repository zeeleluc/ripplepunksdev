<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Nft;

class NftGrid extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind'; // still applies if using built-in styles

    public function render()
    {
        $nfts = Nft::orderBy('nft_id', 'desc')->paginate(4);

        return view('livewire.nft-grid', [
            'nfts' => $nfts,
        ]);
    }

    public function goToPage($page)
    {
        $this->setPage($page);
    }

    public function previousPage()
    {
        $this->previousPage();
    }

    public function nextPage()
    {
        $this->nextPage();
    }

    public function ipfsToHttp($url)
    {
        return str_starts_with($url, 'ipfs://')
            ? 'https://ipfs.io/ipfs/' . substr($url, 7)
            : $url;
    }
}
