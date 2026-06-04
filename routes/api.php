<?php

use Illuminate\Support\Facades\Route;

// ==================== 1. IMPORT MODULES AUTH ====================
use Modules\Auth\F1_Register\Controllers\RegisterController;
use Modules\Auth\F2_Login\Controllers\LoginController;
use Modules\Auth\F3_Logout\Controllers\LogoutController;

// ==================== 2. IMPORT MODULES COMMON ====================
use Modules\Common\F4_SearchPost\Controllers\SearchPostController;
use Modules\Common\F5_FilterByTag\Controllers\FilterTagController;
use Modules\Common\F6_FilterByCategory\Controllers\FilterCategoryController;
use Modules\Common\F7_TrendingPopularPost\Controllers\TrendingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ==================== ROUTE PUBLIK (BISA DIAKSES SIAPA AJA) ====================

// Fitur Auth (Register & Login)
Route::post('/auth/register', [RegisterController::class, 'register']);
Route::post('/auth/login', [LoginController::class, 'login']);

// Fitur F4 Common: Search Post (Contoh: /api/posts/search?q=laravel)
Route::get('/posts/search', [SearchPostController::class, 'search']);

// Fitur F5 Common: Filter Post Berdasarkan Tag (Contoh: /api/posts/tag/javascript)
Route::get('/posts/tag/{slug}', [FilterTagController::class, 'filter']);

// Fitur F6 Common: Filter Post Berdasarkan Kategori (Contoh: /api/posts/category/web-dev)
Route::get('/posts/category/{slug}', [FilterCategoryController::class, 'filter']);

// Fitur F7 Common: Postingan Populer & Trending (Contoh: /api/posts/trending?type=trending)
Route::get('/posts/trending', [TrendingController::class, 'getTrending']);


// ==================== ROUTE PRIVATE (WAJIB BAWA BEARER TOKEN) ====================

Route::middleware('auth:sanctum')->group(function () {
    // Fitur F3 Auth: Logout
    Route::post('/auth/logout', [LogoutController::class, 'logout']);
});