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
        'burned_at',
    ];

    protected $casts = [
        'flags' => 'array',
        'assets' => 'array',
        'metadata' => 'array',
        'burned_at' => 'datetime',
    ];
}
