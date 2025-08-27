<?php

namespace App\Livewire;

use Livewire\Component;

class BuyButton extends Component
{
    public $showModal = false;
    public $buyUrl;

    public function mount($buyUrl = null)
    {
        $this->buyUrl = $buyUrl ?? 'https://xrp.cafe/usercollection/r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS/r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS/604/0/rarity%20high/false/';
    }

    public function toggleModal()
    {
        $this->showModal = !$this->showModal;
    }

    public function render()
    {
        return view('livewire.buy-button');
    }
}
