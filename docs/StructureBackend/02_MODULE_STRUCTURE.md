# 02. Module Structure - Architecture & Organization

## 📁 Struktur Folder Backend

Dokumentasi lengkap tentang organisasi folder, naming conventions, dan arsitektur modular.

---

## 🏗️ Core Architecture Pattern

Proyek ini menggunakan kombinasi dari:
1. **Domain-Driven Design (DDD)**
2. **Feature-Based Modularity**
3. **Clean Architecture**
4. **SOLID Principles**

---

## 📂 Full Folder Hierarchy

```
backend_roleuser/
│
├── Modules/                                    # ⭐ Core business logic (Feature-Based DDD)
│   │                                           # Naming: F{number}_{FeatureName}
│   │
│   ├── Auth/                                   # Authentication & Authorization (Fitur 1-2)
│   │   ├── F1_Register/                        # Fitur 1: Register akun user baru
│   │   │   ├── Controllers/
│   │   │   │   └── RegisterController.php
│   │   │   ├── Requests/
│   │   │   │   └── RegisterRequest.php
│   │   │   ├── Services/
│   │   │   │   └── RegisterService.php
│   │   │   └── Repositories/ (optional)
│   │   │
│   │   └── F2_Login/                           # Fitur 2: Login & penerbitan token API
│   │       ├── Controllers/
│   │       │   └── LoginController.php
│   │       ├── Requests/
│   │       │   └── LoginRequest.php
│   │       ├── Services/
│   │       │   └── LoginService.php
│   │       └── Repositories/ (optional)
│   │
│   ├── Common/                                 # Public/Common features (Fitur 3-6)
│   │   ├── F3_SearchPost/                      # Fitur 3: Search postingan
│   │   │   ├── Controllers/
│   │   │   │   └── SearchController.php
│   │   │   ├── Services/
│   │   │   │   └── SearchService.php
│   │   │   └── Repositories/ (optional)
│   │   │
│   │   ├── F4_FilterByTag/                     # Fitur 4: Filter by Tag
│   │   │   ├── Controllers/
│   │   │   │   └── FilterTagController.php
│   │   │   ├── Services/
│   │   │   │   └── FilterTagService.php
│   │   │   └── Repositories/ (optional)
│   │   │
│   │   ├── F5_FilterByCategory/                # Fitur 5: Filter by Kategori
│   │   │   ├── Controllers/
│   │   │   │   └── FilterCategoryController.php
│   │   │   ├── Services/
│   │   │   │   └── FilterCategoryService.php
│   │   │   └── Repositories/ (optional)
│   │   │
│   │   └── F6_TrendingPopularPost/             # Fitur 6: Trending/Popular Posts
│   │       ├── Controllers/
│   │       │   └── TrendingController.php
│   │       ├── Services/
│   │       │   └── TrendingService.php
│   │       └── Repositories/ (optional)
│   │
│   ├── Admin/                                  # Administrative features (Fitur 7-11)
│   │   ├── F7_RoleAndPermission/               # Fitur 7-8: Multi-role & User Profile Mgmt
│   │   │   ├── Controllers/
│   │   │   │   ├── RoleController.php
│   │   │   │   └── UserManagementController.php
│   │   │   ├── Services/
│   │   │   │   ├── RoleService.php
│   │   │   │   └── UserManagementService.php
│   │   │   ├── Repositories/
│   │   │   │   ├── RoleRepository.php
│   │   │   │   └── UserRepository.php
│   │   │   └── Requests/
│   │   │       ├── StoreRoleRequest.php
│   │   │       └── UpdateUserRequest.php
│   │   │
│   │   ├── F9_UserManagement/                  # User profile, permissions mgmt (sub-feature dari F7)
│   │   │   ├── Controllers/
│   │   │   │   └── UserAdminController.php
│   │   │   ├── Services/
│   │   │   │   └── UserAdminService.php
│   │   │   ├── Repositories/
│   │   │   │   └── UserAdminRepository.php
│   │   │   └── Requests/
│   │   │       ├── UpdateProfileRequest.php
│   │   │       └── ResetPasswordRequest.php
│   │   │
│   │   ├── F10_CategoryMaster/                  # Fitur 10: CRUD Kategori Forum
│   │   │   ├── Controllers/
│   │   │   │   └── CategoryController.php
│   │   │   ├── Services/
│   │   │   │   └── CategoryService.php
│   │   │   ├── Repositories/
│   │   │   │   └── CategoryRepository.php
│   │   │   └── Requests/
│   │   │       ├── StoreCategoryRequest.php
│   │   │       └── UpdateCategoryRequest.php
│   │   │
│   │   ├── F11_TagMaster/                      # Fitur 10: CRUD Master Tag Forum
│   │   │   ├── Controllers/
│   │   │   │   └── TagController.php
│   │   │   ├── Services/
│   │   │   │   └── TagService.php
│   │   │   ├── Repositories/
│   │   │   │   └── TagRepository.php
│   │   │   └── Requests/
│   │   │       ├── StoreTagRequest.php
│   │   │       └── UpdateTagRequest.php
│   │   │
│   │   └── F12_BadgeMaster/                    # Fitur 11: CRUD Badge/Achievement Master
│   │       ├── Controllers/
│   │       │   └── BadgeController.php
│   │       ├── Services/
│   │       │   └── BadgeService.php
│   │       ├── Repositories/
│   │       │   └── BadgeRepository.php
│   │       └── Requests/
│   │           ├── StoreBadgeRequest.php
│   │           └── UpdateBadgeRequest.php
│   │
│   ├── Moderator/                              # Content moderation (Fitur 12-14)
│   │   ├── F13_ContentReportQueue/             # Fitur 13: Manajemen Report Konten
│   │   │   ├── Controllers/
│   │   │   │   └── ReportController.php
│   │   │   ├── Services/
│   │   │   │   └── ReportService.php
│   │   │   ├── Repositories/
│   │   │   │   └── ReportRepository.php
│   │   │   └── Requests/
│   │   │       └── UpdateReportRequest.php
│   │   │
│   │   ├── F14_UserBanSanction/                # Fitur 14: Ban/Unban User
│   │   │   ├── Controllers/
│   │   │   │   └── UserSanctionController.php
│   │   │   ├── Services/
│   │   │   │   └── UserSanctionService.php
│   │   │   ├── Repositories/
│   │   │   │   └── UserSanctionRepository.php
│   │   │   └── Requests/
│   │   │       └── BanUserRequest.php
│   │   │
│   │   └── F15_ModeratorActionLog/             # Fitur 15: Moderator Action Log (auto-tracked)
│   │       ├── Services/
│   │       │   └── ModerationLogService.php
│   │       ├── Repositories/
│   │       │   └── ModerationLogRepository.php
│   │       └── Events/
│   │           └── ModerationActionLogged.php
│   │
│   └── User/                                   # Core forum features (Fitur 15-29) 
│       ├── F15_CreatePost/                     # Fitur 15: Buat Postingan Baru
│       │   ├── Controllers/
│       │   │   └── PostController.php
│       │   ├── Services/
│       │   │   └── PostService.php
│       │   ├── Repositories/
│       │   │   └── PostRepository.php
│       │   └── Requests/
│       │       └── StorePostRequest.php
│       │
│       ├── F16_EditPost/                       # Fitur 16: Edit Postingan
│       │   ├── Controllers/
│       │   │   └── PostEditController.php
│       │   ├── Services/
│       │   │   └── PostEditService.php
│       │   ├── Repositories/
│       │   │   └── PostEditRepository.php
│       │   └── Requests/
│       │       └── UpdatePostRequest.php
│       │
│       ├── F17_DeletePost/                     # Fitur 17: Hapus Postingan (soft delete)
│       │   ├── Controllers/
│       │   │   └── PostDeleteController.php
│       │   ├── Services/
│       │   │   └── PostDeleteService.php
│       │   └── Repositories/ (optional)
│       │
│       ├── F18_MarkAcceptedAnswer/             # Fitur 18: Mark as Accepted Answer
│       │   ├── Controllers/
│       │   │   └── AcceptedAnswerController.php
│       │   ├── Services/
│       │   │   └── AcceptedAnswerService.php
│       │   └── Repositories/ (optional)
│       │
│       ├── F19_PostEditHistory/                # Fitur 19: Post Edit History (auto-tracked)
│       │   ├── Services/
│       │   │   └── PostHistoryService.php
│       │   ├── Repositories/
│       │   │   └── PostEditHistoryRepository.php
│       │   └── Events/
│       │       └── PostEdited.php
│       │
│       ├── F20_CreateComment/                  # Fitur 20: Buat Komentar/Jawaban
│       │   ├── Controllers/
│       │   │   └── CommentController.php
│       │   ├── Services/
│       │   │   └── CommentService.php
│       │   ├── Repositories/
│       │   │   └── CommentRepository.php
│       │   └── Requests/
│       │       └── StoreCommentRequest.php
│       │
│       ├── F21_NestedCommentReply/             # Fitur 21: Nested Reply (Balasan bertingkat)
│       │   ├── Controllers/
│       │   │   └── CommentReplyController.php
│       │   ├── Services/
│       │   │   └── CommentReplyService.php
│       │   ├── Repositories/
│       │   │   └── CommentReplyRepository.php
│       │   └── Requests/
│       │       └── StoreReplyRequest.php
│       │
│       ├── F22_EditDeleteComment/              # Fitur 22: Edit & Hapus Komentar
│       │   ├── Controllers/
│       │   │   └── CommentEditDeleteController.php
│       │   ├── Services/
│       │   │   └── CommentEditDeleteService.php
│       │   ├── Repositories/
│       │   │   └── CommentEditDeleteRepository.php
│       │   └── Requests/
│       │       └── UpdateCommentRequest.php
│       │
│       ├── F23_CommentEditHistory/             # Fitur 23: Comment Edit History (auto-tracked)
│       │   ├── Services/
│       │   │   └── CommentHistoryService.php
│       │   ├── Repositories/
│       │   │   └── CommentEditHistoryRepository.php
│       │   └── Events/
│       │       └── CommentEdited.php
│       │
│       ├── F24_VoteSystem/                     # Fitur 24: Upvote/Downvote Post & Comment
│       │   ├── Controllers/
│       │   │   └── VoteController.php
│       │   ├── Services/
│       │   │   └── VoteService.php
│       │   ├── Repositories/
│       │   │   └── VoteRepository.php
│       │   └── Requests/
│       │       └── VoteRequest.php
│       │
│       ├── F25_LikeSystem/                     # Fitur 25: Like Post & Comment
│       │   ├── Controllers/
│       │   │   └── LikeController.php
│       │   ├── Services/
│       │   │   └── LikeService.php
│       │   ├── Repositories/
│       │   │   └── LikeRepository.php
│       │   └── Requests/ (optional)
│       │
│       ├── F26_BookmarkPost/                   # Fitur 26: Bookmark/Save Post
│       │   ├── Controllers/
│       │   │   └── BookmarkController.php
│       │   ├── Services/
│       │   │   └── BookmarkService.php
│       │   ├── Repositories/
│       │   │   └── BookmarkRepository.php
│       │   └── Requests/ (optional)
│       │
│       ├── F27_FollowUser/                     # Fitur 27: Follow/Unfollow User
│       │   ├── Controllers/
│       │   │   └── FollowController.php
│       │   ├── Services/
│       │   │   └── FollowService.php
│       │   ├── Repositories/
│       │   │   └── FollowRepository.php
│       │   └── Requests/ (optional)
│       │
│       ├── F28_NotificationSystem/             # Fitur 28: Sistem Notifikasi Real-time
│       │   ├── Controllers/
│       │   │   └── NotificationController.php
│       │   ├── Services/
│       │   │   └── NotificationService.php
│       │   ├── Repositories/
│       │   │   └── NotificationRepository.php
│       │   ├── Events/
│       │   │   └── NotificationCreated.php
│       │   └── Listeners/
│       │       └── SendNotification.php
│       │
│       └── F27_GamificationLeaderboard/        # Fitur 29: Reputation Level & Leaderboard
│           ├── Controllers/
│           │   └── GamificationController.php
│           ├── Services/
│           │   └── GamificationService.php
│           ├── Repositories/
│           │   └── GamificationRepository.php
│           ├── Events/
│           │   └── PointsEarned.php
│           └── Listeners/
│               └── UpdateReputation.php
│
├── app/                                    # Laravel core (Models, Providers)
│   ├── Http/
│   │   └── Controllers/                    # Base controllers (if any)
│   ├── Models/                             # Eloquent models (optional, DDD uses repos)
│   │   └── User.php
│   └── Providers/
│       └── AppServiceProvider.php
│
├── bootstrap/                              # Laravel bootstrap
│   ├── app.php
│   ├── providers.php
│   └── cache/
│
├── config/                                 # Configuration files
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── filesystems.php
│   ├── logging.php
│   ├── mail.php
│   ├── queue.php
│   ├── services.php
│   └── session.php
│
├── database/                               # Database related
│   ├── factories/
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   └── 0001_01_01_000002_create_jobs_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
│
├── docs/                                   # Documentation
│   ├── StructureBackend/                   # ⬅️ YOU ARE HERE
│   │   ├── 00_README.md
│   │   ├── 01_FEATURES_OVERVIEW.md
│   │   ├── 02_MODULE_STRUCTURE.md (this file)
│   │   ├── 03_DATABASE_SCHEMA.md
│   │   ├── 04_FEATURE_MAPPING.md
│   │   └── 05_ARCHITECTURE_PATTERNS.md
│   └── [other docs]
│
├── public/                                 # Web root
│   ├── index.php
│   └── robots.txt
│
├── resources/                              # Frontend assets
│   ├── css/
│   ├── js/
│   └── views/
│
├── routes/                                 # Route definitions
│   ├── console.php
│   └── web.php                             # API routes juga bisa di sini
│
├── storage/                                # Storage (logs, sessions, cache)
│   ├── app/
│   ├── framework/
│   └── logs/
│
├── tests/                                  # Unit & Feature tests
│   ├── Feature/
│   └── Unit/
│
├── vendor/                                 # Composer dependencies
│
├── .editorconfig                           # Editor configuration
├── .env                                    # Environment variables (local)
├── .env.example                            # Environment template
├── .gitignore                              # Git ignore rules
├── artisan                                 # Laravel CLI
├── composer.json                           # PHP dependencies
├── composer.lock                           # Locked versions
├── init_modules.php                        # Module initialization
├── package.json                            # NPM dependencies (if any)
├── phpunit.xml                             # Test configuration
├── README.md                               # Project README
└── vite.config.js                          # Vite configuration (frontend build)
```

