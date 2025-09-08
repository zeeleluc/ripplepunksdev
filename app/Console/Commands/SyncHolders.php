<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Nft;
use App\Models\Holder;
use App\Helpers\SlackNotifier;

class SyncHolders extends Command
{
    protected $signature = 'holders:sync';
    protected $description = 'Sync holders table with NFT ownership data';

    public function handle()
    {
        $this->info('🔄 Starting holder sync...');

        // 1️⃣ Aggregate NFT ownership by wallet
        $ownerCounts = Nft::select('owner', DB::raw('COUNT(*) as holdings'))
            ->groupBy('owner')
            ->pluck('holdings', 'owner'); // [wallet => holdings]

        $processed = [];
        $addedCount = 0;
        $updatedCount = 0;

        foreach ($ownerCounts as $wallet => $count) {
            // Normalize wallet string to prevent accidental duplicates
            $wallet = trim(strtolower($wallet));

            $badges = Holder::calculateBadges($wallet);
            $votingPower = Holder::calculateVotingPower($wallet);

            $holder = Holder::updateOrCreate(
                ['wallet' => $wallet],
                [
                    'holdings'     => $count,
                    'badges'       => $badges,
                    'voting_power' => $votingPower,
                ]
            );

            if ($holder->wasRecentlyCreated) {
                $addedCount++;
                SlackNotifier::info("🆕 New holder added: {$wallet} – holdings: {$count}");
            } else {
                // Detect if actual changes occurred
                if ($holder->wasChanged()) {
                    $updatedCount++;
                    SlackNotifier::info("✏️ Holder updated: {$wallet}");
                }
            }

            $processed[] = $wallet;
        }

        // 2️⃣ Handle holders that no longer own NFTs
        $deleted = Holder::whereNotIn('wallet', $processed)->get();
        $deletedCount = $deleted->count();

        if ($deletedCount > 0) {
            foreach ($deleted as $holder) {
                SlackNotifier::warning("❌ Holder removed: {$holder->wallet} – no NFTs remaining");
                $holder->delete();
            }
        }

        // 3️⃣ Summary
        if ($addedCount || $updatedCount || $deletedCount) {
            SlackNotifier::info(
                "✅ Holder sync completed\n" .
                "Processed: " . count($processed) . "\n" .
                "Added: {$addedCount}\n" .
                "Updated: {$updatedCount}\n" .
                "Removed: {$deletedCount}"
            );
        } else {
            $this->info('ℹ️ No changes detected — skipped DB writes.');
        }

        $this->info('🎉 Holder sync finished.');

        return Command::SUCCESS;
    }
}
