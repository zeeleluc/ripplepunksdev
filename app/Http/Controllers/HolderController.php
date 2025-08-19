<?php

namespace App\Http\Controllers;

use App\Models\Holder;
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

    public function show(string $wallet)
    {
        $holder = Holder::where('wallet', $wallet)->first();

        return view('holders.show', compact('holder'));
    }
}
