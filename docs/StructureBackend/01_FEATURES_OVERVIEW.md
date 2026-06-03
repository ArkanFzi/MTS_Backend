# 01. Features Overview - 29 Advanced Features

## 📊 Breakdown Lengkap ke-29 Fitur Fungsional

Dokumentasi ini menjelaskan setiap fitur dalam 29 feature set, diorganisir berdasarkan modul domain.

---

## 🔐 AUTH MODULE (1 Folder) → 2 Fitur

### Modul Path: `Modules/Auth/`

#### ✅ Fitur 1: Register Akun User Baru
- **Deskripsi**: Pengguna baru dapat membuat akun dengan mengisi data profil
- **Related Table**: `users`
- **API Endpoint**: `POST /api/auth/register`
- **Validation Rules**: 
  - Email unik
  - Password minimum 8 karakter
  - Required fields: name, email, password
- **Response**: User object + API token (Laravel Sanctum)
- **File Location**:
  - Controller: `Modules/Auth/Controllers/AuthController.php`
  - Service: `Modules/Auth/Services/AuthService.php`
  - Request: `Modules/Auth/Requests/RegisterRequest.php`

#### ✅ Fitur 2: Login & Penerbitan Token API
- **Deskripsi**: User dapat login dan mendapat token untuk akses API
- **Related Table**: `users`, `personal_access_tokens`
- **API Endpoint**: `POST /api/auth/login`
- **Validation Rules**:
  - Email harus terdaftar
  - Password harus cocok
  - User tidak boleh banned
- **Response**: User object + API token (Sanctum)
- **Token Expiry**: Configurable di `.env`
- **File Location**:
  - Controller: `Modules/Auth/Controllers/AuthController.php`
  - Service: `Modules/Auth/Services/AuthService.php`

---

## 🔍 COMMON/EXPLORE MODULE (1 Folder) → 4 Fitur

### Modul Path: `Modules/Common/Explore/`

**Deskripsi Modul**: Fitur read-only publik untuk browse dan mencari konten forum tanpa perlu login.

#### ✅ Fitur 3: Search Postingan
- **Deskripsi**: User dapat mencari postingan berdasarkan judul dan isi
- **Related Table**: `posts`
- **API Endpoint**: `GET /api/explore/search?q=keyword`
- **Search Fields**: `title`, `content`
- **Features**:
  - Full-text search capability
  - Pagination support
  - Sort by relevance/date
- **File Location**:
  - Controller: `Modules/Common/Explore/Controllers/ExploreController.php`
  - Service: `Modules/Common/Explore/Services/SearchService.php`

#### ✅ Fitur 4: Filter Postingan berdasarkan Tag
- **Deskripsi**: Tampilkan postingan yang di-tag dengan tag tertentu
- **Related Table**: `posts`, `post_tags`, `tags`
- **API Endpoint**: `GET /api/explore/filter/tags?tag_ids=1,2,3`
- **Features**:
  - Single atau multiple tag selection
  - Combine dengan filter lain
  - Show tag metadata
- **File Location**:
  - Controller: `Modules/Common/Explore/Controllers/ExploreController.php`
  - Service: `Modules/Common/Explore/Services/FilterService.php`

#### ✅ Fitur 5: Filter Postingan berdasarkan Kategori
- **Deskripsi**: Tampilkan postingan dalam kategori tertentu
- **Related Table**: `posts`, `categories`
- **API Endpoint**: `GET /api/explore/filter/categories?category_id=1`
- **Features**:
  - Support parent-child category
  - Show category description
  - Recursive listing (sub-categories)
- **File Location**:
  - Controller: `Modules/Common/Explore/Controllers/ExploreController.php`
  - Service: `Modules/Common/Explore/Services/FilterService.php`

#### ✅ Fitur 6: Trending / Popular Posts
- **Deskripsi**: Menampilkan postingan populer berdasarkan engagement metrics
- **Related Table**: `posts`
- **API Endpoint**: `GET /api/explore/trending?period=week`
- **Ranking Metrics**:
  - `view_count` (number of views)
  - `vote_score` (upvote - downvote)
  - `comment_count` (jumlah komentar)
  - Time-based trending (today, week, month)
- **Features**:
  - Weighted scoring algorithm
  - Period filtering (today, week, month, all-time)
