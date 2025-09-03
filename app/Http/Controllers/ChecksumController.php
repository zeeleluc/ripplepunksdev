<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nft;

class ChecksumController extends Controller
{
    /**
     * Check if a given checksum exists in the NFTs table.
     *
     * GET /checksum/{checksum}
     */
    public function check(string $checksum)
    {
        $exists = Nft::where('trait_checksum', $checksum)->exists();

        return response()->json([
            'success' => $exists,
        ]);
    }
}
