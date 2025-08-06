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

        $nfts = Nft::where('owner', $this->wallet)->get();

        $total = $nfts->count();
        $previousMint = $nfts->whereBetween('nft_id', [0, 9999])->count();
        $currentMint = $nfts->whereBetween('nft_id', [10000, 19999])->count();

        if ($total >= 1) {
            $stickers[] = 'OG Punker';
        }

        if ($currentMint >= 1) {
            $stickers[] = 'Other Punker';
        }

        return $stickers;
    }

    public function hasSticker(string $sticker): bool
    {
        return in_array($sticker, $this->getStickers(), true);
    }
}