---

## 🔍 Struktur Per Domain Module

### 1. Auth Domain (Fitur 1-2)
**Path**: `Modules/Auth/`

**Sub-Features**:
- **F1_Register** - Register akun user baru
- **F2_Login** - Login & penerbitan token API

**Naming Pattern**:
```
Auth/
├── F1_Register/
│   ├── Controllers/RegisterController.php
│   ├── Requests/RegisterRequest.php
│   ├── Services/RegisterService.php
│   └── Repositories/ (optional)
│
└── F2_Login/
    ├── Controllers/LoginController.php
    ├── Requests/LoginRequest.php
    ├── Services/LoginService.php
    └── Repositories/ (optional)
```

**Note**: Tidak perlu authorization untuk endpoints ini (public access untuk register)

---

### 2. Common Domain (Fitur 3-6)
**Path**: `Modules/Common/`

**Sub-Features**:
- **F3_SearchPost** - Search functionality
- **F4_FilterByTag** - Filter by tags
- **F5_FilterByCategory** - Filter by category
- **F6_TrendingPopularPost** - Trending/popular posts

**Naming Pattern**:
```
Common/
├── F3_SearchPost/
│   ├── Controllers/SearchController.php
│   ├── Services/SearchService.php
│   └── Repositories/ (optional)
│
├── F4_FilterByTag/
├── F5_FilterByCategory/
└── F6_TrendingPopularPost/
```

