<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('prize');
            $table->integer('total');
            $table->text('required_badges'); // store comma-separated or serialized string
            $table->boolean('is_open')->default(false);
            $table->timestamps();
        });

        Schema::create('claim_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('claimed_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('claims');
        Schema::dropIfExists('claim_submissions');
    }
};
