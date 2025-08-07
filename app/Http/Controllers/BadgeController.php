<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BadgeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!Auth::check()) {
            $userBadges = [];
        } else {
            $userBadges = User::getStickersForWallet($user->wallet);
        }

        $tiers = config('badges.tiers');

        return view('badges', compact('tiers', 'userBadges'));
    }
}
