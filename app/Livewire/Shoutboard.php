<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Shout;
use App\Models\Holder;
use Illuminate\Support\Facades\Auth;

class Shoutboard extends Component
{
    use WithPagination;

    public $message = '';
    public $editingId = null;
    public $editingMessage = '';
    public ?int $confirmingDeletionId = null;

    protected $rules = [
        'message' => 'required|string|max:255',
        'editingMessage' => 'required|string|max:255',
    ];

    protected function ensureCanShout(): void
    {
        if (!Auth::check()) {
            abort(403, 'You must be logged in to post a shout.');
        }

        $holder = Holder::where('wallet', Auth::user()->wallet)->first();

        if (!$holder || !$holder->hasBadge('Punk')) {
            abort(403, 'You need the "Punk" badge to use the shoutboard.');
        }
    }

    public function postShout()
    {
        $this->ensureCanShout();
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

        $this->ensureCanShout();

        // ✅ Must be within 1 hour of posting
        if ($shout->created_at->lt(now()->subHour())) {
            abort(403, 'You can only edit a shout within 1 hour of posting.');
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

        $this->ensureCanShout();

        if ($shout->created_at->lt(now()->subHour())) {
            abort(403, 'You can only edit a shout within 1 hour of posting.');
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

        if ($shout->created_at->lt(now()->subHour())) {
            abort(403, 'You can only delete a shout within 1 hour of posting.');
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
            'shouts' => Shout::latest()->paginate(10),
        ]);
    }
}
