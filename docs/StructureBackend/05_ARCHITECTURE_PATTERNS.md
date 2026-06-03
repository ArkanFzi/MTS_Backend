# 05. Architecture Patterns - Design & Best Practices

## 🏛️ Architecture Overview

Dokumentasi tentang design patterns, principles, dan best practices yang digunakan dalam backend.

---

## 🎯 Core Architecture: Clean Architecture + DDD

### Principles Implemented

1. **Separation of Concerns** - Each layer has single responsibility
2. **Dependency Inversion** - Depend on abstractions, not concretions
3. **Domain-Driven Design** - Business logic in services, not controllers
4. **Repository Pattern** - Abstract data access layer
5. **Service Layer** - Orchestrate complex business operations

---

## 📚 Layer Architecture

```
┌─────────────────────────────────────────────────────┐
│               HTTP / API Layer                      │ ← External Interface
├─────────────────────────────────────────────────────┤
│          Controllers + Requests (Validation)        │ ← Input Validation & Routing
├─────────────────────────────────────────────────────┤
│           Services (Business Logic)                 │ ← Core Application Logic
├─────────────────────────────────────────────────────┤
│        Repositories (Data Access)                   │ ← Database Abstraction
├─────────────────────────────────────────────────────┤
│            Models (Domain Objects)                  │ ← Data Structures
├─────────────────────────────────────────────────────┤
│             Database Layer (SQL)                    │ ← Persistence
└─────────────────────────────────────────────────────┘
```

---

## 🔄 Request Flow Diagram

```
HTTP Request
    ↓
Router (routes/api.php)
    ↓
┌─────────────────────────────────────┐
│   Controller                        │
│   (PostController@store)            │ ← HTTP Layer
└──────────────┬──────────────────────┘
               ↓
┌─────────────────────────────────────┐
│   Form Request                      │
│   (StorePostRequest)                │ ← Input Validation Layer
│   - authorize()                     │   (Authorization check)
│   - rules()                         │   (Validation rules)
│   - messages()                      │   (Custom messages)
└──────────────┬──────────────────────┘
               ↓ (validated data)
┌─────────────────────────────────────┐
│   Service                           │
│   (PostService@create)              │ ← Business Logic Layer
│   - Business validation             │   (Core business rules)
│   - Fire events                     │   (Domain events)
│   - Call repositories               │   (Orchestrate operations)
└──────────────┬──────────────────────┘
               ↓
┌─────────────────────────────────────┐
│   Repository                        │
│   (PostRepository@create)           │ ← Data Access Layer
│   - Eloquent operations             │   (Database abstraction)
└──────────────┬──────────────────────┘
               ↓
┌─────────────────────────────────────┐
│   Database (Eloquent/SQL)           │ ← Persistence Layer
│   INSERT into posts table           │   (Physical storage)
└──────────────┬──────────────────────┘
               ↓
               ← Return Domain Object
               ↓
┌─────────────────────────────────────┐
│   Events Listener                   │ ← Side Effects
│   (PostCreated listener)            │   (Notifications, logs)
└──────────────┬──────────────────────┘
               ↓
               HTTP Response (JSON)
               ↓
               Client
```

---

## 🏗️ Design Patterns Used

### 1. Repository Pattern
**Purpose**: Abstract database access logic

**Implementation**:
```php
// Contract/Interface
interface PostRepositoryInterface {
    public function find($id);
    public function all();
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}

// Concrete Implementation
class PostRepository implements PostRepositoryInterface {
    public function create(array $data) {
        return Post::create($data);
    }
    
    public function whereTag($tagId) {
        return Post::whereHas('tags', fn($q) => $q->where('id', $tagId))->get();
    }
}

// Usage in Service
class PostService {
    public function __construct(
        private PostRepository $repository
    ) {}
    
    public function create(array $data) {
        return $this->repository->create($data);
    }
}
```

**Benefits**:
- Easy to test (mock repository)
- Database agnostic (can switch from SQL to NoSQL)
- Centralized data access logic
- Query optimization in one place

---

### 2. Service Layer Pattern
**Purpose**: Encapsulate business logic

**Implementation**:
```php
class PostService {
    public function __construct(
        private PostRepository $postRepository,
        private TagRepository $tagRepository,
        private NotificationService $notificationService
    ) {}
    
    public function create(array $data) {
        // Business validation
        if ($this->tagRepository->find($data['tags'][0])->is_locked) {
            throw new InvalidTagException();
        }
        
        // Create post
        $post = $this->postRepository->create([
            'user_id' => auth()->id(),
            'title' => $data['title'],
            'content' => $data['content'],
        ]);
        
        // Attach relationships
        $post->tags()->sync($data['tags']);
        
        // Side effects (fire events)
        event(new PostCreated($post));
        
        // Return domain object
        return $post;
    }
}
```

