<?php

namespace App\Livewire;

use App\Models\Nft;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class Punks extends Component
{
    use WithPagination;

    public $color = '';
    public $type = '';
    public $totalAccessories = '';
    public $selectedAccessories = [];
    public $tempSelectedAccessories = [];
    public $showAccessoryModal = false;

    protected $queryString = [
        'color' => ['except' => ''],
        'type' => ['except' => ''],
        'totalAccessories' => ['except' => ''],
        'selectedAccessories' => ['except' => []],
    ];

    public function updatingColor() { $this->resetPage(); }
    public function updatingType() { $this->resetPage(); }
    public function updatingTotalAccessories() { $this->resetPage(); }
    public function updatingSelectedAccessories() { $this->resetPage(); }

    public function openAccessoryModal()
    {
        $this->tempSelectedAccessories = $this->selectedAccessories;
        $this->showAccessoryModal = true;
    }

    public function closeAccessoryModal()
    {
        $this->showAccessoryModal = false;
    }

    public function resetFilters()
    {
        $this->color = '';
        $this->type = '';
        $this->totalAccessories = '';
        $this->tempSelectedAccessories = [];
        $this->selectedAccessories = [];
        $this->showAccessoryModal = false;
        $this->resetPage();
    }

    public function applyFilters()
    {
        $this->selectedAccessories = array_values($this->tempSelectedAccessories);
        $this->showAccessoryModal = false;
        $this->resetPage();
    }

    public function render()
    {
        // Get table columns once (cached)
        $columns = Cache::remember('nft_columns', 86400, function () {
            return Schema::getColumnListing((new Nft)->getTable());
        });

        $query = Nft::query()->orderBy('nft_id', 'asc');

        if ($this->color && in_array('color', $columns)) {
            $query->where('color', $this->color);
        }
        if ($this->type && in_array('type', $columns)) {
            $query->where('type', $this->type);
        }
        if ($this->totalAccessories !== '' && in_array('total_accessories', $columns)) {
            $query->where('total_accessories', (int) $this->totalAccessories);
        }

        foreach ($this->selectedAccessories as $accessory) {
            if (in_array($accessory, $columns)) {
                $query->where($accessory, true);
            }
        }

        $nfts = $query->paginate(25);

        $colors = Cache::remember('nft_colors', 3600, fn() => Nft::select('color')->distinct()->pluck('color')->filter()->sort()->values());
        $types = Cache::remember('nft_types', 3600, fn() => Nft::select('type')->distinct()->pluck('type')->filter()->sort()->values());
        $totals = Cache::remember('nft_totals', 3600, fn() => Nft::select('total_accessories')->distinct()->pluck('total_accessories')->filter()->unique()->sort()->values());

        $excluded = [
            'id', 'nftoken_id', 'issuer', 'owner', 'nftoken_taxon', 'transfer_fee', 'uri', 'url',
            'flags', 'assets', 'metadata', 'sequence', 'name', 'nft_id', 'created_at', 'updated_at',
            'color', 'type', 'total_accessories', 'burned_at'
        ];

        $accessories = collect(array_diff($columns, $excluded))
            ->mapWithKeys(fn($item) => [$item => $this->mapAccessoryDisplayName($item)])
            ->sort()
            ->toArray();

        return view('livewire.punks', compact('nfts', 'colors', 'types', 'totals', 'accessories'));
    }

    public function getImageUrl($nft)
    {
        $path = "ogs/{$nft->nft_id}.png";
        return Storage::disk('spaces')->exists($path)
            ? Storage::disk('spaces')->url($path)
            : asset('images/nft-placeholder.png');
    }

    protected function mapAccessoryDisplayName(string $accessory): string
    {
        $customMap = [
            'v_r' => 'VR',
            '3d_glasses' => '3D Glasses',
        ];
        return $customMap[$accessory] ?? ucwords(str_replace(['_', '-'], ' ', $accessory));
    }
}
