<?php

namespace App\Livewire;

use Livewire\Component;

class GiveawayWrapper extends Component
{
    public $type = 'Celebrate New Punks';

    public function render()
    {
        return view('livewire.giveaway-wrapper');
    }
}
