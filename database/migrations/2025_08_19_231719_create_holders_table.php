<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('holders', function (Blueprint $table) {
            $table->id();
            $table->string('wallet')->unique();
            $table->text('badges');
            $table->integer('holdings');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holders');
    }
};
