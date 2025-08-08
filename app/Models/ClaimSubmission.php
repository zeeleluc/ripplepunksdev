<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClaimSubmission extends Model
{
    protected $fillable = [
        'claim_id', 'user_id', 'claimed_at', 'received_at'
    ];

    public function claim()
    {
        return $this->belongsTo(Claim::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
