<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supply_records', function (Blueprint $table) {
            $table->id();
            $table->integer('out_of_circulation')->unsigned();
            $table->integer('new_mints')->unsigned();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_records');
    }
};
