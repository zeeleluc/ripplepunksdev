<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nft extends Model
{
    // ✅ Allow all columns to be mass assignable (safe in this case because
    // you're controlling the input via your sync command)
    protected $guarded = [];

    protected $casts = [
        'flags' => 'array',
        'assets' => 'array',
        'metadata' => 'array',
        'burned_at' => 'datetime',
    ];

    public $timestamps = false;

    public static function ctoWalletCount(): int
    {
        return static::where('owner', env('CTO_WALLET'))->count();
    }

    public static function projectWalletCount(): int
    {
        return static::where('owner', env('PROJECT_WALLET'))->count();
    }

    public static function rewardsWalletCount(): int
    {
        return static::where('owner', env('REWARDS_WALLET'))->count();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'owner', 'wallet');
    }

    public static function genericCount(?int $fromId = null, ?int $toId = null, ?array $search = null): int
    {
        $query = static::query();

        // Filter by nft_id range if provided
        if (!is_null($fromId) && !is_null($toId)) {
            $query->whereBetween('nft_id', [$fromId, $toId]);
        }

        // Apply search if provided
        if (!empty($search)) {
            foreach ($search as $column => $value) {
                $query->where($column, 'like', "%{$value}%");
            }
        }

        return $query->count();
    }
}
