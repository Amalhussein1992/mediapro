<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ads_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('objective', ['awareness', 'traffic', 'engagement', 'leads', 'conversions', 'sales']);
            $table->json('platforms');
            $table->enum('budget_type', ['daily', 'lifetime'])->default('lifetime');
            $table->decimal('budget', 10, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('timezone')->default('UTC');
            $table->enum('status', ['draft', 'active', 'paused', 'completed'])->default('draft');
            $table->json('analytics')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('start_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads_campaigns');
    }
};
