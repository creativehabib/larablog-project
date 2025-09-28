<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\SubCategoryController;

Route::get('/', function () {
    return view('welcome');
});

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

    Route::middleware(['auth','preventBackHistory','permission:access_admin_panel'])->group(function () {
       Route::controller(AdminController::class)->group(function(){
              Route::get('/dashboard', 'dashboard')->name('dashboard');
              Route::post('/logout', 'logout')->name('logout');
              Route::get('/profile', 'profile')->name('profile');
              Route::post('/update-profile', 'updateProfile')->name('update.profile');
              Route::get('/settings', 'generalSettings')->name('settings')->middleware('permission:manage_content,manage_users');
       });

       Route::resource('categories', CategoryController::class)
           ->except(['show'])
           ->middleware('permission:manage_content');

       Route::resource('subcategories', SubCategoryController::class)
           ->except(['show'])
           ->middleware('permission:manage_content');

       Route::resource('posts', PostController::class)
           ->only(['index', 'create', 'edit'])
           ->middleware('permission:manage_content,publish_posts,edit_any_post,create_posts,submit_posts');
    });
});
