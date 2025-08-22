<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();
            $table->string('identifier'); // The entity being interacted with
            $table->text('type');
            $table->foreignId('holder_id')->constrained('holders')->onDelete('cascade'); // Who interacted
            $table->timestamp('interacted_at')->nullable();
            $table->timestamps(); // includes created_at/updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('interactions');
    }
};
