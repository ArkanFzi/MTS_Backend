# 03. Database Schema - Entity Relationships & Tables

**Versi:** 2.0 (Terverifikasi dari 27 Migration Files)
**Tanggal Update:** 10 Juni 2026

Dokumentasi lengkap tentang struktur database, tabel, relasi, dan field descriptions.

---

## 1. Gambaran Umum

- **Database:** PostgreSQL
- **Primary Key:** UUID (semua tabel menggunakan `HasUuids` trait)
- **UUID Extension:** `uuid-ossp` (diaktifkan di migration pertama)
- **Jumlah Tabel Aplikasi:** 20 tabel + 7 tabel infrastruktur Laravel
- **Soft Deletes:** `posts`, `comments`
- **Polymorphic Relations:** `votes`, `likes`, `reports` (target_id + target_type)
- **Self-Referencing:** `categories` (parent_id), `comments` (parent_id)

---

## 2. Daftar Tabel Aplikasi (20 Tabel)

### 2.1 users
Tabel utama pengguna aplikasi.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| `id` | uuid | PK, default `uuid_generate_v4()` | Primary key |
| `username` | string(100) | UNIQUE, NOT NULL | Nama pengguna |
| `email` | string(255) | UNIQUE, NOT NULL | Alamat email |
| `password_hash` | string(255) | NOT NULL | Hash password (bcrypt) |
| `avatar_url` | string(500) | NULLABLE | URL avatar |
| `bio` | text | NULLABLE | Deskripsi profil |
| `reputation_points` | integer | DEFAULT 0 | Poin reputasi |
| `level` | integer | DEFAULT 1 | Level user |
| `is_banned` | boolean | DEFAULT false | Status banned |
| `created_at` | timestamp | | Waktu registrasi |
| `updated_at` | timestamp | | Waktu update terakhir |

**Relasi:**
- HasMany → `posts`, `comments`, `follows` (follower/following)
- BelongsToMany → `roles` (via `user_roles`)
- BelongsToMany → `badges` (via `user_badges`)

---

### 2.2 roles
Tabel role untuk RBAC (Role-Based Access Control).

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| `id` | uuid | PK, default `uuid_generate_v4()` | Primary key |
| `name` | string(50) | UNIQUE | Nama role (admin, moderator, member) |
| `permissions` | json | NULLABLE | Daftar permission (JSON) |
| `created_at` | timestamp | useCurrent | Waktu pembuatan |

---

### 2.3 user_roles (Junction)
Tabel penghubung many-to-many antara users dan roles.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| `id` | uuid | PK, default `uuid_generate_v4()` | Primary key |
| `user_id` | uuid | FK → users.id ON DELETE CASCADE | ID user |
| `role_id` | uuid | FK → roles.id ON DELETE CASCADE | ID role |
| `assigned_at` | timestamp | useCurrent | Waktu assignment |

---

### 2.4 categories
Tabel kategori dengan hierarki self-referencing (parent-child).

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| `id` | uuid | PK, default `uuid_generate_v4()` | Primary key |
| `name` | string(100) | NOT NULL | Nama kategori |
| `slug` | string(120) | UNIQUE | URL slug |
| `description` | text | NULLABLE | Deskripsi |
| `parent_id` | uuid | FK → categories.id ON DELETE SET NULL | Parent category (self-ref) |
| `created_at` | timestamp | useCurrent | Waktu pembuatan |

---

### 2.5 tags
Tabel tag untuk label postingan.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| `id` | uuid | PK, default `uuid_generate_v4()` | Primary key |
| `name` | string(50) | UNIQUE | Nama tag |
| `slug` | string(60) | UNIQUE | URL slug |
| `color` | string(7) | NULLABLE | Warna hex (mis: `#FF5733`) |
| `usage_count` | integer | DEFAULT 0 | Jumlah penggunaan |
| `created_at` | timestamp | useCurrent | Waktu pembuatan |

---

### 2.6 badges
Tabel badge untuk sistem gamifikasi.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| `id` | uuid | PK, default `uuid_generate_v4()` | Primary key |
| `name` | string(100) | UNIQUE | Nama badge |
| `description` | text | NULLABLE | Deskripsi |
| `icon_url` | string(500) | NULLABLE | URL ikon badge |
| `tier` | string(20) | NULLABLE | Tingkatan (bronze, silver, gold) |
| `condition_type` | string(50) | NULLABLE | Tipe kondisi (post_count, reputation_points, dll) |
| `condition_value` | integer | NULLABLE | Nilai threshold |
| `created_at` | timestamp | useCurrent | Waktu pembuatan |

