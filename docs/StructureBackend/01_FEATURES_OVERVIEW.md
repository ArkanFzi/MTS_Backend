# 01. Features Overview - 31 Advanced Features (F1–F31)

## ��� Breakdown Lengkap 31 Feature Folder

Dokumentasi ini menjelaskan setiap fitur dalam **31 feature folder** (F1–F3, F31, F4–F30), diorganisir berdasarkan modul domain. Semua informasi di halaman ini sudah disesuaikan dengan **codebase aktual** (routes, models, controllers).

---

## ��� AUTH MODULE (4 Folder) → F1, F2, F3, F31

### Modul Path: `Modules/Auth/`

**Autentikasi**: Sanctum SPA cookie-based (bukan token-based). Menggunakan `$middleware->statefulApi()` + `withCredentials: true`. Session-based auth dengan HttpOnly cookies.

---

#### ✅ F1: Register Akun User Baru
**Folder**: `Modules/Auth/F1_Register/`

- **Deskripsi**: Pengguna baru dapat membuat akun dengan mengisi data profil
- **Related Table**: `users`
- **API Endpoint**: `POST /api/auth/register`
- **Controller**: `RegisterController@register`
- **Validation Rules**:
  - Email unik
  - Password minimum 8 karakter
  - Required fields: username, email, password
- **Response**: User object (Sanctum session cookie)
- **File Location**:
  - Controller: `Modules/Auth/F1_Register/Controllers/RegisterController.php`
  - Service: `Modules/Auth/F1_Register/Services/`
  - Request: `Modules/Auth/F1_Register/Requests/`
  - Repository: `Modules/Auth/F1_Register/Repositories/`

---

#### ✅ F2: Login (Sanctum SPA Session)
**Folder**: `Modules/Auth/F2_Login/`

- **Deskripsi**: User login dan mendapatkan session cookie (bukan API token). Sanctum menerbitkan HttpOnly cookie untuk SPA authentication.
- **Related Table**: `users`, `sessions`
- **API Endpoint**: `POST /api/auth/login`
- **Controller**: `LoginController@login`
- **Validation Rules**:
  - Email harus terdaftar
  - Password harus cocok
  - User tidak boleh banned (`is_banned = false`)
- **Response**: User object + Set-Cookie header (HttpOnly session cookie)
- **File Location**:
  - Controller: `Modules/Auth/F2_Login/Controllers/LoginController.php`
  - Service: `Modules/Auth/F2_Login/Services/`
  - Request: `Modules/Auth/F2_Login/Requests/`
  - Repository: `Modules/Auth/F2_Login/Repositories/`

---

#### ✅ F3: Logout (Session Invalidation)
**Folder**: `Modules/Auth/F3_Logout/`

- **Deskripsi**: User logout, session di-invalidate dari server
- **Related Table**: `sessions`
- **API Endpoint**: `POST /api/auth/logout` (protected, `auth:sanctum`)
- **Controller**: `LogoutController@logout`
- **Behavior**: Menghapus session dari database, cookie di-clear
- **File Location**:
  - Controller: `Modules/Auth/F3_Logout/Controllers/LogoutController.php`
  - Service: `Modules/Auth/F3_Logout/Services/`

---

#### ✅ F31: Forgot & Reset Password
**Folder**: `Modules/Auth/F31_ForgotPassword/`

- **Deskripsi**: User yang lupa password dapat meminta link reset via email, lalu mengatur ulang password
- **Related Table**: `users`, `password_reset_tokens`
- **API Endpoints**:
  - `POST /api/auth/forgot-password` — Kirim reset link ke email
  - `POST /api/auth/reset-password` — Reset password dengan token
- **Controller**: `ForgotPasswordController@sendResetLink`, `ForgotPasswordController@resetPassword`
- **CSRF**: Exempt (login, register, forgot-password, reset-password)
- **File Location**:
  - Controller: `Modules/Auth/F31_ForgotPassword/Controllers/ForgotPasswordController.php`
  - Service: `Modules/Auth/F31_ForgotPassword/Services/`
  - Job: `Modules/Auth/F31_ForgotPassword/Jobs/SendResetPasswordEmailJob.php`
  - Mail: `Modules/Auth/F31_ForgotPassword/Mail/ResetPasswordMail.php`
  - Request: `Modules/Auth/F31_ForgotPassword/Requests/`
  - Repository: `Modules/Auth/F31_ForgotPassword/Repositories/`

---

## ��� COMMON/EXPLORE MODULE (4 Folder) → F4–F7

### Modul Path: `Modules/Common/`

**Deskripsi Modul**: Fitur read-only publik untuk browse dan mencari konten forum. Semua endpoint publik (tidak perlu login). Hanya menampilkan post dengan `status = 'published'`.

---

#### ✅ F4: Search Postingan
**Folder**: `Modules/Common/F4_SearchPost/`

