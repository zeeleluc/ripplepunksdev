<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HolderController extends Controller
{
    public function index()
    {
        $holders = DB::table('nfts')
            ->select('owner', DB::raw('COUNT(*) as nft_count'))
            ->groupBy('owner')
            ->orderByDesc('nft_count')
            ->paginate(20);

        return view('holders.index', compact('holders'));
    }
}