**Benefits**:
- Business logic separated from HTTP concerns
- Reusable across different endpoints
- Easy to test
- Single Responsibility Principle

---

### 3. Dependency Injection Pattern
**Purpose**: Loose coupling between classes

**Implementation**:
```php
// Using constructor injection
class PostController {
    public function __construct(
        private PostService $postService,
        private NotificationService $notificationService
    ) {}
    
    public function store(StorePostRequest $request) {
        $post = $this->postService->create($request->validated());
        return response()->json($post, 201);
    }
}

// Laravel's Service Container auto-resolves dependencies
// Register in AppServiceProvider:
$this->app->bind(PostService::class, function() {
    return new PostService(
        new PostRepository(),
        new PostHistoryService()
    );
});
```

**Benefits**:
- Flexible and testable
- Easy to swap implementations
- Explicit dependencies
- Managed by Laravel's IoC container

---

### 4. Observer/Event-Listener Pattern
**Purpose**: Decouple side effects from core logic

**Implementation**:
```php
// Event (Domain Event)
class PostCreated {
    public function __construct(
        public Post $post
    ) {}
}

// Service fires event
class PostService {
    public function create(array $data) {
        $post = $this->repository->create($data);
        event(new PostCreated($post));  // ← Fire event
        return $post;
    }
}

// Listener 1: Update tag usage count
class UpdateTagUsageCount {
    public function handle(PostCreated $event) {
        $event->post->tags->each(fn($tag) => $tag->increment('usage_count'));
    }
}

// Listener 2: Send notification
class SendPostCreatedNotification {
    public function handle(PostCreated $event) {
        // Notify followers
        $event->post->user->followers->each(fn($follower) => 
            $follower->notify(new NewPostNotification($event->post))
        );
    }
}

// Register in EventServiceProvider
protected $listen = [
    PostCreated::class => [
        UpdateTagUsageCount::class,
        SendPostCreatedNotification::class,
    ],
];
```

**Benefits**:
- Decoupled side effects
- Async operations possible
- Easy to add/remove listeners
- Single Responsibility

---

### 5. Strategy Pattern
**Purpose**: Encapsulate different algorithms/strategies

**Implementation**:
```php
// Strategy interface
interface SortStrategy {
    public function sort(Collection $posts): Collection;
}

// Concrete strategies
class SortByRecent implements SortStrategy {
    public function sort(Collection $posts): Collection {
        return $posts->sortByDesc('created_at');
    }
}

class SortByTrending implements SortStrategy {
    public function sort(Collection $posts): Collection {
        return $posts->each(fn($p) => $p->score = 
            ($p->view_count * 0.3) + ($p->vote_score * 0.5)
        )->sortByDesc('score');
    }
}

// Service using strategy
class ExploreService {
    public function __construct(
        private SortStrategy $sortStrategy
    ) {}
    
    public function getPosts() {
        $posts = Post::all();
        return $this->sortStrategy->sort($posts);
    }
}

// Usage with strategy injection
$service = new ExploreService(new SortByTrending());
```

**Benefits**:
- Flexible algorithm selection
- Easy to add new strategies
- Testable strategies independently

---

### 6. Factory Pattern
**Purpose**: Create objects without specifying exact classes

**Implementation**:
```php
// Factory
class NotificationFactory {
    public static function create(string $type, $actor, $target): Notification {
        return match($type) {
            'comment_reply' => new CommentReplyNotification($actor, $target),
            'post_upvote' => new PostUpvoteNotification($actor, $target),
            'badge_earned' => new BadgeEarnedNotification($actor, $target),
            default => throw new InvalidNotificationType(),
        };
    }
}

// Usage
$notification = NotificationFactory::create('comment_reply', $user, $comment);
$notification->send();
```

**Benefits**:
- Centralized object creation
- Easy to add new types
- Single Responsibility

---

## 💾 Database Access Patterns

### N+1 Query Prevention

**Problem** (N+1):
```php
// ❌ BAD - Queries DB in loop
$posts = Post::all();
foreach ($posts as $post) {
    echo $post->user->name;  // Queries DB for each post
}
// Result: 1 query for posts + N queries for users = N+1 queries
```

**Solution** (Eager Loading):
```php
// ✅ GOOD - Eager load relationships
$posts = Post::with('user')->get();
foreach ($posts as $post) {
    echo $post->user->name;  // No additional queries
}
// Result: 2 queries total (posts + users in one batch)
```

