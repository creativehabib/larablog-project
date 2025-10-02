<?php

use App\Models\GeneralSetting;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('settings')) {
    function settings($key = null) {
        if ($key) {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->value : null;
        }
        return Setting::pluck('value', 'key')->all();
    }
}

if (!function_exists('general_settings')) {
    function general_settings($key = null)
    {
        // 1 day cache duration
        $duration = 60 * 60 * 24;

        $settings = Cache::remember('general_settings', $duration, function () {
            return GeneralSetting::first();
        });

        if ($key) {
            return $settings ? $settings->{$key} : null;
        }

        return $settings;
    }
}