---

### 2.7 user_badges (Junction)
Tabel penghubung many-to-many antara users dan badges.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| `id` | uuid | PK, default `uuid_generate_v4()` | Primary key |
| `user_id` | uuid | FK → users.id ON DELETE CASCADE | ID user |
| `badge_id` | uuid | FK → badges.id ON DELETE CASCADE | ID badge |
| `earned_at` | timestamp | useCurrent | Waktu perolehan |

---

### 2.8 posts
Tabel postingan utama. Mendukung soft delete.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| `id` | uuid | PK | Primary key |
| `user_id` | uuid | FK → users.id ON DELETE CASCADE | Penulis |
| `category_id` | uuid | FK → categories.id ON DELETE CASCADE | Kategori |
| `title` | string(300) | NOT NULL | Judul post |
| `body` | text | NOT NULL | Isi post |
| `status` | string(20) | DEFAULT 'draft' | Status (open/closed/draft) |
| `view_count` | integer | DEFAULT 0 | Jumlah views |
| `vote_score` | integer | DEFAULT 0 | Skor vote (denormalized) |
| `is_answered` | boolean | DEFAULT false | Apakah sudah dijawab |
| `accepted_answer_id` | uuid | FK → comments.id ON DELETE SET NULL | ID jawaban diterima |
| `created_at` | timestamp | | Waktu pembuatan |
| `updated_at` | timestamp | | Waktu update |
| `deleted_at` | timestamp | NULLABLE | Soft delete marker |

**Relasi:**
- BelongsTo → `users`, `categories`
- BelongsToMany → `tags` (via `post_tags`)
- HasMany → `comments`, `post_edit_history`, `bookmarks`, `votes`
- BelongsTo → `comments` (accepted_answer)

---

### 2.9 comments
Tabel komentar dengan self-referencing untuk reply. Mendukung soft delete.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| `id` | uuid | PK, default `uuid_generate_v4()` | Primary key |
| `post_id` | uuid | FK → posts.id ON DELETE CASCADE | Post terkait |
| `user_id` | uuid | FK → users.id ON DELETE CASCADE | Penulis komentar |
| `parent_id` | uuid | FK → comments.id ON DELETE CASCADE | Parent comment (untuk reply) |
| `body` | text | NOT NULL | Isi komentar |
| `vote_score` | integer | DEFAULT 0 | Skor vote (denormalized) |
| `is_accepted` | boolean | DEFAULT false | Apakah jawaban diterima |
| `created_at` | timestamp | | Waktu pembuatan |
| `updated_at` | timestamp | | Waktu update |
| `deleted_at` | timestamp | NULLABLE | Soft delete marker |

**Relasi:**
- BelongsTo → `posts`, `users`
- HasMany → `replies` (self-ref via `parent_id`)
- HasMany → `comment_edit_history`, `votes`

---

### 2.10 post_tags (Junction)
Tabel penghubung many-to-many antara posts dan tags.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| `id` | uuid | PK, default `uuid_generate_v4()` | Primary key |
| `post_id` | uuid | FK → posts.id ON DELETE CASCADE | ID post |
| `tag_id` | uuid | FK → tags.id ON DELETE CASCADE | ID tag |

---

### 2.11 follows
Tabel follow antar user (self-referencing).

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| `id` | uuid | PK, default `uuid_generate_v4()` | Primary key |
| `follower_id` | uuid | FK → users.id ON DELETE CASCADE | User yang follow |
| `following_id` | uuid | FK → users.id ON DELETE CASCADE | User yang di-follow |
| `created_at` | timestamp | useCurrent | Waktu follow |

---

### 2.12 votes (Polymorphic)
Tabel voting — mendukung vote untuk post ATAU comment.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| `id` | uuid | PK, default `uuid_generate_v4()` | Primary key |
| `user_id` | uuid | FK → users.id ON DELETE CASCADE | Voter |
| `target_id` | uuid | NOT NULL | ID target (post/comment) |
| `target_type` | string(20) | NOT NULL | Tipe target (`post`/`comment`) |
| `vote_type` | string(10) | NOT NULL | Tipe vote (`1`=up, `-1`=down) |
| `created_at` | timestamp | useCurrent | Waktu vote |

**Index:** `(target_id, target_type)` — untuk query skor vote

---

