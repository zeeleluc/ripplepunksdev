<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Giveaway;
use Carbon\Carbon;

class GiveawayController extends Controller
{
    // GET /giveaway/{type}
    public function index($type)
    {
        $giveaways = Giveaway::where('type', $type)->get();
        return response()->json($giveaways);
    }

    // POST /giveaway/{type}
    public function store(Request $request, $type)
    {
        $request->validate([
            'wallet' => 'required|string|max:255',
        ]);

        $giveaway = Giveaway::create([
            'type' => $type,
            'wallet' => $request->wallet,
            'received_giveaway_at' => now(),
        ]);

        return response()->json($giveaway, 201);
    }

    // POST /giveaway/{id}/decline
    public function decline($id)
    {
        $giveaway = Giveaway::findOrFail($id);
        $giveaway->declined_at = now();
        $giveaway->save();

        return response()->json(['message' => 'Giveaway declined.']);
    }

    // POST /giveaway/{id}/approve
    public function approve($id)
    {
        $giveaway = Giveaway::findOrFail($id);
        $giveaway->claimed_at = now();
        $giveaway->save();

        return response()->json(['message' => 'Giveaway approved.']);
    }
}
