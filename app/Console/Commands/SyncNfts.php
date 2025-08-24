<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;
use App\Models\Nft;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        // Ensure base columns exist with indexes
        $this->ensureColumn('nfts', 'color', 'string', true);
        $this->ensureColumn('nfts', 'type', 'string', true);
        $this->ensureColumn('nfts', 'total_accessories', 'integer', true);

        do {
            $this->info("Fetching NFTs for issuer={$issuer}, taxon={$taxon}, marker={$marker}");

            $response = Http::withHeaders([
                'x-bithomp-token' => env('BITHOMP_API_KEY'),
            ])->get('https://bithomp.com/api/v2/nfts', [
                'issuer' => $issuer,
                'taxon' => $taxon,
                'limit' => 100,
                'marker' => $marker,
            ]);

            if (!$response->successful()) {
                $this->error("Request failed with status: {$response->status()}");
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

                        // Convert to snake_case and validate column name
                        $column = Str::snake($value);
                        if (!$column || is_numeric($column[0])) {
                            continue; // Skip invalid columns
                        }

                        $accessoryFlags[$column] = true;
                        $this->ensureColumn('nfts', $column, 'boolean', false); // No index for accessories
                    }
                }

                // Reset all accessory booleans to false
                $allColumns = Schema::getColumnListing('nfts');
                $reserved = [
                    'id', 'nftoken_id', 'issuer', 'owner', 'nftoken_taxon', 'transfer_fee',
                    'uri', 'url', 'flags', 'assets', 'metadata', 'sequence', 'name', 'nft_id',
                    'created_at', 'updated_at', 'color', 'type', 'total_accessories', 'burned_at'
                ];

                foreach ($allColumns as $col) {
                    if (!in_array($col, $reserved)) {
                        $accessoryFlags[$col] = false;
                    }
                }

                // Build record
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
                    'burned_at' => null, // prevents MySQL datetime errors
                ];

                // Merge accessory flags
                $record = array_merge($record, $accessoryFlags);

                Nft::updateOrCreate(['nftoken_id' => $nftokenId], $record);
            }

            $this->info('Fetched ' . count($nfts) . ' NFTs...');
            $marker = $data['marker'] ?? null;

        } while ($marker);

        // Delete NFTs no longer returned by API
        if (!empty($seenIds)) {
            $deleted = Nft::where('issuer', $issuer)
                ->whereNotIn('nftoken_id', $seenIds)
                ->delete();

            $this->info("Deleted $deleted missing NFTs.");
        }

        $this->info('NFT sync completed ✅');
    }

    /**
     * Ensure a column exists in the table, create it with an index if specified.
     */
    protected function ensureColumn(string $table, string $column, string $type, bool $index = false): void
    {
        if (!Schema::hasColumn($table, $column)) {
            Schema::table($table, function (Blueprint $blueprint) use ($table, $column, $type, $index) {
                switch ($type) {
                    case 'string':
                        $blueprint->string($column)->nullable();
                        break;
                    case 'integer':
                        $blueprint->integer($column)->nullable();
                        break;
                    case 'boolean':
                        $blueprint->boolean($column)->default(false);
                        break;
                }
                if ($index) {
                    $indexName = 'idx_' . Str::snake($table) . '_' . Str::snake($column);
                    try {
                        $blueprint->index($column, $indexName);
                    } catch (\Exception $e) {
                        // Log warning if index creation fails (e.g., due to 64-index limit)
                        Log::warning('Failed to create index for column', [
                            'table' => $table,
                            'column' => $column,
                            'error' => $e->getMessage(),
                        ]);
                        $this->warn("Could not create index for {$column}: {$e->getMessage()}");
                    }
                }
            });

            $this->info("🆕 Added missing column: {$column} ({$type})" . ($index ? ' with index' : ''));
        }
    }
}
