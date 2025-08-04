<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Giveaway;

class GiveawayIndex extends Component
{
    public $type;
    public $giveaways;

    protected $listeners = ['giveawayAdded' => 'refreshGiveaways'];

    public function mount($type)
    {
        $this->type = $type;
        $this->refreshGiveaways();
    }

    public function refreshGiveaways()
    {
        $this->giveaways = Giveaway::where('type', $this->type)->latest()->get();
    }

    public function render()
    {
        return view('livewire.giveaway-index');
    }
}
