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
        Schema::create('brand_kits', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم البراند كيت
            $table->string('name_ar')->nullable(); // الاسم بالعربي
            $table->text('description')->nullable(); // الوصف
            $table->text('description_ar')->nullable(); // الوصف بالعربي

            // الألوان (JSON array of colors)
            $table->json('colors'); // ["#3b82f6", "#8b5cf6", "#0f172a"]

            // الخطوط (JSON array of fonts)
            $table->json('fonts'); // ["Inter", "Roboto"]

            // اللوجو
            $table->string('logo_url')->nullable(); // رابط اللوجو
            $table->string('logo_white_url')->nullable(); // لوجو أبيض للخلفيات الداكنة
            $table->string('logo_dark_url')->nullable(); // لوجو داكن للخلفيات الفاتحة

            // أيقونات
            $table->string('icon_url')->nullable(); // الأيقونة

            // صور إضافية
            $table->json('images')->nullable(); // صور إضافية للبراند

            // إعدادات الـ Gradient
            $table->string('gradient_from')->nullable(); // لون البداية للتدرج
            $table->string('gradient_to')->nullable(); // لون النهاية للتدرج

            // إرشادات الاستخدام
            $table->text('usage_guidelines')->nullable(); // إرشادات استخدام البراند
            $table->text('usage_guidelines_ar')->nullable(); // الإرشادات بالعربي

            // Tone of Voice / نبرة الصوت
            $table->enum('tone', ['professional', 'casual', 'friendly', 'formal', 'playful', 'inspirational'])->default('professional');

            // الحالة
            $table->enum('status', ['active', 'inactive'])->default('active');

            // هل هو افتراضي؟
            $table->boolean('is_default')->default(false);

            // الترتيب
            $table->integer('order')->default(0);

            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('is_default');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_kits');
    }
};
