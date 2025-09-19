<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Nft;
use App\Helpers\SlackNotifier;

class CheckNftIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nfts:check-missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for missing NFT IDs and notify via Slack';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Fetching NFT IDs...');

        // Get all nft_ids from the database
        $nftIds = Nft::pluck('nft_id')->sort()->values()->all();

        $this->info('Checking for missing IDs...');

        $missingIds = [];

        $maxId = 11759;

        for ($i = 0; $i <= $maxId; $i++) {
            if (!in_array($i, $nftIds)) {
                $missingIds[] = $i;
            }
        }

        if ($missingIds) {
            $missingList = implode(', ', $missingIds);
            $this->warn("Missing NFT IDs: {$missingList}");
            SlackNotifier::warning("Missing NFT IDs: {$missingList}");
        }

        return 0;
    }
}
