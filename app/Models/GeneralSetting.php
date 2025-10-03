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
        'dashboard_widget_visibility'
    ];

    protected $casts = [
        'dashboard_widget_visibility' => 'array',
    ];
}
