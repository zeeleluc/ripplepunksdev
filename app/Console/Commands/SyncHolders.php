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

        // Aggregate current NFT ownership by wallet
        $ownerCounts = Nft::select('owner', DB::raw('COUNT(*) as holdings'))
            ->groupBy('owner')
            ->pluck('holdings', 'owner');

        $processed = [];
        $addedCount = 0;
        $updatedCount = 0;

        foreach ($ownerCounts as $wallet => $count) {
            $holder = Holder::where('wallet', $wallet)->first();

            $badges = Holder::calculateBadges($wallet);
            $now = now();

            if (!$holder) {
                // New holder
                Holder::create([
                    'wallet'      => $wallet,
                    'holdings'    => $count,
                    'badges'      => $badges,
                    'last_seen_at'=> $now,
                ]);

                $addedCount++;
                SlackNotifier::info("🆕 New holder added: {$wallet} – holdings: {$count}");
            } else {
                // Only update if values actually changed
                $dirty = false;

                if ($holder->holdings !== $count) {
                    $dirty = true;
                    $msg = "Holder updated: {$wallet} – holdings changed from {$holder->holdings} to {$count}";
                    SlackNotifier::info("✏️ {$msg}");
                }

                if ($holder->badges !== $badges) {
                    $dirty = true;
                }

                // Always update last_seen_at if the record is touched
                if ($dirty || $holder->last_seen_at->lt($now->subMinutes(1))) {
                    $holder->update([
                        'holdings'     => $count,
                        'badges'       => $badges,
                        'last_seen_at' => $now,
                    ]);
                    if ($dirty) {
                        $updatedCount++;
                    }
                }
            }

            $processed[] = $wallet;
        }

        // Handle holders that no longer own NFTs
        $deletedCount = 0;
        Holder::whereNotIn('wallet', $processed)
            ->chunkById(100, function ($holders) use (&$deletedCount) {
                foreach ($holders as $holder) {
                    SlackNotifier::warning("❌ Holder removed: {$holder->wallet} – no NFTs remaining");
                    $holder->delete();
                    $deletedCount++;
                }
            });

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
