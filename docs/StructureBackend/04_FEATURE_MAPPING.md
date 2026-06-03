# 04. Feature Mapping - Implementation Roadmap

## 🗂️ Fitur-to-Code Mapping

Dokumentasi ini menunjukkan untuk setiap fitur, file/folder mana yang terlibat dalam implementasinya.

---

## 📍 Navigasi Cepat

Gunakan Ctrl+F untuk mencari fitur spesifik:
- [Fitur 1-2: Auth](#auth-features)
- [Fitur 3-6: Explore](#explore-features)
- [Fitur 7-11: Admin](#admin-features)
- [Fitur 12-14: Moderator](#moderator-features)
- [Fitur 15-19: Post](#post-features)
- [Fitur 20-23: Comment](#comment-features)
- [Fitur 24-27: Interaction](#interaction-features)
- [Fitur 28: Notification](#notification-features)
- [Fitur 29: Gamification](#gamification-features)

---

## 🔐 AUTH FEATURES

### Fitur 1: Register Akun User Baru

**Controllers**:
- `Modules/Auth/Controllers/AuthController.php`
  - Method: `register(RegisterRequest $request)`
  - Action: Validate & create new user, return token

**Requests (Validation)**:
- `Modules/Auth/Requests/RegisterRequest.php`
  - Rules: email unique, password min 8, name required

**Services**:
- `Modules/Auth/Services/AuthService.php`
  - Method: `register(array $data)`
  - Logic: Hash password, create user, generate Sanctum token

**Database Tables**:
- `users` - insert new user record
- `personal_access_tokens` (Laravel Sanctum) - auto-created

**Models** (if used):
- `app/Models/User.php`

**Routes**:
- `POST /api/auth/register` → AuthController@register

**API Response Example**:
```json
{
  "user": {
    "id": 1,
    "email": "user@example.com",
    "name": "John Doe"
  },
  "token": "1|abcdefgh..."
}
```

---

### Fitur 2: Login & Penerbitan Token API

**Controllers**:
- `Modules/Auth/Controllers/AuthController.php`
  - Method: `login(LoginRequest $request)`
  - Action: Validate credentials, generate token

**Requests**:
- `Modules/Auth/Requests/LoginRequest.php`
  - Rules: email exists, password correct, user not banned

**Services**:
- `Modules/Auth/Services/AuthService.php`
  - Method: `login(array $credentials)`
  - Logic: Verify credentials, create Sanctum token

**Database Tables**:
- `users` - fetch & verify
- `personal_access_tokens` - create token

**Routes**:
- `POST /api/auth/login` → AuthController@login
- `POST /api/auth/logout` → AuthController@logout

**Middleware**:
- Route middleware: `auth:sanctum` untuk protected endpoints

**API Response Example**:
```json
{
  "user": {
    "id": 1,
    "email": "user@example.com",
    "name": "John Doe"
  },
  "token": "1|abcdefgh..."
}
```

---

## 🔍 EXPLORE FEATURES

### Fitur 3: Search Postingan

**Controllers**:
- `Modules/Common/Explore/Controllers/ExploreController.php`
  - Method: `search(Request $request)`
  - Params: `q` (query string)

**Services**:
- `Modules/Common/Explore/Services/SearchService.php`
  - Method: `search(string $query, int $page, int $perPage)`
  - Logic: Full-text search on title & content

**Database Tables**:
- `posts` - SELECT WHERE title LIKE OR content LIKE

**Routes**:
- `GET /api/explore/search?q=keyword` → ExploreController@search

**API Response Example**:
```json
{
  "data": [
    {
      "id": 1,
      "title": "How to use Laravel?",
      "content": "...",
      "user": { "id": 1, "name": "John" },
      "vote_score": 10,
      "view_count": 100
    }
  ],
  "pagination": { "total": 50, "page": 1 }
}
```

---

### Fitur 4: Filter Postingan berdasarkan Tag

**Controllers**:
- `Modules/Common/Explore/Controllers/ExploreController.php`
  - Method: `filterByTags(Request $request)`
  - Params: `tag_ids=1,2,3` (array)

**Services**:
- `Modules/Common/Explore/Services/FilterService.php`
  - Method: `filterByTags(array $tagIds, int $page)`
  - Logic: Join post_tags table, WHERE tag_id IN (...)

**Repositories** (optional):
- `Modules/Common/Explore/Repositories/FilterRepository.php`
  - Method: `findByTags(array $tagIds)`

**Database Tables**:
- `posts` - main table
- `post_tags` - junction
- `tags` - metadata

**Query Example**:
```sql
SELECT DISTINCT p.* FROM posts p
JOIN post_tags pt ON p.id = pt.post_id
WHERE pt.tag_id IN (1, 2, 3)
```

**Routes**:
- `GET /api/explore/filter/tags?tag_ids=1,2,3` → ExploreController@filterByTags

---

### Fitur 5: Filter Postingan berdasarkan Kategori

**Controllers**:
- `Modules/Common/Explore/Controllers/ExploreController.php`
  - Method: `filterByCategory(Request $request)`
  - Params: `category_id=1`

**Services**:
- `Modules/Common/Explore/Services/FilterService.php`
  - Method: `filterByCategory(int $categoryId, int $page)`
  - Logic: Include subcategories (if parent-child exists)

**Database Tables**:
- `posts` - WHERE category_id = ?
- `categories` - for hierarchy info

**Routes**:
- `GET /api/explore/filter/categories?category_id=1` → ExploreController@filterByCategory

---

### Fitur 6: Trending / Popular Posts

**Controllers**:
- `Modules/Common/Explore/Controllers/ExploreController.php`
  - Method: `trending(Request $request)`
  - Params: `period=week|month|day|all`

**Services**:
- `Modules/Common/Explore/Services/TrendingService.php`
  - Method: `getTrending(string $period, int $limit)`
  - Logic: Calculate weighted score (views + votes + comments)

**Database Tables**:
- `posts` - SELECT with scoring

**Scoring Algorithm** (example):
```
score = (view_count * 0.3) + (vote_score * 0.5) + (comment_count * 0.2)
```

**Routes**:
- `GET /api/explore/trending?period=week` → ExploreController@trending

---

## 👨‍💼 ADMIN FEATURES

### Fitur 7 & 8: Roles/Permissions & User Management

**Controllers**:
- `Modules/Admin/RoleManagement/Controllers/RoleController.php`
  - Methods: `index()`, `store()`, `update()`, `destroy()`
  
- `Modules/Admin/RoleManagement/Controllers/UserManagementController.php`
  - Methods: `index()`, `show()`, `update()`, `resetPassword()`

**Services**:
- `Modules/Admin/RoleManagement/Services/RoleService.php`
  - Methods: `createRole()`, `assignPermissions()`, `assignRoleToUser()`

- `Modules/Admin/RoleManagement/Services/UserManagementService.php`
  - Methods: `updateProfile()`, `resetPassword()`, `updateAvatar()`

**Requests**:
- `Modules/Admin/RoleManagement/Requests/StoreRoleRequest.php`
- `Modules/Admin/RoleManagement/Requests/UpdateUserRequest.php`

**Database Tables**:
- `roles` - master roles (admin, moderator, user)
- `permissions` - master permissions
- `user_roles` - user → role assignment
- `role_permissions` - role → permission assignment
- `users` - update avatar_url, bio

**Routes**:
- `GET /api/admin/roles` → RoleController@index
- `POST /api/admin/roles` → RoleController@store
- `GET /api/admin/users` → UserManagementController@index
- `PUT /api/admin/users/{id}` → UserManagementController@update
- `PUT /api/admin/users/{id}/reset-password` → UserManagementController@resetPassword

**Authorization**:
- Only users with "admin" role can access

---

### Fitur 9: CRUD Master Kategori Forum

**Controllers**:
- `Modules/Admin/Category/Controllers/CategoryController.php`
  - Methods: `index()`, `store()`, `show()`, `update()`, `destroy()`

**Services**:
- `Modules/Admin/Category/Services/CategoryService.php`
  - Methods: `create()`, `update()`, `delete()`, `getHierarchy()`

**Repositories**:
- `Modules/Admin/Category/Repositories/CategoryRepository.php`
  - Methods: `find()`, `all()`, `create()`, `update()`, `delete()`

**Requests**:
- `Modules/Admin/Category/Requests/StoreCategoryRequest.php`
  - Rules: name required|unique, parent_id exists
- `Modules/Admin/Category/Requests/UpdateCategoryRequest.php`

**Database Tables**:
- `categories` - parent_id for hierarchy

**Routes**:
- `GET /api/admin/categories` → CategoryController@index
- `POST /api/admin/categories` → CategoryController@store
- `PUT /api/admin/categories/{id}` → CategoryController@update
- `DELETE /api/admin/categories/{id}` → CategoryController@destroy

---

### Fitur 10: CRUD Master Tag Forum

**Controllers**:
- `Modules/Admin/Tag/Controllers/TagController.php`
  - Methods: `index()`, `store()`, `show()`, `update()`, `destroy()`

**Services**:
- `Modules/Admin/Tag/Services/TagService.php`
  - Methods: `create()`, `update()`, `delete()`, `getPopular()`

**Repositories**:
- `Modules/Admin/Tag/Repositories/TagRepository.php`

**Requests**:
- `Modules/Admin/Tag/Requests/StoreTagRequest.php`
  - Rules: name required|unique, color hex format
- `Modules/Admin/Tag/Requests/UpdateTagRequest.php`

**Database Tables**:
- `tags` - name, slug, color, usage_count

**Routes**:
- `GET /api/admin/tags` → TagController@index
- `POST /api/admin/tags` → TagController@store
- `PUT /api/admin/tags/{id}` → TagController@update
- `DELETE /api/admin/tags/{id}` → TagController@destroy

---

### Fitur 11: CRUD Badge/Achievement Master

**Controllers**:
- `Modules/Admin/Badge/Controllers/BadgeController.php`
  - Methods: `index()`, `store()`, `update()`, `destroy()`

**Services**:
- `Modules/Admin/Badge/Services/BadgeService.php`
  - Methods: `create()`, `update()`, `awardBadge()`

**Requests**:
- `Modules/Admin/Badge/Requests/StoreBadgeRequest.php`
  - Rules: name required|unique, tier in:bronze,silver,gold
- `Modules/Admin/Badge/Requests/UpdateBadgeRequest.php`

**Database Tables**:
- `badges` - name, tier, icon_url, points_reward
- `user_badges` - track user ownership

**Routes**:
- `GET /api/admin/badges` → BadgeController@index
- `POST /api/admin/badges` → BadgeController@store
- `PUT /api/admin/badges/{id}` → BadgeController@update
- `DELETE /api/admin/badges/{id}` → BadgeController@destroy

---

## 🛡️ MODERATOR FEATURES

### Fitur 12: Manajemen Report Konten

**Controllers**:
- `Modules/Moderator/Report/Controllers/ReportController.php`
  - Methods: `index()`, `show()`, `updateStatus()`, `addNote()`

**Services**:
- `Modules/Moderator/Report/Services/ReportService.php`
  - Methods: `getReports()`, `updateStatus()`, `takeAction()`

**Repositories**:
- `Modules/Moderator/Report/Repositories/ReportRepository.php`

**Requests**:
- `Modules/Moderator/Report/Requests/UpdateReportRequest.php`
  - Rules: status in:pending,in_review,resolved,dismissed

**Database Tables**:
- `reports` - status, assigned_to, resolution_note

**Routes**:
- `GET /api/moderator/reports?status=pending` → ReportController@index
- `GET /api/moderator/reports/{id}` → ReportController@show
- `PUT /api/moderator/reports/{id}/status` → ReportController@updateStatus
- `POST /api/moderator/reports/{id}/note` → ReportController@addNote

**Authorization**:
- Only moderator role can access

---

### Fitur 13 & 14: User Ban/Unban + Moderation Log

**Controllers**:
- `Modules/Moderator/UserSanction/Controllers/UserSanctionController.php`
  - Methods: `ban()`, `unban()`, `suspensionHistory()`

**Services**:
- `Modules/Moderator/UserSanction/Services/UserSanctionService.php`
  - Methods: `banUser()`, `unbanUser()`
  
- `Modules/Moderator/UserSanction/Services/ModerationLogService.php`
  - Methods: `logAction()` (auto-logged via events)

**Repositories**:
- `Modules/Moderator/UserSanction/Repositories/ModerationLogRepository.php`

**Requests**:
- `Modules/Moderator/UserSanction/Requests/BanUserRequest.php`
  - Rules: reason required, duration_days integer|nullable

**Database Tables**:
- `users` - is_banned flag
- `user_suspensions` - ban history
- `moderation_logs` - audit trail

**Events** (auto-logging):
- `UserBanned` → logs to moderation_logs
- `UserUnbanned` → logs to moderation_logs
- `PostDeleted` → logs to moderation_logs

**Routes**:
- `PUT /api/moderator/users/{id}/ban` → UserSanctionController@ban
- `PUT /api/moderator/users/{id}/unban` → UserSanctionController@unban
- `GET /api/moderator/logs` → ModerationLogController@index

---

## 📝 POST FEATURES

### Fitur 15: Buat Postingan Baru

**Controllers**:
- `Modules/User/Post/Controllers/PostController.php`
  - Method: `store(StorePostRequest $request)`

**Services**:
- `Modules/User/Post/Services/PostService.php`
  - Method: `create(array $data)`
  - Logic: Validate, create post, attach tags, fire event

**Repositories**:
- `Modules/User/Post/Repositories/PostRepository.php`
  - Method: `create(array $data)`

**Requests**:
- `Modules/User/Post/Requests/StorePostRequest.php`
  - Rules: title min:10|max:255, content min:30, category_id exists, tags array|min:1

**Database Tables**:
- `posts` - insert
- `post_tags` - attach tags
- `post_categories` (optional)

**Routes**:
- `POST /api/posts` → PostController@store

**Events Fired**:
- `PostCreated` → update tag usage_count, notification

**API Request Example**:
```json
{
  "title": "How to use Laravel?",
  "content": "I'm new to Laravel...",
  "category_id": 1,
  "tags": [1, 2, 3]
}
```

---

### Fitur 16: Edit Postingan

**Controllers**:
- `Modules/User/Post/Controllers/PostController.php`
  - Method: `update(UpdatePostRequest $request, int $id)`

**Services**:
- `Modules/User/Post/Services/PostService.php`
  - Method: `update(int $id, array $data)`
  - Logic: Validate, store old version to history, update post

**Requests**:
- `Modules/User/Post/Requests/UpdatePostRequest.php`
  - Authorization: Post owner only
  - Rules: Same as store

**Database Tables**:
- `posts` - update
- `post_edit_history` - create history entry
- `post_tags` - sync tags

**Routes**:
- `PUT /api/posts/{id}` → PostController@update

**Events Fired**:
- `PostUpdated` → create edit history

---

### Fitur 17: Hapus Postingan (Soft Delete)

**Controllers**:
- `Modules/User/Post/Controllers/PostController.php`
  - Method: `destroy(int $id)`

**Services**:
- `Modules/User/Post/Services/PostService.php`
  - Method: `delete(int $id)`
  - Logic: Soft delete (set deleted_at)

**Database Tables**:
- `posts` - UPDATE deleted_at = now()

**Routes**:
- `DELETE /api/posts/{id}` → PostController@destroy

**Note**: Admin dapat restore via soft delete retrieval

---

### Fitur 18: Mark as Accepted Answer

**Controllers**:
- `Modules/User/Post/Controllers/PostController.php`
  - Method: `acceptAnswer(int $postId, int $commentId)`

**Services**:
- `Modules/User/Post/Services/PostService.php`
  - Method: `acceptAnswer(int $postId, int $commentId)`
  - Logic: Update posts.accepted_answer_id, award reputation points

**Database Tables**:
- `posts` - UPDATE accepted_answer_id
- `users` - UPDATE reputation_points (for answerer)
- `points_log` - create log entry

**Routes**:
- `PUT /api/posts/{id}/accept-answer/{commentId}` → PostController@acceptAnswer

**Business Logic**:
- Only OP (post creator) can accept answer
- Max 1 accepted answer per post
- Answerer gains reputation

---

### Fitur 19: Post Edit History

**Services**:
- `Modules/User/Post/Services/PostHistoryService.php`
  - Method: `getHistory(int $postId)`
  - Method: `getVersion(int $postId, int $version)`

**Repositories**:
- `Modules/User/Post/Repositories/PostEditHistoryRepository.php`

**Database Tables**:
- `post_edit_history` - auto-populated on update

**Routes**:
- `GET /api/posts/{id}/history` → PostController@getHistory
- `GET /api/posts/{id}/history/{version}` → PostController@getVersion

**API Response Example**:
```json
{
  "data": [
    {
      "id": 1,
      "title": "Original title",
      "content": "Original content...",
      "user": { "id": 1, "name": "John" },
      "reason": "Fixed typo",
      "created_at": "2026-06-03T10:00:00Z"
    }
  ]
}
```

---

## 💬 COMMENT FEATURES

### Fitur 20: Buat Komentar / Jawaban Utama

**Controllers**:
- `Modules/User/Comment/Controllers/CommentController.php`
  - Method: `store(StoreCommentRequest $request, int $postId)`

**Services**:
- `Modules/User/Comment/Services/CommentService.php`
  - Method: `create(int $postId, array $data)`

**Repositories**:
- `Modules/User/Comment/Repositories/CommentRepository.php`

**Requests**:
- `Modules/User/Comment/Requests/StoreCommentRequest.php`
  - Rules: content min:5, post_id exists

**Database Tables**:
- `comments` - insert (parent_id = NULL for top-level)

**Routes**:
- `POST /api/posts/{postId}/comments` → CommentController@store

**Events Fired**:
- `CommentCreated` → notify post author, update post comment_count

---

### Fitur 21: Nested Reply (Balasan di dalam Komentar)

**Controllers**:
- `Modules/User/Comment/Controllers/CommentController.php`
  - Method: `reply(StoreCommentRequest $request, int $commentId)`

**Services**:
- `Modules/User/Comment/Services/CommentService.php`
  - Method: `createReply(int $parentCommentId, array $data)`

**Database Tables**:
- `comments` - insert dengan parent_id = parentCommentId

**Routes**:
- `POST /api/comments/{commentId}/reply` → CommentController@reply

**Database Query** (get threaded comments):
```sql
SELECT * FROM comments 
WHERE post_id = ? 
ORDER BY CASE 
  WHEN parent_id IS NULL THEN id 
  ELSE parent_id 
END, created_at
```

---

### Fitur 22: Edit & Hapus Komentar

**Controllers**:
- `Modules/User/Comment/Controllers/CommentController.php`
  - Methods: `update()`, `destroy()`

**Services**:
- `Modules/User/Comment/Services/CommentService.php`
  - Methods: `update()`, `delete()`

**Database Tables**:
- `comments` - UPDATE or soft delete (deleted_at)
- `comment_edit_history` - create history on update

**Routes**:
- `PUT /api/comments/{id}` → CommentController@update
- `DELETE /api/comments/{id}` → CommentController@destroy

---

### Fitur 23: Comment Edit History

**Services**:
- `Modules/User/Comment/Services/CommentHistoryService.php`

**Repositories**:
- `Modules/User/Comment/Repositories/CommentEditHistoryRepository.php`

**Database Tables**:
- `comment_edit_history`

**Routes**:
- `GET /api/comments/{id}/history` → CommentController@getHistory

---

## ⭐ INTERACTION FEATURES

### Fitur 24: Upvote / Downvote Post & Comment

**Controllers**:
- `Modules/User/Interaction/Controllers/VoteController.php`
  - Method: `vote(Request $request, int $id)`
  - Params: `voteable_type=post|comment`, `vote_type=upvote|downvote|remove`

**Services**:
- `Modules/User/Interaction/Services/VoteService.php`
  - Method: `vote(int $userId, int $voteableId, string $voteableType, string $voteType)`

**Repositories**:
- `Modules/User/Interaction/Repositories/VoteRepository.php`

**Database Tables**:
- `votes` - create/update
- `posts` or `comments` - UPDATE vote_score

**Routes**:
- `POST /api/posts/{id}/votes` → VoteController@vote
- `POST /api/comments/{id}/votes` → VoteController@vote

**Business Logic**:
- 1 vote per user per content
- vote_score = COUNT(upvote) - COUNT(downvote)
- Upvote recipient gains +10 rep, downvote -2 rep

---

### Fitur 25: Like Post & Comment

**Controllers**:
- `Modules/User/Interaction/Controllers/LikeController.php`
  - Method: `toggle(int $id)`

**Services**:
- `Modules/User/Interaction/Services/LikeService.php`
  - Method: `toggle(int $userId, int $likeableId, string $likeableType)`

**Repositories**:
- `Modules/User/Interaction/Repositories/LikeRepository.php`

**Database Tables**:
- `likes` - create/delete (toggle behavior)

**Routes**:
- `POST /api/posts/{id}/likes` → LikeController@toggle
- `POST /api/comments/{id}/likes` → LikeController@toggle

---

### Fitur 26: Bookmark / Save Post

**Controllers**:
- `Modules/User/Interaction/Controllers/BookmarkController.php`
  - Methods: `store()`, `destroy()`, `index()`

**Services**:
- `Modules/User/Interaction/Services/BookmarkService.php`
  - Methods: `bookmark()`, `unbookmark()`, `getBookmarks()`

**Repositories**:
- `Modules/User/Interaction/Repositories/BookmarkRepository.php`

**Database Tables**:
- `bookmarks` - post saved by user

**Routes**:
- `POST /api/posts/{id}/bookmark` → BookmarkController@store
- `DELETE /api/bookmarks/{id}` → BookmarkController@destroy
- `GET /api/bookmarks` → BookmarkController@index (list user bookmarks)

---

### Fitur 27: Follow / Unfollow User

**Controllers**:
- `Modules/User/Interaction/Controllers/FollowController.php`
  - Methods: `follow()`, `unfollow()`

**Services**:
- `Modules/User/Interaction/Services/FollowService.php`
  - Methods: `follow()`, `unfollow()`, `getFollowers()`, `getFollowing()`

**Repositories**:
- `Modules/User/Interaction/Repositories/FollowRepository.php`

**Database Tables**:
- `follows` - follower_id & following_id

**Routes**:
- `POST /api/users/{id}/follow` → FollowController@follow
- `POST /api/users/{id}/unfollow` → FollowController@unfollow
- `GET /api/users/{id}/followers` → FollowController@followers
- `GET /api/users/{id}/following` → FollowController@following

---

## 🔔 NOTIFICATION FEATURES

### Fitur 28: Sistem Notifikasi Real-time + Mark as Read

**Controllers**:
- `Modules/User/Notification/Controllers/NotificationController.php`
  - Methods: `index()`, `markAsRead()`, `markAllAsRead()`, `delete()`

**Services**:
- `Modules/User/Notification/Services/NotificationService.php`
  - Methods: `notify()`, `markAsRead()`, `getUnreadCount()`

**Repositories**:
- `Modules/User/Notification/Repositories/NotificationRepository.php`

**Events** (auto-trigger notifications):
- `CommentCreated` → notify post author
- `VoteCreated` → notify votee
- `UserBadgeEarned` → notify user
- `UserMentioned` → notify mentioned user

**Database Tables**:
- `notifications` - store notification records

**Routes**:
- `GET /api/notifications` → NotificationController@index
- `GET /api/notifications/unread-count` → NotificationController@unreadCount
- `PUT /api/notifications/{id}/read` → NotificationController@markAsRead
- `PUT /api/notifications/read-all` → NotificationController@markAllAsRead
- `DELETE /api/notifications/{id}` → NotificationController@delete

**Real-time Options**:
1. **WebSocket** (Pusher/Ably): Broadcasting via Laravel Events
2. **Polling**: `GET /api/notifications?since_id=X`
3. **Server-Sent Events (SSE)**: `GET /api/notifications/stream`

**Event Example**:
```php
// In CommentService
event(new CommentCreated($comment));

// In CommentCreated Event
public function broadcastOn() {
    return new PrivateChannel('user.'.$this->comment->post->user_id);
}
```

---

## 🏆 GAMIFICATION FEATURES

### Fitur 29: Reputation Level & Leaderboard

**Controllers**:
- `Modules/User/Gamification/Controllers/GamificationController.php`
  - Methods: `leaderboard()`, `userReputation()`, `pointsLog()`

**Services**:
- `Modules/User/Gamification/Services/ReputationService.php`
  - Methods: `calculateReputation()`, `awardPoints()`, `getLevels()`

**Repositories**:
- `Modules/User/Gamification/Repositories/ReputationRepository.php`
  - Methods: `getLeaderboard()`, `getPointsLog()`

**Events** (auto-award points):
- `PostAccepted` → +15 points
- `VoteReceived` → +10 points (upvote), -2 points (downvote)
- `CommentLiked` → +5 points
- `BadgeEarned` → +variable points

**Database Tables**:
- `users` - reputation_points field
- `points_log` - audit trail of point changes

**Reputation Levels**:
- 0-99: Newbie
- 100-499: Member
- 500-999: Senior
- 1000-4999: Expert
- 5000+: Legend

**Routes**:
- `GET /api/leaderboard?period=week` → GamificationController@leaderboard
- `GET /api/leaderboard?period=month` → sorted by period
- `GET /api/users/{id}/reputation` → GamificationController@userReputation
- `GET /api/users/{id}/points-log` → GamificationController@pointsLog

**API Response Example**:
```json
{
  "data": [
    {
      "rank": 1,
      "user": { "id": 1, "name": "Top User", "avatar": "..." },
      "reputation_points": 5500,
      "level": "Legend",
      "badges_count": 15
    }
  ]
}
```

**Leaderboard Query** (example):
```sql
SELECT u.id, u.name, u.avatar_url, u.reputation_points,
  CASE 
    WHEN u.reputation_points < 100 THEN 'Newbie'
    WHEN u.reputation_points < 500 THEN 'Member'
    -- ...
  END as level,
  COUNT(ub.id) as badges_count
FROM users u
LEFT JOIN user_badges ub ON u.id = ub.user_id
WHERE u.created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)
GROUP BY u.id
ORDER BY u.reputation_points DESC
LIMIT 100
```

---

## 📊 Feature Statistics Summary

| Feature | Controllers | Services | Repositories | Requests | Tables | Routes |
|---------|-------------|----------|--------------|----------|--------|--------|
| Fitur 1-2 (Auth) | 1 | 1 | 0 | 2 | 2 | 3 |
| Fitur 3-6 (Explore) | 1 | 3 | 0 | 0 | 3 | 4 |
| Fitur 7-11 (Admin) | 3 | 5 | 3 | 5 | 8 | 10 |
| Fitur 12-14 (Moderator) | 2 | 2 | 1 | 1 | 3 | 6 |
| Fitur 15-19 (Post) | 1 | 2 | 2 | 2 | 3 | 5 |
| Fitur 20-23 (Comment) | 1 | 2 | 2 | 2 | 2 | 5 |
| Fitur 24-27 (Interaction) | 4 | 4 | 4 | 0 | 4 | 10 |
| Fitur 28 (Notification) | 1 | 1 | 1 | 0 | 1 | 6 |
| Fitur 29 (Gamification) | 1 | 1 | 1 | 0 | 2 | 3 |
| **TOTAL** | **15** | **21** | **14** | **12** | **28** | **52** |

---

## 🎯 Development Checklist

**Untuk implement setiap fitur, pastikan:**

- [ ] Controller dibuat dengan proper HTTP methods
- [ ] Service dibuat dengan business logic
- [ ] Request class dibuat dengan validation rules
- [ ] Repository dibuat (jika data-heavy)
- [ ] Database migration dibuat
- [ ] Routes didaftarkan di routes file
- [ ] Tests written (unit & feature)
- [ ] API documentation updated
- [ ] Error handling implemented
- [ ] Authorization checks implemented
- [ ] Events/Listeners setup (jika diperlukan)

---

**Next: Baca [05_ARCHITECTURE_PATTERNS.md](05_ARCHITECTURE_PATTERNS.md) untuk design patterns →**
