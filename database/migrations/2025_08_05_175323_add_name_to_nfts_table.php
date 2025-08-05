<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nfts', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id')->index();
            $table->integer('nft_id')->nullable()->after('name')->index();
        });
    }

    public function down(): void
    {
        Schema::table('nfts', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('nft_id');
        });
    }
};
