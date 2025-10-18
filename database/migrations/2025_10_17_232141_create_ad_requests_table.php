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
        Schema::create('ad_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('campaign_name');
            $table->text('campaign_description')->nullable();
            $table->enum('platform', ['facebook', 'instagram', 'twitter', 'linkedin', 'tiktok', 'snapchat', 'youtube']);
            $table->enum('ad_type', ['image', 'video', 'carousel', 'story', 'collection'])->default('image');
            $table->enum('objective', ['awareness', 'traffic', 'engagement', 'leads', 'sales', 'app_promotion'])->default('awareness');
            $table->decimal('budget', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->integer('duration_days')->default(7);
            $table->date('start_date');
            $table->date('end_date');
            $table->json('targeting')->nullable(); // Age, gender, location, interests
            $table->json('creative_assets')->nullable(); // URLs to images/videos
            $table->string('ad_headline')->nullable();
            $table->text('ad_copy')->nullable();
            $table->string('call_to_action')->nullable();
            $table->string('destination_url')->nullable();
            $table->enum('status', ['pending', 'in_review', 'approved', 'running', 'paused', 'completed', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('performance_metrics')->nullable(); // Impressions, clicks, conversions
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('platform');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_requests');
    }
};
