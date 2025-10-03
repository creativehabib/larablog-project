<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (! Schema::hasColumn('posts', 'content_type')) {
                $table->string('content_type')->default('article')->after('slug');
            }

            if (! Schema::hasColumn('posts', 'video_url')) {
                $table->string('video_url', 500)->nullable()->after('thumbnail_path');
            }

            if (! Schema::hasColumn('posts', 'video_provider')) {
                $table->string('video_provider', 50)->nullable()->after('video_url');
            }

            if (! Schema::hasColumn('posts', 'video_id')) {
                $table->string('video_id', 100)->nullable()->after('video_provider');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'video_id')) {
                $table->dropColumn('video_id');
            }

            if (Schema::hasColumn('posts', 'video_provider')) {
                $table->dropColumn('video_provider');
            }

            if (Schema::hasColumn('posts', 'video_url')) {
                $table->dropColumn('video_url');
            }

            if (Schema::hasColumn('posts', 'content_type')) {
                $table->dropColumn('content_type');
            }
        });
    }
};
