<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('nft_sales', function (Blueprint $table) {
            $table->string('sale_type')->nullable()->after('marketplace');
        });
    }

    public function down()
    {
        Schema::table('nft_sales', function (Blueprint $table) {
            $table->dropColumn('sale_type');
        });
    }
};
