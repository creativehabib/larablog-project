<?php

use App\Models\GeneralSetting;
use App\Models\Post;
use App\Models\Setting;
use App\Support\PermalinkManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

if (!function_exists('settings')) {
    function settings($key = null) {
        if ($key) {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->value : null;
        }
        return Setting::pluck('value', 'key')->all();
    }
}

if (! function_exists('post_permalink')) {
    function post_permalink(Post $post, bool $absolute = true): string
    {
        return PermalinkManager::urlFor($post, $absolute);
    }
}

if (!function_exists('general_settings')) {
    function general_settings($key = null)
    {
        // 1 day cache duration
        $duration = 60 * 60 * 24;

        if (! Schema::hasTable('general_settings')) {
            return null;
        }

        $settings = Cache::remember('general_settings', $duration, function () {
            return GeneralSetting::first();
        });

        if ($key) {
            return $settings ? $settings->{$key} : null;
        }

        return $settings;
    }
}
