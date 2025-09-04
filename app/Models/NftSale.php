<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

    public static function latestHashes($limit = 50)
    {
        return self::where('accepted_at', '>=', now()->subDay())
            ->orderBy('accepted_at', 'desc')
            ->take($limit)
            ->pluck('accepted_tx_hash')
            ->toArray();
    }

    public static function totalXrpLast24h()
    {
        return self::where('accepted_at', '>=', now()->subDay())
                ->sum('amount') / 1_000_000;
    }

    public static function totalUsdLast24h()
    {
        return self::where('accepted_at', '>=', now()->subDay())
            ->get()
            ->sum(function ($sale) {
                $amounts = is_string($sale->amount_in_convert_currencies)
                    ? json_decode($sale->amount_in_convert_currencies, true)
                    : $sale->amount_in_convert_currencies;

                return $amounts['usd'] ?? 0;
            });
    }

    public static function marketplaceCountsLast24h()
    {
        $counts = self::where('accepted_at', '>=', now()->subDay())
            ->select('marketplace')
            ->selectRaw('count(*) as total')
            ->groupBy('marketplace')
            ->pluck('total', 'marketplace')
            ->toArray();

        // Sort descending by count
        arsort($counts);

        return $counts;
    }

    public function getMarketNftLink(): ?string
    {
        if ($this->marketplace === 'xrp.cafe') {
            return 'https://xrp.cafe/nft/' . $this->nftoken_id;
        } elseif ($this->marketplace === 'xpmarket.com') {
            return 'https://xpmarket.com/nfts/item/' . $this->nftoken_id;
        } elseif ($this->marketplace === 'bidds.com') {
            return 'https://bidds.com/nft/' . $this->nftoken_id;
        } elseif ($this->marketplace === 'xspectar.com') {
            return 'https://account.xspectar.com/marketplace';
        } elseif ($this->marketplace === 'xrpns.com') {
            return 'https://xrpns.com';
        } elseif ($this->marketplace === 'opulencex.io') {
            return 'https://nftmarketplace.opulencex.io/nft/' . $this->nftoken_id;
        }

        return null;
    }

    public static function topWalletsLast24h(): array
    {
        $since = now()->utc()->subDay();

        $trades = self::where('accepted_at', '>=', $since)->where('amount', '>', 1)->get();

        $walletStats = [];

        foreach ($trades as $trade) {
            foreach (['seller', 'buyer'] as $role) {
                $wallet = $trade->$role;
                if (!$wallet) continue;

                if (!isset($walletStats[$wallet])) {
                    $walletStats[$wallet] = [
                        'trades' => 0,
                        'xrp' => 0,
                        'usd' => 0,
                    ];
                }

                $walletStats[$wallet]['trades'] += 1;
                $walletStats[$wallet]['xrp'] += $trade->amount / 1_000_000;

                $amounts = is_string($trade->amount_in_convert_currencies)
                    ? json_decode($trade->amount_in_convert_currencies, true)
                    : $trade->amount_in_convert_currencies;

                $walletStats[$wallet]['usd'] += $amounts['usd'] ?? 0;
            }
        }

        // Wallet with most trades
        $mostTradesWallet = collect($walletStats)->sortByDesc('trades')->first();
        $mostTradesWalletName = array_search($mostTradesWallet, $walletStats);

        // Wallet that moved most XRP
        $mostXrpWallet = collect($walletStats)->sortByDesc('xrp')->first();
        $mostXrpWalletName = array_search($mostXrpWallet, $walletStats);

        return [
            'most_trades_wallet' => [
                'wallet' => $mostTradesWalletName,
                'trades' => $mostTradesWallet['trades'] ?? 0,
            ],
            'most_xrp_wallet' => [
                'wallet' => $mostXrpWalletName,
                'xrp' => $mostXrpWallet['xrp'] ?? 0,
                'usd' => $mostXrpWallet['usd'] ?? 0,
            ],
        ];
    }
}