- **Deskripsi**: User dapat mencari postingan berdasarkan judul dan isi
- **Related Table**: `posts`
- **API Endpoint**: `GET /api/explore/search?q=keyword`
- **Controller**: `SearchPostController@search`
- **Search Logic**: Filter `WHERE status = 'published'`, ILIKE search pada kolom `title` dan `body`
- **File Location**:
  - Controller: `Modules/Common/F4_SearchPost/Controllers/SearchPostController.php`
  - Service: `Modules/Common/F4_SearchPost/Services/`

---

#### ✅ F5: Filter Postingan berdasarkan Tag
**Folder**: `Modules/Common/F5_FilterByTag/`

- **Deskripsi**: Tampilkan postingan yang memiliki tag tertentu (berdasarkan slug)
- **Related Table**: `posts`, `post_tags`, `tags`
- **API Endpoint**: `GET /api/explore/tag/{slug}`
- **Controller**: `FilterTagController@filter`
- **Search Logic**: Filter by tag slug, `WHERE status = 'published'`
- **File Location**:
  - Controller: `Modules/Common/F5_FilterByTag/Controllers/FilterTagController.php`
  - Service: `Modules/Common/F5_FilterByTag/Services/`

---

#### ✅ F6: Filter Postingan berdasarkan Kategori
**Folder**: `Modules/Common/F6_FilterByCategory/`

- **Deskripsi**: Tampilkan postingan dalam kategori tertentu (berdasarkan slug)
- **Related Table**: `posts`, `categories`
- **API Endpoint**: `GET /api/explore/category/{slug}`
- **Controller**: `FilterCategoryController@filter`
- **Search Logic**: Filter by category slug, `WHERE status = 'published'`
- **File Location**:
  - Controller: `Modules/Common/F6_FilterByCategory/Controllers/FilterCategoryController.php`
  - Service: `Modules/Common/F6_FilterByCategory/Services/`

---

#### ✅ F7: Trending / Popular Posts
**Folder**: `Modules/Common/F7_TrendingPopularPost/`

- **Deskripsi**: Menampilkan postingan trending atau popular berdasarkan engagement metrics
- **Related Table**: `posts`
- **API Endpoint**: `GET /api/explore/trending`
- **Controller**: `TrendingController@getTrending`
- **Ranking Metrics**:
  - `vote_score` (upvote - downvote) → **Trending**
  - `view_count` (jumlah views) → **Popular**
- **Search Logic**: Filter `WHERE status = 'published'`, sort by metric
- **File Location**:
  - Controller: `Modules/Common/F7_TrendingPopularPost/Controllers/TrendingController.php`
  - Service: `Modules/Common/F7_TrendingPopularPost/Services/`

---

## ���‍��� ADMIN MODULES (5 Folder) → F8–F12

### Path: `Modules/Admin/`

**Deskripsi Modul**: Administrative features untuk manajemen sistem oleh Administrator. Semua endpoint di bawah middleware `role:admin`.

---

#### ✅ F8: Role & Permission Management
**Folder**: `Modules/Admin/F8_RoleAndPermission/`

- **Deskripsi**: Admin dapat melihat daftar role dan mengubah role yang di-assign ke user
- **Related Table**: `roles` (fields: `name`, `permissions`), `user_roles` (pivot: `user_id`, `role_id`, `assigned_at`)
- **API Endpoints**:
  - `GET /api/admin/roles` — List semua role → `RoleController@index`
  - `GET /api/admin/users` — List users → `UserManagementController@index`
  - `GET /api/admin/users/{id}` — Detail user → `UserManagementController@show`
  - `PUT /api/admin/users/{id}/role` — Update role user → `UserManagementController@update`
- **Roles Available**: admin, moderator, user
- **File Location**:
  - Controller: `Modules/Admin/F8_RoleAndPermission/Controllers/RoleController.php`
  - Controller: `Modules/Admin/F8_RoleAndPermission/Controllers/UserManagementController.php`
  - Service: `Modules/Admin/F8_RoleAndPermission/Services/`
  - Request: `Modules/Admin/F8_RoleAndPermission/Requests/`
  - Repository: `Modules/Admin/F8_RoleAndPermission/Repositories/`

---

#### ✅ F9: User Management & Admin Dashboard
**Folder**: `Modules/Admin/F9_UserManagement/`

- **Deskripsi**: Admin dashboard (statistik overview & points summary) serta manajemen profil user (edit profil, reset password)
- **Related Table**: `users`, `points_log`
- **API Endpoints**:
  - `GET /api/admin/stats/overview` — Dashboard statistik → `AdminDashboardController@overview`
  - `GET /api/admin/stats/points-summary` — Ringkasan poin → `AdminDashboardController@pointsSummary`
  - `PUT /api/admin/users/{id}/profile` — Update profil user → `UserAdminController@updateProfile`
  - `PUT /api/admin/users/{id}/reset-password` — Reset password user → `UserAdminController@resetPassword`
