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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // SUMMER2024, WELCOME50
            $table->string('type')->default('percentage'); // percentage, fixed_amount
            $table->decimal('value', 10, 2); // 50 (for 50%) or 20 (for $20 discount)
            $table->text('description')->nullable();

            // Usage limits
            $table->integer('max_uses')->nullable(); // null = unlimited
            $table->integer('uses_count')->default(0);
            $table->integer('max_uses_per_user')->default(1);

            // Applicability
            $table->json('applicable_plans')->nullable(); // [1, 2, 3] plan IDs or null for all
            $table->decimal('min_purchase_amount', 10, 2)->nullable();

            // Validity
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index('code');
            $table->index('is_active');
            $table->index(['valid_from', 'valid_until']);
        });

        // Pivot table for tracking coupon usage per user
        Schema::create('coupon_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('discount_amount', 10, 2);
            $table->timestamp('used_at');

            $table->index(['coupon_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_user');
        Schema::dropIfExists('coupons');
    }
};
