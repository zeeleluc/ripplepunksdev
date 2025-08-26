<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop timestamps and last_seen_at from holders
        Schema::table('holders', function (Blueprint $table) {
            if (Schema::hasColumn('holders', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('holders', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
            if (Schema::hasColumn('holders', 'last_seen_at')) {
                $table->dropColumn('last_seen_at');
            }
        });

        // Drop timestamps from nfts
        Schema::table('nfts', function (Blueprint $table) {
            if (Schema::hasColumn('nfts', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('nfts', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add columns if you rollback
        Schema::table('holders', function (Blueprint $table) {
            $table->timestamps();
            $table->timestamp('last_seen_at')->nullable();
        });

        Schema::table('nfts', function (Blueprint $table) {
            $table->timestamps();
        });
    }
};
