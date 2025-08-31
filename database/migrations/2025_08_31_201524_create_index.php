<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('nfts', function (Blueprint $table) {
            $table->index('trait_checksum', 'idx_trait_checksum');
        });
    }

    public function down()
    {
        Schema::table('nfts', function (Blueprint $table) {
            $table->dropIndex('idx_trait_checksum');
        });
    }
};
