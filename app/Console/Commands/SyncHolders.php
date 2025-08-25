<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Nft;
use App\Models\Holder;
use App\Helpers\SlackNotifier;

class SyncHolders extends Command
{
    protected $signature = 'holders:sync';
    protected $description = 'Sync holders table with NFT ownership data';

    public function handle()
    {
        $owners = Nft::select('owner')->distinct()->pluck('owner');

        $processed = [];
        $addedCount = 0;
        $updatedCount = 0;

        foreach ($owners as $wallet) {
            $count = Nft::where('owner', $wallet)->count();

            if ($count > 0) {
                $holder = Holder::firstOrNew(['wallet' => $wallet]);

                $badges = Holder::calculateBadges($wallet);

                $oldHoldings = $holder->holdings ?? 0;

                $holder->badges = $badges;
                $holder->holdings = $count;
                $holder->last_seen_at = now();
                $holder->save();

                $processed[] = $wallet;

                if (!$holder->wasRecentlyCreated && $oldHoldings != $count) {
                    $updatedCount++;
                    SlackNotifier::info("Holder updated: {$wallet} – holdings changed from {$oldHoldings} to {$count}");
                }

                if ($holder->wasRecentlyCreated) {
                    $addedCount++;
                    SlackNotifier::info("New holder added: {$wallet} – holdings: {$count}");
                }
            }
        }

        // Remove holders with no NFTs anymore
        $deleted = Holder::whereNotIn('wallet', $processed)->get();
        $deletedCount = $deleted->count();

        foreach ($deleted as $holder) {
            SlackNotifier::warning("Holder removed: {$holder->wallet} – no NFTs remaining");
        }

        Holder::whereNotIn('wallet', $processed)->delete();

        $totalProcessed = count($processed);

        SlackNotifier::info(
            "Holder sync completed ✅\n" .
            "Total processed: {$totalProcessed}\n" .
            "Added: {$addedCount}\n" .
            "Updated: {$updatedCount}\n" .
            "Removed: {$deletedCount}"
        );

        return Command::SUCCESS;
    }
}
