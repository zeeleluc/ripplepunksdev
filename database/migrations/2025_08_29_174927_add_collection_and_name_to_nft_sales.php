<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('nft_sales', function (Blueprint $table) {
            $table->string('collection_name')->nullable()->after('marketplace');
            $table->string('nft_name')->nullable()->after('collection_name');
        });
    }

    public function down()
    {
        Schema::table('nft_sales', function (Blueprint $table) {
            $table->dropColumn(['collection_name', 'nft_name']);
        });
    }

};