- **File Location**:
  - Controller: `Modules/Common/Explore/Controllers/ExploreController.php`
  - Service: `Modules/Common/Explore/Services/TrendingService.php`

---

## 👨‍💼 ADMIN MODULES (4 Folder) → 5 Fitur

### Path: `Modules/Admin/`

**Deskripsi Modul**: Administrative features untuk manajemen sistem makro oleh Administrator.

#### ✅ Fitur 7 & 8: Multi-Role & Permissions + User Profile Management
**Module Path**: `Modules/Admin/RoleManagement/`

**Fitur 7: Multi-role & Permissions**
- **Deskripsi**: Admin dapat mengatur role dan permission user
- **Related Table**: `roles`, `permissions`, `user_roles`, `role_permissions`
- **API Endpoint**: 
  - `GET /api/admin/roles` - List semua role
  - `POST /api/admin/roles` - Create role baru
  - `PUT /api/admin/roles/{id}` - Update role
  - `DELETE /api/admin/roles/{id}` - Delete role
- **Features**:
  - Create custom roles
  - Assign permissions ke role
  - Assign role ke user
  - Permission inheritance
- **Roles Available**: Admin, Moderator, User
- **File Location**:
  - Controller: `Modules/Admin/RoleManagement/Controllers/RoleController.php`
  - Service: `Modules/Admin/RoleManagement/Services/RoleService.php`

**Fitur 8: Manajemen Profil User oleh Admin**
- **Deskripsi**: Admin dapat mengedit atau mereset avatar/bio user secara massal
- **Related Table**: `users`
- **API Endpoint**:
  - `GET /api/admin/users` - List users dengan pagination
  - `GET /api/admin/users/{id}` - Detail user
  - `PUT /api/admin/users/{id}` - Update user profile
  - `PUT /api/admin/users/{id}/avatar` - Update avatar
  - `PUT /api/admin/users/{id}/reset-password` - Reset password
- **Features**:
  - Bulk edit users
  - Reset password functionality
  - Update avatar/bio
  - View user statistics
- **File Location**:
  - Controller: `Modules/Admin/RoleManagement/Controllers/UserManagementController.php`
  - Service: `Modules/Admin/RoleManagement/Services/UserManagementService.php`

#### ✅ Fitur 9: CRUD Master Kategori Forum
**Module Path**: `Modules/Admin/Category/`

- **Deskripsi**: Admin dapat create, read, update, delete kategori forum
- **Related Table**: `categories`
- **API Endpoint**:
  - `GET /api/admin/categories` - List semua kategori
  - `POST /api/admin/categories` - Buat kategori baru
  - `PUT /api/admin/categories/{id}` - Update kategori
  - `DELETE /api/admin/categories/{id}` - Hapus kategori
  - `GET /api/admin/categories/{id}/posts` - List posting dalam kategori
- **Features**:
  - Parent-child category support (nested)
  - Icon/image support
  - Description & slug auto-generation
  - Sort ordering
- **Validation**:
  - Name wajib unik
  - Slug auto-generated dan unik
- **File Location**:
  - Controller: `Modules/Admin/Category/Controllers/CategoryController.php`
  - Service: `Modules/Admin/Category/Services/CategoryService.php`
  - Repository: `Modules/Admin/Category/Repositories/CategoryRepository.php`

#### ✅ Fitur 10: CRUD Master Tag Forum
**Module Path**: `Modules/Admin/Tag/`

- **Deskripsi**: Admin dapat create, read, update, delete tag forum
- **Related Table**: `tags`
- **API Endpoint**:
  - `GET /api/admin/tags` - List semua tag
  - `POST /api/admin/tags` - Buat tag baru
  - `PUT /api/admin/tags/{id}` - Update tag
  - `DELETE /api/admin/tags/{id}` - Hapus tag
  - `GET /api/admin/tags/{id}/posts` - List posting dengan tag
- **Features**:
  - Color code per tag (hex color picker)
  - Description & usage count
  - Popular tags dashboard
  - Tag suggestions
- **Validation**:
  - Name wajib unik
  - Name minimum 2 karakter
  - Color harus valid hex format
- **File Location**:
  - Controller: `Modules/Admin/Tag/Controllers/TagController.php`
  - Service: `Modules/Admin/Tag/Services/TagService.php`
  - Repository: `Modules/Admin/Tag/Repositories/TagRepository.php`

