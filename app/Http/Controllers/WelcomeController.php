<?php

namespace App\Http\Controllers;

use App\Models\LogEntry;
use App\Models\Nft;

class WelcomeController extends Controller
{
    public function index()
    {
//        \Illuminate\Support\Facades\Auth::login(\App\Models\User::where('wallet', 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS')->firstOrFail());
//
        $newMints = Nft::count() - 10000;

        return view('welcome', [
            'totalItems' => 10000,
            'bar1Percent' => 50,
            'bar2Percent' => ($newMints / 20000) * 100,
            'bar3Percent' => 50 - (($newMints / 20000) * 100),
            'logEntries' => LogEntry::query()
                ->where('is_published', true)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
        ]);
    }
}
