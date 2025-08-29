<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holder extends Model
{
    protected $fillable = [
        'wallet',
        'badges',
        'holdings',
        'voting_power',
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

    /**
     * Calculate voting power for a given wallet.
     */
    public static function calculateVotingPower(string $wallet): int
    {
        $totalNfts = Nft::where('owner', $wallet)->count();

        if ($totalNfts <= 0) {
            return 0;
        }

        $tiers = config('badges.tiers');
        krsort($tiers);

        $powerConfig = config('badges.votingPower');

        $badge = 'Punk'; // default
        foreach ($tiers as $threshold => $name) {
            if ($totalNfts >= $threshold) {
                $badge = $name;
                break;
            }
        }

        return $powerConfig[$badge] ?? 1;
    }
}
