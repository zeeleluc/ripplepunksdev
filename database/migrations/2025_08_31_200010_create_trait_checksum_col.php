<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('nfts', function (Blueprint $table) {
            $table->string('trait_checksum', 32)->nullable()->after('total_accessories');
        });
    }

    public function down()
    {
        Schema::table('nfts', function (Blueprint $table) {
            $table->dropColumn('trait_checksum');
        });
    }
};
