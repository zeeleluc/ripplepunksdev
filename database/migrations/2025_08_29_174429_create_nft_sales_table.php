<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('nft_sales', function (Blueprint $table) {
            $table->id();
            $table->string('nftokenID')->index();
            $table->timestamp('acceptedAt');
            $table->bigInteger('acceptedLedgerIndex');
            $table->string('acceptedTxHash')->unique();
            $table->string('acceptedAccount')->nullable();
            $table->string('seller')->nullable();
            $table->string('buyer')->nullable();
            $table->string('amount');
            $table->boolean('broker')->default(false);
            $table->string('marketplace')->nullable();
            $table->string('saleType')->nullable();
            $table->json('amountInConvertCurrencies')->nullable();
            $table->json('nftoken')->nullable();
            $table->json('sellerDetails')->nullable();
            $table->json('buyerDetails')->nullable();
            $table->json('acceptedAccountDetails')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nft_sales');
    }
};
