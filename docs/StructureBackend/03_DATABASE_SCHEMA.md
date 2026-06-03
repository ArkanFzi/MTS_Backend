# 03. Database Schema - Entity Relationships & Tables

## 🗄️ Database Schema Overview

Dokumentasi lengkap tentang struktur database, tabel, relasi, dan field descriptions.

---

## 📊 Entity Relationship Diagram (ERD) - Text Format

```
                    ┌──────────────────┐
                    │     users        │ (UUID PKs)
                    ├──────────────────┤
                    │ id (PK, uuid)    │
                    │ username (UNIQUE)│
                    │ email (UNIQUE)   │
                    │ password_hash    │
                    │ avatar_url       │
                    │ bio              │
                    │ reputation_..    │
                    │ level            │
                    │ is_banned        │
                    │ created_at       │
                    └────────┬─────────┘
                             │
        ┌────────────────────┼────────────────────┐
        │                    │                    │
        │                    │                    │
        ▼                    ▼                    ▼
   ┌──────────┐         ┌──────────┐        ┌──────────────┐
   │  posts   │         │ comments │        │ votes        │
   ├──────────┤         ├──────────┤        ├──────────────┤
   │ id (PK)  │◄────────│ id (PK)  │        │ id (PK)      │
   │ user_id  │         │ user_id  │        │ user_id (FK) │
   │ title    │         │ post_id  │        │ target_id    │
   │ body     │         │ parent.. │        │ target_type  │
   │ status   │         │ body     │        │ vote_type    │
   │ vote_    │         │ vote_    │        │ created_at   │
   │  score   │         │  score   │        └──────────────┘
   │ view_    │         │ is_..    │
   │ count    │         │accepted  │
   │ categ..  │         │ created_ │
   │ created_ │         │   at     │
   │   at     │         └──────────┘
   └────┬────┘              │
        │                   │
        │                   ▼
        │            ┌────────────────┐
        │            │ likes          │
        │            ├────────────────┤
        │            │ id (PK)        │
        │            │ user_id (FK)   │
        │            │ target_id      │
        │            │ target_type    │
        │            │ created_at     │
        │            └────────────────┘
        │
        ├─────────────────────────────┐
        │                             │
        ▼                             ▼
   ┌─────────────┐             ┌──────────────┐
   │ categories  │             │ post_tags    │
   ├─────────────┤             ├──────────────┤
   │ id (PK)     │             │ id (PK)      │
   │ name        │             │ post_id (FK) │
   │ parent_id   │             │ tag_id (FK)  │
   │ slug        │             └──────┬───────┘
   │ description │                    │
   │ created_at  │                    │
   └─────────────┘                    │
                                      │
                              ┌───────▼────────┐
                              │ tags           │
                              ├────────────────┤
                              │ id (PK)        │
                              │ name (UNIQUE)  │
                              │ slug (UNIQUE)  │
                              │ color          │
                              │ usage_count    │
                              │ created_at     │
                              └────────────────┘

   ┌─────────────────┐      ┌───────────────────┐
   │ bookmarks       │      │ follows           │
   ├─────────────────┤      ├───────────────────┤
   │ id (PK)         │      │ id (PK)           │
   │ user_id (FK)    │      │ follower_id (FK)  │
   │ post_id (FK)    │      │ following_id (FK) │
   │ created_at      │      │ created_at        │
   └─────────────────┘      └───────────────────┘

   ┌────────────────────────────┐
   │ notifications              │
   ├────────────────────────────┤
   │ id (PK)                    │
   │ user_id (FK)               │
   │ actor_id (FK)              │
   │ type                       │
   │ reference_id               │
   │ reference_type             │
   │ is_read                    │
   │ created_at                 │
   └────────────────────────────┘

   ┌──────────────────────┐     ┌──────────────────┐
   │ points_log           │     │ badges           │
   ├──────────────────────┤     ├──────────────────┤
   │ id (PK)              │     │ id (PK)          │
   │ user_id (FK)         │     │ name (UNIQUE)    │
   │ points               │     │ description      │
   │ action_type          │     │ icon_url         │
   │ reference_id         │     │ tier             │
   │ description          │     │ condition_type   │
   │ created_at           │     │ condition_value  │
   └──────────────────────┘     │ created_at       │
                                └──────────────────┘
                                       │
                                       │
                                  ┌────▼─────────┐
                                  │ user_badges  │
                                  ├──────────────┤
                                  │ id (PK)      │
                                  │ user_id (FK) │
                                  │ badge_id (FK)│
                                  │ earned_at    │
                                  └──────────────┘

   ┌──────────────────┐
   │ reports          │
   ├──────────────────┤
   │ id (PK)          │
   │ reporter_id (FK) │
   │ target_id        │
   │ target_type      │
   │ reason           │
   │ description      │
   │ status           │
   │ resolved_by (FK) │
   │ created_at       │
   │ resolved_at      │
   └──────────────────┘

   ┌──────────────────────────┐
   │ moderation_logs          │
   ├──────────────────────────┤
   │ id (PK)                  │
   │ moderator_id (FK)        │
   │ target_user_id (FK)      │
   │ action_type              │
   │ reason                   │
   │ notes                    │
   │ created_at               │
   └──────────────────────────┘

   ┌──────────────────────┐
   │ edit_history tables  │
   ├──────────────────────┤
   │ post_edit_history    │
   │ comment_edit_history │
   └──────────────────────┘

   ┌──────────────────────┐
   │ roles & auth         │
   ├──────────────────────┤
   │ roles (name UNIQUE)  │
   │ user_roles           │
   └──────────────────────┘
```

