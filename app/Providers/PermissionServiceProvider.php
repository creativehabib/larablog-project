<?php

namespace App\Providers;

use App\Support\Permissions\RoleRegistry;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     */
    public function register(): void
    {
        $this->app->singleton(RoleRegistry::class, function ($app) {
            $config = $app['config']->get('roles', []);

            return RoleRegistry::make(is_array($config) ? $config : []);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