**Implementation in Service**:
```php
class PostService {
    public function getPostsWithComments() {
        return Post::with([
            'user',
            'comments' => fn($q) => $q->orderBy('created_at', 'desc')
                ->with('user'),
            'votes',
            'category'
        ])->paginate(20);
    }
}
```

---

### Query Optimization

**Use Repositories for complex queries**:
```php
class PostRepository {
    public function getTrendingPosts($period = 'week') {
        $date = match($period) {
            'day' => now()->subDay(),
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            default => now()->subWeek(),
        };
        
        return Post::where('created_at', '>=', $date)
            ->selectRaw('*, (view_count * 0.3 + vote_score * 0.5) as score')
            ->orderByDesc('score')
            ->limit(50)
            ->get();
    }
}
```

**Benefits**:
- Readable queries
- Reusable across services
- Optimization in one place

---

## 🧪 Testing Patterns

### Unit Testing (Services)

```php
class PostServiceTest extends TestCase {
    private PostService $service;
    private PostRepository $repository;
    
    protected function setUp(): void {
        parent::setUp();
        $this->repository = Mockery::mock(PostRepository::class);
        $this->service = new PostService($this->repository);
    }
    
    public function test_create_post_validates_title() {
        $this->expectException(ValidationException::class);
        
        $this->service->create([
            'title' => 'Short',  // Too short
            'content' => 'Some content here',
        ]);
    }
    
    public function test_create_post_fires_event() {
        Event::fake();
        
        $this->service->create([
            'title' => 'Valid post title',
            'content' => 'Some content',
        ]);
        
        Event::assertDispatched(PostCreated::class);
    }
}
```

### Feature Testing (API)

```php
class PostApiTest extends TestCase {
    public function test_create_post_endpoint() {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->postJson('/api/posts', [
                'title' => 'Test Post',
                'content' => 'Test content',
                'category_id' => 1,
                'tags' => [1, 2],
            ]);
        
        $response->assertStatus(201);
        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post',
            'user_id' => $user->id,
        ]);
    }
}
```

---

## 🔐 Security Patterns

### Authorization (Policy Pattern)

```php
// Policy
class PostPolicy {
    public function update(User $user, Post $post): bool {
        return $user->id === $post->user_id || $user->isAdmin();
    }
    
    public function delete(User $user, Post $post): bool {
        return $user->id === $post->user_id || $user->isAdmin();
    }
}

// Usage in Controller
class PostController {
    public function update(UpdatePostRequest $request, Post $post) {
        $this->authorize('update', $post);  // Check policy
        return $this->service->update($post->id, $request->validated());
    }
}

// Usage in Blade/API response
@can('update', $post)
    <button>Edit</button>
@endcan
```

### Input Validation (Request Pattern)

```php
class StorePostRequest extends FormRequest {
    public function authorize(): bool {
        return auth()->check();  // User must be authenticated
    }
    
    public function rules(): array {
        return [
            'title' => 'required|string|min:10|max:255',
            'content' => 'required|string|min:30',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'required|array|min:1|max:5',
            'tags.*' => 'exists:tags,id',
        ];
    }
    
    public function messages(): array {
        return [
            'tags.required' => 'At least one tag is required',
            'tags.max' => 'Maximum 5 tags allowed',
        ];
    }
}
```

### Rate Limiting

```php
// In routes
Route::middleware('throttle:60,1')->group(function () {
    Route::post('posts', [PostController::class, 'store']);
    Route::post('comments', [CommentController::class, 'store']);
});

// Custom throttle
Route::middleware('throttle:create-post')->post('posts', ...);
// In config/rate-limiting
// 'create-post' => '5,1'  // Max 5 per 1 minute
```

---

## 📝 Error Handling Pattern

```php
// Custom Exceptions
class PostNotFoundException extends HttpException {
    public function __construct() {
        parent::__construct(404, 'Post not found');
    }
}

class InvalidTagException extends HttpException {
    public function __construct() {
        parent::__construct(422, 'Invalid tag selected');
    }
}

// Exception Handler
class Handler extends ExceptionHandler {
    public function register(): void {
        $this->renderable(function (PostNotFoundException $e, $request) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 'POST_NOT_FOUND'
            ], 404);
        });
    }
}

// Usage in Service
class PostService {
    public function delete($id) {
        $post = $this->repository->find($id);
        if (!$post) {
            throw new PostNotFoundException();
        }
        return $this->repository->delete($id);
    }
}
```

---

## 🔄 Caching Pattern