**Note**: Semua fitur di Common adalah public (tidak perlu authentication)

---

### 3. Admin Domain (Fitur 7-11)
**Path**: `Modules/Admin/`

**Sub-Features**:
- **F7_RoleAndPermission** - Multi-role & permission management
- **F8_UserManagement** - User profile & admin management
- **F9_CategoryMaster** - Category CRUD
- **F10_TagMaster** - Tag CRUD
- **F11_BadgeMaster** - Badge CRUD

**Naming Pattern**:
```
Admin/
├── F7_RoleAndPermission/
│   ├── Controllers/
│   │   ├── RoleController.php
│   │   └── UserManagementController.php
│   ├── Services/
│   ├── Repositories/
│   └── Requests/
│
├── F8_UserManagement/
├── F9_CategoryMaster/
├── F10_TagMaster/
└── F11_BadgeMaster/
```

**Authorization**: Admin role required for all endpoints

---

### 4. Moderator Domain (Fitur 12-14)
**Path**: `Modules/Moderator/`

**Sub-Features**:
- **F12_ContentReportQueue** - Report management & triage
- **F13_UserBanSanction** - Ban/unban users
- **F14_ModeratorActionLog** - Auto-tracked action logs

**Naming Pattern**:
```
Moderator/
├── F12_ContentReportQueue/
│   ├── Controllers/ReportController.php
│   ├── Services/ReportService.php
│   ├── Repositories/ReportRepository.php
│   └── Requests/UpdateReportRequest.php
│
├── F13_UserBanSanction/
│   ├── Controllers/UserSanctionController.php
│   ├── Services/UserSanctionService.php
│   ├── Repositories/UserSanctionRepository.php
│   └── Requests/BanUserRequest.php
│
└── F14_ModeratorActionLog/
    ├── Services/ModerationLogService.php
    ├── Repositories/ModerationLogRepository.php
    └── Events/ModerationActionLogged.php
```

