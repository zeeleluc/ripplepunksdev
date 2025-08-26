<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('nfts', function (Blueprint $table) {
            // Composite index for common filters
            $table->index(['color', 'type', 'total_accessories'], 'idx_nfts_filter');
            // Index for sorting by nft_id
            $table->index('nft_id', 'idx_nfts_nft_id');
        });
    }

    public function down()
    {
        Schema::table('nfts', function (Blueprint $table) {
            $table->dropIndex('idx_nfts_filter');
            $table->dropIndex('idx_nfts_nft_id');
        });
    }
};