### 2.13 likes (Polymorphic)
Tabel like — mendukung like untuk post ATAU comment.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| `id` | uuid | PK, default `uuid_generate_v4()` | Primary key |
| `user_id` | uuid | FK → users.id ON DELETE CASCADE | User yang like |
| `target_id` | uuid | NOT NULL | ID target |
| `target_type` | string(20) | NOT NULL | Tipe target (`post`/`comment`) |
| `created_at` | timestamp | useCurrent | Waktu like |

**Index:** `(target_id, target_type)`

---

### 2.14 bookmarks
Tabel bookmark postingan (bukan polymorphic, hanya post).

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| `id` | uuid | PK, default `uuid_generate_v4()` | Primary key |
| `user_id` | uuid | FK → users.id ON DELETE CASCADE | User yang bookmark |
| `post_id` | uuid | FK → posts.id ON DELETE CASCADE | Post yang di-bookmark |
| `created_at` | timestamp | useCurrent | Waktu bookmark |

---

### 2.15 post_edit_history
Audit trail untuk perubahan body post.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| `id` | uuid | PK, default `uuid_generate_v4()` | Primary key |
| `post_id` | uuid | FK → posts.id ON DELETE CASCADE | Post terkait |
| `edited_by` | uuid | FK → users.id ON DELETE CASCADE | User yang edit |
| `body_before` | text | NULLABLE | Body sebelum edit |
| `body_after` | text | NULLABLE | Body setelah edit |
| `reason` | string(255) | NULLABLE | Alasan edit |
| `edited_at` | timestamp | useCurrent | Waktu edit |

---

### 2.16 comment_edit_history
Audit trail untuk perubahan body komentar.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| `id` | uuid | PK, default `uuid_generate_v4()` | Primary key |
| `comment_id` | uuid | FK → comments.id ON DELETE CASCADE | Komentar terkait |
| `edited_by` | uuid | FK → users.id ON DELETE CASCADE | User yang edit |
| `body_before` | text | NULLABLE | Body sebelum edit |
| `body_after` | text | NULLABLE | Body setelah edit |
| `edited_at` | timestamp | useCurrent | Waktu edit |

---

### 2.17 points_log
Log poin gamifikasi per user.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| `id` | uuid | PK, default `uuid_generate_v4()` | Primary key |
| `user_id` | uuid | FK → users.id ON DELETE CASCADE | User penerima |
| `points` | integer | NOT NULL | Jumlah poin (+/-) |
| `action_type` | string(50) | NOT NULL | Tipe aksi (create_post, upvote_received_post, dll) |
| `reference_id` | uuid | NULLABLE | ID referensi (post/comment terkait) |
| `description` | string(255) | NULLABLE | Deskripsi |
| `created_at` | timestamp | useCurrent | Waktu pencatatan |

---

### 2.18 notifications
Tabel notifikasi dengan referensi polymorphic.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| `id` | uuid | PK, default `uuid_generate_v4()` | Primary key |
| `user_id` | uuid | FK → users.id ON DELETE CASCADE | Penerima notifikasi |
| `actor_id` | uuid | FK → users.id ON DELETE SET NULL | User yang memicu |
| `type` | string(50) | NOT NULL | Tipe (upvote_post, new_comment, dll) |
| `reference_id` | uuid | NULLABLE | ID referensi |
| `reference_type` | string(100) | NULLABLE | Tipe referensi |
| `is_read` | boolean | DEFAULT false | Status baca |
| `created_at` | timestamp | useCurrent | Waktu notifikasi |

---

### 2.19 reports (Polymorphic)
Tabel laporan konten oleh user.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| `id` | uuid | PK, default `uuid_generate_v4()` | Primary key |
| `reporter_id` | uuid | FK → users.id ON DELETE CASCADE | Pelapor |
| `target_id` | uuid | NOT NULL | ID target |
| `target_type` | string(20) | NOT NULL | Tipe target (post/comment) |
| `reason` | string(100) | NOT NULL | Alasan laporan |
| `description` | text | NULLABLE | Detail |
| `status` | string(20) | DEFAULT 'pending' | Status (pending/resolved/dismissed) |
| `resolved_by` | uuid | FK → users.id ON DELETE SET NULL | Moderator yang resolve |
| `created_at` | timestamp | useCurrent | Waktu laporan |
| `resolved_at` | timestamp | NULLABLE | Waktu resolve |

---

### 2.20 moderation_logs
Log aksi moderator terhadap user.