**Authorization**: Moderator role required

---

### 5. User Domain (Fitur 15-29)
**Path**: `Modules/User/`

**Sub-Features** (15 fitur):
- **F15_CreatePost** - Create new post
- **F16_EditPost** - Edit existing post
- **F17_DeletePost** - Delete post (soft delete)
- **F18_MarkAcceptedAnswer** - Mark comment as accepted answer
- **F19_PostEditHistory** - Track post edits (auto-logged)
- **F20_CreateComment** - Create comment/answer
- **F21_NestedCommentReply** - Reply to comments (threaded)
- **F22_EditDeleteComment** - Edit/delete comments
- **F23_CommentEditHistory** - Track comment edits (auto-logged)
- **F24_VoteSystem** - Upvote/downvote system
- **F25_LikeSystem** - Like system
- **F26_BookmarkPost** - Bookmark posts
- **F27_FollowUser** - Follow users
- **F28_NotificationSystem** - Real-time notifications
- **F27_GamificationLeaderboard** - Reputation & leaderboard

**Naming Pattern**:
```
User/
├── F15_CreatePost/
│   ├── Controllers/PostController.php
│   ├── Services/PostService.php
│   ├── Repositories/PostRepository.php
│   └── Requests/StorePostRequest.php
│
├── F16_EditPost/
├── F17_DeletePost/
├── F18_MarkAcceptedAnswer/
├── F19_PostEditHistory/
├── F20_CreateComment/
├── F21_NestedCommentReply/
├── F22_EditDeleteComment/
├── F23_CommentEditHistory/
├── F24_VoteSystem/
├── F25_LikeSystem/
├── F26_BookmarkPost/
├── F27_FollowUser/
├── F28_NotificationSystem/
└── F27_GamificationLeaderboard/
```

