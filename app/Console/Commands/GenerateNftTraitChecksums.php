<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Nft;

class GenerateNftTraitChecksums extends Command
{
    protected $signature = 'nfts:generate-checksums';
    protected $description = 'Generate and populate trait_checksum for NFTs based on non-empty/true trait attributes';

    /**
     * Only trait-related columns (skip id, timestamps, etc.)
     */
    protected array $traitColumns = [
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

    public function handle()
    {
        $this->info('Generating trait checksums for NFTs...');

        Nft::chunk(100, function ($nfts) {
            foreach ($nfts as $nft) {
                $activeTraits = $this->extractActiveTraits($nft);
                $nft->trait_checksum = $this->generateChecksum($activeTraits, $nft->nft_id);
                $nft->save();
            }
        });

        $this->info('Trait checksums generated successfully.');
    }

    /**
     * Extract only "active" traits:
     * - keep strings if not empty
     * - keep ints/bools only if == 1 (skip 0 and null)
     */
    protected function extractActiveTraits(Nft $nft): array
    {
        $attributes = $nft->metadata['attributes'];
        $result = [];

// Add color & type directly
        foreach ($attributes as $attr) {
            if ($attr['trait_type'] === 'Color') {
                $result['color'] = $attr['value'];
            }

            if ($attr['trait_type'] === 'Type') {
                $result['type'] = $attr['value'];
            }
        }

// Add skin from outside metadata
        $result['skin'] = $nft->skin;

// Add accessories as individual keys with value = 1
        foreach ($attributes as $attr) {
            if ($attr['trait_type'] === 'Accessory') {
                // skip generic "3 Attributes" kind of counters
                if (stripos($attr['value'], 'Attributes') === false) {
                    $result[$attr['value']] = 1;
                }
            }
        }

        return $result;

    }

    /**
     * Build a deterministic checksum from active traits
     */
    protected function generateChecksum(array $activeTraits, $nftId): string
    {
        // Sort by key for consistent order
        ksort($activeTraits);

        // Turn into key=value pairs
        $joined = implode(',', array_map(
            fn($k, $v) => "$k=$v",
            array_keys($activeTraits),
            $activeTraits
        ));

        return md5($joined);
    }
}
