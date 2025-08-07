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

    public static function getStickersForWallet(string $wallet): array
    {
        $stickers = [];

        $nfts = \App\Models\Nft::where('owner', $wallet)->get();

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

    public static function walletHasSticker(string $wallet, string $sticker): bool
    {
        return in_array($sticker, static::getStickersForWallet($wallet), true);
    }
}
