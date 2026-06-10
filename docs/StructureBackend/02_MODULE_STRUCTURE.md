# 02. Module Structure - Arsitektur & Organisasi Folder

**Versi:** 2.0 (Terverifikasi dari Codebase Aktual)
**Tanggal Update:** 10 Juni 2026

Dokumentasi lengkap tentang organisasi folder, naming conventions, dan arsitektur modular backend MTS.

---

## 1. Core Architecture Pattern

Proyek ini menggunakan kombinasi dari:
1. **Domain-Driven Design (DDD)** — Modul dikelompokkan berdasarkan domain bisnis
2. **Feature-Based Modularity** — Setiap fitur memiliki folder terisolasi sendiri
3. **Clean Architecture** — Separation of concerns antar layer (Controller → Service → Repository)
4. **SOLID Principles** — Single Responsibility, Dependency Injection

---

## 2. Root Folder: `MTS_backend/`

```
MTS_backend/
│
├── Modules/                          # ⭐ Core business logic (Feature-Based DDD)
│   ├── Auth/                         # Domain: Autentikasi (4 fitur)
│   ├── Common/                       # Domain: Fitur Publik (4 fitur)
│   ├── Admin/                        # Domain: Administrasi (5 fitur)
│   ├── Moderator/                    # Domain: Moderasi (3 fitur)
│   └── User/                         # Domain: Fitur User (15 fitur)
│
├── app/
│   ├── Http/
│   │   ├── Controllers/              # Base controller
│   │   └── Middleware/               # Custom middleware (RoleMiddleware)
│   ├── Models/                       # Shared Eloquent models (6 grup)
│   │   ├── Auth/                     # User, Role, UserRole
│   │   ├── Content/                  # Post, Comment, Category, Tag, PostTag
│   │   ├── Gamification/             # Badge, PointsLog, UserBadge
│   │   ├── History/                  # PostEditHistory, CommentEditHistory
│   │   ├── Interaction/              # Vote, Like, Bookmark, Follow
│   │   └── Moderation/              # Notification, Report, ModerationLog
│   └── Providers/                    # Service providers
│
├── bootstrap/
│   └── app.php                       # Konfigurasi middleware, routing, exceptions
│
├── config/                           # Konfigurasi Laravel (app, auth, database, dll)
│
├── database/
│   ├── migrations/                   # 27 migration files
│   ├── seeders/                      # 10 seeder files + DatabaseSeeder
│   └── factories/                    # Model factories
│
├── routes/
│   ├── api.php                       # ⭐ Semua API routes (166 baris)
│   ├── web.php                       # Web routes
│   └── console.php                   # Console routes
│
├── storage/                          # Logs, cache, file uploads
├── tests/                            # Feature & Unit tests
└── vendor/                           # Composer dependencies
```

---

## 3. Struktur Modul — 5 Domain, 31 Fitur

### 3.1 AUTH Module (`Modules/Auth/`) — 4 Fitur

| Folder | Fitur | Layer |
|--------|-------|-------|
| `F1_Register/` | Register akun baru | Controllers, Services, Repositories, Requests |
| `F2_Login/` | Login (Sanctum SPA session) | Controllers, Services, Repositories, Requests |
| `F3_Logout/` | Logout & session invalidation | Controllers, Services |
| `F31_ForgotPassword/` | Forgot & reset password via email | Controllers, Services, Repositories, Requests, **Jobs, Mail** |

> **Catatan:** F31_ForgotPassword memiliki folder tambahan `Jobs/` (SendResetPasswordEmailJob) dan `Mail/` (ResetPasswordMail) untuk antrean pengiriman email reset password.

### 3.2 COMMON Module (`Modules/Common/`) — 4 Fitur

| Folder | Fitur | Layer |
|--------|-------|-------|
| `F4_SearchPost/` | Pencarian post (keyword) | Controllers, Services |
| `F5_FilterByTag/` | Filter post berdasarkan tag | Controllers, Services |
| `F6_FilterByCategory/` | Filter post berdasarkan kategori | Controllers, Services |
| `F7_TrendingPopularPost/` | Post trending & populer | Controllers, Services |

### 3.3 ADMIN Module (`Modules/Admin/`) — 5 Fitur

| Folder | Fitur | Layer |
|--------|-------|-------|
| `F8_RoleAndPermission/` | Manajemen role & assign role user | Controllers, Services, Repositories, Requests |
| `F9_UserManagement/` | Dashboard admin, CRUD user, reset password | Controllers, Services, Repositories, Requests |
| `F10_CategoryMaster/` | CRUD master kategori | Controllers, Services, Repositories, Requests |
| `F11_BadgeMaster/` | CRUD master badge | Controllers, Services, Repositories, Requests |
| `F12_TagMaster/` | CRUD master tag | Controllers, Services, Repositories, Requests |

