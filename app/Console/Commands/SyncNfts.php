<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Nft;

class SyncNfts extends Command
{
    protected $signature = 'nfts:sync {--issuer=} {--taxon=}';
    protected $description = 'Sync NFTs from the Bithomp API by issuer and taxon using marker-based pagination.';

    public function handle()
    {
        $issuer = $this->option('issuer');
        $taxon = $this->option('taxon');

        if (!$issuer) {
            $this->error('❌ Issuer is required.');
            return;
        }

        $marker = null;
        $seenIds = [];

        do {
            $this->info("📡 Fetching NFTs (issuer={$issuer}, taxon={$taxon}, marker={$marker})");

            $response = Http::withHeaders([
                'x-bithomp-token' => env('BITHOMP_API_KEY'),
            ])->get('https://bithomp.com/api/v2/nfts', [
                'issuer' => $issuer,
                'taxon' => $taxon,
                'limit' => 100,
                'marker' => $marker,
            ]);

            if (!$response->successful()) {
                $this->error("❌ Request failed with status: {$response->status()}");
                return;
            }

            $data = $response->json();
            $nfts = $data['nfts'] ?? [];

            foreach ($nfts as $nft) {
                $nftokenId = $nft['nftokenID'];
                $seenIds[] = $nftokenId;

                $metadata = $nft['metadata'] ?? [];
                $name = $metadata['name'] ?? null;

                // Extract nft_id from name (e.g. "#6508")
                $nftId = null;
                if ($name && preg_match('/#(\d+)/', $name, $matches)) {
                    $nftId = (int) $matches[1];
                }

                // Extract traits
                $color = null;
                $type = null;
                $totalAccessories = 0;
                $accessoryFlags = [];

                $attributes = $metadata['attributes'] ?? [];
                foreach ($attributes as $attr) {
                    $value = $attr['value'] ?? '';
                    $traitType = $attr['trait_type'] ?? '';

                    if ($traitType === 'Color') {
                        $color = $value;
                    } elseif ($traitType === 'Type') {
                        $type = $value;
                    } elseif ($traitType === 'Accessory') {
                        $totalAccessories++;

                        // Map known special cases
                        $specialMap = [
                            '3D Glasses' => '3d_glasses',
                            'Do-rag' => 'do_rag',
                        ];

                        if (isset($specialMap[$value])) {
                            $column = $specialMap[$value];
                        } else {
                            // Only use snake_case if it does not start with a number
                            $column = Str::snake($value);
                            if (is_numeric(substr($column, 0, 1))) {
                                // Skip columns that would start with a number
                                continue;
                            }
                        }

                        if ($column) {
                            $accessoryFlags[$column] = true;
                        }
                    }
                }


                $totalAccessories--; // Accessory "total attributes"

                $record = [
                    'issuer' => $nft['issuer'],
                    'owner' => $nft['owner'],
                    'nftoken_taxon' => $nft['nftokenTaxon'] ?? null,
                    'transfer_fee' => $nft['transferFee'] ?? null,
                    'uri' => $nft['uri'] ?? null,
                    'url' => $nft['url'] ?? null,
                    'flags' => $nft['flags'] ?? [],
                    'assets' => $nft['assets'] ?? [],
                    'metadata' => $metadata,
                    'sequence' => $nft['sequence'] ?? null,
                    'name' => $name,
                    'nft_id' => $nftId,
                    'color' => $color,
                    'type' => $type,
                    'total_accessories' => $totalAccessories,
                    'burned_at' => null,
                ];

                $existing = Nft::where('nftoken_id', $nftokenId)->first();

                if ($existing) {
                    foreach ($accessoryFlags as $col => $val) {
                        $record[$col] = true;
                    }
                    $existing->fill($record)->save();
                } else {
                    $allColumns = Schema::getColumnListing('nfts');
                    $reserved = [
                        'id','nftoken_id','issuer','owner','nftoken_taxon','transfer_fee',
                        'uri','url','flags','assets','metadata','sequence','name','nft_id',
                        'created_at','updated_at','color','type','total_accessories','burned_at'
                    ];
                    foreach ($allColumns as $col) {
                        if (!in_array($col, $reserved)) {
                            $record[$col] = false;
                        }
                    }
                    foreach ($accessoryFlags as $col => $val) {
                        $record[$col] = true;
                    }

                    Nft::create(array_merge(['nftoken_id' => $nftokenId], $record));
                }
            }

            $this->info('✅ Synced ' . count($nfts) . ' NFTs...');
            $marker = $data['marker'] ?? null;

        } while ($marker);

        if (!empty($seenIds)) {
            $deleted = Nft::where('issuer', $issuer)
                ->whereNotIn('nftoken_id', $seenIds)
                ->delete();
            $this->info("🗑️ Deleted $deleted missing NFTs.");
        }

        $this->info('🎉 NFT sync completed');
    }
}
