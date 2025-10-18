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
        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'moderation_status')) {
                $table->string('moderation_status')->default('pending')->after('status');
            }
            if (!Schema::hasColumn('posts', 'moderation_note')) {
                $table->text('moderation_note')->nullable()->after('moderation_status');
            }
            if (!Schema::hasColumn('posts', 'moderated_by')) {
                $table->foreignId('moderated_by')->nullable()->constrained('users')->onDelete('set null')->after('moderation_note');
            }
            if (!Schema::hasColumn('posts', 'moderated_at')) {
                $table->timestamp('moderated_at')->nullable()->after('moderated_by');
            }
            if (!Schema::hasColumn('posts', 'is_flagged')) {
                $table->boolean('is_flagged')->default(false)->after('moderated_at');
            }
            if (!Schema::hasColumn('posts', 'flag_reasons')) {
                $table->json('flag_reasons')->nullable()->after('is_flagged');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['moderated_by']);
            $table->dropColumn([
                'moderation_status',
                'moderation_note',
                'moderated_by',
                'moderated_at',
                'is_flagged',
                'flag_reasons'
            ]);
        });
    }
};