### 3.4 MODERATOR Module (`Modules/Moderator/`) — 3 Fitur

| Folder | Fitur | Layer |
|--------|-------|-------|
| `F13_ContentReportQueue/` | Antrian laporan konten | Controllers, Services, Repositories, Requests |
| `F14_ModeratorActionLog/` | Log aksi moderator | Controllers, Services, Repositories |
| `F15_UserBanSanction/` | Sanksi user (warn/ban/unban) | Controllers, Services, Repositories, Requests |

### 3.5 USER Module (`Modules/User/`) — 15 Fitur

| Folder | Fitur | Layer |
|--------|-------|-------|
| `F16_Post/` | CRUD postingan | Controllers, Services, Repositories, Requests |
| `F17_Comment/` | CRUD komentar | Controllers, Services, Repositories, Requests |
| `F18_MarkAcceptedAnswer/` | Tandai jawaban diterima | Controllers, Services, Repositories |
| `F19_PostEditHistory/` | Riwayat edit post | Controllers, Services, Repositories |
| `F20_NestedCommentReply/` | Balasan komentar bersarang | Controllers, Services |
| `F21_CommentEditHistory/` | Riwayat edit komentar | Controllers, Services, Repositories |
| `F22_VoteSystem/` | Sistem voting (up/down) | Controllers, Services, Repositories, Requests |
| `F23_LikeSystem/` | Sistem like (toggle) | Controllers, Services, Repositories, Requests |
| `F24_BookmarkPost/` | Bookmark postingan | Controllers, Services, Repositories |
| `F25_FollowUser/` | Follow/unfollow user | Controllers, Services, Repositories |
| `F26_NotificationSystem/` | Sistem notifikasi | Controllers, Services, Repositories |
| `F27_GamificationLeaderboard/` | Papan peringkat gamifikasi | Controllers, Services |
| `F28_ProfileSettings/` | Pengaturan profil user | Controllers, Services, Requests |
| `F29_BadgeAchievement/` | Pencapaian badge & poin | Controllers, Services, Repositories |
| `F30_UserReport/` | Laporan konten oleh user | Controllers, Services, Repositories, Requests |

---

## 4. Layer Penjelasan

Setiap fitur idealnya memiliki 4 layer berikut:

### Controllers/ — Pintu masuk HTTP
- **Tanggung jawab:** Terima HTTP request, delegasi ke Service, kembalikan JSON response
- **ATURAN:** Dilarang query DB langsung, dilarang ada logika bisnis
- **Dependency:** Hanya memanggil Service

```php
// Contoh: PostController.php
class PostController extends Controller
{
    public function __construct(protected PostService $service) {}

    public function store(StorePostRequest $request): JsonResponse
    {
        $post = $this->service->createPost($request->validated());
        return response()->json(['success' => true, 'data' => $post], 201);
    }
}
```

### Services/ — Otak bisnis
- **Tanggung jawab:** Semua logika bisnis, kalkulasi poin, validasi otorisasi
- **ATURAN:** Dilarang menyentuh Request/Response HTTP, boleh memanggil Repository dan Service lain
- **Dependency:** Repository, Service lain (cross-feature), Auth facade

```php
// Contoh: PostService memanggil PostRepository + BadgeAchievementService
class PostService
{
    public function __construct(
        protected PostRepository $repo,
        protected BadgeAchievementService $gamification
    ) {}
}
```

### Repositories/ — Penjaga database
- **Tanggung jawab:** Satu-satunya layer yang boleh query Eloquent
- **ATURAN:** Dilarang ada logika bisnis, hanya CRUD + eager loading
- **Dependency:** Eloquent Models

```php
// Contoh: PostRepository dengan eager loading
public function getAllPaginated(int $perPage = 15, ?string $sort = null)
{
    return Post::with(['user:id,username', 'category:id,name'])
        ->latest()->paginate($perPage);
}
```

### Requests/ — Gerbang validasi
- **Tanggung jawab:** Validasi input menggunakan Laravel FormRequest
- **ATURAN:** Dieksekusi otomatis SEBELUM Controller via type-hint
- **Method:** `authorize()` + `rules()` + `messages()` (opsional)

