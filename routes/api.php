<?php

use Illuminate\Support\Facades\Route;

// === Import Controllers (yang sudah ada + baru) ===
use Modules\Auth\F1_Register\Controllers\RegisterController;
use Modules\Auth\F2_Login\Controllers\LoginController;
use Modules\Auth\F3_Logout\Controllers\LogoutController;

// Common
use Modules\Common\F4_SearchPost\Controllers\SearchPostController;
use Modules\Common\F5_FilterByTag\Controllers\FilterTagController;
use Modules\Common\F6_FilterByCategory\Controllers\FilterCategoryController;
use Modules\Common\F7_TrendingPopularPost\Controllers\TrendingController;

// Admin & Moderator Controllers
use Modules\Admin\F8_RoleAndPermission\Controllers\RoleController;
use Modules\Admin\F8_RoleAndPermission\Controllers\UserManagementController;
use Modules\Admin\F9_UserManagement\Controllers\UserAdminController;
use Modules\Admin\F10_CategoryMaster\Controllers\CategoryController;
use Modules\Admin\F11_BadgeMaster\Controllers\BadgeController;
use Modules\Admin\F12_TagMaster\Controllers\TagController;
use Modules\Admin\F13_ContentReportQueue\Controllers\ReportController;
use Modules\Admin\F14_ModeratorActionLog\Controllers\ModerationLogController;
use Modules\Admin\F15_UserBanSanction\Controllers\UserSanctionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ====================== PUBLIC ROUTES ======================
Route::prefix('auth')->group(function () {
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum');
});

// Explore
Route::prefix('explore')->group(function () {
    Route::get('/search', [SearchPostController::class, 'search']);
    Route::get('/tag/{slug}', [FilterTagController::class, 'filter']);
    Route::get('/category/{slug}', [FilterCategoryController::class, 'filter']);
    Route::get('/trending', [TrendingController::class, 'getTrending']);
});

// ====================== PROTECTED ROUTES ======================
Route::middleware('auth:sanctum')->group(function () {

    // === 1. ROUTES UNTUK SEMUA USER YANG LOGIN ===
    Route::prefix('me')->name('me.')->group(function () {
        // Profile, my posts, settings, dll
    });

    // === 2. MODERATOR ROUTES (F13, F14, F15) ===
    Route::middleware('role:moderator|admin')  // Bisa moderator ATAU admin
         ->prefix('moderator')
         ->name('moderator.')
         ->group(function () {

        // F13: Content Report Queue
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('{id}', [ReportController::class, 'show'])->name('show');
            Route::put('{id}', [ReportController::class, 'update'])->name('update');
        });

        // F14: Moderator Action Log
        Route::prefix('logs')->name('logs.')->group(function () {
            Route::get('/', [ModerationLogController::class, 'index'])->name('index');
        });

        // F15: User Ban & Sanction
        Route::prefix('bans')->name('bans.')->group(function () {
            Route::get('/', [UserSanctionController::class, 'index'])->name('index');
            Route::post('{id}/ban', [UserSanctionController::class, 'ban'])->name('ban');
            Route::post('{id}/unban', [UserSanctionController::class, 'unban'])->name('unban');
        });
    });

    // === 3. ADMIN ONLY ROUTES (Full Access) ===
    Route::middleware('role:admin')
         ->prefix('admin')
         ->name('admin.')
         ->group(function () {

        Route::apiResource('roles', RoleController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('badges', BadgeController::class)->except(['show']);
        Route::apiResource('tags', TagController::class)->except(['show']);

        // User Management (Advanced)
        Route::get('users', [UserManagementController::class, 'index']);
        Route::get('users/{id}', [UserManagementController::class, 'show']);
        Route::put('users/{id}/role', [UserManagementController::class, 'update']);
        Route::put('users/{id}/profile', [UserAdminController::class, 'updateProfile']);
        Route::put('users/{id}/reset-password', [UserAdminController::class, 'resetPassword']);
    });
});