---

## 📋 Tabel Lengkap & Deskripsi

### 1. **users** - User Accounts
**Purpose**: Menyimpan data profil user forum

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | uuid (PK) | NO | gen_random_uuid() | User ID unik (UUID) |
| username | string(100) | NO | - | Username unik untuk login |
| email | string(255) | NO | - | Email unik untuk login |
| password_hash | string(255) | NO | - | Hashed password bcrypt |
| avatar_url | string(500) | YES | NULL | URL avatar profile |
| bio | text | YES | NULL | Bio singkat user |
| reputation_points | integer | NO | 0 | Total reputation (untuk leaderboard & gamification) |
| level | integer | NO | 1 | Level user (1-10) based on reputation |
| is_banned | boolean | NO | false | Flag apakah user di-ban |
| created_at | timestamp | NO | now() | Waktu registrasi |
| updated_at | timestamp | NO | now() | Waktu last update |

**Indexes**:
- PRIMARY KEY: `id`
- UNIQUE: `email` (users_email_unique)
- UNIQUE: `username` (users_username_unique)

**Relationships**:
- Has Many: posts, comments, votes, likes, bookmarks, notifications, points_log
- Belongs To Many: roles (via user_roles), badges (via user_badges), follows
- Follows: follower_id, following_id (self-joining)

---

### 2. **roles** - User Roles
**Purpose**: Master data role/permission roles

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | uuid (PK) | NO | gen_random_uuid() | Role ID |
| name | string(50) | NO | - | Nama role (admin, moderator, user) |
| permissions | json | YES | NULL | JSON array of permissions |
| created_at | timestamp | NO | now() | Waktu dibuat |

**Indexes**:
- PRIMARY KEY: `id`
- UNIQUE: `name` (roles_name_unique)

**Predefined Roles**:
- admin
- moderator
- user

---

### 3. **user_roles** - User-Role Assignment
**Purpose**: Many-to-many antara users dan roles

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | uuid (PK) | NO | gen_random_uuid() | Junction ID |
| user_id | uuid (FK) | NO | - | ID user |
| role_id | uuid (FK) | NO | - | ID role |
| assigned_at | timestamp | NO | now() | Waktu diassign |

**Indexes**:
- PRIMARY KEY: `id`
- FOREIGN KEY: `user_id` → users.id
- FOREIGN KEY: `role_id` → roles.id
- UNIQUE: (user_id, role_id) (user_roles_unique) - prevent duplicate role per user

