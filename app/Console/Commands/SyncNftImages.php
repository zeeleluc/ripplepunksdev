<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Models\Nft;
use App\Helpers\SlackNotifier;

class SyncNftImages extends Command
{
    protected $signature = 'nfts:sync-images';
    protected $description = 'Sync missing NFT images to DigitalOcean Spaces as .png';

    // Limit per run
    protected int $limit = 100;

    public function handle()
    {
        // Fetch NFTs where has_image is false or null
        $nfts = Nft::where('has_image', false)
            ->orWhereNull('has_image')
            ->limit($this->limit)
            ->get();

        $total = $nfts->count();
        SlackNotifier::info("Total NFTs to process: {$total}");

        if ($total === 0) {
            SlackNotifier::info("🎉 All NFTs already synced.");
            return;
        }

        foreach ($nfts as $nft) {
            $metadata = $nft->metadata ?? [];
            $imageUrl = $metadata['image'] ?? null;
            $path = "ogs/{$nft->nft_id}.png";

            if (!$imageUrl) {
                $this->line("⚠️ NFT {$nft->nft_id} has no image URL, skipping.");
                continue;
            }

            // Already exists on DO
            if (Storage::disk('spaces')->exists($path)) {
                if (!$nft->has_image) {
                    $nft->has_image = true;
                    $nft->save();
                    $this->line("✅ NFT {$nft->nft_id} exists on DO, flag updated.");
                } else {
                    $this->line("✅ NFT {$nft->nft_id} exists on DO.");
                }
                continue;
            }

            // Fetch and upload
            try {
                $response = Http::timeout(20)->get($imageUrl);
                if ($response->ok()) {
                    Storage::disk('spaces')->put($path, $response->body(), 'public');
                    $nft->has_image = true;
                    $nft->save();
                    $this->line("⬆️ Uploaded NFT {$nft->nft_id}");
                } else {
                    $this->line("❌ Failed to fetch NFT {$nft->nft_id} (HTTP {$response->status()})");
                }
            } catch (\Exception $e) {
                $this->line("❌ Error fetching NFT {$nft->nft_id}: {$e->getMessage()}");
            }
        }

        SlackNotifier::info("✅ NFT image sync run complete. Processed {$total} NFTs.");
    }
}