**Authorization**: User authentication required for most endpoints

---

## 📋 Feature Numbering Reference

| No | Feature Name | Path | Domain |
|----|----|----|----|
| 1 | Register Akun | `Auth/F1_Register/` | Auth |
| 2 | Login API | `Auth/F2_Login/` | Auth |
| 3 | Search Post | `Common/F3_SearchPost/` | Common |
| 4 | Filter by Tag | `Common/F4_FilterByTag/` | Common |
| 5 | Filter by Category | `Common/F5_FilterByCategory/` | Common |
| 6 | Trending Posts | `Common/F6_TrendingPopularPost/` | Common |
| 7 | Role & Permission | `Admin/F7_RoleAndPermission/` | Admin |
| 8 | User Management | `Admin/F8_UserManagement/` | Admin |
| 9 | Category Master | `Admin/F9_CategoryMaster/` | Admin |
| 10 | Tag Master | `Admin/F10_TagMaster/` | Admin |
| 11 | Badge Master | `Admin/F11_BadgeMaster/` | Admin |
| 12 | Report Queue | `Moderator/F12_ContentReportQueue/` | Moderator |
| 13 | User Ban | `Moderator/F13_UserBanSanction/` | Moderator |
| 14 | Mod Log | `Moderator/F14_ModeratorActionLog/` | Moderator |
| 15 | Create Post | `User/F15_CreatePost/` | User |
| 16 | Edit Post | `User/F16_EditPost/` | User |
| 17 | Delete Post | `User/F17_DeletePost/` | User |
| 18 | Accept Answer | `User/F18_MarkAcceptedAnswer/` | User |
| 19 | Post History | `User/F19_PostEditHistory/` | User |
| 20 | Create Comment | `User/F20_CreateComment/` | User |
| 21 | Nested Reply | `User/F21_NestedCommentReply/` | User |
| 22 | Edit Comment | `User/F22_EditDeleteComment/` | User |
| 23 | Comment History | `User/F23_CommentEditHistory/` | User |
| 24 | Vote System | `User/F24_VoteSystem/` | User |
| 25 | Like System | `User/F25_LikeSystem/` | User |
| 26 | Bookmark | `User/F26_BookmarkPost/` | User |
| 27 | Follow User | `User/F27_FollowUser/` | User |
| 28 | Notification | `User/F28_NotificationSystem/` | User |
| 29 | Gamification | `User/F27_GamificationLeaderboard/` | User |