- **File Location**:
  - Controller: `Modules/Admin/F9_UserManagement/Controllers/AdminDashboardController.php`
  - Controller: `Modules/Admin/F9_UserManagement/Controllers/UserAdminController.php`
  - Service: `Modules/Admin/F9_UserManagement/Services/`
  - Request: `Modules/Admin/F9_UserManagement/Requests/`
  - Repository: `Modules/Admin/F9_UserManagement/Repositories/`

---

#### ✅ F10: CRUD Master Kategori Forum
**Folder**: `Modules/Admin/F10_CategoryMaster/`

- **Deskripsi**: CRUD lengkap untuk kategori forum (dapat diakses moderator & admin)
- **Related Table**: `categories` (fields: `name`, `slug`, `description`, `parent_id`, `created_at`)
- **API Endpoint**: `apiResource /api/moderator/categories` (GET, POST, PUT, DELETE) → `CategoryController`
- **Features**:
  - Parent-child hierarchy (`parent_id` self-referencing)
  - Slug auto-generated
- **Validation**: Name wajib unik
- **File Location**:
  - Controller: `Modules/Admin/F10_CategoryMaster/Controllers/CategoryController.php`
  - Service: `Modules/Admin/F10_CategoryMaster/Services/`
  - Request: `Modules/Admin/F10_CategoryMaster/Requests/`
  - Repository: `Modules/Admin/F10_CategoryMaster/Repositories/`

---

#### ✅ F11: CRUD Master Badge / Achievement
**Folder**: `Modules/Admin/F11_BadgeMaster/`

- **Deskripsi**: CRUD lengkap untuk badge/achievement gamification (dapat diakses moderator & admin)
- **Related Table**: `badges` (fields: `name`, `description`, `icon_url`, `tier`, `condition_type`, `condition_value`, `created_at`)
- **API Endpoint**: `apiResource /api/moderator/badges` (GET, POST, PUT, DELETE — except show) → `BadgeController`
- **Badge Tiers**: bronze, silver, gold, platinum
- **Features**:
  - Tier system (bronze → silver → gold → platinum)
  - Auto-award condition configuration (`condition_type` + `condition_value`)
- **File Location**:
  - Controller: `Modules/Admin/F11_BadgeMaster/Controllers/BadgeController.php`
  - Service: `Modules/Admin/F11_BadgeMaster/Services/`
  - Request: `Modules/Admin/F11_BadgeMaster/Requests/`
  - Repository: `Modules/Admin/F11_BadgeMaster/Repositories/`

---

#### ✅ F12: CRUD Master Tag Forum
**Folder**: `Modules/Admin/F12_TagMaster/`

- **Deskripsi**: CRUD lengkap untuk tag forum (dapat diakses moderator & admin)
- **Related Table**: `tags` (fields: `name`, `slug`, `color`, `usage_count`, `created_at`)
- **API Endpoints**:
  - `apiResource /api/moderator/tags` (GET, POST, PUT, DELETE — except show) → `TagController`
  - `GET /api/explore/tags` — List semua tag (publik) → `TagController@index`
  - `POST /api/tags` — User biasa juga bisa create tag (protected)
- **Features**:
  - Hex color per tag
  - Slug & usage_count tracking
- **File Location**:
  - Controller: `Modules/Admin/F12_TagMaster/Controllers/TagController.php`
  - Service: `Modules/Admin/F12_TagMaster/Services/`
  - Request: `Modules/Admin/F12_TagMaster/Requests/`
  - Repository: `Modules/Admin/F12_TagMaster/Repositories/`

---

## ���️ MODERATOR MODULES (3 Folder) → F13–F15

### Path: `Modules/Moderator/`

**Deskripsi Modul**: Moderasi konten dan enforcement aturan komunitas. Semua endpoint di bawah middleware `role:moderator,admin`.

---

#### ✅ F13: Content Report Queue
**Folder**: `Modules/Moderator/F13_ContentReportQueue/`

- **Deskripsi**: Moderator dapat melihat, meninjau, dan menyelesaikan laporan konten yang bermasalah
- **Related Table**: `reports` (fields: `reporter_id`, `target_id`, `target_type`, `reason`, `description`, `status`, `resolved_by`, `created_at`, `resolved_at`)
- **API Endpoints**:
  - `GET /api/moderator/reports` — List report → `ReportController@index`
  - `GET /api/moderator/reports/{id}` — Detail report → `ReportController@show`
  - `PUT /api/moderator/reports/{id}` — Resolve/update report → `ReportController@update`
