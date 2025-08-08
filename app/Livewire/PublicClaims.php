<?php

namespace App\Livewire;

use App\Models\Claim;
use Livewire\Component;

class PublicClaims extends Component
{
    public function render()
    {
        $claims = Claim::where('is_open', true)->latest()->get();

        return view('livewire.public-claims', [
            'claims' => $claims
        ]);
    }
}
