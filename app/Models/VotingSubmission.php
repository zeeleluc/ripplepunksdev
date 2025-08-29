<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotingSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'voting_id',
        'holder_id',
        'user_id',
        'voting_option_id',
        'voting_power',
    ];

    /**
     * A submission belongs to a voting.
     */
    public function voting()
    {
        return $this->belongsTo(Voting::class);
    }

    /**
     * A submission belongs to a voting option.
     */
    public function option()
    {
        return $this->belongsTo(VotingOption::class, 'voting_option_id');
    }

    /**
     * A submission belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