- **Behavior**: Resolve report membuat `ModerationLog`, memotong poin user, memberi warning atau ban
- **Polymorphic**: `target_id` + `target_type` (bisa post atau comment)
- **File Location**:
  - Controller: `Modules/Moderator/F13_ContentReportQueue/Controllers/ReportController.php`
  - Service: `Modules/Moderator/F13_ContentReportQueue/Services/ReportService.php`
  - Request: `Modules/Moderator/F13_ContentReportQueue/Requests/`
  - Repository: `Modules/Moderator/F13_ContentReportQueue/Repositories/`

---

#### ✅ F14: Moderator Action Log
**Folder**: `Modules/Moderator/F14_ModeratorActionLog/`

- **Deskripsi**: Pencatatan otomatis semua aksi moderasi untuk audit trail
- **Related Table**: `moderation_logs` (fields: `moderator_id`, `target_user_id`, `action_type`, `reason`, `notes`, `created_at`)
- **API Endpoint**:
  - `GET /api/moderator/logs` — List semua aksi moderator → `ModerationLogController@index`
- **Logged Actions**: Ban/Unban, Warn, Resolve report, Delete content
- **File Location**:
  - Controller: `Modules/Moderator/F14_ModeratorActionLog/Controllers/ModerationLogController.php`
  - Service: `Modules/Moderator/F14_ModeratorActionLog/Services/ModerationLogService.php`
  - Repository: `Modules/Moderator/F14_ModeratorActionLog/Repositories/`

---

#### ✅ F15: User Ban / Sanction
**Folder**: `Modules/Moderator/F15_UserBanSanction/`

- **Deskripsi**: Moderator dapat memberi warning, ban, atau unban user
- **Related Table**: `users` (field `is_banned`), `moderation_logs`
- **API Endpoints**:
  - `GET /api/moderator/bans` — List user yang di-ban → `UserSanctionController@index`
  - `POST /api/moderator/bans/{id}/warn` — Warning user → `UserSanctionController@warn`
  - `POST /api/moderator/bans/{id}/ban` — Ban user → `UserSanctionController@ban`
  - `POST /api/moderator/bans/{id}/unban` — Unban user → `UserSanctionController@unban`
- **File Location**:
  - Controller: `Modules/Moderator/F15_UserBanSanction/Controllers/UserSanctionController.php`
  - Service: `Modules/Moderator/F15_UserBanSanction/Services/UserSanctionService.php`
  - Request: `Modules/Moderator/F15_UserBanSanction/Requests/`
  - Repository: `Modules/Moderator/F15_UserBanSanction/Repositories/`

---

## ��� USER MODULES (15 Folder) → F16–F30

### Path: `Modules/User/`

**Deskripsi Modul**: Core forum features — tempat user berinteraksi dengan konten dan satu sama lain (Stack Overflow style). Semua endpoint di bawah middleware `auth:sanctum` kecuali yang bersifat publik.

---

### ��� POST & COMMENT FEATURES

#### ✅ F16: CRUD Postingan
**Folder**: `Modules/User/F16_Post/`

- **Deskripsi**: User dapat membuat, mengedit, menghapus, dan mengubah status postingan. Index & show bersifat publik.
- **Related Table**: `posts` (fields: `user_id`, `category_id`, `title`, `body`, `status`, `view_count`, `vote_score`, `is_answered`, `accepted_answer_id`), `post_tags` (pivot), `tags`
- **API Endpoints**:
  - `GET /api/posts` — List semua post (publik) → `PostController@index`
  - `GET /api/posts/{post}` — Detail post (publik) → `PostController@show`
  - `POST /api/posts` — Buat post baru (auth) → `PostController@store`
  - `PUT /api/posts/{post}` — Edit post (auth) → `PostController@update`
  - `PATCH /api/posts/{post}/status` — Update status post (auth) → `PostController@updateStatus`
  - `DELETE /api/posts/{post}` — Hapus post / soft delete (auth) → `PostController@destroy`
  - `GET /api/me/posts` — List postingan milik sendiri (auth) → `PostController@myPosts`
- **Soft Delete**: Menggunakan `SoftDeletes` trait
- **File Location**:
  - Controller: `Modules/User/F16_Post/Controllers/PostController.php`
  - Service: `Modules/User/F16_Post/Services/`
  - Request: `Modules/User/F16_Post/Requests/`
  - Repository: `Modules/User/F16_Post/Repositories/`

---

#### ✅ F17: CRUD Komentar pada Post
**Folder**: `Modules/User/F17_Comment/`

- **Deskripsi**: User dapat membuat, mengedit komentar pada postingan. Moderator/admin dapat menghapus.
- **Related Table**: `comments` (fields: `post_id`, `user_id`, `parent_id`, `body`, `vote_score`, `is_accepted`)
- **API Endpoints**:
  - `GET /api/comments` — List semua komentar (publik) → `CommentController@index`
  - `GET /api/posts/{post}/comments` — List komentar per post (auth) → `CommentController@index`
  - `POST /api/posts/{post}/comments` — Buat komentar (auth) → `CommentController@store`
  - `PUT /api/posts/{post}/comments/{comment}` — Edit komentar (auth) → `CommentController@update`
  - `DELETE /api/moderator/posts/{post}/comments/{comment}` — Hapus komentar (moderator/admin) → `CommentController@destroy`
