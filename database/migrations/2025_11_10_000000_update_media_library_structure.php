<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('media_items') && ! Schema::hasTable('media_files')) {
            Schema::rename('media_items', 'media_files');
        }

        if (! Schema::hasTable('media_folders')) {
            Schema::create('media_folders', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedBigInteger('parent_id')->default(0);
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('media_files')) {
            Schema::table('media_files', function (Blueprint $table) {
                if (! Schema::hasColumn('media_files', 'folder_id')) {
                    $table->unsignedBigInteger('folder_id')->default(0)->after('disk');
                    $table->index('folder_id');
                }

                if (! Schema::hasColumn('media_files', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('folder_id')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('media_files', 'name')) {
                    $table->string('name')->nullable()->after('file_name');
                }
            });

            if (Schema::hasColumn('media_files', 'original_name')) {
                DB::table('media_files')
                    ->whereNull('name')
                    ->update(['name' => DB::raw('original_name')]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('media_files')) {
            Schema::table('media_files', function (Blueprint $table) {
                if (Schema::hasColumn('media_files', 'user_id')) {
                    $table->dropConstrainedForeignId('user_id');
                }

                if (Schema::hasColumn('media_files', 'folder_id')) {
                    $table->dropIndex('media_files_folder_id_index');
                    $table->dropColumn('folder_id');
                }

                if (Schema::hasColumn('media_files', 'name')) {
                    $table->dropColumn('name');
                }
            });
        }

        Schema::dropIfExists('media_folders');

        if (Schema::hasTable('media_files') && ! Schema::hasTable('media_items')) {
            Schema::rename('media_files', 'media_items');
        }
    }
};
