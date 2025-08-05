<?php

// app/Console/Commands/SyncNfts.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Nft;
use Illuminate\Support\Facades\Http;

class SyncNfts extends Command
{
    protected $signature = 'nfts:sync {--issuer=} {--taxon=}';
    protected $description = 'Sync NFTs from the Bithomp API by issuer and taxon using marker-based pagination.';

    public function handle()
    {
        $issuer = $this->option('issuer');
        $taxon = $this->option('taxon');

        if (!$issuer) {
            $this->error('Issuer is required.');
            return;
        }

        $marker = null;
        $seenIds = [];

        do {
            $this->info($issuer);
            $this->info($taxon);
            $response = Http::withHeaders([
                'x-bithomp-token' => env('BITHOMP_API_KEY'),
            ])->get('https://bithomp.com/api/v2/nfts', [
                'issuer' => $issuer,
                'taxon' => $taxon,
//                'assets' => true,
                'limit' => 100,
                'marker' => $marker,
            ]);

            if (!$response->successful()) {
                var_dump($response->reason());
                $this->error("Request failed with status: {$response->status()}");
                return;
            }

            $data = $response->json();
            $nfts = $data['nfts'] ?? [];

            foreach ($nfts as $nft) {
                $nftokenId = $nft['nftokenID'];
                $seenIds[] = $nftokenId;

                Nft::updateOrCreate(
                    ['nftoken_id' => $nftokenId],
                    [
                        'issuer' => $nft['issuer'],
                        'owner' => $nft['owner'],
                        'nftoken_taxon' => $nft['nftokenTaxon'] ?? null,
                        'transfer_fee' => $nft['transferFee'] ?? null,
                        'uri' => $nft['uri'] ?? null,
                        'url' => $nft['url'] ?? null,
                        'flags' => $nft['flags'] ?? [],
                        'assets' => $nft['assets'] ?? [],
                        'metadata' => $nft['metadata'] ?? [],
                        'sequence' => $nft['sequence'] ?? null,
                    ]
                );
            }

            $this->info('Fetched ' . count($nfts) . ' NFTs...');
            $marker = $data['marker'] ?? null;

        } while ($marker);

        if (!empty($seenIds)) {
            $deleted = Nft::where('issuer', $issuer)
                ->whereNotIn('nftoken_id', $seenIds)
                ->delete();

            $this->info("Deleted $deleted missing NFTs.");
        }

        $this->info('NFT sync completed.');
    }
}