#### ✅ Fitur 11: CRUD Jenis Achievement/Badge Master
**Module Path**: `Modules/Admin/Badge/`

- **Deskripsi**: Admin dapat membuat jenis-jenis achievement/badge untuk gamification
- **Related Table**: `badges`
- **API Endpoint**:
  - `GET /api/admin/badges` - List semua badge
  - `POST /api/admin/badges` - Buat badge baru
  - `PUT /api/admin/badges/{id}` - Update badge
  - `DELETE /api/admin/badges/{id}` - Hapus badge
  - `GET /api/admin/badges/{id}/users` - List user yang punya badge
- **Badge Types**: Bronze, Silver, Gold (tier system)
- **Features**:
  - Badge icon/image upload
  - Description & requirements
  - Tier system (bronze → silver → gold)
  - Auto-award logic configuration
- **Validation**:
  - Name wajib unik
  - Tier harus valid (bronze/silver/gold)
- **File Location**:
  - Controller: `Modules/Admin/Badge/Controllers/BadgeController.php`
  - Service: `Modules/Admin/Badge/Services/BadgeService.php`

---

## 🛡️ MODERATOR MODULES (2 Folder) → 3 Fitur

### Path: `Modules/Moderator/`

**Deskripsi Modul**: Moderasi konten dan enforcement aturan komunitas oleh Moderator.

#### ✅ Fitur 12: Manajemen Report Konten
**Module Path**: `Modules/Moderator/Report/`

- **Deskripsi**: Moderator dapat melihat, meninjau, dan menyelesaikan laporan konten yang bermasalah
- **Related Table**: `reports`, `posts`, `comments`
- **API Endpoint**:
  - `GET /api/moderator/reports` - List report dengan status filter
  - `GET /api/moderator/reports/{id}` - Detail report
  - `PUT /api/moderator/reports/{id}/status` - Update status (pending → resolved)
  - `PUT /api/moderator/reports/{id}/action` - Ambil action (hapus konten, ban user, etc)
  - `POST /api/moderator/reports/{id}/note` - Tambah catatan resolusi
- **Report Statuses**: `pending`, `in_review`, `resolved`, `dismissed`
- **Features**:
  - Report queue/dashboard
  - Filter by status, type, date
  - View reported content
  - Bulk action support
- **File Location**:
  - Controller: `Modules/Moderator/Report/Controllers/ReportController.php`
  - Service: `Modules/Moderator/Report/Services/ReportService.php`
  - Repository: `Modules/Moderator/Report/Repositories/ReportRepository.php`

#### ✅ Fitur 13 & 14: User Ban/Unban + Moderator Action Log
**Module Path**: `Modules/Moderator/UserSanction/`

**Fitur 13: Ban / Unban User**
- **Deskripsi**: Moderator dapat melarang (ban) user tertentu dari forum
- **Related Table**: `users` (field `is_banned`), `user_suspensions`
- **API Endpoint**:
  - `PUT /api/moderator/users/{id}/ban` - Ban user
  - `PUT /api/moderator/users/{id}/unban` - Unban user
  - `GET /api/moderator/users/{id}/suspension-history` - Riwayat suspension
- **Features**:
  - Temporary ban (dengan expiry date)
  - Permanent ban
  - Ban reason & duration
  - Auto-unban scheduled tasks
- **Validation**:
  - Alasan ban wajib diisi
  - Admin tidak boleh di-ban
- **File Location**:
  - Controller: `Modules/Moderator/UserSanction/Controllers/UserSanctionController.php`
  - Service: `Modules/Moderator/UserSanction/Services/UserSanctionService.php`

**Fitur 14: Moderator Action Log**
- **Deskripsi**: Sistem pencatatan otomatis semua aksi moderasi untuk audit trail
- **Related Table**: `moderation_logs`
- **Logged Actions**:
  - Ban/Unban user
  - Delete post/comment
  - Close report
  - Add warning to user
- **Features**:
  - Auto-logged setiap aksi moderator
  - Timestamp & moderator ID
  - Affected user/content reference
  - Action reason
  - Reversible actions tracking
- **API Endpoint**:
  - `GET /api/moderator/logs` - List moderator actions
  - `GET /api/moderator/logs?moderator_id=X` - Filter by moderator
  - `GET /api/moderator/logs?user_id=X` - Filter by affected user
