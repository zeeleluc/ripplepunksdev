<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holder extends Model
{
    protected $fillable = [
        'wallet',
        'badges',
        'holdings',
    ];

    protected $casts = [
        'badges' => 'array',
        'last_seen_at' => 'datetime',
    ];

    public $timestamps = false;

    public function nfts()
    {
        return $this->hasMany(Nft::class, 'owner', 'wallet');
    }

    public function hasBadge(string $badge): bool
    {
        return in_array($badge, $this->badges, true);
    }

    /**
     * Calculate badges for a given wallet.
     */
    public static function calculateBadges(string $wallet): array
    {
        $nfts = Nft::where('owner', $wallet)->get();

        $stickers = [];

        $total = $nfts->count();
        $ogCount = $nfts->whereBetween('nft_id', [0, 9999])->count();
        $otherCount = $nfts->whereBetween('nft_id', [10000, 19999])->count();

        $tiers = config('badges.tiers');

        foreach ($tiers as $threshold => [$anyBadge, $ogBadge, $otherBadge]) {
            if ($total >= $threshold && !in_array($anyBadge, $stickers)) {
                $stickers[] = $anyBadge;
            }
            if ($ogCount >= $threshold && !in_array($ogBadge, $stickers)) {
                $stickers[] = $ogBadge;
            }
            if ($otherCount >= $threshold && !in_array($otherBadge, $stickers)) {
                $stickers[] = $otherBadge;
            }
        }

        return $stickers;
    }
}
