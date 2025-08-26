<?php

namespace App\Livewire;

use App\Models\Nft;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class Punks extends Component
{
    use WithPagination;

    public $color = '';
    public $type = '';
    public $totalAccessories = '';
    public $selectedAccessories = [];
    public $tempSelectedAccessories = [];
    public $showAccessoryModal = false;
    public $applyingFilters = false;

    protected $queryString = [
        'color' => ['except' => ''],
        'type' => ['except' => ''],
        'totalAccessories' => ['except' => ''],
        'selectedAccessories' => ['except' => []],
    ];

    public function mount()
    {
        $this->color = request()->query('color', '');
        $this->type = request()->query('type', '');
        $this->totalAccessories = request()->query('totalAccessories', '');
        $this->selectedAccessories = request()->query('selectedAccessories', []);
    }

    public function updatingColor() { $this->resetPage(); }
    public function updatingType() { $this->resetPage(); }
    public function updatingTotalAccessories() { $this->resetPage(); }

    // Open modal instantly
    public function openAccessoryModal()
    {
        $this->tempSelectedAccessories = $this->selectedAccessories;
        $this->showAccessoryModal = true;
    }

    // Close modal instantly
    public function closeAccessoryModal()
    {
        $this->showAccessoryModal = false;
    }

    // Apply filters and close modal
    public function applyFilters()
    {
        $this->applyingFilters = true;

        // Update selected accessories
        $this->selectedAccessories = array_values($this->tempSelectedAccessories);

        // Close modal immediately
        $this->showAccessoryModal = false;

        // Reset pagination
        $this->resetPage();

        $this->applyingFilters = false;
    }

    public function render()
    {
        static $columns = null;
        if ($columns === null) {
            $columns = Schema::getColumnListing('nfts');
        }

        $query = Nft::query()->orderBy('nft_id', 'desc');

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

        $colors = Nft::select('color')->distinct()->pluck('color')->filter()->sort()->values();
        $types = Nft::select('type')->distinct()->pluck('type')->filter()->sort()->values();
        $totals = Nft::select('total_accessories')
            ->distinct()
            ->pluck('total_accessories')
            ->filter(fn($v) => $v !== null)
            ->push(0)
            ->unique()
            ->sort()
            ->values();

        $excluded = [
            'id', 'nftoken_id', 'issuer', 'owner', 'nftoken_taxon', 'transfer_fee',
            'uri', 'url', 'flags', 'assets', 'metadata', 'sequence', 'name', 'nft_id',
            'created_at', 'updated_at', 'color', 'type', 'total_accessories', 'burned_at'
        ];

        $accessories = collect(array_diff($columns, $excluded))
            ->mapWithKeys(fn($item) => [$item => $this->mapAccessoryDisplayName($item)])
            ->sortBy(fn($label) => $label)
            ->toArray();

        return view('livewire.punks', [
            'nfts' => $nfts,
            'colors' => $colors,
            'types' => $types,
            'totals' => $totals,
            'accessories' => $accessories,
        ]);
    }

    public function getImageUrl($nft)
    {
        $path = "ogs/{$nft->nft_id}.png";
        if (Storage::disk('spaces')->exists($path)) {
            return Storage::disk('spaces')->url($path);
        }
        return asset('images/nft-placeholder.png');
    }

    protected function mapAccessoryDisplayName(string $accessory): string
    {
        $customMap = [
            'v_r' => 'VR',
            '3d_glasses' => '3D Glasses',
        ];

        return $customMap[$accessory] ?? ucwords(str_replace(['_', '-'], ' ', strtolower($accessory)));
    }
}
