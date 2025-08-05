<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupplyRecord;
use Illuminate\Support\Facades\Auth;

class SupplyController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        abort_unless($user && $user->isAdmin(), 403);

        // Dummy example values – replace with actual logic or data
        return view('admin.supply', [
            'latest' => SupplyRecord::latestRecord(),
            'initialSupply' => 10000,
            'newBatchMinted' => 3450,
            'outOfCirculation' => 1200,
        ]);
    }
}
