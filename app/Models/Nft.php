<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nft extends Model
{
    protected $fillable = [
        'nftoken_id',
        'issuer',
        'owner',
        'nftoken_taxon',
        'transfer_fee',
        'uri',
        'url',
        'flags',
        'assets',
        'metadata',
        'sequence',
        'name',
        'nft_id',
    ];

    protected $casts = [
        'flags' => 'array',
        'assets' => 'array',
        'metadata' => 'array',
        'burned_at' => 'datetime',
    ];

    public static function ctoWalletCount(): int
    {
        return static::where('owner', env('CTO_WALLET'))->count();
    }
}