| Kolom | Tipe | Constraint | Keterangan |
|-------|------|-----------|------------|
| `id` | uuid | PK, default `uuid_generate_v4()` | Primary key |
| `moderator_id` | uuid | FK → users.id ON DELETE CASCADE | Moderator |
| `target_user_id` | uuid | FK → users.id ON DELETE SET NULL | User target |
| `action_type` | string(50) | NOT NULL | Tipe aksi (warn, ban, unban) |
| `reason` | string(255) | NOT NULL | Alasan |
| `notes` | text | NULLABLE | Catatan tambahan |
| `created_at` | timestamp | useCurrent | Waktu aksi |

---

## 3. Tabel Infrastruktur Laravel (7 Tabel)

| Tabel | Migration | Keterangan |
|-------|-----------|------------|
| `personal_access_tokens` | `2026_06_04_021554` | Sanctum API tokens |
| `cache` | `2026_06_04_031912` | Cache storage |
| `sessions` | `2026_06_08_051102` | Session storage (SPA auth) |
| `jobs` | `2026_06_08_105536` | Queue jobs |
| `failed_jobs` | `2026_06_08_120527` | Failed queue jobs |
| `password_reset_tokens` | `2026_06_08_115948` | Password reset tokens |

### sessions
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | string | PK (session ID) |
| `user_id` | uuid, nullable | User yang login |
| `ip_address` | string(45) | IP address |
| `user_agent` | text | Browser UA |
| `payload` | longText | Session data |
| `last_activity` | integer | Timestamp aktivitas terakhir |

---

## 4. Entity Relationship Diagram (Deskripsi)

```
users ──< user_roles >── roles          (Many-to-Many via junction)
users ──< user_badges >── badges        (Many-to-Many via junction)
users ──< posts >── categories          (User has many posts, post belongs to category)
posts ──< post_tags >── tags            (Many-to-Many via junction)
posts ──< comments                      (Post has many comments)
comments ──< comments (self-ref)        (Reply via parent_id)
posts ──< comments (accepted_answer)    (Post has one accepted comment)
posts ──< post_edit_history             (Audit trail)
comments ──< comment_edit_history       (Audit trail)
users ──< votes (polymorphic)           (User votes on post/comment)
users ──< likes (polymorphic)           (User likes on post/comment)
users ──< bookmarks                     (User bookmarks posts)
users ──< follows (self-ref)            (User follows other users)
users ──< points_log                    (Gamification points)
users ──< notifications                 (User receives notifications)
users ──< reports (as reporter)         (User reports content)
users ──< moderation_logs (as mod)      (Moderator actions)
```

---

## 5. Pola Polymorphic Relation

Tiga tabel menggunakan pola polymorphic:

| Tabel | Kolom Morph | Target |
|-------|-------------|--------|
| `votes` | `target_id` + `target_type` | `post` atau `comment` |
| `likes` | `target_id` + `target_type` | `post` atau `comment` |
| `reports` | `target_id` + `target_type` | `post` atau `comment` |
| `notifications` | `reference_id` + `reference_type` | `post`, `comment`, atau lainnya |

> **Catatan:** Polymorphic di sini menggunakan string literal (`'post'`, `'comment'`) bukan fully-qualified class name, sehingga tidak menggunakan Laravel `morphTo()` bawaan melainkan query manual di Service/Repository.

---

## 6. Pola Self-Referencing

| Tabel | Kolom | FK Target | On Delete |
|-------|-------|-----------|-----------|
| `categories` | `parent_id` | `categories.id` | SET NULL |
| `comments` | `parent_id` | `comments.id` | CASCADE |
| `follows` | `follower_id`/`following_id` | `users.id` | CASCADE |

---

## 7. Ringkasan Index & Constraints

| Tabel | Index | Keterangan |
|-------|-------|------------|
| `users` | UNIQUE `username`, UNIQUE `email` | |
| `roles` | UNIQUE `name` | |
| `tags` | UNIQUE `name`, UNIQUE `slug` | |
| `categories` | UNIQUE `slug` | |
| `badges` | UNIQUE `name` | |
| `votes` | INDEX `(target_id, target_type)` | Query skor vote |
| `likes` | INDEX `(target_id, target_type)` | Query jumlah like |
| `sessions` | INDEX `user_id`, INDEX `last_activity` | Session lookup |

---

**Selanjutnya: Baca [04_FEATURE_MAPPING.md](04_FEATURE_MAPPING.md) untuk mapping fitur ke file implementasi →**