---

## 🔍 Struktur Per Feature

---

## 📋 Naming Conventions

### Controllers
```php
// Pattern: {Resource}Controller
class PostController extends Controller {
    public function index() { }           // GET /posts
    public function store() { }           // POST /posts
    public function show($id) { }         // GET /posts/{id}
    public function update($id) { }       // PUT /posts/{id}
    public function destroy($id) { }      // DELETE /posts/{id}
}

// Custom actions
public function acceptAnswer($postId, $commentId) { }  // POST /posts/{id}/accept-answer/{commentId}
```

### Services
```php
// Pattern: {Resource}Service
class PostService {
    public function create(array $data) { }
    public function update($id, array $data) { }
    public function delete($id) { }
    public function getWithRelations($id) { }
}
```

### Repositories
```php
// Pattern: {Resource}Repository
class PostRepository {
    public function find($id) { }
    public function all() { }
    public function create(array $data) { }
    public function update($id, array $data) { }
    public function delete($id) { }
    public function whereTag($tagId) { }
}
```

### Requests (Form Validation)
```php
// Pattern: {Action}{Resource}Request
class StorePostRequest extends FormRequest {
    public function rules() { }
    public function messages() { }
}

class UpdatePostRequest extends FormRequest { }
```

### Models (if using)
```php
// Pattern: {Resource} (singular, PascalCase)
class Post { }
class Comment { }
class User { }
```

### Database Tables
```php
// Pattern: {resource}_plural (snake_case, plural)
Table: posts
Table: comments
Table: categories
Table: tags
Table: badges
Table: votes
Table: likes
Table: bookmarks
Table: follows
Table: notifications
Table: moderation_logs
```

### Routes
```php
// API routes (typically in routes/api.php or web.php)
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

Route::get('explore/search', [ExploreController::class, 'search']);
Route::get('explore/trending', [ExploreController::class, 'trending']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('posts', [PostController::class, 'store']);
    Route::put('posts/{id}', [PostController::class, 'update']);
});
```

---

## 🏛️ Layer Breakdown

Setiap fitur mengikuti Clean Architecture layers:

### 1. Controller Layer
- **Tanggung Jawab**: HTTP request/response handling
- **Input**: HTTP request parameters
- **Output**: JSON response
- **Validasi**: Delegasi ke Requests
- **File**: `Controllers/{Resource}Controller.php`