- **File Location**:
  - Service: `Modules/Moderator/UserSanction/Services/ModerationLogService.php`
  - Repository: `Modules/Moderator/UserSanction/Repositories/ModerationLogRepository.php`

---

## 👥 USER MODULES (5 Folder) → 15 Fitur

### Path: `Modules/User/`

**Deskripsi Modul**: Core forum features - tempat user berinteraksi dengan konten dan satu sama lain (Stack Overflow style).

---

### 📝 POST MODULE - Fitur 15-19

**Module Path**: `Modules/User/Post/`

#### ✅ Fitur 15: Buat Postingan Baru
- **Deskripsi**: User dapat membuat postingan/pertanyaan baru di forum
- **Related Table**: `posts`, `post_tags`, `post_categories`
- **API Endpoint**: `POST /api/posts`
- **Request Fields**:
  - `title` (required, min 10 char)
  - `content` (required, min 30 char)
  - `category_id` (required)
  - `tags[]` (array, min 1 tag)
- **Features**:
  - Draft support
  - Rich text editor (Markdown/HTML)
  - Tag suggestions
  - Auto-tagging capability
- **Response**: Created post object dengan full metadata
- **File Location**:
  - Controller: `Modules/User/Post/Controllers/PostController.php`
  - Service: `Modules/User/Post/Services/PostService.php`
  - Request: `Modules/User/Post/Requests/StorePostRequest.php`
  - Repository: `Modules/User/Post/Repositories/PostRepository.php`

#### ✅ Fitur 16: Edit Postingan
- **Deskripsi**: User dapat mengedit postingan miliknya sendiri
- **Related Table**: `posts`, `post_edit_history` (created otomatis)
- **API Endpoint**: `PUT /api/posts/{id}`
- **Editable Fields**: `title`, `content`, `tags`, `category_id`
- **Authorization**: User hanya bisa edit postingan miliknya sendiri
- **Features**:
  - Full content update
  - Re-tag capability
  - Edit reason/note
  - Version control (simpl historical tracking)
- **File Location**:
  - Controller: `Modules/User/Post/Controllers/PostController.php`
  - Service: `Modules/User/Post/Services/PostService.php`
  - Request: `Modules/User/Post/Requests/UpdatePostRequest.php`

#### ✅ Fitur 17: Hapus Postingan
- **Deskripsi**: User dapat menghapus postingan miliknya (soft delete)
- **Related Table**: `posts` (field `deleted_at`)
- **API Endpoint**: `DELETE /api/posts/{id}`
- **Behavior**: 
  - Soft delete (tidak benar-benar dihapus dari DB)
  - Post tidak terlihat di listing publik
  - Admin masih bisa melihat dan restore
- **Authorization**: User hanya bisa hapus postingan miliknya sendiri
- **Features**:
  - Restore capability (hard delete tidak ada)
  - Reason for deletion tracking
- **File Location**:
  - Controller: `Modules/User/Post/Controllers/PostController.php`
  - Service: `Modules/User/Post/Services/PostService.php`

#### ✅ Fitur 18: Mark as Accepted Answer
- **Deskripsi**: OP (Original Poster) dapat menandai satu comment sebagai jawaban terbaik
- **Related Table**: `posts` (field `accepted_answer_id`)
- **API Endpoint**: `PUT /api/posts/{id}/accept-answer/{comment_id}`
- **Business Logic**:
  - Hanya OP yang bisa set accepted answer
  - Hanya 1 accepted answer per post
  - Comment yang di-accept akan di-highlight
  - Answerer mendapat reputation points bonus
- **Features**:
  - Lock post dari editing/deletion setelah answer accepted
  - Visual indicator untuk accepted answer
  - Change accepted answer (unmark lama, mark baru)
- **File Location**:
  - Controller: `Modules/User/Post/Controllers/PostController.php`
  - Service: `Modules/User/Post/Services/PostService.php`

#### ✅ Fitur 19: Edit History Postingan
- **Deskripsi**: Sistem otomatis mencatat setiap perubahan yang dilakukan pada postingan
- **Related Table**: `post_edit_history`
- **Tracked Fields**:
  - title
  - content
  - tags
  - category_id
- **API Endpoint**:
  - `GET /api/posts/{id}/history` - List semua edit history
  - `GET /api/posts/{id}/history/{version}` - View specific version
