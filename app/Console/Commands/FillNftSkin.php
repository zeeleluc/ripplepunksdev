<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Nft;
use League\Csv\Reader;

class FillNftSkin extends Command
{
    protected $signature = 'nfts:fill-skin';
    protected $description = 'Fill the skin column of NFTs from CSV files';

    public function handle()
    {
        $this->info('Starting to fill skin column...');

        $csvFiles = [
            'first' => base_path('data/properties.csv'),
            'second' => base_path('data/properties2.csv'),
        ];

        // Load both CSV files once
        $csvData = [];
        foreach ($csvFiles as $key => $path) {
            if (!file_exists($path)) {
                $this->error("CSV file not found: $path");
                return 1;
            }
            $csv = Reader::createFromPath($path, 'r');
            $csv->setHeaderOffset(null); // no headers
            $records = iterator_to_array($csv->getRecords());
            $csvData[$key] = $records;
        }

        // Iterate all NFTs
        Nft::chunk(500, function ($nfts) use ($csvData) {
            foreach ($nfts as $nft) {
                $nftId = (int) $nft->nft_id;

                // Determine which CSV to use
                $row = null;
                if ($nftId <= 9999) {
                    $row = $csvData['first'][$nftId] ?? null;
                } else {
                    $row = $csvData['second'][$nftId - 10000] ?? null; // assuming second CSV starts at 10000
                }

                if ($row && isset($row[3])) {
                    $skin = trim($row[3]); // column 3 = skin
                    if ($skin !== '') {
                        $nft->skin = $skin;
                        $nft->save();
                        $this->info("Updated NFT #{$nftId} with skin: {$skin}");
                    }
                }
            }
        });

        $this->info('Finished filling skin column.');
        return 0;
    }
}
