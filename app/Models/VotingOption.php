<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotingOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'voting_id',
        'title',
    ];

    /**
     * A voting option belongs to a voting.
     */
    public function voting()
    {
        return $this->belongsTo(Voting::class);
    }

    /**
     * A voting option can have many submissions.
     */
    public function submissions()
    {
        return $this->hasMany(VotingSubmission::class);
    }
}
