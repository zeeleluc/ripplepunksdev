<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Nft;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;

class SyncNftImages extends Command
{
    protected $signature = 'nfts:sync-images';
    protected $description = 'Sync missing NFT images to DigitalOcean Spaces in parallel using Guzzle promises';

    public function handle()
    {
        // Only NFTs without images
        $total = Nft::where('has_image', false)->orWhereNull('has_image')->count();
        if ($total === 0) {
            $this->info("🎉 All NFTs already synced.");
            return;
        }

        $this->info("Found {$total} NFTs missing images. Starting sync...");

        // Process in chunks
        Nft::where('has_image', false)
            ->orWhereNull('has_image')
            ->chunk(500, function ($nfts) {
                $client = new Client([
                    'timeout' => 20,
                    'headers' => [
                        'User-Agent' => $this->randomUserAgent(),
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                        'Accept-Language' => 'en-US,en;q=0.9',
                        'Connection' => 'keep-alive',
                    ]
                ]);

                $promises = [];

                foreach ($nfts as $nft) {
                    $path = "ogs/{$nft->nft_id}.png";

                    $metadata = $nft->metadata ?? [];
                    $imageUrl = $metadata['image'] ?? null;
                    if (!$imageUrl) {
                        $this->warn("⚠️ No image URL for NFT {$nft->nft_id}");
                        continue;
                    }

                    $urls = $this->resolveUrls($imageUrl);

                    foreach ($urls as $url) {
                        $promises[] = $client->getAsync($url)
                            ->then(
                                function ($response) use ($nft, $path) {
                                    if ($response->getStatusCode() === 200) {
                                        Storage::disk('spaces')->put($path, $response->getBody()->getContents(), 'public');
                                        $nft->has_image = true;
                                        $nft->save();
                                        $this->line("⬆️ Uploaded: {$path}");
                                    }
                                },
                                function ($reason) use ($nft) {
                                    $this->error("❌ Could not fetch NFT {$nft->nft_id}: " . $reason->getMessage());
                                }
                            );
                        break; // only try one URL per NFT
                    }
                }

                Promise\Utils::settle($promises)->wait();
            });

        $this->info("✅ Sync complete.");
    }

    private function resolveUrls(string $url): array
    {
        if (str_starts_with($url, 'ipfs://')) {
            $cidPath = substr($url, 7);

            // Encode full ipfs:// URI for Bidds CDN
            $encodedUri = urlencode("ipfs://{$cidPath}");

            return [
//                "https://ipfs.io/ipfs/{$cidPath}",
//                "https://cloudflare-ipfs.com/ipfs/{$cidPath}",
//                "https://gateway.pinata.cloud/ipfs/{$cidPath}",
//                "https://nftstorage.link/ipfs/{$cidPath}",
                // ✅ new bidds.com fallback
                "https://image-cdn-v2.bidds.com/api/image/?uri={$encodedUri}&collection=27779563&width=1000",
            ];
        }

        return [$url];
    }

    private function randomUserAgent()
    {
        $agents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/117.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Safari/605.1.15',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
        ];

        return $agents[array_rand($agents)];
    }
}
