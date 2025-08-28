<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\XrpPrice;
use Carbon\Carbon;

class FetchXRPPrice extends Command
{
    protected $signature = 'xrp:fetch-price';
    protected $description = 'Fetch XRP/USD price from CoinGecko';

    public function handle()
    {
        $this->info('Fetching XRP price...');

        $response = Http::get('https://api.coingecko.com/api/v3/simple/price', [
            'ids' => 'ripple',
            'vs_currencies' => 'usd',
        ]);

        if ($response->failed()) {
            $this->error('Failed to fetch price.');
            return 1;
        }

        $price = $response->json()['ripple']['usd'];

        // Opslaan in database
        XrpPrice::create([
            'price_usd' => $price,
        ]);

        $this->info("Price saved: $price USD");

        // Verwijder oude prijzen ouder dan 1 week
        $deleted = XrpPrice::where('created_at', '<', Carbon::now()->subWeek())->delete();
        $this->info("Deleted $deleted old records.");

        return 0;
    }
}