- **Features**:
  - Timestamp setiap edit
  - User ID & username
  - Diff between versions
  - Revert capability (soft-revert, create new history entry)
  - Reason/note untuk edit
- **File Location**:
  - Repository: `Modules/User/Post/Repositories/PostEditHistoryRepository.php`
  - Service: `Modules/User/Post/Services/PostHistoryService.php`

---

### 💬 COMMENT MODULE - Fitur 20-23

**Module Path**: `Modules/User/Comment/`

#### ✅ Fitur 20: Buat Komentar / Jawaban Utama
- **Deskripsi**: User dapat memberi komentar atau menjawab postingan (bersifat flat/non-nested di level ini)
- **Related Table**: `comments`
- **API Endpoint**: `POST /api/posts/{post_id}/comments`
- **Request Fields**:
  - `content` (required, min 5 char)
  - `post_id` (required)
  - `parent_id` (optional, null untuk top-level)
- **Features**:
  - Rich text support
  - Mention users (@username)
  - Link preview
- **Response**: Created comment object
- **File Location**:
  - Controller: `Modules/User/Comment/Controllers/CommentController.php`
  - Service: `Modules/User/Comment/Services/CommentService.php`
  - Request: `Modules/User/Comment/Requests/StoreCommentRequest.php`
  - Repository: `Modules/User/Comment/Repositories/CommentRepository.php`

#### ✅ Fitur 21: Nested Reply (Balasan di dalam Komentar)
- **Deskripsi**: User dapat membalas komentar lain secara bersarang menggunakan `parent_id`
- **Related Table**: `comments` (field `parent_id`)
- **API Endpoint**: `POST /api/comments/{comment_id}/reply`
- **Behavior**:
  - Reply langsung ke comment lain
  - Dapat di-nest hingga N level
  - Mention parent commenter otomatis
- **Features**:
  - Thread view untuk balasan
  - Nested indentation di frontend
  - Collapse/expand nested replies
  - Quote reply capability
- **File Location**:
  - Controller: `Modules/User/Comment/Controllers/CommentController.php`
  - Service: `Modules/User/Comment/Services/CommentService.php`

#### ✅ Fitur 22: Edit & Hapus Komentar
- **Deskripsi**: User dapat edit atau delete komentar miliknya
- **Related Table**: `comments` (field `deleted_at`)
- **API Endpoint**:
  - `PUT /api/comments/{id}` - Edit comment
  - `DELETE /api/comments/{id}` - Hapus comment
- **Authorization**: User hanya bisa edit/delete komentar miliknya
- **Features**:
  - Soft delete untuk comment
  - Edit tracking (marked as "edited")
  - Content update
  - Restore capability (soft delete)
- **File Location**:
  - Controller: `Modules/User/Comment/Controllers/CommentController.php`
  - Service: `Modules/User/Comment/Services/CommentService.php`

#### ✅ Fitur 23: Edit History Komentar
- **Deskripsi**: Otomatis mencatat setiap perubahan pada komentar
- **Related Table**: `comment_edit_history`
- **API Endpoint**:
  - `GET /api/comments/{id}/history` - List edit history
  - `GET /api/comments/{id}/history/{version}` - View version
- **Tracked Fields**:
  - content
- **Features**:
  - Timestamp
  - User & username
  - Diff view
  - Version comparison
- **File Location**:
  - Repository: `Modules/User/Comment/Repositories/CommentEditHistoryRepository.php`
  - Service: `Modules/User/Comment/Services/CommentHistoryService.php`

---

### ⭐ INTERACTION MODULE - Fitur 24-27

**Module Path**: `Modules/User/Interaction/`

#### ✅ Fitur 24: Upvote / Downvote Post & Comment
- **Deskripsi**: User dapat memberikan upvote atau downvote pada postingan dan komentar
- **Related Table**: `votes`, `posts` (field `vote_score`), `comments` (field `vote_score`)
- **API Endpoint**:
  - `POST /api/posts/{id}/votes` - Upvote/downvote post
  - `POST /api/comments/{id}/votes` - Upvote/downvote comment
- **Request Body**: `{ "vote_type": "upvote" | "downvote" | "remove" }`
- **Business Logic**:
  - 1 vote per user per content
  - Dapat change vote (upvote → downvote)
  - Dapat remove vote
  - Vote score = upvotes - downvotes
