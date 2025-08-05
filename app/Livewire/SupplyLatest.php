<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SupplyRecord;

class SupplyLatest extends Component
{
    public $latest;

    protected $listeners = ['supplyRecordUpdated' => 'refreshLatest'];

    public function mount()
    {
        $this->refreshLatest();
    }

    public function refreshLatest()
    {
        $this->latest = SupplyRecord::latestRecord();
    }

    public function render()
    {
        return view('livewire.supply-latest');
    }
}
