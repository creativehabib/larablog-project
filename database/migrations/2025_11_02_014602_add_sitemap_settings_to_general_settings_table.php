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
        Schema::table('general_settings', function (Blueprint $table) {
            $table->boolean('sitemap_enabled')->default(true)->after('site_title');
            $table->integer('sitemap_items_per_page')->default(1000)->after('sitemap_enabled');
            $table->boolean('sitemap_enable_index_now')->default(false)->after('sitemap_items_per_page');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn(['sitemap_enabled', 'sitemap_items_per_page', 'sitemap_enable_index_now']);
        });
    }
};