- **Features**:
  - Real-time vote count update
  - User vote status indicator
  - Reputation point impact
- **File Location**:
  - Controller: `Modules/User/Interaction/Controllers/VoteController.php`
  - Service: `Modules/User/Interaction/Services/VoteService.php`
  - Repository: `Modules/User/Interaction/Repositories/VoteRepository.php`

#### ✅ Fitur 25: Like Post & Comment
- **Deskripsi**: User dapat memberikan "like" (berbeda dari upvote) pada konten
- **Related Table**: `likes`
- **API Endpoint**:
  - `POST /api/posts/{id}/likes` - Like/unlike post
  - `POST /api/comments/{id}/likes` - Like/unlike comment
- **Behavior**:
  - 1 like per user per content (toggle)
  - Like count tracking
  - Separate dari vote system
- **Features**:
  - User like list
  - Like count display
  - Like notification
- **File Location**:
  - Controller: `Modules/User/Interaction/Controllers/LikeController.php`
  - Service: `Modules/User/Interaction/Services/LikeService.php`
  - Repository: `Modules/User/Interaction/Repositories/LikeRepository.php`

#### ✅ Fitur 26: Bookmark / Save Post
- **Deskripsi**: User dapat menyimpan postingan untuk dibaca kemudian
- **Related Table**: `bookmarks`
- **API Endpoint**:
  - `POST /api/posts/{id}/bookmark` - Save/unsave post
  - `GET /api/bookmarks` - List user's bookmarks
- **Features**:
  - Collection/folder support (bookmark categories)
  - Search dalam bookmarks
  - Export bookmarks
  - Share bookmark collection
- **File Location**:
  - Controller: `Modules/User/Interaction/Controllers/BookmarkController.php`
  - Service: `Modules/User/Interaction/Services/BookmarkService.php`
  - Repository: `Modules/User/Interaction/Repositories/BookmarkRepository.php`

#### ✅ Fitur 27: Follow / Unfollow User
- **Deskripsi**: User dapat follow/unfollow user lain untuk melihat activity mereka
- **Related Table**: `follows`
- **API Endpoint**:
  - `POST /api/users/{id}/follow` - Follow user
  - `POST /api/users/{id}/unfollow` - Unfollow user
  - `GET /api/users/{id}/followers` - List followers
  - `GET /api/users/{id}/following` - List user yang di-follow
- **Features**:
  - Follow activity feed
  - Follower badges
  - Follower count
  - Mutual follow indicator
- **File Location**:
  - Controller: `Modules/User/Interaction/Controllers/FollowController.php`
  - Service: `Modules/User/Interaction/Services/FollowService.php`
  - Repository: `Modules/User/Interaction/Repositories/FollowRepository.php`

---

### 🔔 NOTIFICATION MODULE - Fitur 28

**Module Path**: `Modules/User/Notification/`

#### ✅ Fitur 28: Sistem Notifikasi Real-time + Mark as Read
- **Deskripsi**: Sistem notifikasi terintegrasi untuk memberitahu user tentang event penting
- **Related Table**: `notifications`
- **Notification Types**:
  - Comment reply
  - Upvote received
  - Post accepted as answer
  - User mentioned
  - Badge earned
  - Followed by someone
- **API Endpoint**:
  - `GET /api/notifications` - List notifications
  - `PUT /api/notifications/{id}/read` - Mark as read
  - `PUT /api/notifications/read-all` - Mark all as read
  - `DELETE /api/notifications/{id}` - Delete notification
  - `GET /api/notifications/unread-count` - Unread count
- **Features**:
  - Real-time push (WebSocket/polling)
  - In-app notification badge
  - Email notification optional
  - Notification preferences per user
  - Read/unread status
  - Notification grouping
- **Real-time Implementation**:
  - Laravel Broadcasting (Pusher/Ably)
  - Or polling via GET /api/notifications?since_id=X
  - Or WebSocket connection
- **File Location**:
  - Controller: `Modules/User/Notification/Controllers/NotificationController.php`
  - Service: `Modules/User/Notification/Services/NotificationService.php`
  - Repository: `Modules/User/Notification/Repositories/NotificationRepository.php`
  - Event: `Modules/User/Notification/Events/NotificationCreated.php`

---

### 🏆 GAMIFICATION MODULE - Fitur 29

**Module Path**: `Modules/User/Gamification/`

