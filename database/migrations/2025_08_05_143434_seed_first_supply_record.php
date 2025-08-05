<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('supply_records')->insert([
            'out_of_circulation' => 1375,
            'new_mints' => 110,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('supply_records')->where('out_of_circulation', 0)->where('new_mints', 0)->delete();
    }
};
