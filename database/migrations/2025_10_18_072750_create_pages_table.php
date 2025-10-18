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
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->string('slug')->unique();
            $table->text('content')->nullable();
            $table->text('content_ar')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_description_ar')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->enum('status', ['published', 'draft'])->default('published');
            $table->integer('order')->default(0);
            $table->string('icon')->nullable();
            $table->string('section')->default('main'); // main, features, company, resources, legal
            $table->boolean('show_in_header')->default(true);
            $table->boolean('show_in_footer')->default(true);
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
