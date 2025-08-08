<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\Shout;
use Illuminate\Support\Facades\Auth;

class Shoutboard extends Component
{
    public $message = '';
    public $editingId = null;
    public $editingMessage = '';
    public ?int $confirmingDeletionId = null;

    protected $rules = [
        'message' => 'required|string|max:255',
        'editingMessage' => 'required|string|max:255',
    ];

    public function postShout()
    {
        if (!Auth::check()) {
            abort(403);
        }

        if (!User::walletHasSticker(Auth::user()->wallet, 'Colony Climber')) {
            abort(403);
        }

        $this->validateOnly('message');

        Shout::create([
            'wallet' => Auth::user()->wallet,
            'message' => $this->message,
        ]);

        $this->reset('message');
    }

    public function editShout($id)
    {
        $shout = Shout::findOrFail($id);

        if (!Auth::check() || $shout->wallet !== Auth::user()->wallet) {
            abort(403);
        }

        if (!User::walletHasSticker(Auth::user()->wallet, 'Colony Climber')) {
            abort(403);
        }

        $this->editingId = $shout->id;
        $this->editingMessage = $shout->message;
    }

    public function updateShout()
    {
        $this->validateOnly('editingMessage');

        $shout = Shout::findOrFail($this->editingId);

        if ($shout->wallet !== Auth::user()->wallet) {
            abort(403);
        }

        if (!User::walletHasSticker(Auth::user()->wallet, 'Colony Climber')) {
            abort(403);
        }

        $shout->update([
            'message' => $this->editingMessage,
        ]);

        $this->cancelEdit();
    }

    public function deleteShout($id)
    {
        $shout = Shout::findOrFail($id);

        if (!Auth::check() || $shout->wallet !== Auth::user()->wallet) {
            abort(403);
        }

        $shout->delete();

        $this->confirmingDeletionId = null;
    }

    public function confirmDelete(int $id)
    {
        $this->confirmingDeletionId = $id;
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->editingMessage = '';
    }

    public function cancel()
    {
        $this->confirmingDeletionId = null;
        $this->cancelEdit();
    }

    public function render()
    {
        return view('livewire.shoutboard', [
            'shouts' => Shout::latest()->get(),
        ]);
    }
}
