# 05. Architecture Patterns - Design & Best Practices

**Versi:** 2.0 (Terverifikasi dari Codebase Aktual)
**Tanggal Update:** 10 Juni 2026

Dokumentasi tentang design patterns, principles, dan best practices yang digunakan dalam backend MTS. Semua contoh kode diambil langsung dari codebase.

---

## 1. Clean Architecture — 4 Layer Separation

### Diagram Alur Request

```
HTTP Request → Router (api.php)
    ↓
FormRequest (validasi input otomatis)
    ↓
Controller (HTTP layer — delegasi saja)
    ↓
Service (business logic — kalkulasi, otorisasi)
    ↓
Repository (data access — Eloquent queries)
    ↓
Model (domain object) → Database
```

### Prinsip: Setiap layer HANYA tahu layer di bawahnya

| Layer | Boleh | Dilarang |
|-------|-------|----------|
| Controller | Panggil Service, return JSON response | Query DB, logika bisnis |
| Service | Panggil Repository, Service lain | Sentuh Request/Response HTTP |
| Repository | Query Eloquent, eager loading | Logika bisnis, validasi input |
| Request | Definisikan rules validasi | Logika bisnis |

### Contoh Nyata: F16_Post

**Controller** — Hanya delegasi:
```php
// Modules/User/F16_Post/Controllers/PostController.php
class PostController extends Controller
{
    public function __construct(protected PostService $service) {}

    public function store(StorePostRequest $request): JsonResponse
    {
        $post = $this->service->createPost($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Post berhasil dibuat',
            'data' => $post
        ], 201);
    }
}
```

**Service** — Logika bisnis:
```php
// Modules/User/F16_Post/Services/PostService.php
class PostService
{
    public function __construct(
        protected PostRepository $repo,
        protected BadgeAchievementService $gamification  // Cross-feature dependency
    ) {}

    public function createPost(array $data)
    {
        $user = Auth::user();
        if ($user->reputation_points < 15) {
            abort(403, 'Anda membutuhkan minimal 15 poin untuk membuat post.');
        }

        $data['user_id'] = $user->id;
        $data['status']  = 'open';
        $post = $this->repo->create($data);

        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        // Beri poin gamifikasi
        $this->gamification->addPoints($user, 10, 'create_post', $post->id, 'Membuat postingan baru');
        return $post;
    }
}
```

**Repository** — Hanya query:
```php
// Modules/User/F16_Post/Repositories/PostRepository.php
class PostRepository
{
    public function getAllPaginated(int $perPage = 15, ?string $sort = null)
    {
        $query = Post::with(['user:id,username', 'category:id,name']);
        if ($sort === 'view_count') {
            $query->orderBy('view_count', 'desc');
        } else {
            $query->latest();
        }
        return $query->paginate($perPage);
    }
}
```

---

## 2. Feature-Based Modularity (Domain-Driven Design)

### Struktur: Satu Fitur = Satu Folder Terisolasi

```
Modules/User/F22_VoteSystem/
├── Controllers/VoteController.php
├── Services/VoteService.php
├── Repositories/VoteRepository.php
└── Requests/VoteRequest.php
```

### Keuntungan:
- **High Cohesion:** Semua kode terkait voting ada di satu folder
- **Low Coupling:** Menghapus fitur = menghapus satu folder
- **Parallel Development:** Tim bisa bekerja di fitur berbeda tanpa konflik
- **Discoverability:** `Ctrl+P` langsung ketemu file yang dicari

### Naming Convention:
- **Folder:** `F{number}_{PascalCaseName}` — contoh: `F22_VoteSystem`
- **Namespace:** `Modules\{Domain}\{Feature}\{Layer}` — contoh: `Modules\User\F22_VoteSystem\Services`
- **Dependency Injection via constructor** (PHP 8 promoted properties)

---

## 3. Sanctum SPA Cookie Authentication

### Bukan Token-Based — Tapi Session Cookie

Proyek ini menggunakan **Laravel Sanctum SPA mode**, bukan API token mode. Ini berarti:
- Frontend mengirim `withCredentials: true` via Axios
- Backend mengaktifkan `$middleware->statefulApi()`
- Sesi disimpan di tabel `sessions` sebagai HttpOnly cookie
- **TIDAK ADA** header `Authorization: Bearer` yang dikirim

### Konfigurasi: `bootstrap/app.php`

```php
$middleware->statefulApi();

$middleware->validateCsrfTokens(except: [
    'api/auth/login',
    'api/auth/register',
    'api/auth/forgot-password',
    'api/auth/reset-password',
]);
```

### Implementasi Login: `LoginController.php`

```php
public function login(LoginRequest $request): JsonResponse
{
    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Email atau password salah.'
        ], 401);
    }

    // WAJIB: Mencegah Session Fixation Attack
    $request->session()->regenerate();

    $user = Auth::user();
    return response()->json([
        'status' => 'success',
        'data' => ['user' => [...]],  // Tidak ada access_token!
    ], 200);
}
```

