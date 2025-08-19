<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Holder as HolderModel;

class Holder extends Component
{
    use WithPagination;

    public HolderModel $holder;

    public function mount(HolderModel $holder)
    {
        $this->holder = $holder;
    }

    public function render()
    {
        return view('livewire.holder', [
            'holder' => $this->holder,
        ]);
    }
}
