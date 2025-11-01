<?php

use App\Http\Controllers\Admin\PermissionController;
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

Route::get('/', [FrontHomeController::class, 'index'])->name('home');
Route::get('/category/{category:slug}', [FrontCategoryController::class, 'show'])->name('categories.show');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/feed', FeedController::class)->name('feed');
Route::get('/polls', [PollController::class, 'index'])->name('polls.index');
Route::post('/polls/{poll}/vote', [PollController::class, 'vote'])->name('polls.vote');


//Testing route
Route::view('/example-page', 'example-page');
Route::view('/example-auth', 'example-auth');
// Admin route

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
       });

        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('subcategories', SubCategoryController::class)->except(['show']);
        Route::resource('posts', AdminPostController::class)->only(['index', 'create', 'edit']);
        Route::get('media-library', MediaLibraryController::class)
            ->name('media-library.index')
            ->middleware('permission:media.view');
        Route::resource('polls', AdminPollController::class)->only(['index', 'create', 'edit']);

       Route::resource('roles', RoleController::class);
       Route::resource('permissions', PermissionController::class);
       Route::resource('users', UserManagementController::class);
    });
});

$permalinkRoute = PermalinkManager::routeDefinition();

$postRoute = Route::get($permalinkRoute['uri'], [FrontPostController::class, 'show'])
    ->name('posts.show');

if (! empty($permalinkRoute['constraints'])) {
    $postRoute->where($permalinkRoute['constraints']);
}
