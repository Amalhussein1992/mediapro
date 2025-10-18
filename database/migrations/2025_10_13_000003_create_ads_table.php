<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_set_id')->constrained('ad_sets')->onDelete('cascade');
            $table->string('name');
            $table->json('creative')->nullable();
            $table->enum('status', ['active', 'paused', 'completed'])->default('active');
            $table->json('analytics')->nullable();
            $table->timestamps();

            $table->index('ad_set_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
