<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Nft extends Model
{
    protected $guarded = [];

    protected $casts = [
        'flags' => 'array',
        'assets' => 'array',
        'metadata' => 'array',
        'burned_at' => 'datetime',
    ];

    public $timestamps = false;

    public static function ctoWalletCount(): int
    {
        return static::where('owner', env('CTO_WALLET'))->count();
    }

    public static function projectWalletCount(): int
    {
        return static::where('owner', env('PROJECT_WALLET'))->count();
    }

    public static function rewardsWalletCount(): int
    {
        return static::where('owner', env('REWARDS_WALLET'))->count();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'owner', 'wallet');
    }

    public static function genericCount(?int $fromId = null, ?int $toId = null, ?array $search = null): int
    {
        $query = static::query();

        if (!is_null($fromId) && !is_null($toId)) {
            $query->whereBetween('nft_id', [$fromId, $toId]);
        }

        if (!empty($search)) {
            foreach ($search as $column => $value) {
                $query->where($column, 'like', "%{$value}%");
            }
        }

        return $query->count();
    }

    public static function getAttributeColumns(): array
    {
        return  [
            'earring','normal_beard_black',
            'wild_hair','muttonchops','cigarette','small_shades','eye_patch',
            'ripple_short','choker','regular_shades','wild_white_hair','black_lipstick',
            'clown_eyes_green','clown_hair_green','handlebars','crazy_hair','cap_forward',
            'smile','goat','mohawk','nerd_glasses','mohawk_dark','medical_mask','cap',
            'hot_ripple_lipstick','tassle_hat','v_r','stringy_hair','chinstrap',
            'horned_rim_glasses','blue_eye_shadow','tiara','welding_goggles','normal_beard',
            'clown_nose','pipe','peak_spike','classic_shades','ripple_mohawk',
            'purple_eye_shadow','mole','headband','clown_eyes_blue','straight_hair_ripple_blue',
            'silver_chain','mohawk_thin','ripple_hair','spots','shaved_head','eye_mask',
            'big_beard','bandana','shadow_beard','frown','front_beard_dark','knitted_cap',
            'big_shades','green_eye_shadow','ripple_bob','pigtails','purple_lipstick',
            'hoodie','frumpy_hair','police_cap','straight_hair_ripple_dark','pink_with_hat',
            'cowboy_hat','straight_hair_ripple_light','front_beard','fedora','half_shaved',
            'messy_hair','gold_chain','luxurious_beard','wild_ripple','vampire_hair',
            'do_rag','orange_side','mustache','buck_teeth','dark_hair','beanie','top_hat',
            'rosy_cheeks','vape','pilot_helmet','blue_bandana','3d_glasses',
        ];
    }

    /**
     * 🔎 Get all duplicate NFTs (same trait combination).
     */
    public static function getDuplicateGroups()
    {
        $columns = array_merge(['color','skin','type','total_accessories'], self::getAttributeColumns());

        // First: find trait-combos that appear > 1
        $dupes = static::select($columns)
            ->selectRaw('COUNT(*) as dup_count')
            ->groupBy($columns)
            ->having('dup_count', '>', 1)
            ->get();

        // Then: fetch the actual NFTs for each duplicate combo
        $groups = [];
        foreach ($dupes as $combo) {
            $query = static::query();
            foreach ($columns as $col) {
                $query->where($col, $combo->$col);
            }
            $nfts = $query->get();
            $groups[] = [
                'traits' => $combo->only($columns),
                'nfts'   => $nfts,
            ];
        }

        return $groups;
    }

    public function getImageUrl(): ?string
    {
        $path = "ogs/{$this->nft_id}.png";

        if (Storage::disk('spaces')->exists($path)) {
            return Storage::disk('spaces')->url($path);
        }

        // Return a placeholder image if the file doesn't exist
        return asset('images/nft-placeholder.png');
    }

    /**
     * 🎯 Get all NFTs with blue_bandana = 1.
     */
    public static function getBlueBandanas()
    {
        return static::where('blue_bandana', 1)->get();
    }
}
