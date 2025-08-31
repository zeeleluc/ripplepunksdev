<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    public function up(): void
    {
        // Run the command once during migration
        Artisan::call('nfts:fill-skin');

        // Optional: log output to the console
        $output = Artisan::output();
        echo $output;
    }

    public function down(): void
    {
        // Optionally, you can clear the skin column if rolling back
        \App\Models\Nft::query()->update(['skin' => null]);
    }
};
