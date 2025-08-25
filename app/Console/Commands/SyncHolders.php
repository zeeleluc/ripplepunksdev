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
                $oldHoldings = $holder->holdings ?? 0;

                $holder->badges = Holder::calculateBadges($wallet);
                $holder->holdings = $count;
                $holder->last_seen_at = now();
                $holder->save();

                $processed[] = $wallet;

                if ($holder->wasRecentlyCreated) {
                    $addedCount++;
                    SlackNotifier::info("New holder added: {$wallet} – holdings: {$count}");
                } elseif ($oldHoldings != $count) {
                    $updatedCount++;
                    SlackNotifier::info("Holder updated: {$wallet} – holdings changed from {$oldHoldings} to {$count}");
                }
            }
        }

        // Handle holders with no NFTs anymore
        $deleted = Holder::whereNotIn('wallet', $processed)->get();
        $deletedCount = $deleted->count();

        if ($deletedCount > 0) {
            foreach ($deleted as $holder) {
                SlackNotifier::warning("Holder removed: {$holder->wallet} – no NFTs remaining");
            }
            Holder::whereNotIn('wallet', $processed)->delete();
        }

        // Send summary only if there are any changes
        if ($addedCount > 0 || $updatedCount > 0 || $deletedCount > 0) {
            SlackNotifier::info(
                "Holder sync completed ✅\n" .
                "Total processed: " . count($processed) . "\n" .
                "Added: {$addedCount}\n" .
                "Updated: {$updatedCount}\n" .
                "Removed: {$deletedCount}"
            );
        }

        return Command::SUCCESS;
    }
}
