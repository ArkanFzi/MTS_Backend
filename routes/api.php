<?php

use Illuminate\Support\Facades\Route;

// Auth Modules
use Modules\Auth\F1_Register\Controllers\RegisterController;
use Modules\Auth\F2_Login\Controllers\LoginController;
use Modules\Auth\F3_Logout\Controllers\LogoutController;

// Common Modules
use Modules\Common\F4_SearchPost\Controllers\SearchPostController;
use Modules\Common\F5_FilterByTag\Controllers\FilterTagController;
use Modules\Common\F6_FilterByCategory\Controllers\FilterCategoryController;
use Modules\Common\F7_TrendingPopularPost\Controllers\TrendingController;

// Admin Modules
use Modules\Admin\F8_RoleAndPermission\Controllers\RoleController;
use Modules\Admin\F8_RoleAndPermission\Controllers\UserManagementController;
use Modules\Admin\F9_UserManagement\Controllers\UserAdminController;
use Modules\Admin\F10_CategoryMaster\Controllers\CategoryController;
use Modules\Admin\F11_BadgeMaster\Controllers\BadgeController;
use Modules\Admin\F12_TagMaster\Controllers\TagController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Auth
Route::prefix('auth')->group(function () {
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum');
});

// Explore / Common
Route::prefix('explore')->group(function () {
    Route::get('/search', [SearchPostController::class, 'search']);
    Route::get('/tag/{slug}', [FilterTagController::class, 'filter']);
    Route::get('/category/{slug}', [FilterCategoryController::class, 'filter']);
    Route::get('/trending', [TrendingController::class, 'getTrending']);
});

// Admin (Protected)
Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    // F8: Role & Permission
    Route::apiResource('roles', RoleController::class);
    Route::get('users', [UserManagementController::class, 'index']);
    Route::get('users/{id}', [UserManagementController::class, 'show']);
    Route::put('users/{id}/role', [UserManagementController::class, 'update']); // Focus on role/status update
    Route::put('users/{id}/reset-password-basic', [UserManagementController::class, 'resetPassword']);

    // F9: User Management (Advanced Admin Action)
    Route::put('users/{id}/profile', [UserAdminController::class, 'updateProfile']);
    Route::put('users/{id}/reset-password', [UserAdminController::class, 'resetPassword']);

    // F10: Category Master
    Route::apiResource('categories', CategoryController::class);

    // F11: Badge Master
    Route::apiResource('badges', BadgeController::class)->except(['show']);

    // F12: Tag Master
    Route::apiResource('tags', TagController::class)->except(['show']);
});
