<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Claim;
use App\Models\ClaimSubmission;
use Illuminate\Support\Carbon;

class Claims extends Component
{
    public $claimId = null;  // For edit mode
    public $title, $description, $prize, $total, $is_open = false;
    public $selectedBadges = [];
    public $badges = [];
    public $isEditing = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'prize' => 'required|string',
        'total' => 'required|integer|min:1',
        'selectedBadges' => 'required|array|min:1',
        'is_open' => 'boolean'
    ];

    protected $messages = [
        'selectedBadges.required' => 'Please select at least one badge.',
        'selectedBadges.min' => 'Please select at least one badge.',
    ];

    public function mount()
    {
        $this->badges = config('badges.tiers');
    }

    public function createClaim()
    {
        $this->validate();

        Claim::create([
            'title' => $this->title,
            'description' => $this->description,
            'prize' => $this->prize,
            'total' => $this->total,
            'required_badges' => implode(',', $this->selectedBadges),
            'is_open' => $this->is_open,
        ]);

        session()->flash('message', 'Claim created successfully!');

        $this->resetForm();
    }

    public function editClaim($id)
    {
        $claim = Claim::findOrFail($id);

        $this->claimId = $claim->id;
        $this->title = $claim->title;
        $this->description = $claim->description;
        $this->prize = $claim->prize;
        $this->total = $claim->total;
        $this->selectedBadges = explode(',', $claim->required_badges);
        $this->is_open = $claim->is_open;
        $this->isEditing = true;
    }

    public function updateClaim()
    {
        $this->validate();

        if (!$this->claimId) {
            session()->flash('error', 'No claim selected for updating.');
            return;
        }

        $claim = Claim::findOrFail($this->claimId);

        if (!$claim->is_open) {
            session()->flash('error', 'Only open claims can be updated.');
            return;
        }

        $claim->update([
            'title' => $this->title,
            'description' => $this->description,
            'prize' => $this->prize,
            'total' => $this->total,
            'required_badges' => implode(',', $this->selectedBadges),
            'is_open' => $this->is_open,
        ]);

        session()->flash('message', 'Claim updated successfully!');
        $this->resetForm();
    }

    public function cancelEdit()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['title', 'description', 'prize', 'total', 'is_open', 'selectedBadges', 'claimId', 'isEditing']);
    }

    public function toggleClaim($id)
    {
        $claim = Claim::findOrFail($id);
        $claim->update(['is_open' => !$claim->is_open]);

        // If toggled closed while editing this claim, cancel edit mode
        if ($this->isEditing && $this->claimId == $id && !$claim->is_open) {
            $this->resetForm();
        }
    }

    public function togglePrize($submissionId)
    {
        $submission = ClaimSubmission::findOrFail($submissionId);
        $submission->update([
            'received_at' => $submission->received_at ? null : Carbon::now()
        ]);
    }

    public function render()
    {
        return view('livewire.admin.claims', [
            'claims' => Claim::with(['submissions.user'])->latest()->get()
        ]);
    }
}
