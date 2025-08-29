<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'wallet', 'xumm_token'];

    protected $hidden = [
        'xumm_token',
        'remember_token',
    ];

    protected $casts = [
        'is_admin' => 'bool',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /** Holder relation */
    public function holder()
    {
        return $this->hasOne(Holder::class, 'wallet', 'wallet');
    }

    /** Total NFTs for user */
    public function totalNFTs(): int
    {
        return $this->holder?->nfts()->count() ?? Nft::where('owner', $this->wallet)->count();
    }

    /**
     * Get badges optionally via a wallet
     *
     * @param string|null $wallet
     * @return array
     */
    public static function badges(?string $wallet = null): array
    {
        if (!$wallet) return [];

        $user = self::where('wallet', $wallet)->first();
        return $user?->holder?->badges ?? [];
    }

    public function votingSubmissions()
    {
        return $this->hasMany(VotingSubmission::class);
    }
}
