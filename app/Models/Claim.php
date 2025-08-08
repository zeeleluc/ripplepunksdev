<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $fillable = [
        'title', 'description', 'prize', 'total', 'required_badges', 'is_open'
    ];

    public function submissions()
    {
        return $this->hasMany(ClaimSubmission::class);
    }
}
