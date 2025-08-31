<?php

namespace App\Http\Controllers;

use App\Models\Nft;

class CleanupController extends Controller
{
    public function index()
    {
        $duplicates = Nft::getDuplicateGroups();
        $blueBandanas = Nft::getBlueBandanas();

        return view('cleanup', compact('duplicates', 'blueBandanas'));
    }
}
