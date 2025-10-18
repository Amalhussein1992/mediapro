<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brand_kits', function (Blueprint $table) {
            $table->json('languages')->nullable()->after('templates');
            $table->json('tone_of_voice')->nullable()->after('languages');
            $table->json('guidelines')->nullable()->after('tone_of_voice');
            $table->json('hashtags')->nullable()->after('guidelines');
            $table->json('arabic_settings')->nullable()->after('hashtags');
        });
    }

    public function down(): void
    {
        Schema::table('brand_kits', function (Blueprint $table) {
            $table->dropColumn(['languages', 'tone_of_voice', 'guidelines', 'hashtags', 'arabic_settings']);
        });
    }
};
