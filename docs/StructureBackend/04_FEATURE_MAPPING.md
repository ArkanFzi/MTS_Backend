# 04. Feature Mapping - Pemetaan Fitur ke Implementasi

**Versi:** 2.0 (Terverifikasi dari Codebase Aktual)
**Tanggal Update:** 10 Juni 2026

Dokumen ini memetakan setiap fitur ke file implementasi, route API, model, dan middleware yang terlibat.

---

## Navigasi Cepat

- [AUTH Module (F1, F2, F3, F31)](#auth-module)
- [COMMON Module (F4–F7)](#common-module)
- [ADMIN Module (F8–F12)](#admin-module)
- [MODERATOR Module (F13–F15)](#moderator-module)
- [USER Module (F16–F30)](#user-module)

---

## AUTH MODULE

### F1: Register Akun User Baru

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/Auth/F1_Register/` |
| **Controller** | `Controllers/RegisterController.php` |
| **Service** | `Services/RegisterService.php` |
| **Repository** | `Repositories/RegisterRepository.php` |
| **Request** | `Requests/RegisterRequest.php` |
| **Model** | `app/Models/Auth/User.php` |
| **Route** | `POST /api/auth/register` |
| **Middleware** | Publik (CSRF exempt) |
| **Tabel** | `users` |

---

### F2: Login (Sanctum SPA Session)

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/Auth/F2_Login/` |
| **Controller** | `Controllers/LoginController.php` |
| **Service** | `Services/LoginService.php` |
| **Repository** | `Repositories/LoginRepository.php` |
| **Request** | `Requests/LoginRequest.php` |
| **Model** | `app/Models/Auth/User.php` |
| **Route** | `POST /api/auth/login` |
| **Middleware** | Publik (CSRF exempt) |
| **Tabel** | `users`, `sessions` |

---

### F3: Logout (Session Invalidation)

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/Auth/F3_Logout/` |
| **Controller** | `Controllers/LogoutController.php` |
| **Service** | `Services/LogoutService.php` |
| **Model** | — (menggunakan session) |
| **Route** | `POST /api/auth/logout` |
| **Middleware** | `auth:sanctum` |
| **Tabel** | `sessions` |

---

### F31: Forgot & Reset Password

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/Auth/F31_ForgotPassword/` |
| **Controller** | `Controllers/ForgotPasswordController.php` |
| **Service** | `Services/ForgotPasswordService.php` |
| **Repository** | `Repositories/ForgotPasswordRepository.php` |
| **Request** | `Requests/ForgotPasswordRequest.php`, `Requests/ResetPasswordRequest.php` |
| **Jobs** | `Jobs/SendResetPasswordEmailJob.php` |
| **Mail** | `Mail/ResetPasswordMail.php` |
| **Model** | `app/Models/Auth/User.php` |
| **Routes** | `POST /api/auth/forgot-password`, `POST /api/auth/reset-password` |
| **Middleware** | Publik (CSRF exempt) |
| **Tabel** | `users`, `password_reset_tokens` |

---

## COMMON MODULE

### F4: Search Post

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/Common/F4_SearchPost/` |
| **Controller** | `Controllers/SearchPostController.php` |
| **Service** | `Services/SearchPostService.php` |
| **Model** | `app/Models/Content/Post.php` |
| **Route** | `GET /api/explore/search` |
| **Middleware** | Publik |
| **Tabel** | `posts` |

---

### F5: Filter By Tag

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/Common/F5_FilterByTag/` |
| **Controller** | `Controllers/FilterTagController.php` |
| **Service** | `Services/FilterTagService.php` |
| **Model** | `app/Models/Content/Post.php`, `app/Models/Content/Tag.php` |
| **Route** | `GET /api/explore/tag/{slug}` |
| **Middleware** | Publik |
| **Tabel** | `posts`, `post_tags`, `tags` |

---

### F6: Filter By Category

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/Common/F6_FilterByCategory/` |
| **Controller** | `Controllers/FilterCategoryController.php` |
| **Service** | `Services/FilterCategoryService.php` |
| **Model** | `app/Models/Content/Post.php`, `app/Models/Content/Category.php` |
| **Route** | `GET /api/explore/category/{slug}` |
| **Middleware** | Publik |
| **Tabel** | `posts`, `categories` |

---

### F7: Trending & Popular Post

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/Common/F7_TrendingPopularPost/` |
| **Controller** | `Controllers/TrendingController.php` |
| **Service** | `Services/TrendingService.php` |
| **Model** | `app/Models/Content/Post.php` |
| **Route** | `GET /api/explore/trending` |
| **Middleware** | Publik |
| **Tabel** | `posts` |

---

## ADMIN MODULE

### F8: Role & Permission Management

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/Admin/F8_RoleAndPermission/` |
| **Controllers** | `Controllers/RoleController.php`, `Controllers/UserManagementController.php` |
| **Service** | `Services/UserManagementService.php` |
| **Repository** | `Repositories/UserRepository.php` |
| **Request** | `Requests/UpdateUserRequest.php` |
| **Model** | `app/Models/Auth/User.php`, `app/Models/Auth/Role.php`, `app/Models/Auth/UserRole.php` |
| **Routes** | `GET /api/admin/roles`, `GET /api/admin/users`, `GET /api/admin/users/{id}`, `PUT /api/admin/users/{id}/role` |
| **Middleware** | `auth:sanctum` + `role:admin` |
| **Tabel** | `users`, `roles`, `user_roles` |

---

### F9: User Management (Admin Dashboard)

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/Admin/F9_UserManagement/` |
| **Controllers** | `Controllers/AdminDashboardController.php`, `Controllers/UserAdminController.php` |
| **Service** | `Services/UserAdminService.php` |
| **Repository** | `Repositories/UserAdminRepository.php` |
| **Requests** | `Requests/ResetPasswordRequest.php`, `Requests/UpdateProfileRequest.php` |
| **Model** | `app/Models/Auth/User.php` |
| **Routes** | `GET /api/admin/stats/overview`, `GET /api/admin/stats/points-summary`, `PUT /api/admin/users/{id}/profile`, `PUT /api/admin/users/{id}/reset-password` |
| **Middleware** | `auth:sanctum` + `role:admin` |
| **Tabel** | `users`, `points_log` |

---

### F10: Category Master (CRUD)

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/Admin/F10_CategoryMaster/` |
| **Controller** | `Controllers/CategoryController.php` |
| **Service** | `Services/CategoryService.php` |
| **Repository** | `Repositories/CategoryRepository.php` |
| **Requests** | `Requests/StoreCategoryRequest.php`, `Requests/UpdateCategoryRequest.php` |
| **Model** | `app/Models/Content/Category.php` |
| **Routes** | `apiResource /api/moderator/categories` (full CRUD) |
| **Middleware** | `auth:sanctum` + `role:moderator,admin` |
| **Tabel** | `categories` |

---

### F11: Badge Master (CRUD)

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/Admin/F11_BadgeMaster/` |
| **Controller** | `Controllers/BadgeController.php` |
| **Service** | `Services/BadgeService.php` |
| **Repository** | `Repositories/BadgeRepository.php` |
| **Requests** | `Requests/StoreBadgeRequest.php`, `Requests/UpdateBadgeRequest.php` |
| **Model** | `app/Models/Gamification/Badge.php` |
| **Routes** | `apiResource /api/moderator/badges` (except show) |
| **Middleware** | `auth:sanctum` + `role:moderator,admin` |
| **Tabel** | `badges` |

---

### F12: Tag Master (CRUD)

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/Admin/F12_TagMaster/` |
| **Controller** | `Controllers/TagController.php` |
| **Service** | `Services/TagService.php` |
| **Repository** | `Repositories/TagRepository.php` |
| **Requests** | `Requests/StoreTagRequest.php`, `Requests/UpdateTagRequest.php` |
| **Model** | `app/Models/Content/Tag.php` |
| **Routes** | `apiResource /api/moderator/tags` (except show) + `GET /api/explore/tags` (publik) + `POST /api/tags` (auth) |
| **Middleware** | Publik (index), `auth:sanctum` (store), `role:moderator,admin` (CRUD) |
| **Tabel** | `tags` |

---

## MODERATOR MODULE

### F13: Content Report Queue

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/Moderator/F13_ContentReportQueue/` |
| **Controller** | `Controllers/ReportController.php` |
| **Service** | `Services/ReportService.php` |
| **Repository** | `Repositories/ReportRepository.php` |
| **Request** | `Requests/UpdateReportRequest.php` |
| **Model** | `app/Models/Moderation/Report.php` |
| **Routes** | `GET /api/moderator/reports`, `GET /api/moderator/reports/{id}`, `PUT /api/moderator/reports/{id}` |
| **Middleware** | `auth:sanctum` + `role:moderator,admin` |
| **Tabel** | `reports` |

---

### F14: Moderator Action Log

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/Moderator/F14_ModeratorActionLog/` |
| **Controller** | `Controllers/ModerationLogController.php` |
| **Service** | `Services/ModerationLogService.php` |
| **Repository** | `Repositories/ModerationLogRepository.php` |
| **Model** | `app/Models/Moderation/ModerationLog.php` |
| **Route** | `GET /api/moderator/logs` |
| **Middleware** | `auth:sanctum` + `role:moderator,admin` |
| **Tabel** | `moderation_logs` |

---

### F15: User Ban & Sanction

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/Moderator/F15_UserBanSanction/` |
| **Controller** | `Controllers/UserSanctionController.php` |
| **Service** | `Services/UserSanctionService.php` |
| **Repository** | `Repositories/UserSanctionRepository.php` |
| **Requests** | `Requests/BanUserRequest.php`, `Requests/WarnUserRequest.php` |
| **Model** | `app/Models/Auth/User.php`, `app/Models/Moderation/ModerationLog.php` |
| **Routes** | `GET /api/moderator/bans`, `POST /api/moderator/bans/{id}/warn`, `POST /api/moderator/bans/{id}/ban`, `POST /api/moderator/bans/{id}/unban` |
| **Middleware** | `auth:sanctum` + `role:moderator,admin` |
| **Tabel** | `users`, `moderation_logs`, `notifications` |

---

## USER MODULE

### F16: Post (CRUD)

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/User/F16_Post/` |
| **Controller** | `Controllers/PostController.php` |
| **Service** | `Services/PostService.php` |
| **Repository** | `Repositories/PostRepository.php` |
| **Requests** | `Requests/StorePostRequest.php`, `Requests/UpdatePostRequest.php` |
| **Model** | `app/Models/Content/Post.php` |
| **Routes** | `GET /api/posts` (publik), `GET /api/posts/{id}` (publik), `POST /api/posts` (auth), `PUT /api/posts/{post}` (auth), `PATCH /api/posts/{post}/status` (auth), `DELETE /api/posts/{post}` (auth), `GET /api/me/posts` (auth) |
| **Middleware** | Publik (index, show), `auth:sanctum` (store, update, delete, myPosts) |
| **Tabel** | `posts`, `post_tags`, `post_edit_history`, `points_log` |

---

### F17: Comment (CRUD)

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/User/F17_Comment/` |
| **Controller** | `Controllers/CommentController.php` |
| **Service** | `Services/CommentService.php` |
| **Repository** | `Repositories/CommentRepository.php` |
| **Requests** | `Requests/StoreCommentRequest.php`, `Requests/UpdateCommentRequest.php` |
| **Model** | `app/Models/Content/Comment.php` |
| **Routes** | `GET /api/comments` (publik), `GET /api/posts/{post}/comments` (auth), `POST /api/posts/{post}/comments` (auth), `PUT /api/posts/{post}/comments/{comment}` (auth), `DELETE /api/moderator/posts/{post}/comments/{comment}` (mod) |
| **Middleware** | Publik (index), `auth:sanctum` (CRUD), `role:moderator,admin` (delete) |
| **Tabel** | `comments`, `comment_edit_history`, `points_log`, `notifications` |

---

### F18: Mark Accepted Answer

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/User/F18_MarkAcceptedAnswer/` |
| **Controller** | `Controllers/AcceptedAnswerController.php` |
| **Service** | `Services/AcceptedAnswerService.php` |
| **Repository** | `Repositories/AcceptedAnswerRepository.php` |
| **Model** | `app/Models/Content/Post.php`, `app/Models/Content/Comment.php` |
| **Route** | `POST /api/posts/{post}/comments/{comment}/accept` |
| **Middleware** | `auth:sanctum` |
| **Tabel** | `posts`, `comments` |

---

### F19: Post Edit History

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/User/F19_PostEditHistory/` |
| **Controller** | `Controllers/PostHistoryController.php` |
| **Service** | `Services/PostHistoryService.php` |
| **Repository** | `Repositories/PostEditHistoryRepository.php` |
| **Model** | `app/Models/History/PostEditHistory.php` |
| **Route** | `GET /api/moderator/posts/{post}/history` |
| **Middleware** | `auth:sanctum` + `role:moderator,admin` |
| **Tabel** | `post_edit_history` |

---

### F20: Nested Comment Reply

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/User/F20_NestedCommentReply/` |
| **Controller** | `Controllers/CommentReplyController.php` |
| **Service** | `Services/CommentReplyService.php` |
| **Model** | `app/Models/Content/Comment.php` |
| **Routes** | `POST /api/posts/{post}/comments/{comment}/replies`, `PUT /api/posts/{post}/comments/{comment}/replies/{reply}` |
| **Middleware** | `auth:sanctum` |
| **Tabel** | `comments` (parent_id) |

---

### F21: Comment Edit History

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/User/F21_CommentEditHistory/` |
| **Controller** | `Controllers/CommentHistoryController.php` |
| **Service** | `Services/CommentHistoryService.php` |
| **Repository** | `Repositories/CommentEditHistoryRepository.php` |
| **Model** | `app/Models/History/CommentEditHistory.php` |
| **Route** | `GET /api/moderator/comments/{comment}/history` |
| **Middleware** | `auth:sanctum` + `role:moderator,admin` |
| **Tabel** | `comment_edit_history` |

---

### F22: Vote System

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/User/F22_VoteSystem/` |
| **Controller** | `Controllers/VoteController.php` |
| **Service** | `Services/VoteService.php` |
| **Repository** | `Repositories/VoteRepository.php` |
| **Request** | `Requests/VoteRequest.php` |
| **Model** | `app/Models/Interaction/Vote.php`, `app/Models/Content/Post.php`, `app/Models/Content/Comment.php` |
| **Route** | `POST /api/votes` |
| **Middleware** | `auth:sanctum` |
| **Tabel** | `votes`, `posts` (vote_score), `comments` (vote_score), `points_log`, `notifications` |

---

### F23: Like System

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/User/F23_LikeSystem/` |
| **Controller** | `Controllers/LikeController.php` |
| **Service** | `Services/LikeService.php` |
| **Repository** | `Repositories/LikeRepository.php` |
| **Request** | `Requests/ToggleLikeRequest.php` |
| **Model** | `app/Models/Interaction/Like.php` |
| **Route** | `POST /api/likes/toggle` |
| **Middleware** | `auth:sanctum` |
| **Tabel** | `likes` |

---

### F24: Bookmark Post

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/User/F24_BookmarkPost/` |
| **Controller** | `Controllers/BookmarkController.php` |
| **Service** | `Services/BookmarkService.php` |
| **Repository** | `Repositories/BookmarkRepository.php` |
| **Model** | `app/Models/Interaction/Bookmark.php` |
| **Routes** | `POST /api/bookmarks/toggle`, `GET /api/bookmarks` |
| **Middleware** | `auth:sanctum` |
| **Tabel** | `bookmarks` |

---

### F25: Follow User

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/User/F25_FollowUser/` |
| **Controller** | `Controllers/FollowController.php` |
| **Service** | `Services/FollowService.php` |
| **Repository** | `Repositories/FollowRepository.php` |
| **Model** | `app/Models/Interaction/Follow.php` |
| **Routes** | `POST /api/users/{id}/follow`, `GET /api/users/{id}/followers`, `GET /api/users/{id}/following` |
| **Middleware** | `auth:sanctum` |
| **Tabel** | `follows` |

---

### F26: Notification System

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/User/F26_NotificationSystem/` |
| **Controller** | `Controllers/NotificationController.php` |
| **Service** | `Services/NotificationService.php` |
| **Repository** | `Repositories/NotificationRepository.php` |
| **Model** | `app/Models/Moderation/Notification.php` |
| **Routes** | `GET /api/notifications`, `PATCH /api/notifications/mark-all-read`, `PATCH /api/notifications/{id}/read` |
| **Middleware** | `auth:sanctum` |
| **Tabel** | `notifications` |

---

### F27: Gamification Leaderboard

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/User/F27_GamificationLeaderboard/` |
| **Controller** | `Controllers/LeaderboardController.php` |
| **Service** | `Services/GamificationService.php` |
| **Model** | `app/Models/Auth/User.php` |
| **Route** | `GET /api/explore/leaderboard` |
| **Middleware** | Publik |
| **Tabel** | `users` (reputation_points, level) |

---

### F28: Profile Settings

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/User/F28_ProfileSettings/` |
| **Controller** | `Controllers/ProfileController.php` |
| **Service** | `Services/ProfileService.php` |
| **Request** | `Requests/UpdateProfileRequest.php` |
| **Model** | `app/Models/Auth/User.php` |
| **Routes** | `GET /api/settings/profile`, `PUT /api/settings/profile`, `PUT /api/settings/password` |
| **Middleware** | `auth:sanctum` |
| **Tabel** | `users` |

---

### F29: Badge Achievement

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/User/F29_BadgeAchievement/` |
| **Controller** | `Controllers/BadgeAchievementController.php` |
| **Service** | `Services/BadgeAchievementService.php` |
| **Repository** | `Repositories/BadgeAchievementRepository.php` |
| **Model** | `app/Models/Gamification/UserBadge.php`, `app/Models/Gamification/Badge.php`, `app/Models/Gamification/PointsLog.php` |
| **Route** | `GET /api/me/badges` |
| **Middleware** | `auth:sanctum` |
| **Tabel** | `user_badges`, `badges`, `points_log` |

> **Catatan:** `BadgeAchievementService` digunakan secara cross-feature oleh PostService, VoteService, CommentService untuk pemberian poin dan badge otomatis.

---

### F30: User Report

| Aspek | Detail |
|-------|--------|
| **Folder** | `Modules/User/F30_UserReport/` |
| **Controller** | `Controllers/UserReportController.php` |
| **Service** | `Services/UserReportService.php` |
| **Repository** | `Repositories/UserReportRepository.php` |
| **Request** | `Requests/StoreReportRequest.php` |
| **Model** | `app/Models/Moderation/Report.php` |
| **Route** | `POST /api/reports` |
| **Middleware** | `auth:sanctum` |
| **Tabel** | `reports` |

---

## Ringkasan Middleware per Route Group

| Route Group | Middleware | Jumlah Route |
|-------------|-----------|-------------|
| Publik Auth | *(none)* | 4 (register, login, forgot-password, reset-password) |
| Publik Explore | *(none)* | 6 (search, tags, tag/{slug}, category/{slug}, trending, leaderboard) |
| Publik Posts/Comments | *(none)* | 3 (posts index, posts show, comments index) |
| User (auth:sanctum) | `auth:sanctum` | ~25 routes |
| Moderator | `auth:sanctum` + `role:moderator,admin` | ~15 routes |
| Admin | `auth:sanctum` + `role:admin` | ~7 routes |

---

## Cross-Feature Dependencies

Beberapa Service digunakan oleh fitur lain:

| Service | Dipanggil Oleh |
|---------|---------------|
| `BadgeAchievementService` (F29) | PostService (F16), VoteService (F22), CommentService (F17), UserSanctionService (F15) |
| `NotificationService` (F26) | VoteService (F22), CommentService (F17), UserSanctionService (F15) |
| `ModerationLogService` (F14) | UserSanctionService (F15), ReportService (F13) |

---

**Selanjutnya: Baca [05_ARCHITECTURE_PATTERNS.md](05_ARCHITECTURE_PATTERNS.md) untuk detail pola arsitektur →**
