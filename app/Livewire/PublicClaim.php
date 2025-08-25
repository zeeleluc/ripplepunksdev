<?php

namespace App\Livewire;

use App\Models\Claim;
use App\Models\ClaimSubmission;
use App\Models\Holder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PublicClaim extends Component
{
    public Claim $claim;
    public $hasClaimed = false;
    public $submissions = [];
    public $confirmingMissingBadges = false;
    public $missingBadgesList = [];
    public $isFull = false;
    public $userSubmissionDistributed = false;
    public $message;
    public $error;

    public function mount(Claim $claim)
    {
        $this->claim = $claim;
        $this->loadSubmissions();
    }

    public function loadSubmissions()
    {
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

    public function claimNow()
    {
        if (!Auth::check()) {
            $this->error = 'You are not logged in.';
            return;
        }

        if ($this->hasClaimed) {
            $this->error = 'You have already claimed this prize.';
            return;
        }

        $requiredBadges = array_map('trim', explode(',', $this->claim->required_badges));
        $userWallet = Auth::user()->wallet;

        // Fetch the Holder instance for this wallet
        $holder = Holder::where('wallet', $userWallet)->first();
        $missingBadges = [];

        foreach ($requiredBadges as $badge) {
            if (!$holder || !$holder->hasBadge($badge)) {
                $missingBadges[] = $badge;
            }
        }

        if (count($missingBadges) > 0) {
            $this->missingBadgesList = $missingBadges;
            $this->confirmingMissingBadges = true;
            return;
        }

        // Create the claim submission
        ClaimSubmission::create([
            'claim_id' => $this->claim->id,
            'user_id' => Auth::id(),
            'claimed_at' => now(),
        ]);

        $this->hasClaimed = true;
        $this->message = 'You claimed successfully!';
        $this->loadSubmissions();

        // --- Slack Notification ---
        \App\Helpers\SlackNotifier::info(
            "🎉 New claim!\n" .
            "User: " . Auth::user()->name . " ({$userWallet})\n" .
            "Claim: {$this->claim->title} (ID: {$this->claim->id})"
        );
    }

    public function render()
    {
        return view('livewire.public-claim');
    }
}
