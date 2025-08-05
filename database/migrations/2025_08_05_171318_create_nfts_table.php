<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nfts', function (Blueprint $table) {
            $table->id();
            $table->string('nftoken_id')->unique();
            $table->string('issuer');
            $table->string('owner')->nullable();
            $table->unsignedBigInteger('nftoken_taxon')->nullable();
            $table->unsignedInteger('transfer_fee')->nullable();
            $table->string('uri')->nullable();
            $table->string('url')->nullable();
            $table->json('flags')->nullable();
            $table->json('assets')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('sequence')->nullable();
            $table->timestamp('burned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfts');
    }
};