### Alur Auth:
1. Frontend kirim `POST /api/auth/login` dengan credentials
2. Laravel `Auth::attempt()` memvalidasi + membuat session
3. Response berisi `Set-Cookie` header (HttpOnly, session cookie)
4. Semua request selanjutnya otomatis menyertakan cookie ini
5. `auth:sanctum` middleware memvalidasi cookie di setiap protected route

---

## 4. UUID Primary Keys (HasUuids Trait)

### Semua 20 Model Menggunakan UUID

```php
// app/Models/Content/Post.php
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Post extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
}
```

### Keuntungan:
- Tidak bisa ditebak (security through obscurity)
- Aman untuk URL sharing (tidak sequential)
- Compatible dengan distributed systems

### Konsekuensi:
- Semua foreign key bertipe `uuid`
- Junction table juga pakai UUID PK
- PostgreSQL extension `uuid-ossp` diaktifkan di migration pertama

---

## 5. Role-Based Access Control (Custom Middleware)

### RoleMiddleware: Variadic Roles

```php
// app/Http/Middleware/RoleMiddleware.php
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();
        $user->load('roles');
        $userRoles = $user->roles->pluck('name')->toArray();
        $hasAccess = !empty(array_intersect($userRoles, $roles));

        if (!$hasAccess) {
            return response()->json([
                'message' => 'Forbidden. You do not have the required role.',
            ], 403);
        }

        return $next($request);
    }
}
```

### Penggunaan di Route:

```php
// Hanya Admin
Route::middleware('role:admin')->group(function () {
    Route::get('stats/overview', [AdminDashboardController::class, 'overview']);
});

// Moderator ATAU Admin
Route::middleware('role:moderator,admin')->group(function () {
    Route::apiResource('categories', CategoryController::class);
});
```

### Hierarki Akses:
```
Public → auth:sanctum → role:moderator,admin → role:admin
  (semua)  (login)        (mod + admin)          (admin only)
```

---

## 6. FormRequest Validation Layer

### Validasi Otomatis Sebelum Controller

```php
// Modules/User/F16_Post/Requests/StorePostRequest.php
class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Auth dicek di middleware route
    }

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
}
```

```php
// Modules/User/F22_VoteSystem/Requests/VoteRequest.php
class VoteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'target_id'   => 'required|uuid',
            'target_type' => 'required|in:post,comment',
            'type'        => 'required|in:up,down',
        ];
    }
}
```

### Cara Kerja:
1. Controller menerima `StorePostRequest $request` sebagai parameter
2. Laravel **otomatis** menjalankan validasi SEBELUM method dieksekusi
3. Jika gagal → return 422 JSON otomatis
4. Jika lolos → `$request->validated()` berisi data bersih

---

## 7. Polymorphic Relations (Manual Implementation)

### Pattern: target_id + target_type

Digunakan di: `votes`, `likes`, `reports`

```php
// Modules/User/F22_VoteSystem/Services/VoteService.php
public function vote(string $userId, string $targetId, string $targetType, string $voteInput): array
{
    // Resolve model berdasarkan target_type
    $model = ($targetType === 'post') ? Post::find($targetId) : Comment::find($targetId);

    // Prevent self-voting
    if ($model && $model->user_id === $userId) {
        abort(403, 'Anda tidak bisa memberikan vote pada konten Anda sendiri.');
    }

    // Toggle logic: create, cancel, atau update vote
    $typeValue = ($voteInput === 'up') ? 1 : -1;
    $existingVote = $this->repository->getExistingVote($userId, $targetId, $targetType);

    if (!$existingVote) {
        $this->repository->createVote($userId, $targetId, $targetType, $typeValue);
    } elseif ($existingVote->vote_type === $typeValue) {
        $this->repository->deleteVote($existingVote);  // Cancel
    } else {
        $this->repository->updateVote($existingVote, $typeValue);  // Switch
    }

    // Sync denormalized score
    $actionScore = $this->repository->getScore($targetId, $targetType);
    $this->syncVoteScore($targetId, $targetType, $actionScore);

    return ['action' => $action, 'score' => $actionScore];
}
```

### Repository — UUID manual:
```php
// Modules/User/F22_VoteSystem/Repositories/VoteRepository.php
public function createVote(string $userId, string $targetId, string $targetType, int $type): void
{
    Vote::create([
        'id' => (string) Str::uuid(),
        'user_id' => $userId,
        'target_id' => $targetId,
        'target_type' => $targetType,
        'vote_type' => $type,
    ]);
}

public function getScore(string $targetId, string $targetType): int
{
    return (int) Vote::where('target_id', $targetId)
        ->where('target_type', $targetType)
        ->sum(DB::raw('CAST(vote_type AS INTEGER)'));
}
```

> **Catatan:** Tidak menggunakan Laravel `morphTo()` bawaan karena target_type menggunakan string literal (`'post'`, `'comment'`), bukan fully-qualified class name.

---

## 8. Eloquent Eager Loading

### Preventing N+1 Query Problem

