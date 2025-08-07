<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class BadgeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!Auth::check()) {
            $userBadges = [];
        } else {
            $userBadges = $user->getStickers();
        }

        $tiers = [
            1000 => ['Ledger Legend', 'Chain Immortal', 'Cyber Monarch'],
            500  => ['Meta Mogul', 'OG Tycoon', 'Neo-Punk Magnate'],
            225  => ['Digital Don', 'Original Boss', 'Uprising Leader'],
            150  => ['Ripple Overlord', 'Ledger Lord', 'Punk Syndicate'],
            100  => ['Punk King', 'Ripple Monarch', 'Chain King'],
            25   => ['Vault Dweller', 'Time-Locked', 'Deep Vault'],
            10   => ['Street Raider', 'Genesis Raider', 'Colony Climber'],
            1    => ['Punk', 'OG Initiate', 'Other Punk'],
        ];

        return view('badges', compact('tiers', 'userBadges'));
    }
}
