<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('nft_sales', function (Blueprint $table) {
            $table->renameColumn('nftokenID', 'nftoken_id');
            $table->renameColumn('acceptedAt', 'accepted_at');
            $table->renameColumn('acceptedLedgerIndex', 'accepted_ledger_index');
            $table->renameColumn('acceptedTxHash', 'accepted_tx_hash');
            $table->renameColumn('acceptedAccount', 'accepted_account');
            $table->renameColumn('amountInConvertCurrencies', 'amount_in_convert_currencies');
            $table->renameColumn('sellerDetails', 'seller_details');
            $table->renameColumn('buyerDetails', 'buyer_details');
            $table->renameColumn('acceptedAccountDetails', 'accepted_account_details');
            // collection_name & nft_name are already snake_case ✅
        });
    }

    public function down()
    {
        Schema::table('nft_sales', function (Blueprint $table) {
            $table->renameColumn('nftoken_id', 'nftokenID');
            $table->renameColumn('accepted_at', 'acceptedAt');
            $table->renameColumn('accepted_ledger_index', 'acceptedLedgerIndex');
            $table->renameColumn('accepted_tx_hash', 'acceptedTxHash');
            $table->renameColumn('accepted_account', 'acceptedAccount');
            $table->renameColumn('amount_in_convert_currencies', 'amountInConvertCurrencies');
            $table->renameColumn('seller_details', 'sellerDetails');
            $table->renameColumn('buyer_details', 'buyerDetails');
            $table->renameColumn('accepted_account_details', 'acceptedAccountDetails');
        });
    }

};