- **Soft Delete**: Menggunakan `SoftDeletes` trait
- **File Location**:
  - Controller: `Modules/User/F17_Comment/Controllers/CommentController.php`
  - Service: `Modules/User/F17_Comment/Services/`
  - Request: `Modules/User/F17_Comment/Requests/`
  - Repository: `Modules/User/F17_Comment/Repositories/`

---

#### ✅ F18: Mark Accepted Answer
**Folder**: `Modules/User/F18_MarkAcceptedAnswer/`

- **Deskripsi**: OP (Original Poster) dapat menandai satu comment sebagai jawaban terbaik
- **Related Table**: `posts` (fields `is_answered`, `accepted_answer_id`), `comments` (field `is_accepted`)
- **API Endpoint**: `POST /api/posts/{post}/comments/{comment}/accept` → `AcceptedAnswerController@store`
- **Business Logic**:
  - Set `comments.is_accepted = true`
  - Update `posts.is_answered = true` dan `posts.accepted_answer_id`
  - Kirim notifikasi ke comment author
  - Award **+15 poin** ke comment author (via `points_log`)
- **File Location**:
  - Controller: `Modules/User/F18_MarkAcceptedAnswer/Controllers/AcceptedAnswerController.php`
  - Service: `Modules/User/F18_MarkAcceptedAnswer/Services/`
  - Repository: `Modules/User/F18_MarkAcceptedAnswer/Repositories/`

---

#### ✅ F19: Post Edit History
**Folder**: `Modules/User/F19_PostEditHistory/`

- **Deskripsi**: Melihat riwayat edit postingan (hanya moderator/admin)
- **Related Table**: `post_edit_history` (fields: `post_id`, `edited_by`, `body_before`, `body_after`, `reason`, `edited_at`)
- **API Endpoint**: `GET /api/moderator/posts/{post}/history` → `PostHistoryController@index`
- **Tracked Fields**: `body` (before & after), `reason`
- **File Location**:
  - Controller: `Modules/User/F19_PostEditHistory/Controllers/PostHistoryController.php`
  - Service: `Modules/User/F19_PostEditHistory/Services/`
  - Repository: `Modules/User/F19_PostEditHistory/Repositories/`

---

#### ✅ F20: Nested Comment Reply
**Folder**: `Modules/User/F20_NestedCommentReply/`

- **Deskripsi**: User dapat membalas komentar lain secara nested menggunakan `parent_id`
- **Related Table**: `comments` (field `parent_id` — self-referencing)
- **API Endpoints**:
  - `POST /api/posts/{post}/comments/{comment}/replies` — Buat balasan → `CommentReplyController@store`
  - `PUT /api/posts/{post}/comments/{comment}/replies/{reply}` — Edit balasan → `CommentReplyController@update`
- **File Location**:
  - Controller: `Modules/User/F20_NestedCommentReply/Controllers/CommentReplyController.php`
  - Service: `Modules/User/F20_NestedCommentReply/Services/`

---

#### ✅ F21: Comment Edit History
**Folder**: `Modules/User/F21_CommentEditHistory/`

- **Deskripsi**: Melihat riwayat edit komentar (hanya moderator/admin)
- **Related Table**: `comment_edit_history` (fields: `comment_id`, `edited_by`, `body_before`, `body_after`, `edited_at`)
- **API Endpoint**: `GET /api/moderator/comments/{comment}/history` → `CommentHistoryController@index`
- **File Location**:
  - Controller: `Modules/User/F21_CommentEditHistory/Controllers/CommentHistoryController.php`
  - Service: `Modules/User/F21_CommentEditHistory/Services/`
  - Repository: `Modules/User/F21_CommentEditHistory/Repositories/`

---

### ⭐ INTERACTION FEATURES

#### ✅ F22: Vote System (Upvote / Downvote)
**Folder**: `Modules/User/F22_VoteSystem/`

- **Deskripsi**: User dapat memberikan upvote atau downvote pada post maupun comment (polymorphic)
- **Related Table**: `votes` (fields: `user_id`, `target_id`, `target_type`, `vote_type`, `created_at`), `posts` (field `vote_score`), `comments` (field `vote_score`)
- **API Endpoint**: `POST /api/votes` → `VoteController@vote`
- **Request Body**: `{ "target_id": "uuid", "target_type": "post|comment", "vote_type": "upvote|downvote" }`
- **Business Logic**: 1 vote per user per target, dapat change vote, vote_score = upvotes - downvotes
- **Polymorphic**: `target_id` + `target_type`
- **File Location**:
  - Controller: `Modules/User/F22_VoteSystem/Controllers/VoteController.php`
  - Service: `Modules/User/F22_VoteSystem/Services/`
  - Request: `Modules/User/F22_VoteSystem/Requests/`
  - Repository: `Modules/User/F22_VoteSystem/Repositories/`

