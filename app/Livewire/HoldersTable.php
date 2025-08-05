<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class HoldersTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind'; // or 'bootstrap'

    public function render()
    {
        $holders = DB::table('nfts')
            ->select('owner', DB::raw('COUNT(*) as nft_count'))
            ->groupBy('owner')
            ->orderByDesc('nft_count')
            ->paginate(20);

        return view('livewire.holders-table', [
            'holders' => $holders,
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

}
