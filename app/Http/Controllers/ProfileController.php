<?php

namespace App\Http\Controllers;

use App\Models\User;

class ProfileController extends Controller
{
    public function index(?string $wallet = null)
    {
        $user = null;

        if ($wallet) {
            $user = User::where('wallet', $wallet)->first();
        } else {
            $user = auth()->user();
        }

        if (! $user) {
            abort(404);
        }

        return view('profile', compact('user'));
    }
}
