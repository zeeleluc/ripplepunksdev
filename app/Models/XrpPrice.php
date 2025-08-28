<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XrpPrice extends Model
{
    use HasFactory;

    protected $fillable = ['price_usd'];
}
