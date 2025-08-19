<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Holder;

class BadgeController extends Controller
{
    public function index(?string $wallet = null)
    {
        // If wallet not provided, use authenticated user's wallet
        $wallet ??= Auth::user()?->wallet;

        // Resolve the holder directly from the holders table
        $holder = $wallet ? Holder::where('wallet', $wallet)->first() : null;

        // If holder exists, get badges, otherwise empty array
        $userBadges = $holder?->badges ?? [];

        $tiers = config('badges.tiers');

        return view('badges', compact('tiers', 'userBadges', 'holder'));
    }
}
