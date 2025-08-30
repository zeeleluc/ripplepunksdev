<?php

namespace App\Http\Controllers;

use App\Models\LogEntry;
use App\Models\Nft;
use App\Services\XPost;

class WelcomeController extends Controller
{
    public function index()
    {
//        \Illuminate\Support\Facades\Auth::login(\App\Models\User::where('wallet', 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS')->firstOrFail());
//        \Illuminate\Support\Facades\Auth::login(\App\Models\User::where('wallet', 'rtest')->firstOrFail());

        $newMints = Nft::count() - 10000;

        return view('welcome', [
            'totalItems' => 10000,
            'bar1Percent' => 50,
            'bar2Percent' => ($newMints / 20000) * 100,
            'bar3Percent' => 50 - (($newMints / 20000) * 100),
            'logEntries' => LogEntry::query()
                ->where('is_published', true)
                ->orderBy('created_at', 'desc')
                ->limit(2)
                ->get(),
            'counts' => [
                'Alien' => [
                    Nft::genericCount(0, 9999, ['type' => 'Alien']),
                    Nft::genericCount(10000, 19999, ['type' => 'Alien']),
                ],
                'Ape' => [
                    Nft::genericCount(0, 9999, ['type' => 'Ape']),
                    Nft::genericCount(10000, 19999, ['type' => 'Ape']),
                ],
                'Zombie' => [
                    Nft::genericCount(0, 9999, ['type' => 'Zombie']),
                    Nft::genericCount(10000, 19999, ['type' => 'Zombie']),
                ],
                'VR' => [
                    Nft::genericCount(0, 9999, ['v_r' => true]),
                    Nft::genericCount(10000, 19999, ['v_r' => true]),
                ],
                'Hoodie' => [
                    Nft::genericCount(0, 9999, ['hoodie' => true]),
                    Nft::genericCount(10000, 19999, ['hoodie' => true]),
                ],
                'Beanie' => [
                    Nft::genericCount(0, 9999, ['beanie' => true]),
                    Nft::genericCount(10000, 19999, ['beanie' => true]),
                ],
                'Big Beard' => [
                    Nft::genericCount(0, 9999, ['big_beard' => true]),
                    Nft::genericCount(10000, 19999, ['big_beard' => true]),
                ],
//                'Choker' => [
//                    Nft::genericCount(0, 9999, ['choker' => true]),
//                    Nft::genericCount(10000, 19999, ['choker' => true]),
//                ],
                'Top Hat' => [
                    Nft::genericCount(0, 9999, ['top_hat' => true]),
                    Nft::genericCount(10000, 19999, ['top_hat' => true]),
                ],
                'Buck Teeth' => [
                    Nft::genericCount(0, 9999, ['buck_teeth' => true]),
                    Nft::genericCount(10000, 19999, ['buck_teeth' => true]),
                ],
                '3D Glasses' => [
                    Nft::genericCount(0, 9999, ['3d_glasses' => true]),
                    Nft::genericCount(10000, 19999, ['3d_glasses' => true]),
                ],
                'Cowboy Hat' => [
                    Nft::genericCount(0, 9999, ['cowboy_hat' => true]),
                    Nft::genericCount(10000, 19999, ['cowboy_hat' => true]),
                ],
                'Tiara' => [
                    Nft::genericCount(0, 9999, ['tiara' => true]),
                    Nft::genericCount(10000, 19999, ['tiara' => true]),
                ],
                'Cap' => [
                    Nft::genericCount(0, 9999, ['cap' => true]),
                    Nft::genericCount(10000, 19999, ['cap' => true]),
                ],
            ]
        ]);
    }
}
