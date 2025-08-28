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

        $tiers = config('badges.tiers');

        foreach ($tiers as $threshold => $badge) {
            if ($total >= $threshold && !in_array($badge, $stickers)) {
                $stickers[] = $badge;
            }
        }

        return $stickers;
    }
}
