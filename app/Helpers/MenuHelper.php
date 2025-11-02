<?php
// app/Helpers/MenuHelper.php

use App\Models\Menu;
use Illuminate\Support\Facades\Cache;

if (!function_exists('menu_cache_key')) {
    function menu_cache_key(string $location): string
    {
        return 'menu_location_' . $location;
    }
}

if (!function_exists('get_menu_by_location')) {
    function get_menu_by_location(string $location)
    {
        return Cache::remember(menu_cache_key($location), now()->addMinutes(30), function () use ($location) {
            return Menu::query()
                ->where('location', $location)
                ->with('items')
                ->first();
        });
    }
}

if (!function_exists('forget_menu_cache')) {
    function forget_menu_cache(?string $location = null): void
    {
        if ($location) {
            Cache::forget(menu_cache_key($location));

            return;
        }

        Menu::pluck('location')->each(function ($menuLocation) {
            Cache::forget(menu_cache_key($menuLocation));
        });
    }
}
