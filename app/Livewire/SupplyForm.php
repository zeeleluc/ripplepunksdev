<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SupplyRecord;

class SupplyForm extends Component
{
    public int $out_of_circulation = 0;
    public int $new_mints = 0;

    public function mount()
    {
        if ($latest = SupplyRecord::latestRecord()) {
            $this->out_of_circulation = $latest->out_of_circulation;
            $this->new_mints = $latest->new_mints;
        }
    }

    public function submit()
    {
        $this->validate([
            'out_of_circulation' => 'required|integer|min:1|max:10000',
            'new_mints' => 'required|integer|min:1|max:10000',
        ]);

        SupplyRecord::create([
            'out_of_circulation' => $this->out_of_circulation,
            'new_mints' => $this->new_mints,
        ]);

        $this->dispatch('supplyRecordUpdated');

        session()->flash('message', 'Supply record saved successfully.');

        $this->resetValidation(); // keep the values but clear errors
    }

    public function render()
    {
        return view('livewire.supply-form');
    }
}
