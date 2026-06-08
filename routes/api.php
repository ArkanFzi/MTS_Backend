<?php

use Illuminate\Support\Facades\Route;

use Modules\Auth\F1_Register\Controllers\RegisterController;
use Modules\Auth\F2_Login\Controllers\LoginController;
use Modules\Auth\F3_Logout\Controllers\LogoutController;

use Modules\Common\F4_SearchPost\Controllers\SearchPostController;
use Modules\Common\F5_FilterByTag\Controllers\FilterTagController;
use Modules\Common\F6_FilterByCategory\Controllers\FilterCategoryController;
use Modules\Common\F7_TrendingPopularPost\Controllers\TrendingController;

use Modules\Admin\F9_UserManagement\Controllers\AdminDashboardController;
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
use Modules\User\F17_Comment\Controllers\CommentController;
use Modules\User\F28_ProfileSettings\Controllers\ProfileController;
use Modules\User\F27_GamificationLeaderboard\Controllers\LeaderboardController;
use Modules\User\F26_NotificationSystem\Controllers\NotificationController;
use Modules\User\F25_FollowUser\Controllers\FollowController;
use Modules\User\F23_LikeSystem\Controllers\LikeController;
use Modules\User\F22_VoteSystem\Controllers\VoteController;
use Modules\User\F20_NestedCommentReply\Controllers\CommentReplyController;
use Modules\User\F19_PostEditHistory\Controllers\PostHistoryController;
use Modules\User\F21_CommentEditHistory\Controllers\CommentHistoryController;
use Modules\User\F18_MarkAcceptedAnswer\Controllers\AcceptedAnswerController;
use Modules\User\F29_BadgeAchievement\Controllers\BadgeAchievementController;
use Modules\User\F24_BookmarkPost\Controllers\BookmarkController;
use Modules\User\F30_UserReport\Controllers\UserReportController;

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
    Route::get('/tags', [TagController::class, 'index']);
    Route::get('/tag/{slug}', [FilterTagController::class, 'filter']);
    Route::get('/category/{slug}', [FilterCategoryController::class, 'filter']);
    Route::get('/trending', [TrendingController::class, 'getTrending']);
    Route::get('/leaderboard', [LeaderboardController::class, 'index']);
});

Route::apiResource('posts', PostController::class)->only(['index', 'show']);
Route::get('comments', [CommentController::class, 'index']);

// ====================== PROTECTED ROUTES ======================
// 1. Semua route di bawah ini wajib sudah login (Sanctum)
Route::middleware(['auth:sanctum'])->group(function () {
    
    Route::post('/auth/logout', [LogoutController::class, 'logout']);

    // --- FITUR PROFILE SETTINGS ---
    Route::prefix('settings')->group(function () {
        Route::get('profile', [ProfileController::class, 'show']);
        Route::put('profile', [ProfileController::class, 'update']);
        Route::put('password', [ProfileController::class, 'updatePassword']);
    });

    // --- FITUR UMUM USER (Sudah Login) ---
    Route::prefix('me')->name('me.')->group(function () {
        Route::get('posts', [PostController::class, 'myPosts']);
        Route::get('badges', [BadgeAchievementController::class, 'index']);
    });
    
    Route::post('posts', [PostController::class, 'store']);
    Route::put('posts/{post}', [PostController::class, 'update']);
    Route::patch('posts/{post}/status', [PostController::class, 'updateStatus']); // Endpoint baru
    Route::delete('posts/{post}', [PostController::class, 'destroy']);

    Route::prefix('posts/{post}')->group(function () {
        Route::get('comments', [CommentController::class, 'index']);
        Route::post('comments', [CommentController::class, 'store']);
        Route::put('comments/{comment}', [CommentController::class, 'update']);
        Route::post('comments/{comment}/replies', [CommentReplyController::class, 'store']);
        Route::put('comments/{comment}/replies/{reply}', [CommentReplyController::class, 'update']);
        Route::post('comments/{comment}/accept', [AcceptedAnswerController::class, 'store']);
    });

    // --- FITUR TAGS (Bisa ditambah user) ---
    Route::post('tags', [TagController::class, 'store']);

    // --- FITUR NOTIFIKASI ---
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::patch('/mark-all-read', [NotificationController::class, 'markAllRead']);
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead']);
    });

    // FITUR FOLLOW
    Route::prefix('users/{id}')->group(function () {
        Route::post('/follow', [FollowController::class, 'toggle']);
        Route::get('/followers', [FollowController::class, 'followers']);
        Route::get('/following', [FollowController::class, 'following']);
    });

    Route::post('/likes/toggle', [LikeController::class, 'toggle']);
    Route::post('/bookmarks/toggle', [BookmarkController::class, 'toggle']);
    Route::get('/bookmarks', [BookmarkController::class, 'index']);
    Route::post('/votes', [VoteController::class, 'vote']);
    Route::post('reports', [UserReportController::class, 'store']);

    // FITUR MODERATOR (Bisa diakses Moderator ATAU Admin) 
    Route::middleware('role:moderator,admin')->prefix('moderator')->name('moderator.')->group(function () {
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('badges', BadgeController::class)->except(['show']);
        Route::apiResource('tags', TagController::class)->except(['show']);

        Route::delete('posts/{post}/comments/{comment}', [CommentController::class, 'destroy']);

        
        Route::prefix('reports')->group(function () {
            Route::get('/', [ReportController::class, 'index']);
            Route::get('{id}', [ReportController::class, 'show']);
            Route::put('{id}', [ReportController::class, 'update']);    
        });

        Route::get('logs', [ModerationLogController::class, 'index']);
        Route::get('posts/{post}/history', [PostHistoryController::class, 'index']);
        Route::get('comments/{comment}/history', [CommentHistoryController::class, 'index']);

        Route::prefix('bans')->group(function () {
            Route::get('/', [UserSanctionController::class, 'index']);
            Route::post('{id}/warn', [UserSanctionController::class, 'warn']);
            Route::post('{id}/ban', [UserSanctionController::class, 'ban']);
            Route::post('{id}/unban', [UserSanctionController::class, 'unban']);
        });
    });

    // --- FITUR KHUSUS ADMIN ---
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('stats/overview', [AdminDashboardController::class, 'overview']);
        Route::get('stats/points-summary', [AdminDashboardController::class, 'pointsSummary']);

        Route::prefix('users')->group(function () {
            Route::get('/', [UserManagementController::class, 'index']);
            Route::get('{id}', [UserManagementController::class, 'show']);
            Route::put('{id}/role', [UserManagementController::class, 'update']);
            Route::put('{id}/profile', [UserAdminController::class, 'updateProfile']);
            Route::put('{id}/reset-password', [UserAdminController::class, 'resetPassword']);
        });
    });
});