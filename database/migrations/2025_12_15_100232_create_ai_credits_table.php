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
        Schema::create('ai_credits', function (Blueprint $table) {
            $table->id();
            $table->string('room_type');
            $table->string('room_style');
            $table->decimal('cost', 8, 4);
            $table->string('model')->default('dall-e-3');
            $table->string('size')->default('1024x1024');
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_credits');
    }
};
