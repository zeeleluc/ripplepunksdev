<?php

namespace App\Livewire;

use App\Models\Nft;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Schema;

class Punks extends Component
{
    use WithPagination;

    public $color = '';
    public $type = '';
    public $totalAccessories = '';
    public $accessory = '';

    protected $queryString = [
        'color' => ['except' => ''],
        'type' => ['except' => ''],
        'totalAccessories' => ['except' => ''],
        'accessory' => ['except' => ''],
    ];

    public function mount()
    {
        // Initialize filters from query string
        $this->color = request()->query('color', '');
        $this->type = request()->query('type', '');
        $this->totalAccessories = request()->query('totalAccessories', '');
        $this->accessory = request()->query('accessory', '');
    }

    public function updating($name, $value)
    {
        // Reset pagination when any filter changes
        if (in_array($name, ['color', 'type', 'totalAccessories', 'accessory'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        // Cache column listing for performance
        static $columns = null;
        if ($columns === null) {
            $columns = Schema::getColumnListing('nfts');
        }

        $query = Nft::query()->orderBy('nft_id', 'desc');

        // Apply filters
        if ($this->color && in_array('color', $columns)) {
            $query->where('color', $this->color);
        }

        if ($this->type && in_array('type', $columns)) {
            $query->where('type', $this->type);
        }

        if ($this->totalAccessories !== '' && in_array('total_accessories', $columns)) {
            $query->where('total_accessories', (int) $this->totalAccessories);
        }

        if ($this->accessory && in_array($this->accessory, $columns)) {
            $query->where($this->accessory, '!=', null)
                ->where($this->accessory, '!=', false);
        }

        $nfts = $query->paginate(64);

        // Filters data for selects
        $colors = Nft::select('color')
            ->distinct()
            ->pluck('color')
            ->filter()
            ->sort()
            ->values();

        $types = Nft::select('type')
            ->distinct()
            ->pluck('type')
            ->filter()
            ->sort()
            ->values();

        $totals = Nft::select('total_accessories')
            ->distinct()
            ->pluck('total_accessories')
            ->filter()
            ->sort()
            ->values();

        $excluded = [
            'id', 'nftoken_id', 'issuer', 'owner', 'nftoken_taxon', 'transfer_fee',
            'uri', 'url', 'flags', 'assets', 'metadata', 'sequence', 'name', 'nft_id',
            'created_at', 'updated_at', 'color', 'type', 'total_accessories', 'burned_at'
        ];
        $accessories = array_values(array_diff($columns, $excluded));

        return view('livewire.punks', [
            'nfts' => $nfts,
            'colors' => $colors,
            'types' => $types,
            'totals' => $totals,
            'accessories' => $accessories,
        ]);
    }

    public function ipfsToHttp($url)
    {
        return str_starts_with($url, 'ipfs://')
            ? 'https://ipfs.io/ipfs/' . substr($url, 7)
            : $url;
    }
}