---

#### ✅ F23: Like System
**Folder**: `Modules/User/F23_LikeSystem/`

- **Deskripsi**: User dapat memberikan like pada post atau comment (polymorphic toggle)
- **Related Table**: `likes` (fields: `user_id`, `target_id`, `target_type`, `created_at`)
- **API Endpoint**: `POST /api/likes/toggle` → `LikeController@toggle`
- **Behavior**: Toggle — like jika belum, unlike jika sudah. 1 like per user per target.
- **Polymorphic**: `target_id` + `target_type`
- **File Location**:
  - Controller: `Modules/User/F23_LikeSystem/Controllers/LikeController.php`
  - Service: `Modules/User/F23_LikeSystem/Services/`
  - Request: `Modules/User/F23_LikeSystem/Requests/`
  - Repository: `Modules/User/F23_LikeSystem/Repositories/`

---

#### ✅ F24: Bookmark Post
**Folder**: `Modules/User/F24_BookmarkPost/`

- **Deskripsi**: User dapat menyimpan postingan untuk dibaca kemudian
- **Related Table**: `bookmarks` (fields: `user_id`, `post_id`, `created_at`)
- **API Endpoints**:
  - `POST /api/bookmarks/toggle` — Toggle bookmark → `BookmarkController@toggle`
  - `GET /api/bookmarks` — List user's bookmarks → `BookmarkController@index`
- **File Location**:
  - Controller: `Modules/User/F24_BookmarkPost/Controllers/BookmarkController.php`
  - Service: `Modules/User/F24_BookmarkPost/Services/`
  - Repository: `Modules/User/F24_BookmarkPost/Repositories/`

---

#### ✅ F25: Follow User
**Folder**: `Modules/User/F25_FollowUser/`

- **Deskripsi**: User dapat follow/unfollow user lain
- **Related Table**: `follows` (fields: `follower_id`, `following_id`, `created_at`)
- **API Endpoints**:
  - `POST /api/users/{id}/follow` — Toggle follow → `FollowController@toggle`
  - `GET /api/users/{id}/followers` — List followers → `FollowController@followers`
  - `GET /api/users/{id}/following` — List following → `FollowController@following`
- **File Location**:
  - Controller: `Modules/User/F25_FollowUser/Controllers/FollowController.php`
  - Service: `Modules/User/F25_FollowUser/Services/`
  - Repository: `Modules/User/F25_FollowUser/Repositories/`

---

### ��� NOTIFICATION, GAMIFICATION, PROFILE & REPORT

#### ✅ F26: Notification System
**Folder**: `Modules/User/F26_NotificationSystem/`

- **Deskripsi**: Sistem notifikasi untuk memberitahu user tentang event penting
- **Related Table**: `notifications` (fields: `user_id`, `actor_id`, `type`, `reference_id`, `reference_type`, `is_read`, `created_at`)
- **Notification Types**: `comment`, `upvote`, `accepted_answer`, `follow`, `badge`
- **API Endpoints**:
  - `GET /api/notifications` — List notifications → `NotificationController@index`
  - `PATCH /api/notifications/mark-all-read` — Mark semua read → `NotificationController@markAllRead`
  - `PATCH /api/notifications/{id}/read` — Mark satu read → `NotificationController@markAsRead`
- **File Location**:
  - Controller: `Modules/User/F26_NotificationSystem/Controllers/NotificationController.php`
  - Service: `Modules/User/F26_NotificationSystem/Services/`
  - Repository: `Modules/User/F26_NotificationSystem/Repositories/`

---

#### ✅ F27: Gamification Leaderboard
**Folder**: `Modules/User/F27_GamificationLeaderboard/`

- **Deskripsi**: Menampilkan leaderboard berdasarkan reputation points
- **Related Table**: `users` (field `reputation_points`), `points_log` (fields: `user_id`, `points`, `action_type`, `reference_id`, `description`, `created_at`)
- **API Endpoint**: `GET /api/explore/leaderboard` → `LeaderboardController@index` (publik)
- **File Location**:
  - Controller: `Modules/User/F27_GamificationLeaderboard/Controllers/LeaderboardController.php`
  - Service: `Modules/User/F27_GamificationLeaderboard/Services/`

---

#### ✅ F28: Profile Settings
**Folder**: `Modules/User/F28_ProfileSettings/`

