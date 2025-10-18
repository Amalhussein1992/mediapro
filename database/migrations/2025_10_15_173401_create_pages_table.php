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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // about, contact, pricing, etc.
            $table->string('title'); // Page title (English)
            $table->string('title_ar')->nullable(); // Page title (Arabic)
            $table->text('content'); // Page content (English)
            $table->text('content_ar')->nullable(); // Page content (Arabic)
            $table->string('meta_description', 160)->nullable(); // SEO meta description
            $table->string('meta_description_ar', 160)->nullable(); // SEO meta description (Arabic)
            $table->string('meta_keywords')->nullable(); // SEO keywords
            $table->boolean('is_active')->default(true); // Active/Inactive status
            $table->integer('order')->default(0); // Display order
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
