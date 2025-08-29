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
        Schema::create('votings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_active');
            $table->timestamps();
        });

        Schema::create('voting_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voting_id')->constrained('votings')->onDelete('cascade');
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('voting_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voting_id')->constrained('votings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('voting_option_id')->constrained('voting_options')->onDelete('cascade');
            $table->integer('voting_power')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votings');
        Schema::dropIfExists('voting_options');
        Schema::dropIfExists('voting_submissions');
    }
};
