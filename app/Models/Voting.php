<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'is_active',
    ];

    /**
     * A voting has many options.
     */
    public function options()
    {
        return $this->hasMany(VotingOption::class);
    }

    /**
     * A voting has many submissions.
     */
    public function submissions()
    {
        return $this->hasMany(VotingSubmission::class);
    }
}
