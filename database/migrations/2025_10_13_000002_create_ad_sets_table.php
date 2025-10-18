<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('ads_campaigns')->onDelete('cascade');
            $table->string('name');
            $table->json('targeting')->nullable();
            $table->decimal('budget', 10, 2)->nullable();
            $table->enum('status', ['active', 'paused', 'completed'])->default('active');
            $table->json('analytics')->nullable();
            $table->timestamps();

            $table->index('campaign_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_sets');
    }
};