- **Deskripsi**: User dapat melihat dan mengedit profil serta mengganti password
- **Related Table**: `users` (fields: `username`, `email`, `avatar_url`, `bio`, `password_hash`)
- **API Endpoints**:
  - `GET /api/settings/profile` — Lihat profil → `ProfileController@show`
  - `PUT /api/settings/profile` — Update profil → `ProfileController@update`
  - `PUT /api/settings/password` — Ganti password → `ProfileController@updatePassword`
- **File Location**:
  - Controller: `Modules/User/F28_ProfileSettings/Controllers/ProfileController.php`
  - Service: `Modules/User/F28_ProfileSettings/Services/`
  - Request: `Modules/User/F28_ProfileSettings/Requests/`
  - Repository: `Modules/User/F28_ProfileSettings/Repositories/`

---

#### ✅ F29: Badge Achievement
**Folder**: `Modules/User/F29_BadgeAchievement/`

- **Deskripsi**: User dapat melihat badge yang telah diraih
- **Related Table**: `user_badges` (fields: `user_id`, `badge_id`, `earned_at`), `badges`
- **API Endpoint**: `GET /api/me/badges` → `BadgeAchievementController@index`
- **File Location**:
  - Controller: `Modules/User/F29_BadgeAchievement/Controllers/BadgeAchievementController.php`
  - Service: `Modules/User/F29_BadgeAchievement/Services/`
  - Repository: `Modules/User/F29_BadgeAchievement/Repositories/`

---

#### ✅ F30: User Report (Lapor Konten)
**Folder**: `Modules/User/F30_UserReport/`

- **Deskripsi**: User dapat melaporkan konten yang melanggar aturan (post atau comment)
- **Related Table**: `reports` (fields: `reporter_id`, `target_id`, `target_type`, `reason`, `description`, `status`, `resolved_by`, `created_at`, `resolved_at`)
- **API Endpoint**: `POST /api/reports` → `UserReportController@store`
- **Request Body**: `{ "target_id": "uuid", "target_type": "post|comment", "reason": "...", "description": "..." }`
- **Polymorphic**: `target_id` + `target_type`
- **File Location**:
  - Controller: `Modules/User/F30_UserReport/Controllers/UserReportController.php`
  - Service: `Modules/User/F30_UserReport/Services/`
  - Request: `Modules/User/F30_UserReport/Requests/`
  - Repository: `Modules/User/F30_UserReport/Repositories/`

---

## ��� Summary Table

