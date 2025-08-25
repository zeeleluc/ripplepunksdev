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
        'has_image' => 'bool',
    ];

    public static function ctoWalletCount(): int
    {
        return static::where('owner', env('CTO_WALLET'))->count();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'owner', 'wallet');
    }
}
