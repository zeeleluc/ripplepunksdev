<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\NftSale;

class FetchXrplSales extends Command
{
    protected $signature = 'xrpl:fetch-sales';
    protected $description = 'Fetch XRPL NFT sales from the last 15 minutes and clean old records';

    public function handle()
    {
        $from = Carbon::now()->subMinutes(15)->timestamp * 1000; // in ms
        $to   = Carbon::now()->timestamp * 1000;

        $url = "https://bithomp.com/api/v2/nft-sales";
        $params = [
            'list' => 'lastSold',
            'currency' => 'xrp',
            'convertCurrencies' => 'usd,eur',
            'period' => "{$from}..{$to}",
        ];

        $response = Http::withHeaders([
            'x-bithomp-token' => env('BITHOMP_API_KEY'), // your API key
        ])->get($url, $params);

        if ($response->failed()) {
            $this->error("Failed to fetch sales.");
            return;
        }

        $sales = $response->json('sales') ?? [];

        foreach ($sales as $sale) {
            $metadata = $sale['nftoken']['metadata'] ?? [];
            $nft_name = $metadata['name'] ?? null;

            NftSale::updateOrCreate(
                ['accepted_tx_hash' => $sale['acceptedTxHash']],
                [
                    'nftoken_id' => $sale['nftokenID'],
                    'accepted_at' => Carbon::createFromTimestamp($sale['acceptedAt']),
                    'accepted_ledger_index' => $sale['acceptedLedgerIndex'],
                    'accepted_account' => $sale['acceptedAccount'],
                    'seller' => $sale['seller'] ?? null,
                    'buyer' => $sale['buyer'] ?? null,
                    'amount' => $sale['amount'],
                    'broker' => $sale['broker'] ?? false,
                    'marketplace' => $sale['marketplace'] ?? null,
                    'sale_type' => $sale['saleType'] ?? null,
                    'amount_in_convert_currencies' => json_encode($sale['amountInConvertCurrencies'] ?? []),
                    'nftoken' => json_encode($sale['nftoken'] ?? []),
                    'seller_details' => json_encode($sale['sellerDetails'] ?? []),
                    'buyer_details' => json_encode($sale['buyerDetails'] ?? []),
                    'accepted_account_details' => json_encode($sale['acceptedAccountDetails'] ?? []),
                    'nft_name' => $nft_name,
                ]
            );
        }

        // Delete old sales (older than 7 days)
        NftSale::where('accepted_at', '<', Carbon::now()->subDays(7))->delete();

        $this->info("Fetched " . count($sales) . " sales. Old sales cleaned.");
    }
}