| # | Folder | Fitur | Modul | API Endpoint | Table(s) |
|---|--------|-------|-------|--------------|----------|
| F1 | F1_Register | Register | Auth | POST /api/auth/register | users |
| F2 | F2_Login | Login (Session) | Auth | POST /api/auth/login | users, sessions |
| F3 | F3_Logout | Logout | Auth | POST /api/auth/logout | sessions |
| F31 | F31_ForgotPassword | Forgot & Reset Password | Auth | POST /api/auth/forgot-password, POST /api/auth/reset-password | users, password_reset_tokens |
| F4 | F4_SearchPost | Search | Common | GET /api/explore/search?q= | posts |
| F5 | F5_FilterByTag | Filter by Tag | Common | GET /api/explore/tag/{slug} | posts, post_tags, tags |
| F6 | F6_FilterByCategory | Filter by Category | Common | GET /api/explore/category/{slug} | posts, categories |
| F7 | F7_TrendingPopularPost | Trending / Popular | Common | GET /api/explore/trending | posts |
| F8 | F8_RoleAndPermission | Role & Permission | Admin | GET /api/admin/roles, GET/PUT /api/admin/users/{id}/role | roles, user_roles |
| F9 | F9_UserManagement | Admin Dashboard & User Mgmt | Admin | GET /api/admin/stats/*, PUT /api/admin/users/{id}/profile | users, points_log |
| F10 | F10_CategoryMaster | Category CRUD | Admin | apiResource /api/moderator/categories | categories |
| F11 | F11_BadgeMaster | Badge CRUD | Admin | apiResource /api/moderator/badges | badges |
| F12 | F12_TagMaster | Tag CRUD | Admin | apiResource /api/moderator/tags | tags |
| F13 | F13_ContentReportQueue | Report Queue | Moderator | GET/PUT /api/moderator/reports | reports |
| F14 | F14_ModeratorActionLog | Moderation Log | Moderator | GET /api/moderator/logs | moderation_logs |
| F15 | F15_UserBanSanction | Ban / Warn / Unban | Moderator | POST /api/moderator/bans/{id}/* | users, moderation_logs |
| F16 | F16_Post | CRUD Post | User | apiResource posts + /api/me/posts | posts, post_tags |
| F17 | F17_Comment | CRUD Comment | User | /api/posts/{post}/comments | comments |
| F18 | F18_MarkAcceptedAnswer | Accept Answer | User | POST /api/posts/{post}/comments/{comment}/accept | posts, comments, points_log, notifications |
| F19 | F19_PostEditHistory | Post History | User | GET /api/moderator/posts/{post}/history | post_edit_history |
| F20 | F20_NestedCommentReply | Nested Reply | User | POST/PUT /api/posts/{post}/comments/{comment}/replies | comments |
| F21 | F21_CommentEditHistory | Comment History | User | GET /api/moderator/comments/{comment}/history | comment_edit_history |
| F22 | F22_VoteSystem | Upvote / Downvote | User | POST /api/votes | votes, posts, comments |
| F23 | F23_LikeSystem | Like | User | POST /api/likes/toggle | likes |
| F24 | F24_BookmarkPost | Bookmark | User | POST /api/bookmarks/toggle, GET /api/bookmarks | bookmarks |
| F25 | F25_FollowUser | Follow | User | POST /api/users/{id}/follow, GET followers/following | follows |
| F26 | F26_NotificationSystem | Notifications | User | GET /api/notifications, PATCH mark-read | notifications |
| F27 | F27_GamificationLeaderboard | Leaderboard | User | GET /api/explore/leaderboard | users, points_log |
| F28 | F28_ProfileSettings | Profile Settings | User | GET/PUT /api/settings/profile, PUT /api/settings/password | users |
| F29 | F29_BadgeAchievement | Badge Achievement | User | GET /api/me/badges | user_badges, badges |
| F30 | F30_UserReport | User Report | User | POST /api/reports | reports |

**Total: 31 feature folder** (F1–F3, F31, F4–F30)

---

## ���️ Database Models (20 Models, 6 Groups)

Semua model menggunakan **UUID** (`HasUuids` trait) dan database **PostgreSQL**.

| Group | Model | Table | Key Traits |
|-------|-------|-------|------------|
| **Auth** | User | `users` | HasUuids, HasApiTokens, SoftDeletes(-), HasFactory, Notifiable |
| **Auth** | Role | `roles` | HasUuids, `permissions` cast array |
| **Auth** | UserRole | `user_roles` | HasUuids, pivot (user_id, role_id, assigned_at) |
| **Content** | Post | `posts` | HasUuids, SoftDeletes, HasFactory |
| **Content** | Comment | `comments` | HasUuids, SoftDeletes, HasFactory |
| **Content** | Category | `categories` | HasUuids, HasFactory, self-referencing (parent_id) |
| **Content** | Tag | `tags` | HasUuids |
| **Content** | PostTag | `post_tags` | HasUuids, pivot (post_id, tag_id) |
| **Gamification** | Badge | `badges` | HasUuids, HasFactory, tiers (bronze/silver/gold/platinum) |
| **Gamification** | PointsLog | `points_log` | HasUuids |
| **Gamification** | UserBadge | `user_badges` | HasUuids, pivot (user_id, badge_id, earned_at) |
| **History** | PostEditHistory | `post_edit_history` | HasUuids |
| **History** | CommentEditHistory | `comment_edit_history` | HasUuids |
| **Interaction** | Vote | `votes` | HasUuids, polymorphic (target_id, target_type) |
| **Interaction** | Like | `likes` | HasUuids, polymorphic (target_id, target_type) |
| **Interaction** | Bookmark | `bookmarks` | HasUuids |
| **Interaction** | Follow | `follows` | HasUuids (follower_id, following_id) |
| **Moderation** | Notification | `notifications` | HasUuids, polymorphic (reference_id, reference_type) |
| **Moderation** | Report | `reports` | HasUuids, polymorphic (target_id, target_type) |
| **Moderation** | ModerationLog | `moderation_logs` | HasUuids |

---

## ���️ Architecture Highlights

- ✅ **Feature-Based Modularity**: Setiap folder F-prefix = satu feature set (Controllers, Services, Repositories, Requests)
- ✅ **Clean Architecture**: Separation of concerns — Controller (HTTP only) → Service (business logic) → Repository (Eloquent queries)
- ✅ **Sanctum SPA Auth**: Cookie-based session (bukan token), `statefulApi()` middleware, CSRF exempt untuk auth routes
- ✅ **UUID Primary Keys**: Semua tabel menggunakan UUID, bukan auto-increment
- ✅ **PostgreSQL**: Database utama
- ✅ **Role-Based Access**: `role:admin` dan `role:moderator,admin` middleware
- ✅ **Polymorphic Relations**: Vote, Like, Report, Notification menggunakan `target_id/reference_id` + `target_type/reference_type`
- ✅ **RESTful API**: Semua fitur exposed melalui REST endpoints
- ✅ **Soft Deletes**: Posts dan Comments menggunakan soft delete
- ✅ **Audit Trail**: Post & Comment edit history, moderation logs

---

**Next: Baca [02_MODULE_STRUCTURE.md](02_MODULE_STRUCTURE.md) untuk detail struktur folder →**
