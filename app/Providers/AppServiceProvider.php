<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Redirect an Authenticated user to dashboard
        RedirectIfAuthenticated::redirectUsing(function ($request) {
            return route('admin.dashboard');
        });

        Authenticate::redirectUsing(function ($request) {
            Session::flash('fail', 'You mush be logged in to access admin area. Please login to continue.');
            return route('admin.login');
        });
    }
}