```php
// PostRepository — Selective column eager loading
Post::with(['user:id,username', 'category:id,name'])

// PostRepository — Deep eager loading untuk detail
Post::with(['user', 'category', 'tags', 'comments.user', 'comments.replies.user'])

// PostRepository — Conditional withTrashed untuk staff
if ($isStaff) {
    $post = $query->withTrashed()->findOrFail($id);
} else {
    $post = $query->findOrFail($id);
}
```

### Pola:
- **List views:** Selective columns (`user:id,username`) — ringan
- **Detail views:** Full relations — lengkap
- **Staff access:** Include soft-deleted records (`withTrashed`)

---

## 9. Audit Trail Pattern (Edit History)

### Mencatat Setiap Perubahan Body

```php
// PostService.php — Saat update post
$oldBody = $post->body;
$post = $this->repo->update($id, $data);

if (isset($data['body']) && $data['body'] !== $oldBody) {
    $post->editHistories()->create([
        'edited_by'   => Auth::id(),
        'body_before' => $oldBody,
        'body_after'  => $data['body'],
        'reason'      => $data['edit_reason'] ?? 'Update konten',
        'edited_at'   => now(),
    ]);
}
```

### Tabel Audit:
- `post_edit_history` — riwayat perubahan body post
- `comment_edit_history` — riwayat perubahan body comment
- `moderation_logs` — aksi moderator (warn/ban/unban)

---

## 10. Gamification System

### Poin + Badge + Level

```php
// Modules/User/F29_BadgeAchievement/Services/BadgeAchievementService.php
public function addPoints(User $user, int $points, string $actionType, ...)
{
    return DB::transaction(function () use ($user, $points, $actionType, ...) {
        // 1. Catat ke log
        PointsLog::create([
            'user_id' => $user->id,
            'points' => $points,
            'action_type' => $actionType,
            'reference_id' => $refId,
        ]);

        // 2. Increment reputation
        $user->increment('reputation_points', $points);

        // 3. Hitung level baru (setiap 50 poin = +1 level)
        $newLevel = floor($user->reputation_points / 50) + 1;
        if ($newLevel > $user->level) {
            $user->update(['level' => $newLevel]);
        }

        // 4. Cek badge otomatis
        $this->checkAndAwardBadges($user);
    });
}
```

### Poin per Aksi:
| Aksi | Poin |
|------|------|
| Membuat post | +10 |
| Upvote diterima (post) | +5 |
| Upvote diterima (comment) | +3 |
| Downvote diterima | -2 |

### Badge Conditions:
- `reputation_points` — threshold poin reputasi
- `post_count` — jumlah post
- `answer_accepted` — jawaban diterima
- `upvote_received` — upvote yang diterima

---

## 11. Cross-Feature Service Dependencies

### Dependency Injection antar Modul

```
PostService (F16)
  ├── PostRepository (F16)
  └── BadgeAchievementService (F29)
        └── NotificationService (F26)

VoteService (F22)
  ├── VoteRepository (F22)
  ├── NotificationService (F26)
  └── BadgeAchievementService (F29)

UserSanctionService (F15)
  ├── UserSanctionRepository (F15)
  ├── ModerationLogService (F14)
  └── NotificationService (F26)
```

### Prinsip:
- Service boleh memanggil Service dari modul lain
- Repository TIDAK boleh memanggil Repository lain
- Controller TIDAK boleh memanggil Service dari modul lain

---

## 12. Soft Deletes

### Hanya Post dan Comment

```php
// app/Models/Content/Post.php
class Post extends Model
{
    use HasUuids, SoftDeletes, HasFactory;
}
```

### Penggunaan:
- `Post::findOrFail($id)` — hanya record non-deleted
- `Post::withTrashed()->findOrFail($id)` — termasuk deleted (untuk staff)
- `$post->delete()` — soft delete (set `deleted_at`)
- Migration: `2026_06_06_113504_add_soft_deletes_to_posts_and_comments_table.php`

---

## 13. RESTful API Design

### Konvensi Endpoint:

| Operasi | Method | Endpoint | Status Code |
|---------|--------|----------|-------------|
| List | GET | `/api/posts` | 200 |
| Detail | GET | `/api/posts/{id}` | 200 |
| Create | POST | `/api/posts` | 201 |
| Update | PUT | `/api/posts/{post}` | 200 |
| Delete | DELETE | `/api/posts/{post}` | 200 |
| Partial | PATCH | `/api/posts/{post}/status` | 200 |
| Toggle | POST | `/api/likes/toggle` | 200 |
| Custom | POST | `/api/votes` | 200 |

### Response Format:
```json
{
    "success": true,
    "message": "Post berhasil dibuat",
    "data": { ... }
}
```

---

## 14. Ringkasan Technology Stack

| Komponen | Teknologi |
|----------|-----------|
| Framework | Laravel 12 |
| PHP | 8.3+ |
| Database | PostgreSQL |
| Auth | Sanctum SPA Cookie |
| Primary Key | UUID |
| Queue | Database driver (jobs table) |
| Session | Database driver (sessions table) |
| Mail | Configurable (untuk reset password) |
| Testing | PHPUnit |
| IDE Helper | barryvdh/laravel-ide-helper |

---

**Kembali ke: [00_README.md](00_README.md) untuk overview →**
