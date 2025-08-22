<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interaction extends Model
{
    use HasFactory;

    protected $fillable = ['identifier', 'type', 'holder_id', 'interacted_at'];

    public $timestamps = true;

    public function holder()
    {
        return $this->belongsTo(User::class, 'holder_id');
    }
}
