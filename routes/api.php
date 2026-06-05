<?php

use Illuminate\Support\Facades\Route;

use Modules\Auth\F1_Register\Controllers\RegisterController;
use Modules\Auth\F2_Login\Controllers\LoginController;
use Modules\Auth\F3_Logout\Controllers\LogoutController;

use Modules\Common\F4_SearchPost\Controllers\SearchPostController;
use Modules\Common\F5_FilterByTag\Controllers\FilterTagController;
use Modules\Common\F6_FilterByCategory\Controllers\FilterCategoryController;
use Modules\Common\F7_TrendingPopularPost\Controllers\TrendingController;

use Modules\Admin\F8_RoleAndPermission\Controllers\RoleController;
use Modules\Admin\F8_RoleAndPermission\Controllers\UserManagementController;
use Modules\Admin\F9_UserManagement\Controllers\UserAdminController;
use Modules\Admin\F10_CategoryMaster\Controllers\CategoryController;
use Modules\Admin\F11_BadgeMaster\Controllers\BadgeController;
use Modules\Admin\F12_TagMaster\Controllers\TagController;

use Modules\Moderator\F13_ContentReportQueue\Controllers\ReportController;
use Modules\Moderator\F14_ModeratorActionLog\Controllers\ModerationLogController;
use Modules\Moderator\F15_UserBanSanction\Controllers\UserSanctionController;

use Modules\User\F16_Post\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ====================== PUBLIC ROUTES ======================
Route::prefix('auth')->group(function () {
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login']);
});

Route::prefix('explore')->group(function () {
    Route::get('/search', [SearchPostController::class, 'search']);
    Route::get('/tag/{slug}', [FilterTagController::class, 'filter']);
    Route::get('/category/{slug}', [FilterCategoryController::class, 'filter']);
    Route::get('/trending', [TrendingController::class, 'getTrending']);
});

Route::apiResource('posts', PostController::class)->only(['index', 'show']);

// ====================== PROTECTED ROUTES ======================
// 1. Semua route di bawah ini wajib sudah login (Sanctum)
Route::middleware(['auth:sanctum'])->group(function () {
    
    Route::post('/auth/logout', [LogoutController::class, 'logout']);

    // --- FITUR UMUM USER (Sudah Login) ---
    Route::prefix('me')->name('me.')->group(function () {
        Route::get('posts', [PostController::class, 'myPosts']);
    });
    
    Route::post('posts', [PostController::class, 'store']);
    Route::put('posts/{post}', [PostController::class, 'update']);
    Route::delete('posts/{post}', [PostController::class, 'destroy']);

    // --- FITUR MODERATOR (Bisa diakses Moderator ATAU Admin) ---
    Route::middleware('role:moderator,admin')->prefix('moderator')->name('moderator.')->group(function () {
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('badges', BadgeController::class)->except(['show']);
        Route::apiResource('tags', TagController::class)->except(['show']);
        
        Route::prefix('reports')->group(function () {
            Route::get('/', [ReportController::class, 'index']);
            Route::get('{id}', [ReportController::class, 'show']);
            Route::put('{id}', [ReportController::class, 'update']);
        });

        Route::get('logs', [ModerationLogController::class, 'index']);

        Route::prefix('bans')->group(function () {
            Route::get('/', [UserSanctionController::class, 'index']);
            Route::post('{id}/ban', [UserSanctionController::class, 'ban']);
            Route::post('{id}/unban', [UserSanctionController::class, 'unban']);
        });
    });

    // --- FITUR KHUSUS ADMIN ---
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('badges', BadgeController::class)->except(['show']);
        Route::apiResource('tags', TagController::class)->except(['show']);

        Route::prefix('users')->group(function () {
            Route::get('/', [UserManagementController::class, 'index']);
            Route::get('{id}', [UserManagementController::class, 'show']);
            Route::put('{id}/role', [UserManagementController::class, 'update']);
            Route::put('{id}/profile', [UserAdminController::class, 'updateProfile']);
            Route::put('{id}/reset-password', [UserAdminController::class, 'resetPassword']);
        });

        Route::prefix('bans')->group(function () {
            Route::get('/', [UserSanctionController::class, 'index']);
            Route::post('{id}/ban', [UserSanctionController::class, 'ban']);
            Route::post('{id}/unban', [UserSanctionController::class, 'unban']);
        });
    });
});