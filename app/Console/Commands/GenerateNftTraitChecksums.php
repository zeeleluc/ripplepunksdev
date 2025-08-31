<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Nft;
use Illuminate\Support\Facades\DB;

class GenerateNftTraitChecksums extends Command
{
    protected $signature = 'nfts:generate-checksums';
    protected $description = 'Generate and populate trait_checksum for NFTs based on trait columns';

    public function handle()
    {
        $columns = [
            'color', 'skin', 'type', 'total_accessories', 'earring', 'normal_beard_black',
            'wild_hair', 'muttonchops', 'cigarette', 'small_shades', 'eye_patch',
            'ripple_short', 'choker', 'regular_shades', 'wild_white_hair', 'black_lipstick',
            'clown_eyes_green', 'clown_hair_green', 'handlebars', 'crazy_hair', 'cap_forward',
            'smile', 'goat', 'mohawk', 'nerd_glasses', 'mohawk_dark', 'medical_mask', 'cap',
            'hot_ripple_lipstick', 'tassle_hat', 'v_r', 'stringy_hair', 'chinstrap',
            'horned_rim_glasses', 'blue_eye_shadow', 'tiara', 'welding_goggles', 'normal_beard',
            'clown_nose', 'pipe', 'peak_spike', 'classic_shades', 'ripple_mohawk',
            'purple_eye_shadow', 'mole', 'headband', 'clown_eyes_blue', 'straight_hair_ripple_blue',
            'silver_chain', 'mohawk_thin', 'ripple_hair', 'spots', 'shaved_head', 'eye_mask',
            'big_beard', 'bandana', 'shadow_beard', 'frown', 'front_beard_dark', 'knitted_cap',
            'big_shades', 'green_eye_shadow', 'ripple_bob', 'pigtails', 'purple_lipstick',
            'hoodie', 'frumpy_hair', 'police_cap', 'straight_hair_ripple_dark', 'pink_with_hat',
            'cowboy_hat', 'straight_hair_ripple_light', 'front_beard', 'fedora', 'half_shaved',
            'messy_hair', 'gold_chain', 'luxurious_beard', 'wild_ripple', 'vampire_hair',
            'do_rag', 'orange_side', 'mustache', 'buck_teeth', 'dark_hair', 'beanie', 'top_hat',
            'rosy_cheeks', 'vape', 'pilot_helmet', 'blue_bandana', '3d_glasses',
        ];

        $this->info('Generating trait checksums for NFTs...');

        // Process NFTs in chunks to avoid memory issues
        Nft::chunk(100, function ($nfts) use ($columns) {
            foreach ($nfts as $nft) {
                // Concatenate trait values, handling nulls
                $traitValues = array_map(function ($column) use ($nft) {
                    return $nft->$column ?? '';
                }, $columns);

                // Generate MD5 checksum
                $checksum = md5(implode(',', $traitValues));

                // Update the NFT
                $nft->trait_checksum = $checksum;
                $nft->save();
            }
        });

        $this->info('Trait checksums generated successfully.');
    }
}
