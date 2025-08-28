<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\XrpPrice;
use Carbon\Carbon;
use App\Helpers\SlackNotifier;

class FetchXRPPrice extends Command
{
    protected $signature = 'xrp:fetch-price';
    protected $description = 'Fetch XRP/USD price from CoinGecko';

    public function handle()
    {
        $this->info('Fetching XRP price...');

        try {
            $response = Http::timeout(10)->get('https://api.coingecko.com/api/v3/simple/price', [
                'ids' => 'ripple',
                'vs_currencies' => 'usd',
            ]);

            if ($response->failed()) {
                $msg = 'Failed to fetch price from CoinGecko.';
                $this->error($msg);
                SlackNotifier::error($msg);
                return 1;
            }

            $data = $response->json();

            if (!isset($data['ripple']['usd'])) {
                $msg = 'Unexpected API response, missing XRP/USD price.';
                $this->error($msg);
                SlackNotifier::warning($msg);
                return 1;
            }

            $price = $data['ripple']['usd'];

            // Opslaan in database
            XrpPrice::create([
                'price_usd' => $price,
            ]);

            // Verwijder oude prijzen ouder dan 1 week
            $deleted = XrpPrice::where('created_at', '<', Carbon::now()->subWeek())->delete();
            $this->info("Deleted $deleted old records.");

        } catch (\Exception $e) {
            $msg = 'Exception while fetching XRP price: ' . $e->getMessage();
            $this->error($msg);
            SlackNotifier::error($msg);
            return 1;
        }

        return 0;
    }
}
