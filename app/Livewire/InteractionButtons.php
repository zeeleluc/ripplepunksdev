<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Interaction;

class InteractionButtons extends Component
{
    public $identifier;
    public $class = '';
    public $canInteract = false;
    public $interactions = [];

    public function mount($identifier, $class = '')
    {
        $this->identifier = $identifier;
        $this->class = $class;

        $holder = optional(Auth::user())->holder;

        $this->canInteract = Auth::check() && $holder && $holder->hasBadge('Punk');

        $this->loadInteractions();
    }

    public function loadInteractions()
    {
        $types = ['thumb-up','thumb-down','middle-finger','eyes','lightning','heart'];

        foreach ($types as $type) {
            $count = Interaction::where('identifier', $this->identifier)
                ->where('type', $type)
                ->count();

            $pressed = false;
            if (Auth::check() && optional(Auth::user()->holder)->id) {
                $pressed = Interaction::where('identifier', $this->identifier)
                    ->where('type', $type)
                    ->where('holder_id', Auth::user()->holder->id)
                    ->exists();
            }

            $this->interactions[$type] = [
                'count' => $count,
                'pressed_by_user' => $pressed,
                // Assign tie-breaker priority (lower = higher priority)
                'priority' => match($type) {
                    'thumb-down' => 99,
                    'middle-finger' => 100,
                    default => 0,
                },
            ];
        }

        // Sort by count descending, then by priority ascending
        uasort($this->interactions, fn($a, $b) =>
        $b['count'] <=> $a['count'] ?: $a['priority'] <=> $b['priority']
        );
    }

    public function toggle($type)
    {
        if (!$this->canInteract) return;

        $holderId = Auth::user()->holder->id;

        $interaction = Interaction::where('identifier', $this->identifier)
            ->where('type', $type)
            ->where('holder_id', $holderId)
            ->first();

        if ($interaction) {
            $interaction->delete();
        } else {
            Interaction::create([
                'identifier' => $this->identifier,
                'type' => $type,
                'holder_id' => $holderId,
                'interacted_at' => now(),
            ]);

            $emojiMap = [
                'thumb-up' => '👍🏼',
                'eyes' => '👀',
                'lightning' => '⚡️',
                'heart' => '💙',
                'thumb-down' => '👎🏼',
                'middle-finger' => '🖕🏼',
            ];

            $emoji = $emojiMap[$type] ?? $type;

            \App\Helpers\SlackNotifier::info("{$emoji} pressed by " . Auth::user()->wallet . " on '{$this->identifier}'", false);


        }

        $this->loadInteractions();
    }

    public function render()
    {
        return view('livewire.interaction-buttons');
    }
}
