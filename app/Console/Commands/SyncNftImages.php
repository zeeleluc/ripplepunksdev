<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Nft;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use App\Helpers\SlackNotifier;

class SyncNftImages extends Command
{
    protected $signature = 'nfts:sync-images';
    protected $description = 'Sync missing NFT images to DigitalOcean Spaces as .png with retries and limited concurrency';

    protected int $concurrency = 20;
    protected int $maxRetries = 2;

    public function handle()
    {
        $missingCount = Nft::where('has_image', false)->orWhereNull('has_image')->count();

        if ($missingCount === 0) {
            SlackNotifier::info("🎉 All NFTs already synced.");
            return;
        }

        SlackNotifier::info("Found {$missingCount} NFTs missing images. Starting sync...");

        Nft::where('has_image', false)
            ->orWhereNull('has_image')
            ->chunk(100, function ($nfts) {
                $client = new Client([
                    'timeout' => 20,
                    'headers' => [
                        'User-Agent' => $this->randomUserAgent(),
                        'Accept' => 'image/*,*/*;q=0.8',
                        'Accept-Language' => 'en-US,en;q=0.9',
                        'Connection' => 'keep-alive',
                    ],
                ]);

                $promises = [];
                $active = 0;

                foreach ($nfts as $nft) {
                    $metadata = $nft->metadata ?? [];
                    $imageUrl = $metadata['image'] ?? null;

                    if (!$imageUrl) {
                        continue;
                    }

                    $urls = $this->resolveUrls($imageUrl);
                    $path = "ogs/{$nft->nft_id}.png";

                    if (Storage::disk('spaces')->exists($path)) {
                        $nft->has_image = true;
                        $nft->save();
                        continue;
                    }

                    $promises[] = $this->fetchWithRetries($client, $urls, $nft, $path, $this->maxRetries);

                    $active++;
                    if ($active >= $this->concurrency) {
                        Promise\Utils::settle($promises)->wait();
                        $promises = [];
                        $active = 0;
                    }
                }

                if (!empty($promises)) {
                    Promise\Utils::settle($promises)->wait();
                }
            });

        SlackNotifier::info("✅ Sync complete.");
    }

    private function fetchWithRetries(Client $client, array $urls, $nft, string $path, int $retries)
    {
        $url = $urls[0];

        return $client->getAsync($url)
            ->then(
                function ($response) use ($nft, $path) {
                    if ($response->getStatusCode() !== 200) return;

                    Storage::disk('spaces')->put($path, $response->getBody()->getContents(), 'public');

                    $nft->has_image = true;
                    $nft->save();

                    SlackNotifier::info("⬆️ Uploaded: {$path}");
                },
                function ($reason) use ($client, $urls, $nft, $path, $retries) {
                    if ($retries > 0) {
                        sleep(2);
                        SlackNotifier::info("🔄 Retrying NFT {$nft->nft_id}...");
                        return $this->fetchWithRetries($client, $urls, $nft, $path, $retries - 1);
                    }

                    SlackNotifier::error("❌ Could not fetch NFT {$nft->nft_id}: " . $reason->getMessage());
                }
            );
    }

    private function resolveUrls(string $url): array
    {
        if (str_starts_with($url, 'ipfs://')) {
            $cidPath = substr($url, 7);
            $encodedUri = urlencode("ipfs://{$cidPath}");

            return [
                "https://ipfs.io/ipfs/{$cidPath}",
                "https://cloudflare-ipfs.com/ipfs/{$cidPath}",
                "https://gateway.pinata.cloud/ipfs/{$cidPath}",
                "https://nftstorage.link/ipfs/{$cidPath}",
                "https://image-cdn-v2.bidds.com/api/image/?uri={$encodedUri}&collection=27779563&width=1000",
            ];
        }

        return [$url];
    }

    private function randomUserAgent(): string
    {
        $agents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:117.0) Gecko/20100101 Firefox/117.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 13_4) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Safari/605.1.15',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 16_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Mobile Safari/605.1.15',
            'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
        ];

        return $agents[array_rand($agents)];
    }
}
