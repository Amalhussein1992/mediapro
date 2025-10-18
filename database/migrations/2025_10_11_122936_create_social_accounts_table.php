<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('platform'); // facebook, instagram, twitter, etc.
            $table->string('platform_user_id')->nullable(); // Platform's user ID
            $table->string('account_name');
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('profile_picture')->nullable();
            $table->json('metrics')->nullable(); // followers, posts, engagement, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sync')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();

            // Unique constraint: one account per platform per user
            $table->unique(['user_id', 'platform', 'platform_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
