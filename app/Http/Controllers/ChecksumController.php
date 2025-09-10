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

    public function checkPunk(Request $request)
    {
        // Merge core + attribute columns
        $columns = array_merge(
            ['color', 'skin', 'type', 'total_accessories'],
            Nft::getAttributeColumns()
        );

        $query = Nft::query();

        foreach ($request->all() as $column => $value) {
            // Only allow whitelisted columns
            if (in_array($column, $columns)) {
                $query->where($column, $value);
            }
        }

        return response()->json([
            'success' => $query->exists(),
        ]);
    }
}
