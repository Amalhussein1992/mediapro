<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add indexes to improve query performance in production
     */
    public function up(): void
    {
        // Posts table indexes
        Schema::table('posts', function (Blueprint $table) {
            $table->index('user_id', 'posts_user_id_index');
            $table->index('status', 'posts_status_index');
            $table->index('scheduled_at', 'posts_scheduled_at_index');
            $table->index('created_at', 'posts_created_at_index');
            $table->index(['user_id', 'status'], 'posts_user_status_index');
            $table->index(['status', 'scheduled_at'], 'posts_status_scheduled_index');
        });

        // Social accounts table indexes
        Schema::table('social_accounts', function (Blueprint $table) {
            $table->index('user_id', 'social_accounts_user_id_index');
            $table->index('platform', 'social_accounts_platform_index');
            $table->index('is_active', 'social_accounts_is_active_index');
            $table->index(['user_id', 'platform'], 'social_accounts_user_platform_index');
            $table->index(['platform', 'is_active'], 'social_accounts_platform_active_index');
        });

        // Brand kits table indexes
        Schema::table('brand_kits', function (Blueprint $table) {
            $table->index('user_id', 'brand_kits_user_id_index');
            $table->index('is_default', 'brand_kits_is_default_index');
            $table->index('created_at', 'brand_kits_created_at_index');
        });

        // Analytics table indexes (if exists)
        if (Schema::hasTable('analytics')) {
            Schema::table('analytics', function (Blueprint $table) {
                $table->index('user_id', 'analytics_user_id_index');
                $table->index('post_id', 'analytics_post_id_index');
                $table->index('platform', 'analytics_platform_index');
                $table->index('date', 'analytics_date_index');
                $table->index(['user_id', 'date'], 'analytics_user_date_index');
                $table->index(['post_id', 'platform'], 'analytics_post_platform_index');
            });
        }

        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->index('email', 'users_email_index');
            $table->index('created_at', 'users_created_at_index');
            // Only add role index if column exists
            if (Schema::hasColumn('users', 'role')) {
                $table->index('role', 'users_role_index');
            }
        });

        // Post schedules table indexes (if exists)
        if (Schema::hasTable('post_schedules')) {
            Schema::table('post_schedules', function (Blueprint $table) {
                $table->index('post_id', 'post_schedules_post_id_index');
                $table->index('scheduled_at', 'post_schedules_scheduled_at_index');
                $table->index('status', 'post_schedules_status_index');
                $table->index(['status', 'scheduled_at'], 'post_schedules_status_scheduled_index');
            });
        }

        // Media table indexes (if exists)
        if (Schema::hasTable('media')) {
            Schema::table('media', function (Blueprint $table) {
                $table->index('user_id', 'media_user_id_index');
                $table->index('post_id', 'media_post_id_index');
                $table->index('type', 'media_type_index');
                $table->index('created_at', 'media_created_at_index');
            });
        }

        // Comments/inbox table indexes (if exists)
        if (Schema::hasTable('comments')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->index('post_id', 'comments_post_id_index');
                $table->index('user_id', 'comments_user_id_index');
                $table->index('created_at', 'comments_created_at_index');
                $table->index('is_read', 'comments_is_read_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Posts table
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_user_id_index');
            $table->dropIndex('posts_status_index');
            $table->dropIndex('posts_scheduled_at_index');
            $table->dropIndex('posts_created_at_index');
            $table->dropIndex('posts_user_status_index');
            $table->dropIndex('posts_status_scheduled_index');
        });

        // Social accounts table
        Schema::table('social_accounts', function (Blueprint $table) {
            $table->dropIndex('social_accounts_user_id_index');
            $table->dropIndex('social_accounts_platform_index');
            $table->dropIndex('social_accounts_is_active_index');
            $table->dropIndex('social_accounts_user_platform_index');
            $table->dropIndex('social_accounts_platform_active_index');
        });

        // Brand kits table
        Schema::table('brand_kits', function (Blueprint $table) {
            $table->dropIndex('brand_kits_user_id_index');
            $table->dropIndex('brand_kits_is_default_index');
            $table->dropIndex('brand_kits_created_at_index');
        });

        // Analytics table (if exists)
        if (Schema::hasTable('analytics')) {
            Schema::table('analytics', function (Blueprint $table) {
                $table->dropIndex('analytics_user_id_index');
                $table->dropIndex('analytics_post_id_index');
                $table->dropIndex('analytics_platform_index');
                $table->dropIndex('analytics_date_index');
                $table->dropIndex('analytics_user_date_index');
                $table->dropIndex('analytics_post_platform_index');
            });
        }

        // Users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_email_index');
            $table->dropIndex('users_created_at_index');
            // Only drop role index if it exists
            if (Schema::hasColumn('users', 'role')) {
                $table->dropIndex('users_role_index');
            }
        });

        // Post schedules table (if exists)
        if (Schema::hasTable('post_schedules')) {
            Schema::table('post_schedules', function (Blueprint $table) {
                $table->dropIndex('post_schedules_post_id_index');
                $table->dropIndex('post_schedules_scheduled_at_index');
                $table->dropIndex('post_schedules_status_index');
                $table->dropIndex('post_schedules_status_scheduled_index');
            });
        }

        // Media table (if exists)
        if (Schema::hasTable('media')) {
            Schema::table('media', function (Blueprint $table) {
                $table->dropIndex('media_user_id_index');
                $table->dropIndex('media_post_id_index');
                $table->dropIndex('media_type_index');
                $table->dropIndex('media_created_at_index');
            });
        }

        // Comments table (if exists)
        if (Schema::hasTable('comments')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropIndex('comments_post_id_index');
                $table->dropIndex('comments_user_id_index');
                $table->dropIndex('comments_created_at_index');
                $table->dropIndex('comments_is_read_index');
            });
        }
    }
};
