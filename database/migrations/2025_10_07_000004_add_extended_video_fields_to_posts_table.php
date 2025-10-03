<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (! Schema::hasColumn('posts', 'video_source')) {
                $table->string('video_source', 20)->nullable()->after('video_id');
            }

            if (! Schema::hasColumn('posts', 'video_embed_code')) {
                $table->text('video_embed_code')->nullable()->after('video_source');
            }

            if (! Schema::hasColumn('posts', 'video_path')) {
                $table->string('video_path', 500)->nullable()->after('video_embed_code');
            }

            if (! Schema::hasColumn('posts', 'video_duration')) {
                $table->string('video_duration', 50)->nullable()->after('video_path');
            }

            if (! Schema::hasColumn('posts', 'video_playlist_id')) {
                $table->foreignId('video_playlist_id')
                    ->nullable()
                    ->after('video_duration')
                    ->constrained('video_playlists')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'video_playlist_id')) {
                $table->dropConstrainedForeignId('video_playlist_id');
            }

            if (Schema::hasColumn('posts', 'video_duration')) {
                $table->dropColumn('video_duration');
            }

            if (Schema::hasColumn('posts', 'video_path')) {
                $table->dropColumn('video_path');
            }

            if (Schema::hasColumn('posts', 'video_embed_code')) {
                $table->dropColumn('video_embed_code');
            }

            if (Schema::hasColumn('posts', 'video_source')) {
                $table->dropColumn('video_source');
            }
        });
    }
};
