<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Giveaway extends Model
{
    protected $fillable = [
        'type',
        'wallet',
        'claimed_at',
        'received_giveaway_at',
        'declined_at',
        'comments',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'received_giveaway_at' => 'datetime',
        'declined_at' => 'datetime',
    ];
}
