<?php

namespace App\Http\Controllers;

use App\Models\Nft;

class WelcomeController extends Controller
{
    public function index()
    {
//        \Illuminate\Support\Facades\Auth::login(\App\Models\User::where('wallet', 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS')->firstOrFail());

        return view('welcome', [
            'totalItems' => 10000,
            'bar1Count' => 10000,
            'bar2Count' => Nft::count() - 10000,
        ]);
    }
}