```php
class PostService {
    public function __construct(
        private PostRepository $repository,
        private Cache $cache
    ) {}
    
    public function getPopularPosts() {
        return $this->cache->remember(
            key: 'posts.popular',
            minutes: 60,
            callback: fn() => $this->repository->getPopular()
        );
    }
    
    public function create(array $data) {
        $post = $this->repository->create($data);
        
        // Invalidate cache
        $this->cache->forget('posts.popular');
        $this->cache->forget('users.'.$data['user_id'].'.posts');
        
        return $post;
    }
}
```

---

## 📊 Response Pattern

**Consistent API Response**:

```php
// Success Response
{
  "success": true,
  "message": "Post created successfully",
  "data": {
    "id": 1,
    "title": "Post title",
    "content": "Post content",
    "user": { "id": 1, "name": "John" },
    "created_at": "2026-06-03T10:00:00Z"
  }
}

// Error Response
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "title": ["Title must be at least 10 characters"],
    "content": ["Content is required"]
  }
}

// Pagination Response
{
  "success": true,
  "data": [...],
  "pagination": {
    "total": 100,
    "per_page": 20,
    "current_page": 1,
    "last_page": 5,
    "from": 1,
    "to": 20
  }
}
```

**Response Trait** (for consistency):
```php
trait ApiResponse {
    public function successResponse($data, $message = null, $code = 200) {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
    
    public function errorResponse($message, $errors = null, $code = 400) {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}

// Usage in Controller
class PostController {
    use ApiResponse;
    
    public function store(StorePostRequest $request) {
        $post = $this->service->create($request->validated());
        return $this->successResponse($post, 'Post created', 201);
    }
}
```

---

## 🚀 Performance Optimization Patterns

### 1. Batch Processing
```php
// Process large datasets efficiently
class ProcessReportsJob {
    public function handle() {
        Report::where('status', 'pending')
            ->chunk(100, function ($reports) {
                foreach ($reports as $report) {
                    $this->processReport($report);
                }
            });
    }
}
```

### 2. Query Scopes
```php
class Post extends Model {
    public function scopeRecent($query) {
        return $query->orderBy('created_at', 'desc');
    }
    
    public function scopePopular($query) {
        return $query->orderByDesc('vote_score');
    }
    
    public function scopePublished($query) {
        return $query->whereNull('deleted_at');
    }
}

// Usage
Post::recent()->popular()->published()->get();
```

### 3. Database Indexes
```php
// In migration
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->index();
    $table->foreignId('category_id')->index();
    $table->string('title');
    $table->text('content');
    $table->integer('vote_score')->index();  // Indexed for sorting
    $table->integer('view_count')->index();
    $table->softDeletes();
    $table->timestamps();
    
    // Composite index
    $table->index(['category_id', 'created_at']);
});
```

---

## 📚 SOLID Principles Implementation

| Principle | Implementation |
|-----------|-----------------|
| **S**ingle Responsibility | Each service has one job (PostService, NotificationService) |
| **O**pen/Closed | Open for extension (interfaces), closed for modification |
| **L**iskov Substitution | Repositories implement consistent interface |
| **I**nterface Segregation | Specific interfaces (PostRepository, NotificationService) |
| **D**ependency Inversion | Depend on abstractions, injected via constructor |

---

## 🎓 Best Practices Checklist

### Code Organization
- [ ] Controllers are thin (max 50 lines)
- [ ] Services contain business logic
- [ ] Repositories handle data access
- [ ] One class per file
- [ ] Meaningful class/method names

### Database
- [ ] Eager loading to prevent N+1 queries
- [ ] Proper indexes on frequently queried columns
- [ ] Soft deletes for audit trail
- [ ] Foreign keys enforced
- [ ] Migrations versioned

### Testing
- [ ] Unit tests for services
- [ ] Feature tests for API endpoints
- [ ] Test coverage > 80%
- [ ] Mock external dependencies
- [ ] Clear test names

### Security
- [ ] Authorization checks in policies
- [ ] Input validation in requests
- [ ] Rate limiting on sensitive endpoints
- [ ] SQL injection prevention (use eloquent)
- [ ] CSRF protection

### Performance
- [ ] Query optimization
- [ ] Caching strategy
- [ ] Batch processing for large datasets
- [ ] Async jobs for heavy operations
- [ ] API response pagination

### Documentation
- [ ] Code comments for complex logic
- [ ] API documentation (this folder)
- [ ] README setup instructions
- [ ] Architecture diagrams
- [ ] Feature mapping

---

**End of Architecture Documentation**

**References**:
- [Laravel Documentation](https://laravel.com/docs)
- [Domain-Driven Design](https://en.wikipedia.org/wiki/Domain-driven_design)
- [Clean Architecture](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
- [SOLID Principles](https://en.wikipedia.org/wiki/SOLID)
- [Repository Pattern](https://martinfowler.com/eaaCatalog/repository.html)