**Relationships**:
- Belongs To: user, role

---

### 4. **categories** - Forum Categories
**Purpose**: Menyimpan kategori forum (hierarchical/nested)

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | uuid (PK) | NO | gen_random_uuid() | Category ID |
| name | string(100) | NO | - | Nama kategori |
| slug | string(120) | NO | - | URL-friendly slug |
| description | text | YES | NULL | Deskripsi kategori |
| parent_id | uuid (FK) | YES | NULL | ID kategori parent (nested) - null = root category |
| created_at | timestamp | NO | now() | Waktu dibuat |

**Indexes**:
- PRIMARY KEY: `id`
- UNIQUE: `slug` (categories_slug_unique)
- FOREIGN KEY: `parent_id` → categories.id (self-referencing)

**Relationships**:
- Belongs To: parent (self)
- Has Many: posts, children

---

### 5. **tags** - Forum Tags
**Purpose**: Menyimpan master tag untuk kategori konten

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | uuid (PK) | NO | gen_random_uuid() | Tag ID |
| name | string(50) | NO | - | Nama tag |
| slug | string(60) | NO | - | URL-friendly slug |
| color | varchar(7) | YES | NULL | Hex color (#XXXXXX) untuk UI |
| usage_count | integer | NO | 0 | Cached count penggunaan |
| created_at | timestamp | NO | now() | Waktu dibuat |

**Indexes**:
- PRIMARY KEY: `id`
- UNIQUE: `slug` (tags_slug_unique)
- KEY: `usage_count` (untuk popular tags)

**Relationships**:
- Belongs To Many: posts (via post_tags)

---

### 6. **posts** - Forum Posts/Questions
**Purpose**: Menyimpan postingan utama di forum

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | uuid (PK) | NO | gen_random_uuid() | Post ID unik (UUID) |
| user_id | uuid (FK) | NO | - | ID user pembuat |
| category_id | uuid (FK) | NO | - | ID kategori post |
| title | string(300) | NO | - | Judul postingan |
| body | text | NO | - | Isi/body postingan (Markdown/HTML) |
| status | varchar(20) | NO | 'open' | Status post: 'open', 'closed', 'deleted' |
| view_count | integer | NO | 0 | Berapa kali post dibuka |
| vote_score | integer | NO | 0 | Skor voting (upvotes - downvotes) |
| is_answered | boolean | NO | false | Apakah sudah ada accepted answer |
| accepted_answer_id | uuid (FK) | YES | NULL | Comment ID yang marked as accepted answer |
| created_at | timestamp | NO | now() | Waktu dibuat |
| updated_at | timestamp | NO | now() | Waktu last update |

**Indexes**:
- PRIMARY KEY: `id`
- FOREIGN KEY: `user_id` → users.id (posts_user_id_idx)
- FOREIGN KEY: `category_id` → categories.id (posts_category_id_idx)
- KEY: `status` (posts_status_idx - untuk filter by status)
- KEY: `created_at` (posts_created_at_idx - untuk sorting recent)
- KEY: `vote_score` (untuk trending - implicit)
- KEY: `view_count` (untuk popular - implicit)

**Relationships**:
- Belongs To: user, category
- Has Many: comments, votes, likes, bookmarks, post_tags, post_edit_history

---

### 7. **post_tags** - Post-Tag Junction Table
**Purpose**: Many-to-many relationship antara posts dan tags

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | uuid (PK) | NO | gen_random_uuid() | Junction ID |
| post_id | uuid (FK) | NO | - | ID post |
| tag_id | uuid (FK) | NO | - | ID tag |

**Indexes**:
- PRIMARY KEY: `id`
- FOREIGN KEY: `post_id` → posts.id
- FOREIGN KEY: `tag_id` → tags.id
- UNIQUE: (post_id, tag_id) (post_tags_unique) - prevent duplicate tag per post

---

### 8. **post_edit_history** - Post Edit History
**Purpose**: Audit trail untuk perubahan post

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | uuid (PK) | NO | gen_random_uuid() | History ID |
| post_id | uuid (FK) | NO | - | ID post yang diedit |
| edited_by | uuid (FK) | NO | - | ID user yang edit |
| body_before | text | NO | - | Content sebelum edit |
| body_after | text | NO | - | Content sesudah edit |
| reason | varchar(255) | YES | NULL | Alasan edit |
| edited_at | timestamp | NO | now() | Waktu edit |

**Indexes**:
- PRIMARY KEY: `id`
- FOREIGN KEY: `post_id` → posts.id
- FOREIGN KEY: `edited_by` → users.id
- KEY: `post_id` (post_edit_history_post_id_idx)

**Relationships**:
- Belongs To: post, editor (user)

---

### 9. **comments** - Forum Comments/Answers/Replies
**Purpose**: Menyimpan komentar, jawaban, dan reply bertingkat

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | uuid (PK) | NO | gen_random_uuid() | Comment ID unik |
| post_id | uuid (FK) | NO | - | ID post yang dikomentari |
| user_id | uuid (FK) | NO | - | ID user pembuat |
| parent_id | uuid (FK) | YES | NULL | ID parent comment (untuk nested reply). NULL = top-level comment |
| body | text | NO | - | Isi komentar |
| vote_score | integer | NO | 0 | Skor voting komentar |
| is_accepted | boolean | NO | false | Apakah comment ini accepted answer |
| created_at | timestamp | NO | now() | Waktu dibuat |
| updated_at | timestamp | NO | now() | Waktu last update |

**Indexes**:
- PRIMARY KEY: `id`
- FOREIGN KEY: `post_id` → posts.id (comments_post_id_idx)
- FOREIGN KEY: `user_id` → users.id (comments_user_id_idx)
- FOREIGN KEY: `parent_id` → comments.id (comments_parent_id_idx - self-referencing)

**Relationships**:
- Belongs To: user, post, parent (self)
- Has Many: replies (children), votes, likes, comment_edit_history

---

### 10. **comment_edit_history** - Comment Edit History
**Purpose**: Audit trail untuk perubahan comment

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | uuid (PK) | NO | gen_random_uuid() | History ID |
| comment_id | uuid (FK) | NO | - | ID comment yang diedit |
| edited_by | uuid (FK) | NO | - | ID user yang edit |
| body_before | text | NO | - | Content sebelum edit |
| body_after | text | NO | - | Content sesudah edit |
| edited_at | timestamp | NO | now() | Waktu edit |

**Indexes**:
- PRIMARY KEY: `id`
- FOREIGN KEY: `comment_id` → comments.id
- FOREIGN KEY: `edited_by` → users.id
- KEY: `comment_id` (comment_edit_history_comment_id_idx)

**Relationships**:
- Belongs To: comment, editor (user)

---

### 11. **votes** - Upvote/Downvote System
**Purpose**: Track voting pada posts dan comments dengan polymorphic design

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | uuid (PK) | NO | gen_random_uuid() | Vote ID |
| user_id | uuid (FK) | NO | - | ID user yang voting |
| target_id | uuid | NO | - | ID target (post_id atau comment_id) |
| target_type | varchar(20) | NO | - | Tipe target: 'post' atau 'comment' |
| vote_type | varchar(10) | NO | - | Tipe voting: 'upvote' atau 'downvote' |
| created_at | timestamp | NO | now() | Waktu voting |

**Indexes**:
- PRIMARY KEY: `id`
- FOREIGN KEY: `user_id` → users.id
- UNIQUE: (user_id, target_id, target_type) (votes_unique) - prevent duplicate vote
- KEY: (target_id, target_type) (votes_target_idx - untuk query votes per target)

**Business Rules**:
- Setiap user hanya boleh vote 1x per post/comment
- Dapat change dari upvote → downvote atau sebaliknya
- vote_score diperhitungkan otomatis = COUNT(upvote) - COUNT(downvote)

**Relationships**:
- Belongs To: user

---

### 12. **likes** - Like System
**Purpose**: Track "likes" pada posts dan comments (separate dari votes)

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | uuid (PK) | NO | gen_random_uuid() | Like ID |
| user_id | uuid (FK) | NO | - | ID user yang like |
| target_id | uuid | NO | - | ID target (post_id atau comment_id) |
| target_type | varchar(20) | NO | - | Tipe target: 'post' atau 'comment' |
| created_at | timestamp | NO | now() | Waktu like |

**Indexes**:
- PRIMARY KEY: `id`
- FOREIGN KEY: `user_id` → users.id
- UNIQUE: (user_id, target_id, target_type) (likes_unique) - toggle like
- KEY: (target_id, target_type) (likes_target_idx)

**Relationships**:
- Belongs To: user

---

### 13. **bookmarks** - Saved Posts
**Purpose**: Track postingan yang di-save oleh user

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | uuid (PK) | NO | gen_random_uuid() | Bookmark ID |
| user_id | uuid (FK) | NO | - | ID user |
| post_id | uuid (FK) | NO | - | ID post yang di-save |
| created_at | timestamp | NO | now() | Waktu di-save |

**Indexes**:
- PRIMARY KEY: `id`
- FOREIGN KEY: `user_id` → users.id
- FOREIGN KEY: `post_id` → posts.id
- UNIQUE: (user_id, post_id) (bookmarks_unique) - prevent duplicate bookmark

**Relationships**:
- Belongs To: user, post

---

### 14. **follows** - User Following System
**Purpose**: Track siapa follow siapa

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | uuid (PK) | NO | gen_random_uuid() | Follow ID |
| follower_id | uuid (FK) | NO | - | ID user yang follow (follower) |
| following_id | uuid (FK) | NO | - | ID user yang di-follow |
| created_at | timestamp | NO | now() | Waktu follow |

**Indexes**:
- PRIMARY KEY: `id`
- FOREIGN KEY: `follower_id` → users.id
- FOREIGN KEY: `following_id` → users.id
- UNIQUE: (follower_id, following_id) (follows_unique) - prevent duplicate follow
- KEY: `follower_id` (follows_follower_idx)
- KEY: `following_id` (follows_following_idx)

**Relationships**:
- Belongs To Many: users (both sides - self-referencing)

---

### 15. **points_log** - Reputation Points Log
**Purpose**: Audit trail perubahan reputation points via gamification

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | uuid (PK) | NO | gen_random_uuid() | Log ID |
| user_id | uuid (FK) | NO | - | ID user |
| points | integer | NO | - | Jumlah points (+ atau -). Positive = earn, negative = deduct |
| action_type | varchar(50) | NO | - | Jenis aksi: 'post_upvoted', 'answer_accepted', 'comment_upvoted', 'post_created', 'daily_login', dll |
| reference_id | uuid | YES | NULL | ID related post atau comment |
| description | varchar(255) | YES | NULL | Deskripsi untuk log |
| created_at | timestamp | NO | now() | Waktu earning |

**Indexes**:
- PRIMARY KEY: `id`
- FOREIGN KEY: `user_id` → users.id
- KEY: `user_id` (points_log_user_id_idx)
- KEY: `action_type` (points_log_action_type_idx)

**Relationships**:
- Belongs To: user

---

### 16. **badges** - Achievement Badge Master
**Purpose**: Master data jenis-jenis badge/achievement

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | uuid (PK) | NO | gen_random_uuid() | Badge ID |
| name | string(100) | NO | - | Nama badge |
| description | text | YES | NULL | Deskripsi badge & kriteria |
| icon_url | string(500) | YES | NULL | URL icon/image |
| tier | varchar(20) | NO | - | Badge tier: 'bronze', 'silver', 'gold', 'platinum' |
| condition_type | varchar(50) | NO | - | Tipe kondisi: 'reputation_points', 'posts_count', 'answers_accepted', dll |
| condition_value | integer | NO | - | Nilai threshold untuk unlock badge |
| created_at | timestamp | NO | now() | Waktu dibuat |

**Indexes**:
- PRIMARY KEY: `id`
- UNIQUE: `name` (badges_name_unique)

**Relationships**:
- Belongs To Many: users (via user_badges)

---

### 17. **user_badges** - User Badge Ownership
**Purpose**: Track badge yang dimiliki user

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | uuid (PK) | NO | gen_random_uuid() | Junction ID |
| user_id | uuid (FK) | NO | - | ID user |
| badge_id | uuid (FK) | NO | - | ID badge |
| earned_at | timestamp | NO | now() | Waktu badge diperoleh |

**Indexes**:
- PRIMARY KEY: `id`
- FOREIGN KEY: `user_id` → users.id
- FOREIGN KEY: `badge_id` → badges.id
- UNIQUE: (user_id, badge_id) (user_badges_unique) - prevent duplicate badge per user

**Relationships**:
- Belongs To: user, badge

---

### 18. **notifications** - User Notifications
**Purpose**: Menyimpan notifikasi untuk user (real-time & in-app)

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | uuid (PK) | NO | gen_random_uuid() | Notification ID |
| user_id | uuid (FK) | NO | - | ID user penerima (recipient) |
| actor_id | uuid (FK) | YES | NULL | ID user yang trigger notif (who triggered) |
| type | varchar(50) | NO | - | Notification type: 'reply', 'like', 'upvote', 'follow', 'badge_earned', 'answer_accepted', 'mention', dll |
| reference_id | uuid | YES | NULL | ID related post atau comment |
| reference_type | varchar(20) | YES | NULL | Tipe reference: 'post', 'comment' |
| is_read | boolean | NO | false | Apakah notifikasi sudah dibaca |
| created_at | timestamp | NO | now() | Waktu dibuat |

**Indexes**:
- PRIMARY KEY: `id`
- FOREIGN KEY: `user_id` → users.id
- FOREIGN KEY: `actor_id` → users.id
- KEY: `user_id` (notifications_user_id_idx)
- KEY: (user_id, is_read) (notifications_user_unread_idx - untuk query unread)
- KEY: `created_at` (notifications_created_at_idx - untuk sorting)

**Relationships**:
- Belongs To: user (recipient), actor (user who triggered)

---

### 19. **reports** - Content Report Management
**Purpose**: Track laporan konten yang bermasalah

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | uuid (PK) | NO | gen_random_uuid() | Report ID |
| reporter_id | uuid (FK) | NO | - | ID user yang report |
| target_id | uuid | NO | - | ID target yang dilaporkan (post, comment, atau user) |
| target_type | varchar(20) | NO | - | Tipe target: 'post', 'comment', 'user' |
| reason | varchar(100) | NO | - | Alasan laporan: 'spam', 'harassment', 'misinformation', 'inappropriate', dll |
| description | text | YES | NULL | Deskripsi detail laporan |
| status | varchar(20) | NO | 'pending' | Status: 'pending', 'reviewed', 'resolved', 'dismissed' |
| resolved_by | uuid (FK) | YES | NULL | ID moderator yang resolve |
| created_at | timestamp | NO | now() | Waktu dibuat |
| resolved_at | timestamp | YES | NULL | Waktu resolved |

**Indexes**:
- PRIMARY KEY: `id`
- FOREIGN KEY: `reporter_id` → users.id
- FOREIGN KEY: `resolved_by` → users.id
- KEY: `reporter_id` (reports_reporter_id_idx)
- KEY: (target_id, target_type) (reports_target_idx)
- KEY: `status` (reports_status_idx - untuk filter queue)

**Relationships**:
- Belongs To: reporter (user), resolved_by (moderator)

---

### 20. **moderation_logs** - Moderator Action Audit Trail
**Purpose**: Track semua aksi moderasi untuk audit & transparency

| Field | Type | Nullable | Default | Description |
|-------|------|----------|---------|-------------|
| id | uuid (PK) | NO | gen_random_uuid() | Log ID |
| moderator_id | uuid (FK) | NO | - | ID moderator |
| target_user_id | uuid (FK) | NO | - | ID user yang terkena aksi |
| action_type | varchar(50) | NO | - | Jenis aksi: 'ban', 'unban', 'warning', 'delete_post', 'delete_comment', 'close_post', dll |
| reason | varchar(255) | NO | - | Alasan moderasi |
| notes | text | YES | NULL | Catatan tambahan dari moderator |
| created_at | timestamp | NO | now() | Waktu aksi |

**Indexes**:
- PRIMARY KEY: `id`
- FOREIGN KEY: `moderator_id` → users.id
- FOREIGN KEY: `target_user_id` → users.id
- KEY: `moderator_id` (moderation_logs_moderator_id_idx)
- KEY: `target_user_id` (moderation_logs_target_user_id_idx)

**Relationships**:
- Belongs To: moderator (user), target_user (user)

---

## 🔗 Relationship Summary

### One-to-Many
- User → Posts (1 user buat banyak post)
- User → Comments (1 user buat banyak comment)
- User → Votes (1 user buat banyak votes)
- User → Likes (1 user buat banyak likes)
- User → Bookmarks (1 user buat banyak bookmarks)
- User → PointsLogs (1 user punya banyak log points)
- User → Notifications (1 user terima banyak notifikasi)
- Post → Comments (1 post punya banyak comment)
- Post → PostEditHistory (1 post punya banyak history)
- Post → Votes (1 post terima banyak votes)
- Post → Likes (1 post terima banyak likes)
- Post → Bookmarks (1 post di-save oleh banyak user)
- Comment → Replies (1 comment punya banyak nested reply)
- Comment → CommentEditHistory (1 comment punya banyak history)
- Category → Posts (1 category punya banyak post)
- Category → Categories (1 category bisa punya many subcategories)

### Many-to-Many
- Users ↔ Users Follow (via follows - self-referencing)
- Users ↔ Badges (via user_badges)
- Users ↔ Roles (via user_roles)
- Posts ↔ Tags (via post_tags)

### Polymorphic Relationships (Conceptual)
- Votes → target (post atau comment)
- Likes → target (post atau comment)
- Notifications → reference (post atau comment)
- Reports → target (post, comment, atau user)

---

## 🗄️ Migration Creation Order

**Respecting FK constraints, migrations should be created in this order:**

1. `users` - Base table
2. `roles` - Role master
3. `user_roles` - User-role relationship
4. `categories` - Category master (dengan self-FK untuk parent_id)
5. `tags` - Tag master
6. `posts` - Main content table
7. `post_tags` - Post-tag relationship
8. `post_edit_history` - Post edit tracking
9. `comments` - Comments table (dengan self-FK untuk parent_id)
10. `comment_edit_history` - Comment edit tracking
11. `votes` - Voting system
12. `likes` - Like system
13. `bookmarks` - Bookmark system
14. `follows` - Follow system (self-referencing)
15. `points_log` - Gamification points
16. `badges` - Badge master
17. `user_badges` - User-badge relationship
18. `notifications` - Notification system
19. `reports` - Report system
20. `moderation_logs` - Moderation audit trail

---

## 📊 Caching & Performance Strategies

### Recommended Fields untuk Di-Cache
- **users**: reputation_points, level (updated from points_log)
- **posts**: vote_score, view_count (aggregated dari votes)
- **comments**: vote_score (aggregated dari votes)
- **tags**: usage_count (aggregated dari post_tags)

### Cache Invalidation Triggers
- Vote created/updated/deleted → recalculate post/comment vote_score
- Comment created on post → increment post view stats
- Tag added to post → increment tag usage_count
- User badge earned → update user reputation & level
- Post status changed → update category statistics

### Materialized Views (Optional)
For performance at scale, consider materialized views for:
- Leaderboard (top users by reputation)
- Trending posts (calculated from votes + views + comments)
- Popular tags (top tags by usage_count)

### Index Strategy
**High-priority indexes** (already defined in schema):
- (user_id, created_at) on posts & comments
- (post_id, is_read) on notifications
- (target_id, target_type) on votes & likes
- (user_id, is_read) on notifications

---

**Next: Baca [04_FEATURE_MAPPING.md](04_FEATURE_MAPPING.md) untuk mapping fitur ke kode →**
