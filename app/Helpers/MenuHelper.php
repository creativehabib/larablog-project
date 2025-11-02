<?php
// app/Helpers/MenuHelper.php

use App\Models\Menu;

if (!function_exists('get_menu_by_location')) {
    function get_menu_by_location($location)
    {
        return Menu::with('items.children')->where('location', $location)->first();
    }
}
