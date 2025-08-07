<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'wallet', 'xumm_token'];

    protected $hidden = [
        'xumm_token',
        'remember_token',
    ];

    protected $casts = [
        'is_admin' => 'bool',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function totalNFTs(): int
    {
        return Nft::where('owner', $this->wallet)->count();
    }

    public function getStickers(): array
    {
        $stickers = [];

        // Fetch all NFTs owned by the user
        $nfts = Nft::where('owner', $this->wallet)->get();

        $total = $nfts->count();
        $ogCount = $nfts->whereBetween('nft_id', [0, 9999])->count();
        $otherCount = $nfts->whereBetween('nft_id', [10000, 19999])->count();

        // Badge tiers (shared thresholds)
        $tiers = [
            1000 => ['Ledger Legend', 'Chain Immortal', 'Cyber Monarch'],
            500  => ['Meta Mogul', 'OG Tycoon', 'Neo-Punk Magnate'],
            225  => ['Digital Don', 'Original Boss', 'Uprising Leader'],
            150  => ['Ripple Overlord', 'Ledger Lord', 'Punk Syndicate'],
            100  => ['Punk King', 'Ripple Monarch', 'Chain King'],
            25   => ['Vault Dweller', 'Time-Locked', 'Deep Vault'],
            10   => ['Street Raider', 'Genesis Raider', 'Colony Climber'],
            1    => ['Punk', 'OG Initiate', 'Other Punk'],
        ];

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

    public function hasSticker(string $sticker): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return in_array($sticker, $this->getStickers(), true);
    }
}