#### ✅ Fitur 29: Reputation Level & Leaderboard
- **Deskripsi**: Sistem gamifikasi untuk memotivasi user melalui reputation points dan ranking
- **Related Table**: `users` (field `reputation_points`), `points_log`
- **Reputation Sources**:
  - Post accepted as answer: +15 points
  - Upvote received: +10 points
  - Downvote received: -2 points
  - Badge earned: +X points (per badge tier)
  - Comment liked: +5 points
- **Reputation Levels**:
  - Newbie: 0-99 points
  - Member: 100-499 points
  - Senior: 500-999 points
  - Expert: 1000-4999 points
  - Legend: 5000+ points
- **API Endpoint**:
  - `GET /api/leaderboard` - Global leaderboard
  - `GET /api/leaderboard?period=week` - Time-based leaderboard
  - `GET /api/users/{id}/reputation` - User reputation detail
  - `GET /api/users/{id}/points-log` - User points history
- **Features**:
  - Real-time leaderboard
  - User rank badge display
  - Top contributors dashboard
  - Monthly/yearly rankings
  - Achievement badges unlock
  - Points milestone notifications
  - Points earn breakdown
- **File Location**:
  - Controller: `Modules/User/Gamification/Controllers/GamificationController.php`
  - Service: `Modules/User/Gamification/Services/ReputationService.php`
  - Repository: `Modules/User/Gamification/Repositories/ReputationRepository.php`
  - Events: `Modules/User/Gamification/Events/PointsEarned.php`

---

## 📊 Summary Table

| # | Fitur | Modul | Folder | Table(s) |
|----|-------|-------|--------|----------|
| 1 | Register | Auth | Auth | users |
| 2 | Login & Token | Auth | Auth | users, personal_access_tokens |
| 3 | Search | Common | Explore | posts |
| 4 | Filter by Tag | Common | Explore | posts, post_tags, tags |
| 5 | Filter by Category | Common | Explore | posts, categories |
| 6 | Trending Posts | Common | Explore | posts |
| 7 | Roles & Permissions | Admin | RoleManagement | roles, permissions, user_roles |
| 8 | User Profile Mgmt | Admin | RoleManagement | users |
| 9 | Category CRUD | Admin | Category | categories |
| 10 | Tag CRUD | Admin | Tag | tags |
| 11 | Badge CRUD | Admin | Badge | badges |
| 12 | Report Management | Moderator | Report | reports, posts, comments |
| 13 | Ban/Unban User | Moderator | UserSanction | users, user_suspensions |
| 14 | Moderation Log | Moderator | UserSanction | moderation_logs |
| 15 | Create Post | User | Post | posts, post_tags, post_categories |
| 16 | Edit Post | User | Post | posts, post_edit_history |
| 17 | Delete Post | User | Post | posts |
| 18 | Accept Answer | User | Post | posts, comments |
| 19 | Post Edit History | User | Post | post_edit_history |
| 20 | Create Comment | User | Comment | comments |
| 21 | Nested Reply | User | Comment | comments |
| 22 | Edit/Delete Comment | User | Comment | comments |
| 23 | Comment Edit History | User | Comment | comment_edit_history |
| 24 | Upvote/Downvote | User | Interaction | votes, posts, comments |
| 25 | Like | User | Interaction | likes |
| 26 | Bookmark | User | Interaction | bookmarks |
| 27 | Follow User | User | Interaction | follows |
| 28 | Notifications | User | Notification | notifications |
| 29 | Reputation/Leaderboard | User | Gamification | users, points_log |

---

## 🎯 Architecture Highlights

- ✅ **DDD (Domain-Driven Design)**: Setiap modul merepresentasikan bounded context
- ✅ **Feature-Based Modularity**: 1 folder dapat membungkus multiple fitur terkait
- ✅ **Clean Architecture**: Separation of concerns (Controller → Service → Repository)
- ✅ **RESTful API**: Semua fitur exposed melalui REST endpoints
- ✅ **Authorization & Validation**: Setiap endpoint memiliki permission checks
- ✅ **Audit Trail**: Edit history dan moderation logs tracked otomatis
- ✅ **Real-time**: Notifications & leaderboard updates

---

**Next: Baca [02_MODULE_STRUCTURE.md](02_MODULE_STRUCTURE.md) untuk detail struktur folder →**
