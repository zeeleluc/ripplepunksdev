<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Nft;
use App\Models\Holder;

class SyncHolders extends Command
{
    protected $signature = 'holders:sync';
    protected $description = 'Sync holders table with NFT ownership data';

    public function handle()
    {
        $this->info('Starting holder sync...');

        $owners = Nft::select('owner')->distinct()->pluck('owner');

        $processed = [];

        foreach ($owners as $wallet) {
            $count = Nft::where('owner', $wallet)->count();

            if ($count > 0) {
                $badges = Holder::calculateBadges($wallet);

                Holder::updateOrCreate(
                    ['wallet' => $wallet],
                    [
                        'badges' => $badges,
                        'holdings' => $count,
                        'last_seen_at' => now(),
                    ]
                );

                $processed[] = $wallet;
            }
        }

        // Remove holders with no NFTs anymore
        $deleted = Holder::whereNotIn('wallet', $processed)->delete();

        $this->info('Holders updated: ' . count($processed));
        $this->info('Holders removed: ' . $deleted);
        $this->info('Holder sync completed.');

        return Command::SUCCESS;
    }
}
