<?php

namespace App\Livewire;

use App\Models\Claim;
use App\Models\ClaimSubmission;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PublicClaim extends Component
{
    public $claim;
    public $hasClaimed = false;
    public $submissions = [];
    public $confirmingMissingBadges = false;
    public $missingBadgesList = [];
    public $userSubmissionDistributed = false;
    public $isFull = false;

    public function mount()
    {
        $this->claim = Claim::where('is_open', true)->latest()->first();

        if ($this->claim) {
            $this->submissions = ClaimSubmission::where('claim_id', $this->claim->id)
                ->with('user')
                ->get();

            $this->isFull = $this->submissions->count() >= $this->claim->total;

            if (Auth::check()) {
                $this->hasClaimed = ClaimSubmission::where('claim_id', $this->claim->id)
                    ->where('user_id', Auth::id())
                    ->exists();

                $submission = ClaimSubmission::where('claim_id', $this->claim->id)
                    ->where('user_id', Auth::id())
                    ->first();

                $this->userSubmissionDistributed = $submission && $submission->received_at !== null;
            }
        }
    }

    public function claimNow()
    {
        if (!Auth::check()) {
            session()->flash('error', 'You are not logged in.');
            return;
        }

        if (!$this->claim) {
            session()->flash('error', 'No open claim available.');
            return;
        }

        if ($this->hasClaimed) {
            session()->flash('error', 'You have already claimed this prize.');
            return;
        }

        $requiredBadges = array_map('trim', explode(',', $this->claim->required_badges));
        $userWallet = Auth::user()->wallet;
        $missingBadges = [];

        foreach ($requiredBadges as $badge) {
            if (!\App\Models\User::walletHasSticker($userWallet, $badge)) {
                $missingBadges[] = $badge;
            }
        }

        if (count($missingBadges) > 0) {
            $this->missingBadgesList = $missingBadges;
            $this->confirmingMissingBadges = true;
            return;
        }

        $this->processClaim();
    }

    public function processClaim()
    {
        ClaimSubmission::create([
            'claim_id' => $this->claim->id,
            'user_id' => Auth::id(),
            'claimed_at' => now(),
        ]);

        $this->hasClaimed = true;

        $this->submissions = ClaimSubmission::where('claim_id', $this->claim->id)
            ->with('user')
            ->get();

        session()->flash('message', 'You claimed successfully!');
    }

    public function render()
    {
        return view('livewire.public-claim');
    }
}
