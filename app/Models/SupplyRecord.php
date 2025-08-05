<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplyRecord extends Model
{
    protected $fillable = ['out_of_circulation', 'new_mints'];

    public static function latestRecord(): ?self
    {
        return self::latest('created_at')->first();
    }
}
