<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shout extends Model
{
    protected $fillable = [
        'wallet',
        'message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'wallet', 'wallet');
    }
}