**Example**:
```php
class PostController {
    public function store(StorePostRequest $request) {
        // Request sudah validated
        return $this->service->create($request->validated());
    }
}
```

### 2. Service Layer
- **Tanggung Jawab**: Business logic & orchestration
- **Input**: Validated data
- **Output**: Domain objects
- **Validasi**: Business rule validation
- **File**: `Services/{Resource}Service.php`

**Example**:
```php
class PostService {
    public function create(array $data) {
        // Business logic: check user reputation, add tags, etc
        return $this->repository->create($data);
    }
}
```

### 3. Repository Layer
- **Tanggung Jawab**: Data access & persistence
- **Input**: Domain objects or arrays
- **Output**: Domain objects (Eloquent models)
- **File**: `Repositories/{Resource}Repository.php`

**Example**:
```php
class PostRepository {
    public function create(array $data) {
        return Post::create($data);
    }
    
    public function whereTag($tagId) {
        return Post::whereHas('tags', fn($q) => $q->where('id', $tagId))->get();
    }
}
```

### 4. Request Layer (Validation)
- **Tanggung Jawab**: Input validation & authorization
- **File**: `Requests/{Action}{Resource}Request.php`

**Example**:
```php
class StorePostRequest extends FormRequest {
    public function authorize() {
        return $this->user()->can('create', Post::class);
    }
    
    public function rules() {
        return [
            'title' => 'required|min:10|max:255',
            'content' => 'required|min:30',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'required|array|min:1',
        ];
    }
}
```

---

## 🔄 Typical Request Flow

```
HTTP Request
    ↓
Router (routes/api.php)
    ↓
Controller (PostController@store)
    ↓
Form Validation (StorePostRequest@validate)
    ├─ authorize() → Check permission
    ├─ rules() → Validate input
    ├─ validated() → Get clean data
    ↓
Service (PostService@create)
    ├─ Business logic
    ├─ Fire events
    ├─ Call repositories
    ↓
Repository (PostRepository@create)
    ├─ Eloquent operations
    ├─ Database insert
    ↓
Response (JSON)
    ↓
HTTP Response (200, 201, etc)
```

---

## 📦 Dependency Injection

Laravel Service Container mengelola dependencies:

```php
// In Service Provider (config/app.php)
$this->app->bind(PostService::class, function() {
    return new PostService(
        new PostRepository(),
        new PostHistoryService()
    );
});

// In Controller
class PostController {
    public function __construct(
        private PostService $postService,
        private NotificationService $notificationService
    ) {}
    
    public function store(StorePostRequest $request) {
        $post = $this->postService->create($request->validated());
        // Auto-injected services ready to use
    }
}
```

---

## 📝 File Location Quick Reference

| Purpose | Location |
|---------|----------|
| HTTP Endpoints | `Modules/{Domain}/{Feature}/Controllers/{Feature}Controller.php` |
| Business Logic | `Modules/{Domain}/{Feature}/Services/{Feature}Service.php` |
| Data Access | `Modules/{Domain}/{Feature}/Repositories/{Feature}Repository.php` |
| Input Validation | `Modules/{Domain}/{Feature}/Requests/{Action}{Feature}Request.php` |
| Database Models | `app/Models/{Feature}.php` (optional, keep minimal) |
| Routes | `routes/api.php` or `routes/web.php` |
| Tests | `tests/Feature/{Feature}Test.php` |
| Migrations | `database/migrations/{timestamp}_create_{table}_table.php` |

---

## 🚀 Best Practices

### ✅ DO
- Keep controllers thin (max 50 lines)
- Put business logic in services
- Use repositories for data access
- Validate input in Request classes
- Inject dependencies via constructor
- Use meaningful names (PostService, not PS)
- One responsibility per class

### ❌ DON'T
- Put business logic in controllers
- Query database directly in controllers
- Duplicate validation logic
- Create "god" services (too many methods)
- Hardcode values (use config)
- Skip error handling

---

**Next: Baca [03_DATABASE_SCHEMA.md](03_DATABASE_SCHEMA.md) untuk skema database →**
