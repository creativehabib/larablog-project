<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'site_title',
        'site_description',
        'site_email',
        'site_phone',
        'site_meta_keywords',
        'site_meta_description',
        'site_favicon',
        'site_copyright',
        'site_logo',
        'dashboard_widget_visibility',
        'permalink_structure',
        'custom_permalink_structure',
        'category_slug_prefix_enabled',
        'sitemap_enabled',
        'sitemap_items_per_page',
        'sitemap_enable_index_now',
    ];

    protected $casts = [
        'dashboard_widget_visibility' => 'array',
        'category_slug_prefix_enabled' => 'boolean',
        'sitemap_enabled' => 'boolean',
        'sitemap_items_per_page' => 'integer',
        'sitemap_enable_index_now' => 'boolean',
    ];
}
