<?php

namespace App\Http\Controllers;

use App\Models\Nft;

class PunksController extends Controller
{
    public function index()
    {
        return view('punks');
    }

    public function show(int $id)
    {
        return view('punks.show', [
            'id' => $id,
            'nft' => Nft::where('nft_id', $id)->first(),
        ]);
    }
}
