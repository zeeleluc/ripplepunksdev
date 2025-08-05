<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogEntry extends Model
{
    protected $fillable = [
        'text',
        'link',
        'is_published',
        'likes',
        'dislikes',
    ];
}
