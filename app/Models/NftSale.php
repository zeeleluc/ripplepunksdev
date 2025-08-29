<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NftSale extends Model
{
    protected $fillable = [
        'nftoken_id',
        'accepted_at',
        'accepted_ledger_index',
        'accepted_tx_hash',
        'accepted_account',
        'seller',
        'buyer',
        'amount',
        'broker',
        'marketplace',
        'sale_type',
        'amount_in_convert_currencies',
        'nftoken',
        'seller_details',
        'buyer_details',
        'accepted_account_details',
        'nft_name',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'amount_in_convert_currencies' => 'array',
        'nftoken' => 'array',
        'seller_details' => 'array',
        'buyer_details' => 'array',
        'accepted_account_details' => 'array',
    ];

    public function getAmountInConvertCurrenciesAttribute($value)
    {
        return is_string($value) ? json_decode($value, true) : $value;
    }
}
