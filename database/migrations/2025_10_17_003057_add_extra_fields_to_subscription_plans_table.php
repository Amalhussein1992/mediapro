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
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->boolean('ai_features')->default(false)->after('is_active');
            $table->boolean('analytics')->default(false)->after('ai_features');
            $table->boolean('priority_support')->default(false)->after('analytics');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn(['ai_features', 'analytics', 'priority_support']);
        });
    }
};
