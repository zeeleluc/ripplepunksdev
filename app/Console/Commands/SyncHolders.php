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

        // 2️⃣ Load all existing holders in one query
        $holders = Holder::all()->keyBy('wallet'); // [wallet => Holder model]

        $processed = [];
        $addedCount = 0;
        $updatedCount = 0;

        foreach ($ownerCounts as $wallet => $count) {
            $badges = Holder::calculateBadges($wallet);
            $holder = $holders->get($wallet);

            if (!$holder) {
                // New holder
                Holder::create([
                    'wallet'   => $wallet,
                    'holdings' => $count,
                    'badges'   => $badges,
                ]);

                $addedCount++;
                SlackNotifier::info("🆕 New holder added: {$wallet} – holdings: {$count}");
            } else {
                $changes = [];

                if ($holder->holdings !== $count) {
                    $changes['holdings'] = $count;
                    SlackNotifier::info("✏️ Holder updated: {$wallet} – holdings changed from {$holder->holdings} to {$count}");
                    $updatedCount++;
                }

                if ($holder->badges !== $badges) {
                    $changes['badges'] = $badges;
                }

                if (!empty($changes)) {
                    $holder->update($changes);
                }
            }

            $processed[] = $wallet;
        }

        // 3️⃣ Handle holders that no longer own NFTs
        $deletedCount = Holder::whereNotIn('wallet', $processed)->delete();

        if ($deletedCount > 0) {
            foreach (array_diff($holders->keys()->toArray(), $processed) as $wallet) {
                SlackNotifier::warning("❌ Holder removed: {$wallet} – no NFTs remaining");
            }
        }

        // Summary
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
