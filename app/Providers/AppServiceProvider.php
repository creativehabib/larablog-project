<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\GeneralSetting;
use App\Models\Poll;
use App\Models\Post;
use App\Support\BanglaFormatter;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
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

        // --- ১. গ্লোবাল ডেটা কম্পোজার (Header, Footer, Layout) ---
        // $settings, $logoUrl, $navCategories, $currentDateBangla এই ভেরিয়েবলগুলো টার্গেট করা হয়েছে।
        View::composer(['front.partials.header', 'front.partials.footer', 'front.partials.sidebar', 'layouts.frontend'], function ($view) {

            // সেটিংস ডেটা (ক্যাশিং সহ)
            $settings = Cache::remember('general_settings', now()->addHour(), function () {
                return GeneralSetting::query()->first();
            });

            $logoUrl = null;
            if ($settings?->site_logo) {
                $logoUrl = Storage::url($settings->site_logo);
            }

            $currentDate = now(config('app.timezone', 'Asia/Dhaka'));
            $currentDateBangla = BanglaFormatter::fullDate($currentDate);
            $currentTimeBangla = BanglaFormatter::time($currentDate);

            // নেভিগেশন ক্যাটাগরি
            $navCategories = Category::query()
                ->withCount(['posts as published_posts_count' => fn ($query) => $query->where('is_indexable', true)])
                ->having('published_posts_count', '>', 0)
                ->orderByDesc('published_posts_count')
                ->take(12)
                ->get();

            // সব ক্যাটাগরি
            $allCategories = Category::query()
                ->withCount(['posts as published_posts_count' => fn ($query) => $query->where('is_indexable', true)])
                ->having('published_posts_count', '>', 0)
                ->orderBy('name')
                ->get();

            $primaryMenu = get_menu_by_location('primary');
            $footerMenu = get_menu_by_location('footer');

            $view->with([
                'settings' => $settings,
                'logoUrl' => $logoUrl,
                'currentDateBangla' => $currentDateBangla,
                'currentTimeBangla' => $currentTimeBangla,
                'navCategories' => $navCategories,
                'allCategories' => $allCategories,
                'primaryMenu' => $primaryMenu,
                'footerMenu' => $footerMenu,
            ]);
        });


        // --- ২. সাইডবার ডেটা কম্পোজার ---
        // $latestSidebarPosts, $popularPosts, $trendingTopics, $activePoll এই ভেরিয়েবলগুলো টার্গেট করা হয়েছে।
        View::composer(['front.partials.sidebar', 'front.index', 'front.category'], function ($view) {

            $latestSidebarPosts = Post::query()
                ->with(['category'])
                ->where('is_indexable', true)
                ->latest('created_at')
                ->take(12)
                ->get();

            $popularPosts = Post::query()
                ->with(['category'])
                ->where('is_indexable', true)
                ->orderByDesc('is_featured')
                ->orderByDesc('created_at')
                ->take(12)
                ->get();

            if ($popularPosts->isEmpty()) {
                $popularPosts = $latestSidebarPosts;
            }

            $trendingTopics = Category::query()
                ->withCount(['posts as published_posts_count' => fn ($query) => $query->where('is_indexable', true)])
                ->having('published_posts_count', '>', 0)
                ->orderByDesc('published_posts_count')
                ->take(8)
                ->get();

            $activePoll = Poll::query()
                ->where('is_active', true)
                ->orderByDesc('poll_date')
                ->orderByDesc('created_at')
                ->first();

            $view->with([
                'latestSidebarPosts' => $latestSidebarPosts,
                'popularPosts' => $popularPosts,
                'trendingTopics' => $trendingTopics,
                'activePoll' => $activePoll,
            ]);
        });

    }
}