```php
// Contoh: StorePostRequest
public function rules(): array
{
    return [
        'category_id' => 'required|uuid|exists:categories,id',
        'title'       => 'required|string|max:255',
        'body'        => 'required|string',
        'tags'        => 'nullable|array',
        'tags.*'      => 'exists:tags,id',
    ];
}
```

---

## 5. Shared Models (`app/Models/`)

Model Eloquent tidak berada di dalam folder modul, melainkan di folder shared yang dikelompokkan berdasarkan domain:

| Grup | Model | Tabel |
|------|-------|-------|
| `Auth/` | User, Role, UserRole | `users`, `roles`, `user_roles` |
| `Content/` | Post, Comment, Category, Tag, PostTag | `posts`, `comments`, `categories`, `tags`, `post_tags` |
| `Gamification/` | Badge, PointsLog, UserBadge | `badges`, `points_log`, `user_badges` |
| `History/` | PostEditHistory, CommentEditHistory | `post_edit_history`, `comment_edit_history` |
| `Interaction/` | Vote, Like, Bookmark, Follow | `votes`, `likes`, `bookmarks`, `follows` |
| `Moderation/` | Notification, Report, ModerationLog | `notifications`, `reports`, `moderation_logs` |

Semua model menggunakan:
- **HasUuids** trait — Primary key UUID (bukan auto-increment)
- **PostgreSQL** sebagai database
- **SoftDeletes** pada Post dan Comment

---

## 6. Middleware & Konfigurasi

### File: `bootstrap/app.php`

```php
// Sanctum SPA cookie-based authentication
$middleware->statefulApi();

// CSRF exemption untuk route auth publik
$middleware->validateCsrfTokens(except: [
    'api/auth/login',
    'api/auth/register',
    'api/auth/forgot-password',
    'api/auth/reset-password',
]);

// Custom role middleware alias
$middleware->alias([
    'role' => \App\Http\Middleware\RoleMiddleware::class,
]);
```

### Role Middleware: `app/Http/Middleware/RoleMiddleware.php`

```php
// Penggunaan di routes:
// 'role:admin'           → Hanya admin
// 'role:moderator,admin' → Moderator ATAU admin

public function handle(Request $request, Closure $next, ...$roles): Response
{
    $user = auth()->user();
    $user->load('roles');
    $userRoles = $user->roles->pluck('name')->toArray();
    $hasAccess = !empty(array_intersect($userRoles, $roles));
    // ...
}
```

### Route Groups di `routes/api.php`:

| Middleware | Prefix | Akses |
|---|---|---|
| *(none)* | `auth/`, `explore/`, `posts`, `comments` | Publik (tanpa login) |
| `auth:sanctum` | `settings/`, `me/`, `posts`, `notifications/`, dll | User terautentikasi |
| `auth:sanctum` + `role:moderator,admin` | `moderator/` | Moderator & Admin |
| `auth:sanctum` + `role:admin` | `admin/` | Hanya Admin |

---

## 7. Perbedaan dengan Struktur Laravel Standar

| Aspek | Laravel Standar | Proyek Ini |
|-------|----------------|------------|
| Controllers | `app/Http/Controllers/` | `Modules/{Domain}/{F##}/Controllers/` |
| Services | Tidak ada | `Modules/{Domain}/{F##}/Services/` |
| Repositories | Tidak ada | `Modules/{Domain}/{F##}/Repositories/` |
| Requests | `app/Http/Requests/` | `Modules/{Domain}/{F##}/Requests/` |
| Models | `app/Models/` (flat) | `app/Models/{Group}/` (6 sub-grup) |
| Primary Key | Auto-increment INT | UUID (HasUuids trait) |
| Auth | Sanctum API Token | Sanctum SPA Cookie Session |
| Database | MySQL (default) | PostgreSQL |

---

## 8. Naming Conventions

| Item | Konvensi | Contoh |
|------|----------|--------|
| Feature folder | `F{number}_{PascalCase}` | `F16_Post`, `F22_VoteSystem` |
| Controller | `{Entity}Controller` | `PostController`, `VoteController` |
| Service | `{Entity}Service` | `PostService`, `VoteService` |
| Repository | `{Entity}Repository` | `PostRepository`, `VoteRepository` |
| Request | `{Action}{Entity}Request` | `StorePostRequest`, `VoteRequest` |
| Namespace | `Modules\{Domain}\{Feature}\{Layer}` | `Modules\User\F16_Post\Controllers` |
| Route name | `{domain}.{resource}` | `moderator.reports`, `admin.users` |

---

**Selanjutnya: Baca [03_DATABASE_SCHEMA.md](03_DATABASE_SCHEMA.md) untuk detail struktur database →**
