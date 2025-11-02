<?php

use App\Http\Controllers\Admin\PermissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\PollController as AdminPollController;
use App\Http\Controllers\Admin\MediaLibraryController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Frontend\FeedController;
use App\Http\Controllers\Frontend\HomeController as FrontHomeController;
use App\Http\Controllers\Frontend\CategoryController as FrontCategoryController;
use App\Http\Controllers\Frontend\PollController;
use App\Http\Controllers\Frontend\PostController as FrontPostController;
use App\Http\Controllers\Frontend\SitemapController;
use App\Support\PermalinkManager;

// === (পরিবর্তন শুরু) ===
// ১. সমস্ত সুনির্দিষ্ট (Specific) রুট প্রথমে ডিফাইন করুন।

Route::get('/', [FrontHomeController::class, 'index'])->name('home');
Route::get('/feed', FeedController::class)->name('feed');
Route::get('/polls', [PollController::class, 'index'])->name('polls.index');
Route::post('/polls/{poll}/vote', [PollController::class, 'vote'])->name('polls.vote');

//Testing route
Route::view('/example-page', 'example-page');
Route::view('/example-auth', 'example-auth');
// === (পরিবর্তন শেষ) ===

// --- নতুন সাইটম্যাপ রুট ---
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-posts-{year}-{month}.xml', [SitemapController::class, 'posts'])
    ->where(['year' => '[0-9]{4}', 'month' => '[0-9]{2}']) // YYYY-MM ফরম্যাট নিশ্চিত করা
    ->name('sitemap.posts');
Route::get('/sitemap-categories.xml', [SitemapController::class, 'categories'])->name('sitemap.categories');
Route::get('/sitemap-pages.xml', [SitemapController::class, 'pages'])->name('sitemap.pages');
// --- সাইটম্যাপ রুট শেষ ---

// ২. এখন "Greedy" (ওয়াইল্ডকার্ড) ক্যাটাগরি রুট ডিফাইন করুন।
$categoryPrefixEnabled = general_settings('category_slug_prefix_enabled');
$categoryPrefixEnabled = is_null($categoryPrefixEnabled) ? true : (bool) $categoryPrefixEnabled;
$categoryRouteUri = $categoryPrefixEnabled ? '/category/{category:slug}' : '/{category:slug}';

$categoryRoute = Route::get($categoryRouteUri, [FrontCategoryController::class, 'show'])
    ->name('categories.show');

$permalinkRoute = PermalinkManager::routeDefinition();


if (! $categoryPrefixEnabled && $permalinkRoute['template'] === '%postname%') {
    $categoryRoute->missing(function (Request $request) {
        $view = app(FrontPostController::class)->show($request->route('category'));

        // "setCookie() on null" এরর সমাধানের জন্য response() হেল্পার
        return response($view);
    });
}

// Admin route (এটিও '/admin' প্রিফিক্স সহ একটি সুনির্দিষ্ট রুট, তাই এটি Greedy রুটের আগে বা পরে থাকতে পারে, কোনো সমস্যা নেই)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['guest','preventBackHistory'])->group(function () {
        Route::controller(AuthController::class)->group(function(){
            Route::get('/login', 'loginForm')->name('login');
            Route::post('/login', 'login')->name('login.submit');
            Route::get('/forgot-password', 'forgotForm')->name('forgot');
            Route::post('/send-password-reset-link', 'sendPasswordResetLink')->name('send.password.reset.link');
            Route::get('/password/reset/{token}', 'resetForm')->name('reset.password.form');
            Route::post('/password/reset', 'resetPassword')->name('reset.password.submit');
        });
    });

    Route::middleware(['auth','preventBackHistory'])->group(function () {
        Route::controller(AdminController::class)->group(function(){
            Route::get('/dashboard', 'dashboard')->name('dashboard');
            Route::post('/logout', 'logout')->name('logout');
            Route::get('/profile', 'profile')->name('profile');
            Route::post('/update-profile', 'updateProfile')->name('update.profile');
            Route::get('/settings', 'generalSettings')->name('settings')->middleware('permission:setting.view');
            Route::get('/settings/sitemap', 'sitemapSettings')->name('settings.sitemap');
        });

        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('subcategories', SubCategoryController::class)->except(['show']);
        Route::resource('posts', AdminPostController::class)->only(['index', 'create', 'edit']);
        Route::get('media-library', MediaLibraryController::class)->name('media-library.index')->middleware('permission:media.view');
        Route::resource('polls', AdminPollController::class)->only(['index', 'create', 'edit']);

        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
        Route::resource('users', UserManagementController::class);

        Route::view('menus', 'back.pages.menus.index')
            ->name('menus.index')
            ->middleware('permission:menu.view');
    });
});


// ৩. সবশেষে "Greedy" পোস্ট রুটটি ডিফাইন করুন।
$postRoute = Route::get($permalinkRoute['uri'], [FrontPostController::class, 'show'])
    ->name('posts.show');

if (! empty($permalinkRoute['constraints'])) {
    $postRoute->where($permalinkRoute['constraints']);
}
