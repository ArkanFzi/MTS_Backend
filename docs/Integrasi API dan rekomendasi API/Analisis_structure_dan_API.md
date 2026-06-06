# 🔍 Analisis Backend MauTanyaSuhu vs Design Prompt

## 📋 Overview Teknologi Stack

| Layer | Teknologi |
|-------|-----------|
| **Backend** | Laravel (PHP) — Modular Architecture |
| **Frontend** | React + TypeScript + Vite |
| **Database** | PostgreSQL (UUID primary keys) |
| **Auth** | Laravel Sanctum |
| **Styling** | Vanilla CSS (dari `App.css` & `index.css`) |

---

## 🗄️ Peta Database Lengkap (dari Migrations)

| Tabel | Kolom Utama |
|-------|-------------|
| `users` | `id` (uuid), `username`, `email`, `password_hash`, `avatar_url`, `bio`, `reputation_points`, `level`, `is_banned`, `created_at`, `updated_at` |
| `roles` | `id` (uuid), `name`, `permissions` (json), `created_at` |
| `user_roles` | `id`, `user_id`, `role_id`, `assigned_at` |
| `tags` | `id` (uuid), `name`, `slug`, `color` (7 char hex), `usage_count`, `created_at` |
| `categories` | `id` (uuid), `name`, `slug`, `description`, `parent_id` (self-ref), `created_at` |
| `badges` | *(ada di migrations)* |
| `posts` | `id` (uuid), `user_id`, `category_id`, `title`, `body`, `status`, `view_count`, `vote_score`, `is_answered`, `accepted_answer_id`, `created_at`, `updated_at` |
| `comments` | `id`, `post_id`, `user_id`, `parent_id`, `body`, timestamps |
| `post_tags` | pivot `post_id` + `tag_id` |
| `post_edit_history` | `id`, `post_id`, `edited_by`, `body_before`, `body_after`, `reason`, `edited_at` |
| `comment_edit_history` | `id`, `comment_id`, `edited_by`, `body_before`, `body_after`, `edited_at` ⚠️ |
| `votes` | `id`, `user_id`, `votable_id`, `votable_type`, `value` |
| `likes` | `id`, `user_id`, `likeable_id`, `likeable_type` |
| `bookmarks` | `id`, `user_id`, `post_id` |
| `points_log` | `id`, `user_id`, `points`, `action_type`, `reference_id`, `description`, `created_at` |
| `notifications` | `id`, `user_id`, `type`, `data`, `read_at`, `created_at` |
| `reports` | `id`, `reporter_id`, `reportable_id`, `reportable_type`, `reason`, `status` |
| `moderation_logs` | `id`, `moderator_id`, `target_user_id`, `action_type`, `reason`, `notes`, `created_at` |
| `user_badges` | pivot `user_id` + `badge_id` |
| `follows` | `id`, `follower_id`, `following_id` |

---

## 🗺️ Peta Halaman Frontend (Struktur Features)

### ✅ Halaman yang Sudah Ada (Feature Folders)
```
features/
├── Admin/
│   ├── F7_RoleAndPermission/   → PAGE 7 (partial) + PAGE 9 (partial)
│   ├── F8_UserManagement/      → PAGE 9
│   ├── F9_CategoryMaster/      → PAGE 8 (partial)
│   ├── F10_TagMaster/          → PAGE 8
│   └── F11_BadgeMaster/        → tidak di-prompt
├── Auth/
│   ├── F1_Register, F2_Login, F3_Logout
├── Common/
│   ├── F3_SearchPost, F4_FilterByTag
│   ├── F5_FilterByCategory, F6_TrendingPopularPost
├── Moderator/
│   ├── F12_ContentReportQueue  → PAGE 10 (partial)
│   └── F13_ModeratorActionLog  → PAGE 10
└── User/
    ├── F15–F29 (Post, Comment, Vote, Like, dll.)
```

### ❌ Halaman Admin yang BELUM Ada
- **Dashboard overview (PAGE 7)** — Tidak ada feature folder untuk "Admin Dashboard/Overview"
- **Content Audit Timeline (PAGE 10)** — Ada di Moderator, bukan Admin standalone

---

## ⚖️ GAP Analysis: Prompt vs Backend

### PAGE 7 — Admin Master Dashboard

| Elemen Prompt | Status | Catatan |
|---------------|--------|---------|
| Sidebar: System Overview | ❌ BELUM | Tidak ada endpoint `/admin/stats` atau `/admin/overview` |
| Sidebar: Tag CRUD Master | ✅ Ada | `GET/POST/PUT/DELETE /admin/tags` |
| Sidebar: User Directory Rules | ✅ Ada | `GET /admin/users`, `PUT /admin/users/{id}/role` |
| Sidebar: Content History Explorer | ✅ Ada | `GET /admin/posts/{post}/history` |
| Quadrant 1: Chart post creation + user registration | ❌ BELUM | Tidak ada endpoint analytics/timeline. Backend tidak expose `/admin/stats/posts-over-time` or `/admin/stats/registrations` |
| Quadrant 2: Points circulation dari `points_log` | ⚠️ PARTIAL | Tabel `points_log` ada, tapi **tidak ada endpoint admin khusus** untuk sum/aggregate `points_log` |
| Quadrant 3: Top 5 posts by `view_count` & `vote_score` | ⚠️ PARTIAL | Endpoint `GET /posts` ada, tapi tidak ada sort parameter `order_by=view_count` atau agregasi admin-level |
| Quadrant 4: Shortcut ke `user_roles` & `roles` | ✅ Ada | `GET /admin/roles` via `apiResource` |
| Modal: Emergency Registration Lockdown | ❌ BELUM | **Tidak ada endpoint toggle registrasi** di backend. Tidak ada `POST /admin/system/lock-registration` |

---

### PAGE 8 — Tag & Category CRUD

| Elemen Prompt | Status | Catatan |
|---------------|--------|---------|
| Tabel tags: `id`, `name`, `slug`, `color`, `usage_count` | ✅ Match | Schema `tags` table persis sama |
| Tabel categories: `id`, `name`, `slug` | ✅ Match | Ada, tapi categories **tidak punya `color`** kolom |
| Color indicator (hex circle) dari `tags.color` | ✅ Ada | Kolom `color` varchar(7) ada di `tags` |
| `usage_count` metric | ✅ Ada | Kolom `usage_count` ada di `tags` |
| Edit/Delete action columns | ✅ Ada | `PUT/DELETE /admin/tags/{id}` tersedia |
| Slider Form: Add New Tag | ✅ Ada | `POST /admin/tags` endpoint ada |
| `categories` punya `usage_count` | ❌ TIDAK ADA | Schema `categories` **tidak punya `usage_count`** — hanya `name`, `slug`, `description`, `parent_id` |

---

### PAGE 9 — User Account Control

| Elemen Prompt | Status | Catatan |
|---------------|--------|---------|
| Kolom: User UUID | ✅ Match | `users.id` UUID |
| Kolom: Username | ✅ Match | `users.username` |
| Kolom: Email | ✅ Match | `users.email` |
| Kolom: Reputation Points | ✅ Match | `users.reputation_points` |
| Kolom: Level | ✅ Match | `users.level` |
| Kolom: Account Status (is_banned) | ✅ Match | `users.is_banned` boolean |
| Filter dropdown: `is_banned` | ✅ Match | Backend: `GET /admin/users` |
| Filter dropdown: role | ✅ Match | `PUT /admin/users/{id}/role` |
| Expandable row: adjust `reputation_points` | ⚠️ PARTIAL | Ada `PUT /admin/users/{id}/profile` tapi tidak jelas apakah mencakup `reputation_points` |
| Set `users.is_banned` | ✅ Ada | `POST /admin/bans/{id}/ban` & `unban` |
| Log ke `moderation_logs` dengan `action_type` | ✅ Match | Schema `moderation_logs` punya `action_type`, `reason`, `notes` |
| **MASALAH**: Prompt minta log `reason` ke `moderation_logs` | ⚠️ PARTIAL | Field `reason` ada, tapi prompt bilang "mandatory text area" — perlu validasi backend |

---

### PAGE 10 — Content Audit Ledger

| Elemen Prompt | Status | Catatan |
|---------------|--------|---------|
| `post_edit_history` table | ✅ Ada | Kolom: `post_id`, `edited_by`, `body_before`, `body_after`, `reason`, `edited_at` |
| `comment_edit_history` table | ⚠️ PARTIAL | Kolom ada, **tapi TIDAK ADA `reason` kolom** di `comment_edit_history`! Hanya `body_before`, `body_after`, `edited_at` |
| Timestamp millisecond (`edited_at`) | ✅ Ada | `timestamp` type tersedia |
| Target ID (Post ID / Comment ID) | ✅ Ada | `post_id` / `comment_id` |
| Editor UUID (`edited_by`) | ✅ Ada | `edited_by` FK ke `users.id` |
| Reason field | ❌ GAP | `post_edit_history.reason` ada ✅ tapi `comment_edit_history` **tidak punya `reason`** ❌ |
| Dual `body_before` vs `body_after` | ✅ Ada | Ada di kedua tabel |
| Endpoint akses history | ✅ Ada | `GET /admin/posts/{post}/history` tapi **tidak ada** `/admin/comments/{comment}/history` di admin route! (hanya ada di moderator) |

---

## 🔴 TEMUAN KRITIS (Critical Gaps)

1. **Tidak ada Admin Analytics Endpoint** — Backend tidak punya endpoint untuk chart data (post creation over time, user registration timeline). Perlu dibuat.

2. **`comment_edit_history` tidak punya kolom `reason`** — Prompt PAGE 10 minta `reason` field, tapi migration-nya tidak punya.

3. **Emergency Lockdown tidak ada di backend** — Tidak ada mekanisme toggle registrasi di routes maupun controller.

4. **`categories` tidak punya `usage_count`** — Prompt PAGE 8 implies ada, tapi schema tidak punya kolom ini.

5. **Admin comment history missing** — Route `/admin/comments/{comment}/history` tidak ada, hanya ada di moderator prefix.

6. **`points_log` tidak ada aggregate endpoint** — Prompt minta sum total points circulation, tapi tidak ada route admin untuk itu.

---

## ✏️ REVISI PROMPT YANG DETAIL DAN AKURAT

> Berikut adalah prompt yang direvisi, disesuaikan 100% dengan schema database aktual:

---

### REVISED PROMPT — PAGE 7: Admin Master Dashboard

```
Generate a high-density, centralized System Administrator Master Dashboard interface for 'MauTanyaSuhu'. This is PAGE 7.

[LAYOUT CONFIGURATION]: Strict 2-column corporate workspace layout. Optimized for desktop widescreen (1440px–1920px viewports).

[VISUAL THEME]: Dark industrial luxury aesthetic. Background is deep obsidian black (#0B0B0C). Container boxes use dark slate-gray (#161618) with a 1px solid steel border. Accent colors: burnished gold (#D4AF37) for positive metrics, warning red (#E53E3E) for critical status indicators.

[LEFT ADMIN NAV — Fixed 250px]:
A permanent fixed-position sidebar with clean vector icon navigation links for:
- 'System Overview' (active state, gold underline indicator)
- 'Tag & Category Master' (links to PAGE 8)
- 'User Directory Control' (links to PAGE 9)
- 'Content Audit Timeline' (links to PAGE 10)

[RIGHT MASTER PANEL — Bento Grid 4-Quadrant]:

QUADRANT 1 (Top Left — Analytics Chart):
A dual-line time-series chart rendered using a library like Recharts or Chart.js.
- Line 1: Total post creation count plotted per day/week (data source: `posts` table, grouped by `created_at`).
- Line 2: New user registrations plotted per day/week (data source: `users` table, grouped by `created_at`).
- Gold line = posts, Steel-blue line = registrations.
- X-axis: calendar dates. Y-axis: count integer.
- Requires backend endpoint: GET /admin/stats/overview returning `{ posts_over_time: [...], registrations_over_time: [...] }`.

QUADRANT 2 (Top Right — Points Ledger):
A summary metrics box calculating aggregate data from the `points_log` table:
- Total points in circulation: SUM of all positive `points_log.points` values.
- Total points deducted: SUM of all negative `points_log.points` values (displayed in warning red).
- Net balance: overall total SUM.
- Group breakdown by `points_log.action_type` (e.g., 'upvote', 'accepted_answer', 'penalty'), displayed as a mini-table with action_type label + sum value.
- Requires backend endpoint: GET /admin/stats/points-summary.

QUADRANT 3 (Bottom Left — Top Engaging Content):
A compact data table rendering the top 5 posts from the `posts` table ordered by `view_count DESC` then `vote_score DESC`:
- Columns: Post UUID (truncated 8 chars), Title (max 40 chars with ellipsis), view_count (gold number), vote_score (signed integer), Status badge (`posts.status`: 'draft'/'published').
- Requires backend endpoint: GET /admin/posts?sort=view_count&limit=5.

QUADRANT 4 (Bottom Right — Role Access Shortcuts):
A shortcut grid of action cards navigating to role management:
- Card 1: 'Manage Roles' — navigates to GET /admin/roles (shows `roles` table: id, name, permissions JSON).
- Card 2: 'Assign User Roles' — navigates to PUT /admin/users/{id}/role (maps `user_roles` table: user_id, role_id, assigned_at).
- Card 3: 'Banned Users' — quick link filter to GET /admin/users?is_banned=true.
- Card 4: 'Moderation Logs' — link to GET /moderator/logs.

[EMBEDDED MODAL]:
'Emergency Registration Lockdown Modal':
A critical high-alert floating modal with warning-red (#E53E3E) thick border stroke, appearing over a translucent dim overlay.
- Title: "⚠ SYSTEM LOCKDOWN CONFIRMATION"
- Body text: "This action will toggle the registration lock on the system. All new account creation will be suspended."
- Authorization key input field (password-type) — NOTE: This is a frontend-side UI concept. Backend endpoint POST /admin/system/toggle-registration must be implemented separately with an `authorization_key` body parameter.
- Two buttons: 'Cancel' (steel-gray) and 'Confirm Lockdown' (warning red, requires key validation).
```

---

### REVISED PROMPT — PAGE 8: Tag & Category CRUD Master

```
Generate a high-density administrative CRUD spreadsheet interface for Tags and Categories in 'MauTanyaSuhu'. This is PAGE 8.

[VISUAL INHERITANCE]: 100% inherit dark luxury layout from PAGE 7. Same color tokens, typography, border radius, spacing.

[LEFT ADMIN NAV]: Persistent sidebar from PAGE 7. 'Tag & Category Master' item is now in active/highlighted state.

[RIGHT CONFIGURATOR GRID]:

SECTION A — TAGS TABLE (Primary Section):
An alternating-row dark data table mapping to the `tags` database table schema:
- Column 1: 'Tag ID' — displays `tags.id` UUID, truncated to first 8 characters in monospace font.
- Column 2: 'Name' — displays `tags.name` (max 50 chars).
- Column 3: 'Slug' — displays `tags.slug` (max 60 chars), styled in steel-gray monospace.
- Column 4: 'Color' — displays a filled circle (12px diameter) using `tags.color` as the fill hex value (e.g., `#FF5733`), followed by the hex string in small text.
- Column 5: 'Usage Count' — displays `tags.usage_count` integer metric with a subtle gold tint for high values (>100).
- Column 6: 'Actions' — two icon buttons: Edit (pencil icon, gold hover) and Delete (trash icon, red hover).
Table data source: GET /admin/tags (moderator or admin role required).

SECTION B — CATEGORIES TABLE (Secondary Section, below tags):
A second alternating-row dark data table mapping to the `categories` database schema:
- Column 1: 'Category ID' — `categories.id` UUID, 8-char truncated.
- Column 2: 'Name' — `categories.name` (max 100 chars).
- Column 3: 'Slug' — `categories.slug` (max 120 chars).
- Column 4: 'Description' — `categories.description` text (truncated to 60 chars with ellipsis, full text on hover tooltip).
- Column 5: 'Parent' — displays parent category name if `categories.parent_id` is not null, else shows '— Root —' label in italic.
- Column 6: 'Actions' — Edit (pencil) and Delete (trash) icon buttons.
NOTE: Categories do NOT have a `color` or `usage_count` column in the actual schema.
Table data source: GET /admin/categories.

[EMBEDDED COMPONENT]:
'Add New Tag Slider Panel':
A smooth right-side drawer that slides in from the right viewport edge on '+Add Tag' button click:
- Text input: 'Tag Name' → maps to `tags.name` (max 50 chars, required).
- Text input: 'Slug' → maps to `tags.slug` (auto-generated from name, but editable, max 60 chars).
- Color Picker Grid: A 16-swatch color grid for selecting `tags.color` hex value. Clicking a swatch fills a small preview circle and populates a text input with the hex string (e.g., `#D4AF37`).
- Save Button: Submits POST /admin/tags with body `{ name, slug, color }`.
- Close (X) button to slide panel back out.
```

---

### REVISED PROMPT — PAGE 9: User Account Control Directory

```
Generate a secure Master User Account Control Directory for 'MauTanyaSuhu' Administrators. This is PAGE 9.

[VISUAL INHERITANCE]: 100% inherit dark industrial luxury canvas from PAGE 7 & 8.

[LEFT ADMIN NAV]: Persistent from PAGE 7. 'User Directory Control' item is active.

[RIGHT USER DATABASE GRID]:

TOP TOOLBAR:
- Dropdown Filter 1: 'Account Status' — options: 'All Users', 'Active Only' (`is_banned = false`), 'Banned Only' (`is_banned = true`). Maps to GET /admin/users?is_banned=true|false.
- Dropdown Filter 2: 'Role Assignment' — options: 'All Roles', 'Admin', 'Moderator', 'User'. Maps to GET /admin/users?role=admin|moderator|user.
- Search input field (username or email search).

MASTER USER DIRECTORY TABLE:
A dense data table mapping to the `users` table schema:
- Column 1: 'User ID' — `users.id` UUID, first 8 chars in monospace, gold tint.
- Column 2: 'Username' — `users.username` (max 100 chars).
- Column 3: 'Email' — `users.email` (max 255 chars), smaller font weight.
- Column 4: 'Reputation' — `users.reputation_points` integer, gold color for values > 1000.
- Column 5: 'Level' — `users.level` integer badge (pill-shaped chip with dark bg).
- Column 6: 'Status' — boolean badge: `is_banned = true` renders red 'BANNED' pill, `is_banned = false` renders green 'ACTIVE' pill.

[EMBEDDED EXPANDABLE ROW — 'Account Sanction Control Panel']:
Clicking any user row expands it vertically to reveal an inline sub-form:
- Reputation Adjustment Input: A numeric +/- input field labeled 'Adjust Reputation Points' that writes to `users.reputation_points`. Requires backend endpoint: PUT /admin/users/{id}/profile with body `{ reputation_points: integer }`.
- Ban Toggle Checkbox: 'Is Banned' boolean checkbox writing to `users.is_banned`. Uses endpoints:
  * To ban: POST /admin/bans/{id}/ban
  * To unban: POST /admin/bans/{id}/unban
- Sanction Reason Textarea: A required multi-line text area labeled 'Sanction Reason / Moderator Note'. Value is submitted to POST /moderator/bans/{id}/ban (or unban) as the `reason` field. This is logged in the `moderation_logs` table with the following schema mapping:
  * `moderation_logs.moderator_id` = currently authenticated admin UUID
  * `moderation_logs.target_user_id` = the selected user's UUID
  * `moderation_logs.action_type` = 'ban' | 'unban' | 'warning'
  * `moderation_logs.reason` = text from the textarea (max 255 chars)
  * `moderation_logs.notes` = optional extended notes (nullable)
- Action Buttons: 'Apply Ban' (warning red) | 'Apply Unban' (gold) | 'Send Warning' (steel-gray) | 'Cancel' (close row).
```

---

### REVISED PROMPT — PAGE 10: Content Audit Ledger & Timeline

```
Generate a dense chronological Application Content Audit Ledger and Timeline for 'MauTanyaSuhu'. This is PAGE 10.

[VISUAL INHERITANCE]: 100% inherit dark luxury canvas from Admin Page Group (PAGE 7–9). Background: obsidian black (#0B0B0C).

[LEFT ADMIN NAV]: Persistent from PAGE 7. 'Content Audit Timeline' item is active.

[RIGHT AUDIT TIMELINE STREAM]:

TOP TOOLBAR:
- Toggle Tab: 'Post Edits' | 'Comment Edits' — switches between reading from `post_edit_history` vs `comment_edit_history` table.
- Date range picker (from/to) for filtering by `edited_at` timestamp.

HISTORICAL CHRONOLOGICAL STREAM FEED:
A dense vertical scroll timeline. Each entry card represents one database row.

FOR POST EDIT HISTORY (source: `post_edit_history` table):
Each card renders:
- Timestamp: `post_edit_history.edited_at` — displayed down to the millisecond in format `YYYY-MM-DD HH:mm:ss.SSS`, monospace Fira Code font, steel-gray color.
- Target: 'Post ID: {post_edit_history.post_id}' — UUID string in gold monospace.
- Editor: 'Edited By: {post_edit_history.edited_by}' — UUID mapping back to `users.id`.
- Reason: 'Reason: {post_edit_history.reason}' — plain text string (max 255 chars). NOTE: This field IS present in `post_edit_history`. Display as italic gray text. If null, show '— No reason provided —'.
- Diff Area: A dual-column comparison box rendered in monospace Fira Code:
  * LEFT panel labeled 'BEFORE' (red-tinted dark bg): shows `post_edit_history.body_before`.
  * RIGHT panel labeled 'AFTER' (green-tinted dark bg): shows `post_edit_history.body_after`.
  * Both panels are max 8 lines tall with internal scrollbar.
Data source: GET /admin/posts/{post_id}/history OR GET /moderator/posts/{post_id}/history.

FOR COMMENT EDIT HISTORY (source: `comment_edit_history` table):
Each card renders:
- Timestamp: `comment_edit_history.edited_at` — same format as above.
- Target: 'Comment ID: {comment_edit_history.comment_id}' — UUID in gold monospace.
- Editor: 'Edited By: {comment_edit_history.edited_by}' — UUID.
- ⚠️ IMPORTANT NOTE: The `comment_edit_history` table does NOT have a `reason` column in the current schema. Do NOT render a Reason field for comment edits. If a 'reason' field is needed, it must be added via a backend migration: `$table->string('reason', 255)->nullable()`.
- Diff Area: Same dual-column BEFORE/AFTER comparison box using `body_before` and `body_after`.
Data source: GET /moderator/comments/{comment_id}/history.
NOTE: Admin-specific route for comment history does NOT currently exist — only the moderator route does. Frontend must call the moderator-prefixed endpoint, or a new route GET /admin/comments/{comment}/history must be added to the backend.
```

---

## 📌 Ringkasan Aksi yang Diperlukan

### Backend (Laravel) — Harus Ditambah:
1. `GET /admin/stats/overview` — return data chart posts + registrations per hari
2. `GET /admin/stats/points-summary` — aggregate SUM dari `points_log`
3. `POST /admin/system/toggle-registration` — lockdown mechanism
4. `GET /admin/comments/{comment}/history` — admin access ke comment history
5. `GET /admin/posts?sort=view_count&limit=5` — parameter sort untuk top posts
6. Migration: tambah `reason` kolom ke `comment_edit_history`
7. Konfirmasi: apakah `PUT /admin/users/{id}/profile` sudah support update `reputation_points`?

### Frontend (React) — Harus Dibuat:
- `features/Admin/F1_AdminDashboard/` — PAGE 7 (dashboard belum ada!)
- `features/Admin/F10_TagMaster/components/` — PAGE 8 (folder komponen masih kosong)
- `features/Admin/F7_RoleAndPermission/components/` — PAGE 7 shortcut cards (masih kosong)
- `features/Admin/F8_UserManagement/components/` — PAGE 9 (masih kosong)
- `features/Admin/F12_AuditTimeline/` — PAGE 10 (belum ada folder)

---

---

# 📱 DAFTAR LENGKAP HALAMAN YANG DIBUTUHKAN

> Berdasarkan **seluruh backend module, route API, dan schema database** yang sudah dibangun.
> Total: **29 halaman** terbagi dalam 5 kelompok role.

---

## 🔐 GRUP 1 — AUTH PAGES (Publik, tanpa login)

### PAGE 1 — Register
| Detail | Info |
|--------|------|
| **Route Frontend** | `/register` |
| **Backend Endpoint** | `POST /api/auth/register` |
| **Module Backend** | `Modules/Auth/F1_Register` |
| **Tabel Terkait** | `users` |
| **Data yang Dikirim** | `username`, `email`, `password`, `password_confirmation` |
| **Komponen UI** | Form input, validation error messages, link ke login |

---

### PAGE 2 — Login
| Detail | Info |
|--------|------|
| **Route Frontend** | `/login` |
| **Backend Endpoint** | `POST /api/auth/login` |
| **Module Backend** | `Modules/Auth/F2_Login` |
| **Tabel Terkait** | `users`, `personal_access_tokens` |
| **Response** | Bearer token Sanctum + user object |
| **Komponen UI** | Form email + password, remember me, link register |

---

## 🌐 GRUP 2 — PUBLIC / EXPLORE PAGES (Bisa diakses tanpa login)

### PAGE 3 — Home / Feed Utama
| Detail | Info |
|--------|------|
| **Route Frontend** | `/` atau `/feed` |
| **Backend Endpoint** | `GET /api/posts` |
| **Module Backend** | `Modules/User/F16_Post` |
| **Tabel Terkait** | `posts`, `users`, `categories`, `post_tags`, `tags` |
| **Komponen UI** | List kartu post, filter sidebar, pagination |
| **Data Ditampilkan** | `title`, body preview, `vote_score`, `view_count`, `is_answered`, username penulis, tags, category |

---

### PAGE 4 — Detail Post (Q&A Thread)
| Detail | Info |
|--------|------|
| **Route Frontend** | `/posts/:id` |
| **Backend Endpoints** | `GET /api/posts/{post}`, `GET /api/posts/{post}/comments` |
| **Module Backend** | `F16_Post`, `F17_Comment`, `F20_NestedCommentReply`, `F22_VoteSystem`, `F23_LikeSystem`, `F18_MarkAcceptedAnswer`, `F24_BookmarkPost` |
| **Tabel Terkait** | `posts`, `comments`, `votes`, `likes`, `bookmarks`, `users` |
| **Komponen UI** | Judul post, body, vote up/down, accept answer button, comment section, nested replies, bookmark button |
| **Aksi Tersedia** | Vote `POST /api/votes`, Like `POST /api/likes/toggle`, Bookmark `POST /api/bookmarks/toggle`, Accept `POST /api/posts/{post}/comments/{comment}/accept` |

---

### PAGE 5 — Halaman Pencarian
| Detail | Info |
|--------|------|
| **Route Frontend** | `/search?q={keyword}` |
| **Backend Endpoint** | `GET /api/explore/search?q=` |
| **Module Backend** | `Modules/Common/F4_SearchPost` |
| **Tabel Terkait** | `posts`, `tags`, `categories` |
| **Komponen UI** | Search bar, hasil pencarian berupa kartu post, highlight keyword |

---

### PAGE 6 — Filter by Tag
| Detail | Info |
|--------|------|
| **Route Frontend** | `/tags/:slug` |
| **Backend Endpoint** | `GET /api/explore/tag/{slug}` |
| **Module Backend** | `Modules/Common/F5_FilterByTag` |
| **Tabel Terkait** | `tags`, `post_tags`, `posts` |
| **Komponen UI** | Header info tag (nama, warna, `usage_count`), list post dengan tag tersebut |

---

### PAGE 7 — Filter by Category
| Detail | Info |
|--------|------|
| **Route Frontend** | `/categories/:slug` |
| **Backend Endpoint** | `GET /api/explore/category/{slug}` |
| **Module Backend** | `Modules/Common/F6_FilterByCategory` |
| **Tabel Terkait** | `categories`, `posts` |
| **Komponen UI** | Nama category, list post, breadcrumb parent category |

---

### PAGE 8 — Trending & Popular
| Detail | Info |
|--------|------|
| **Route Frontend** | `/trending` |
| **Backend Endpoint** | `GET /api/explore/trending` |
| **Module Backend** | `Modules/Common/F7_TrendingPopularPost` |
| **Tabel Terkait** | `posts`, `votes` |
| **Komponen UI** | List post ranked by score, badge "Hot/Trending", `view_count`, `vote_score` |

---

### PAGE 9 — Leaderboard Publik
| Detail | Info |
|--------|------|
| **Route Frontend** | `/leaderboard` |
| **Backend Endpoint** | `GET /api/explore/leaderboard?limit=10` |
| **Module Backend** | `Modules/User/F27_GamificationLeaderboard` |
| **Tabel Terkait** | `users` (ordered by `reputation_points DESC`) |
| **Komponen UI** | Ranking table: rank number, avatar, username, reputation_points, level badge |

---

### PAGE 10 — Daftar Semua Tags
| Detail | Info |
|--------|------|
| **Route Frontend** | `/tags` |
| **Backend Endpoint** | `GET /api/explore/tags` |
| **Module Backend** | `Modules/Admin/F12_TagMaster` |
| **Tabel Terkait** | `tags` |
| **Komponen UI** | Grid tag chips dengan color swatch, nama tag, `usage_count` |

---

## 👤 GRUP 3 — USER PAGES (Butuh Login)

### PAGE 11 — Create Post / Buat Pertanyaan
| Detail | Info |
|--------|------|
| **Route Frontend** | `/posts/create` |
| **Backend Endpoint** | `POST /api/posts` |
| **Module Backend** | `Modules/User/F16_Post` |
| **Tabel Terkait** | `posts`, `post_tags`, `tags` |
| **Data Dikirim** | `title`, `body`, `category_id`, `tag_ids[]`, `status` |
| **Komponen UI** | Rich text editor, input title, dropdown category, multi-select tags, tombol publish/draft |

---

### PAGE 12 — Edit Post
| Detail | Info |
|--------|------|
| **Route Frontend** | `/posts/:id/edit` |
| **Backend Endpoint** | `PUT /api/posts/{post}` |
| **Module Backend** | `Modules/User/F16_Post`, `Modules/User/F19_PostEditHistory` |
| **Tabel Terkait** | `posts`, `post_edit_history` |
| **Catatan** | Setiap edit otomatis simpan ke `post_edit_history` (`body_before`, `body_after`, `reason`, `edited_by`) |
| **Komponen UI** | Form pre-filled data, field "Reason for Edit" (wajib) |

---

### PAGE 13 — My Posts
| Detail | Info |
|--------|------|
| **Route Frontend** | `/me/posts` |
| **Backend Endpoint** | `GET /api/me/posts` |
| **Module Backend** | `Modules/User/F16_Post` (method `myPosts`) |
| **Tabel Terkait** | `posts` (filtered `user_id = auth()->id()`) |
| **Komponen UI** | List post milik user, status badge (draft/published/answered), tombol edit/delete |

---

### PAGE 14 — Bookmarks
| Detail | Info |
|--------|------|
| **Route Frontend** | `/bookmarks` |
| **Backend Endpoint** | `GET /api/bookmarks` |
| **Module Backend** | `Modules/User/F24_BookmarkPost` |
| **Tabel Terkait** | `bookmarks`, `posts` |
| **Komponen UI** | List post yang di-bookmark, tombol remove bookmark |

---

### PAGE 15 — Notifications
| Detail | Info |
|--------|------|
| **Route Frontend** | `/notifications` |
| **Backend Endpoints** | `GET /api/notifications`, `PATCH /api/notifications/mark-all-read`, `PATCH /api/notifications/{id}/read` |
| **Module Backend** | `Modules/User/F26_NotificationSystem` |
| **Tabel Terkait** | `notifications` (`user_id`, `actor_id`, `type`, `reference_id`, `is_read`, `created_at`) |
| **Komponen UI** | List notifikasi, badge unread count, tombol "Mark all as read" |

---

### PAGE 16 — Profile Settings
| Detail | Info |
|--------|------|
| **Route Frontend** | `/settings/profile` |
| **Backend Endpoints** | `GET /api/settings/profile`, `PUT /api/settings/profile`, `PUT /api/settings/password` |
| **Module Backend** | `Modules/User/F28_ProfileSettings` |
| **Tabel Terkait** | `users` (`username`, `email`, `avatar_url`, `bio`) |
| **Komponen UI** | Form edit username/bio/avatar, form ganti password (old -> new -> confirm) |

---

### PAGE 17 — User Public Profile
| Detail | Info |
|--------|------|
| **Route Frontend** | `/users/:id` |
| **Backend Endpoints** | `GET /api/users/{id}/followers`, `GET /api/users/{id}/following`, `POST /api/users/{id}/follow` |
| **Module Backend** | `Modules/User/F25_FollowUser` |
| **Tabel Terkait** | `users`, `follows`, `user_badges`, `badges` |
| **Komponen UI** | Avatar, username, bio, reputation, level, follower/following count, follow/unfollow button, badge list |

---

### PAGE 18 — My Badges / Achievements
| Detail | Info |
|--------|------|
| **Route Frontend** | `/me/badges` |
| **Backend Endpoint** | `GET /api/me/badges` |
| **Module Backend** | `Modules/User/F29_BadgeAchievement` |
| **Tabel Terkait** | `user_badges`, `badges` (`name`, `description`, `icon_url`, `tier`, `condition_type`, `condition_value`) |
| **Komponen UI** | Grid badge cards dengan icon, nama badge, tier (bronze/silver/gold), deskripsi kondisi unlock |

---

## 🛡️ GRUP 4 — MODERATOR PAGES (Role: moderator atau admin)

### PAGE 19 — Moderator: Content Report Queue
| Detail | Info |
|--------|------|
| **Route Frontend** | `/moderator/reports` |
| **Backend Endpoints** | `GET /api/moderator/reports`, `GET /api/moderator/reports/{id}`, `PUT /api/moderator/reports/{id}` |
| **Module Backend** | `Modules/Moderator/F13_ContentReportQueue` |
| **Tabel Terkait** | `reports` (`reporter_id`, `target_id`, `target_type`, `reason`, `description`, `status`, `resolved_by`, `resolved_at`) |
| **Komponen UI** | Tabel laporan, filter by `status` (pending/resolved/rejected), tombol Resolve/Reject, detail modal |

---

### PAGE 20 — Moderator: Action Log
| Detail | Info |
|--------|------|
| **Route Frontend** | `/moderator/logs` |
| **Backend Endpoint** | `GET /api/moderator/logs` |
| **Module Backend** | `Modules/Moderator/F14_ModeratorActionLog` |
| **Tabel Terkait** | `moderation_logs` (`moderator_id`, `target_user_id`, `action_type`, `reason`, `notes`, `created_at`) |
| **Komponen UI** | Timeline/tabel log, filter by `action_type` (ban/unban/warning) |

---

### PAGE 21 — Moderator: Ban Management
| Detail | Info |
|--------|------|
| **Route Frontend** | `/moderator/bans` |
| **Backend Endpoints** | `GET /api/moderator/bans`, `POST /api/moderator/bans/{id}/ban`, `POST /api/moderator/bans/{id}/unban` |
| **Module Backend** | `Modules/Moderator/F15_UserBanSanction` |
| **Tabel Terkait** | `users` (`is_banned`), `moderation_logs` |
| **Komponen UI** | List banned users, tombol unban, form alasan ban |

---

### PAGE 22 — Moderator: Tag & Category Management
| Detail | Info |
|--------|------|
| **Route Frontend** | `/moderator/tags` |
| **Backend Endpoints** | `GET/POST/PUT/DELETE /api/moderator/tags`, `/api/moderator/categories` |
| **Module Backend** | `Modules/Admin/F12_TagMaster`, `Modules/Admin/F10_CategoryMaster` |
| **Tabel Terkait** | `tags`, `categories` |
| **Komponen UI** | Sama dengan Admin PAGE 8 (shared akses) |

---

### PAGE 23 — Moderator: Edit History Viewer
| Detail | Info |
|--------|------|
| **Route Frontend** | `/moderator/posts/:postId/history`, `/moderator/comments/:commentId/history` |
| **Backend Endpoints** | `GET /api/moderator/posts/{post}/history`, `GET /api/moderator/comments/{comment}/history` |
| **Module Backend** | `Modules/User/F19_PostEditHistory`, `Modules/User/F21_CommentEditHistory` |
| **Tabel Terkait** | `post_edit_history`, `comment_edit_history` |
| **Komponen UI** | Timeline diff viewer, body_before vs body_after, `edited_at`, `edited_by` |

---

## ⚙️ GRUP 5 — ADMIN PAGES (Role: admin only)

### PAGE 24 — Admin: Master Dashboard *(= Prompt PAGE 7)*
| Detail | Info |
|--------|------|
| **Route Frontend** | `/admin/dashboard` |
| **Backend Endpoints** | `GET /admin/stats/overview` ***(perlu dibuat)***, `GET /admin/stats/points-summary` ***(perlu dibuat)*** |
| **Module Backend** | Belum ada — perlu `Modules/Admin/F0_AdminDashboard` |
| **Tabel Terkait** | `posts`, `users`, `points_log`, `roles`, `user_roles` |
| **Komponen UI** | 4-quadrant Bento Grid, dual-line chart, points ledger, top 5 posts table, role shortcut cards, lockdown modal |

---

### PAGE 25 — Admin: Tag & Category CRUD *(= Prompt PAGE 8)*
| Detail | Info |
|--------|------|
| **Route Frontend** | `/admin/tags` |
| **Backend Endpoints** | `GET/POST/PUT/DELETE /api/admin/tags`, `/api/admin/categories` |
| **Module Backend** | `Modules/Admin/F12_TagMaster`, `Modules/Admin/F10_CategoryMaster` |
| **Tabel Terkait** | `tags`, `categories` |
| **Komponen UI** | Dual spreadsheet table (tags + categories), right-side Add Tag drawer dengan color picker |

---

### PAGE 26 — Admin: User Directory Control *(= Prompt PAGE 9)*
| Detail | Info |
|--------|------|
| **Route Frontend** | `/admin/users` |
| **Backend Endpoints** | `GET /api/admin/users`, `PUT /api/admin/users/{id}/role`, `PUT /api/admin/users/{id}/profile`, `POST /api/admin/bans/{id}/ban`, `POST /api/admin/bans/{id}/unban` |
| **Module Backend** | `Modules/Admin/F9_UserManagement`, `Modules/Admin/F8_RoleAndPermission` |
| **Tabel Terkait** | `users`, `user_roles`, `roles`, `moderation_logs` |
| **Komponen UI** | Dense user table, filter dropdowns, expandable row sanction form |

---

### PAGE 27 — Admin: Role Management
| Detail | Info |
|--------|------|
| **Route Frontend** | `/admin/roles` |
| **Backend Endpoint** | `GET/POST/PUT/DELETE /api/admin/roles` |
| **Module Backend** | `Modules/Admin/F8_RoleAndPermission` |
| **Tabel Terkait** | `roles` (`name`, `permissions` JSON), `user_roles` |
| **Komponen UI** | CRUD table untuk roles, JSON editor untuk `permissions`, assign role ke user |

---

### PAGE 28 — Admin: Badge Master
| Detail | Info |
|--------|------|
| **Route Frontend** | `/admin/badges` |
| **Backend Endpoint** | `GET/POST/PUT/DELETE /api/admin/badges` |
| **Module Backend** | `Modules/Admin/F11_BadgeMaster` |
| **Tabel Terkait** | `badges` (`name`, `description`, `icon_url`, `tier`, `condition_type`, `condition_value`) |
| **Komponen UI** | CRUD table, upload `icon_url`, dropdown tier, input `condition_value` |

---

### PAGE 29 — Admin: Content Audit Timeline *(= Prompt PAGE 10)*
| Detail | Info |
|--------|------|
| **Route Frontend** | `/admin/audit` |
| **Backend Endpoints** | `GET /api/admin/posts/{post}/history`, `GET /api/moderator/comments/{comment}/history` ⚠️ |
| **Module Backend** | `Modules/User/F19_PostEditHistory`, `Modules/User/F21_CommentEditHistory` |
| **Tabel Terkait** | `post_edit_history`, `comment_edit_history` |
| **Komponen UI** | Toggle tab Post/Comment, vertical timeline stream, diff viewer BEFORE vs AFTER, Fira Code monospace |

---

## 📊 RINGKASAN TOTAL HALAMAN

| Grup | Jumlah | Halaman |
|------|--------|---------|
| 🔐 Auth | 2 | Register, Login |
| 🌐 Public/Explore | 8 | Home Feed, Detail Post, Search, Filter Tag, Filter Category, Trending, Leaderboard, All Tags |
| 👤 User (Login) | 8 | Create Post, Edit Post, My Posts, Bookmarks, Notifications, Profile Settings, Public Profile, My Badges |
| 🛡️ Moderator | 5 | Report Queue, Action Log, Ban Management, Tag/Category CRUD, Edit History |
| ⚙️ Admin | 6 | Dashboard, Tag/Category CRUD, User Directory, Role Management, Badge Master, Audit Timeline |
| **TOTAL** | **29** | **—** |

---

## 🗂️ Struktur Folder Frontend yang Direkomendasikan

```
src/
├── components/
│   ├── ui/
│   │   ├── button.tsx                  // Komponen primitif tombol (Tema Aksen Emas #D4AF37)
│   │   ├── card.tsx                    // Komponen primitif pembungkus kartu (Obsidian Black #161618)
│   │   ├── input.tsx                   // Komponen primitif kolom input tekstual form
│   │   ├── table.tsx                   // Komponen primitif rendering grid tabel data tabular
│   │   ├── avatar.tsx                  // Komponen primitif bingkai foto profil pengguna
│   │   ├── badge.tsx                   // Komponen primitif label kecil penanda status / tag
│   │   ├── tabs.tsx                    // Komponen primitif pengontrol navigasi tab internal halaman
│   │   └── dialog.tsx                  // Komponen primitif modal alert popup / overlay box
│   └── shared/
│       ├── StatusBadge.tsx             // Render teks status terstandardisasi seperti 'SYSTEM.ONLINE', 'MUTED'
│       ├── TechnicalTimestamp.tsx      // Mengonversi waktu audit/UUID menggunakan font monospace Fira Code
│       └── LoadingSpinner.tsx          // Komponen indikator animasi loading berputar untuk fetch data global
│
├── features/
│   ├── Auth/
│   │   ├── F1_Register/
│   │   │   ├── api/
│   │   │   │   └── index.ts            // Request Axios / fetch pendaftaran akun baru ke Laravel Sanctum
│   │   │   ├── components/
│   │   │   │   ├── RegisterForm.tsx    // Formulir antarmuka isian pendaftaran (Dumb UI View)
│   │   │   │   └── TermsCheckbox.tsx   // Elemen checkbox persetujuan syarat dan ketentuan sistem
│   │   │   └── types/
│   │   │       └── index.ts            // Definisi objek TypeScript RegisterPayload dan RegisterResponse
│   │   ├── F2_Login/
│   │   │   ├── api/
│   │   │   │   └── index.ts            // Handler autentikasi kredensial untuk mendapatkan access Bearer Token
│   │   │   ├── components/
│   │   │   │   └── LoginForm.tsx       // Formulir input email dan password login (Dumb UI View)
│   │   │   └── types/
│   │   │       └── index.ts            // Kontrak tipe data payload pengiriman login user
│   │   └── F3_Logout/
│   │       ├── api/
│   │       │   └── index.ts            // Request penghancuran token sesi aktif di server backend Laravel
│   │       ├── components/
│   │       │   └── LogoutButton.tsx    // Aksi trigger tombol keluar aman dari sistem aplikasi
│   │       └── types/
│   │           └── index.ts            // Model skema respons penutupan sesi login
│   │
│   ├── Common/
│   │   ├── F4_SearchPost/
│   │   │   ├── api/
│   │   │   │   └── index.ts            // Integrasi query pencarian teks penuh (full-text search) ke database
│   │   │   ├── components/
│   │   │   │   └── SearchBar.tsx       // Komponen kotak bar pencarian utama dengan ikon kaca pembesar
│   │   │   └── types/
│   │   │       └── index.ts            // Interface parameter pencarian kata kunci postingan
│   │   ├── F5_FilterTag/
│   │   │   ├── api/
│   │   │   │   └── index.ts            // Endpoint penarik koleksi post terfilter berdasarkan parameter tag
│   │   │   ├── components/
│   │   │   │   └── TagBadgeGroup.tsx   // Komponen baris horizontal penampung koleksi tag terikat
│   │   │   └── types/
│   │   │       └── index.ts            // Definisi tipe data id tag filter
│   │   ├── F6_FilterCategory/
│   │   │   ├── api/
│   │   │   │   └── index.ts            // Endpoint penarik daftar pertanyaan berdasarkan rumpun kategori
│   │   │   ├── components/
│   │   │   │   └── CategoryList.tsx    // Komponen daftar navigasi list kategori untuk area sidebar
│   │   │   └── types/
│   │   │       └── index.ts            // Aturan tipe struktur data objek kategori master
│   │   └── F7_TrendingPopularPost/
│   │       ├── api/
│   │       │   └── index.ts            // Mengambil peringkat metrik post paling banyak di-vote dan dilihat
│   │       ├── components/
│   │       │   └── TrendingSidebar.tsx // Panel informasi samping visual pembungkus daftar postingan viral
│   │       └── types/
│   │           └── index.ts            // Interface skema urutan statistik data trending
│   │
│   ├── Admin/
│   │   ├── F8_AdminDashboard/
│   │   │   ├── api/
│   │   │   │   └── index.ts            // Fetch total data aggregat user, post, dan laporan masuk untuk dashboard
│   │   │   ├── components/
│   │   │   │   ├── AnalyticsChart.tsx  // Komponen grafik performa keaktifan data statistik komunitas
│   │   │   │   └── SystemToggleCard.tsx// Kartu panel sakelar kendali pembatasan pendaftaran sistem (Lockdown)
│   │   │   └── types/
│   │   │       └── index.ts            // Interface struktur ringkasan statistik admin
│   │   ├── F9_UserDirectoryControl/
│   │   │   ├── api/
│   │   │   │   └── index.ts            // Mengambil keseluruhan data daftar pengguna terdaftar dari sistem
│   │   │   ├── components/
│   │   │   │   └── UserTable.tsx       // Komponen tabel direktori akun dilengkapi filter role dan status
│   │   │   └── types/
│   │   │       └── index.ts            // Definisi interface baris data user manajemen
│   │   ├── F10_CategoryMaster/
│   │   │   ├── api/
│   │   │   │   └── index.ts            // Eksekusi fungsi CRUD modifikasi entitas master kategori aplikasi
│   │   │   ├── components/
│   │   │   │   └── CategoryFormModal.tsx// Jendela pop-up pengisian tambah dan sunting nama kategori master
│   │   │   └── types/
│   │   │       └── index.ts            // Tipe data payload pembentukan kategori baru
│   │   ├── F11_BadgeMaster/
│   │   │   ├── api/
│   │   │   │   └── index.ts            // Operasi manajemen pembuatan dan penghapusan lencana penghargaan baru
│   │   │   ├── components/
│   │   │   │   └── BadgeFormModal.tsx  // Form pengunggahan ikon lencana serta penentuan syarat poin minimum
│   │   │   └── types/
│   │   │       └── index.ts            // Interface syarat kualifikasi data master badge
│   │   └── F12_TagMaster/
│   │       ├── api/
│   │       │   └── index.ts            // Operasi pengelolaan penambahan tag baru beserta konfigurasi warna hex
│   │       ├── components/
│   │       │   └── TagFormModal.tsx    // Input form master penciptaan tag lengkap dengan pemilih warna hex asli
│   │       └── types/
│   │           └── index.ts            // Aturan tipe data hexadecimal dan nama entitas tag
│   │
│   ├── Moderator/
│   │   ├── F13_ReportQueue/
│   │   │   ├── api/
│   │   │   │   └── index.ts            // Fetch antrean komplain laporan pelanggaran postingan/komentar dari user
│   │   │   ├── components/
│   │   │   │   ├── ReportListRow.tsx   // Komponen baris laporan aktif bermuatan detail pelanggar konten
│   │   │   │   └── ActionReasonModal.tsx// Jendela konfirmasi penindakan laporan disertai pengisian opsi alasan
│   │   │   └── types/
│   │   │       └── index.ts            // Model data pelaporan tiket komplain komunitas
│   │   ├── F14_ModeratorActionLog/
│   │   │   ├── api/
│   │   │   │   └── index.ts            // Penarik data rekam jejak riwayat putusan tindakan seluruh moderator
│   │   │   ├── components/
│   │   │   │   └── ActionLogTable.tsx  // Tabel data rekam jejak penalti postingan dan pembersihan spam
│   │   │   └── types/
│   │   │       └── index.ts            // Interface log baris history keputusan moderator
│   │   └── F15_UserBanSanction/
│   │       ├── api/
│   │       │   └── index.ts            // Hit API penjatuhan sanksi pembekuan/banned akun pengguna bermasalah
│   │       ├── components/
│   │       │   └── BanControlForm.tsx  // Formulir pemilihan jenis suspensi beserta penentu tenggat durasi waktu
│   │       └── types/
│   │           └── index.ts            // Payload skema pembekuan akses user terhukum
│   │
│   └── User/
│       ├── F16_Post/
│       │   ├── api/
│       │   │   └── index.ts            // Pendekatan Makro: Sentralisasi CRUD data postingan pertanyaan utama
│       │   ├── components/
│       │   │   ├── CreatePostForm.tsx  // Komponen area form editor input pembuatan topik diskusi baru
│       │   │   ├── EditPostForm.tsx    // Komponen form koreksi konten pertanyaan wajib melampirkan alasan edit
│       │   │   └── PostCardItem.tsx    // Tampilan card utama penampil konten ringkasan postingan pada feed publik
│       │   └── types/
│       │       └── index.ts            // Interface payload struktur konten teks postingan dan id pembuat
│       ├── F17_Comment/
│       │   ├── api/
│       │   │   └── index.ts            // Pendekatan Makro: Sentralisasi CRUD data komentar dan sub-balasan opini
│       │   ├── components/
│       │   │   ├── CommentList.tsx     // Area render baris pohon kumpulan komentar di bawah suatu pertanyaan
│       │   │   └── ReplyForm.tsx       // Kotak input teks melayang mini khusus untuk membalas baris komentar orang lain
│       │   └── types/
│       │       └── index.ts            // Interface data node komentar berwujud relasi bertingkat
│       ├── F18_Bookmark/
│       │   ├── api/
│       │   │   └── index.ts            // Mengelola penyimpanan interaksi penandaan post favorit ke akun pribadi
│       │   ├── components/
│       │   │   └── BookmarkToggle.tsx  // Komponen tombol berbentuk pin penanda simpan postingan terarsivasi
│       │   └── types/
│       │       └── index.ts            // Aturan tipe data relasi id user dan id post bookmark
│       ├── F19_Notification/
│       │   ├── api/
│       │   │   └── index.ts            // Polling fetch daftar pemberitahuan interaksi balasan/upvote terbaru
│       │   ├── components/
│       │   │   └── NotificationRow.tsx // Baris notifikasi pembawa ringkasan aktivitas masuk ke akun pengguna
│       │   └── types/
│       │       └── index.ts            // Interface objek metadata notifikasi sistem
│       ├── F20_Leaderboard/
│       │   ├── api/
│       │   │   └── index.ts            // Mengambil daftar peringkat profil user berlandaskan raihan poin tertinggi
│       │   ├── components/
│       │   │   ├── LeaderboardTable.tsx// Komponen tabel baris panjang pemegang posisi ranking reputasi komunitas
│       │   │   └── RankCard.tsx        // Desain komponen bento podium mini istimewa bagi top 3 master suhu
│       │   └── types/
│       │       └── index.ts            // Skema urutan ranking poin user global
│       ├── F21_TagList/
│       │   ├── api/
│       │   │   └── index.ts            // Ambil seluruh daftar tag terdaftar berserta jumlah statistik akumulasi pemakaian
│       │   ├── components/
│       │   │   └── TagGridItem.tsx     // Kotak bento mini visual penjelas deskripsi satu tag spesifik
│       │   └── types/
│       │       └── index.ts            // Aturan interface perhitungan total usage tag
│       ├── F22_MyPosts/
│       │   ├── api/
│       │   │   └── index.ts            // Fetch muatan kompilasi data arsip seluruh post milik user terotentikasi
│       │   ├── components/
│       │   │   └── PersonalPostRow.tsx // Baris ringkas penampilan rekam jejak status pertanyaan pribadi
│       │   └── types/
│       │       └── index.ts            // Skema data postingan internal kepemilikan user
│       ├── F23_PublicProfile/
│       │   ├── api/
│       │   │   └── index.ts            // Penarik informasi biodata publik user lain menggunakan kunci username
│       │   ├── components/
│       │   │   └── ProfileHeader.tsx   // Komponen banner atas visual profil pembawa nama, bio, dan total poin
│       │   └── types/
│       │       └── index.ts            // Model pemetaan profil user eksternal
│       ├── F24_ModTagCategory/
│       │   ├── api/
│       │   │   └── index.ts            // Aksi intervensi pemindahan rumpun kategori/tag post salah sasaran oleh mod
│       │   ├── components/
│       │   │   └── ModQuickAction.tsx  // Dropdown drop-panel aksi cepat pembenahan klasifikasi konten tulisan
│       │   └── types/
│       │       └── index.ts            // Struktur data payload mutasi taksonomi konten post
│       ├── F25_EditHistory/
│       │   ├── api/
│       │   │   └── index.ts            // Mengambil data log riwayat versi perbaikan teks tulisan dari masa ke masa
│       │   ├── components/
│       │   │   └── HistoryDiffLog.tsx  // Komponen komparasi visual pelacak perbedaan teks sebelum dan sesudah diedit
│       │   └── types/
│       │       └── index.ts            // Interface data histori perbaikan teks revisi
│       ├── F26_RoleManagement/
│       │   ├── api/
│       │   │   └── index.ts            // Hit API mutasi perubahan kasta jabatan akun (Reguler, Mod, Administrator)
│       │   ├── components/
│       │   │   └── RoleSelectDropdown.tsx// Komponen selektor aman pengubah tingkat otorisasi kedudukan user
│       │   └── types/
│       │       └── index.ts            // Model payload pengangkatan role jabatan akun baru
│       ├── F27_AuditTimeline/
│       │   ├── api/
│       │   │   └── index.ts            // Tarik seluruh runtutan log audit krusial sistem demi transparansi internal
│       │   ├── components/
│       │   │   └── TimelineFeed.tsx    // Garis linimasa vertikal penunjuk urutan eksekusi command penting admin
│       │   └── types/
│       │       └── index.ts            // Interface baris riwayat data audit trail sistem
│       ├── F28_ProfileSettings/
│       │   ├── api/
│       │   │   └── index.ts            // Pembaruan data pribadi sensitif seperti bio, kata sandi, dan foto avatar baru
│       │   ├── components/
│       │   │   └── SettingsForm.tsx    // Formulir isian pengubahan rahasia data identitas diri user sendiri
│       │   └── types/
│       │       └── index.ts            // Model payload pembaharuan profile user terotentikasi
│       └── F29_BadgeAchievement/
│           ├── api/
│           │   └── index.ts            // Tarik katalog seluruh medali lencana penghargaan yang berhasil diklaim user
│           ├── components/
│           │   └── BadgeGridDisplay.tsx// Lemari pameran grid visual koleksi pin lencana emas pencapaian pribadi
│           └── types/
│               └── index.ts            // Interface jalinan data lencana pencapaian koleksi user
│
├── pages/
│   ├── Auth/
│   │   ├── RegisterPage.tsx            // PAGE 1 - Smart Container mengomandoi validasi form alur F1_Register
│   │   └── LoginPage.tsx               // PAGE 2 - Smart Container mengomandoi otentikasi data alur F2_Login
│   ├── Public/
│   │   ├── HomePage.tsx                // PAGE 3 - Smart Container pusat muatan kompilasi feed utama beranda publik
│   │   ├── PostDetailPage.tsx          // PAGE 4 - Smart Container pusat rendering detail thread pertanyaan F16 & F17
│   │   ├── SearchPage.tsx              // PAGE 5 - Smart Container orkestrasi hasil filter pencarian teks F4_SearchPost
│   │   ├── TagFilterPage.tsx           // PAGE 6 - Smart Container penampil list data post terikat dengan F5_FilterTag
│   │   ├── CategoryFilterPage.tsx      // PAGE 7 - Smart Container penampil list data post terikat dengan F6_FilterCategory
│   │   ├── TrendingPage.tsx            // PAGE 8 - Smart Container penampil kompilasi thread post terhangat F7_Trending
│   │   ├── LeaderboardPage.tsx         // PAGE 9 - Smart Container orkestrasi data ranking teratas suhu F20_Leaderboard
│   │   └── TagsListPage.tsx            // PAGE 10 - Smart Container halaman katalog ensiklopedia tag komunitas F21_TagList
│   ├── User/
│   │   ├── CreatePostPage.tsx          // PAGE 11 - Smart Container pengarah submit posting teks pertanyaan baru F16_Post
│   │   ├── EditPostPage.tsx            // PAGE 12 - Smart Container pengarah revisi teks thread pertanyaan lama F16_Post
│   │   ├── MyPostsPage.tsx             // PAGE 13 - Smart Container penampil arsip history pertanyaan sendiri F22_MyPosts
│   │   ├── BookmarksPage.tsx           // PAGE 14 - Smart Container penampil koleksi thread tersimpan user F18_Bookmark
│   │   ├── NotificationsPage.tsx       // PAGE 15 - Smart Container pusat kendali masuknya pemberitahuan F19_Notification
│   │   ├── ProfileSettingsPage.tsx     // PAGE 16 - Smart Container pengelola form ubah biodata akun F28_ProfileSettings
│   │   ├── PublicProfilePage.tsx       // PAGE 17 - Smart Container portofolio pameran profil eksternal user F23_PublicProfile
│   │   └── MyBadgesPage.tsx            // PAGE 18 - Smart Container galeri raihan lencana prestasi personal F29_Badge
│   ├── Moderator/
│   │   ├── ReportQueuePage.tsx         // PAGE 19 - Smart Container panel antrean tiket aduan pelanggaran F13_ReportQueue
│   │   ├── ActionLogPage.tsx           // PAGE 20 - Smart Container monitoring rekam jejak keputusan moderasi F14_Log
│   │   ├── BanManagementPage.tsx       // PAGE 21 - Smart Container eksekusi isolasi pembekuan akses user F15_UserBan
│   │   ├── ModTagCategoryPage.tsx      // PAGE 22 - Smart Container panel cepat pembenahan klasifikasi tulisan F24_ModTag
│   │   └── EditHistoryPage.tsx         // PAGE 23 - Smart Container audit pelacak perubahan teks postingan user F25_History
│   └── Admin/
│       ├── AdminDashboardPage.tsx      // PAGE 24 - Smart Container pengawas statistik aggregat utama bento grid F8_Admin
│       ├── TagCategoryPage.tsx         // PAGE 25 - Smart Container manajemen master data taksonomi F10_Category & F12_Tag
│       ├── UserDirectoryPage.tsx       // PAGE 26 - Smart Container otoritas kontrol seluruh akun user terdaftar F9_User
│       ├── RoleManagementPage.tsx      // PAGE 27 - Smart Container otoritas penentu kenaikan kasta/jabatan user F26_Role
│       ├── BadgeMasterPage.tsx         // PAGE 28 - Smart Container pabrik cetak pembuatan master reward medali F11_Badge
│       └── AuditTimelinePage.tsx       // PAGE 29 - Smart Container pusat rekam jejak audit komando krusial sistem F27_Audit
│
├── layouts/
│   ├── PublicLayout.tsx                // Kerangka arsitektur navigasi header atas bagi pengunjung tamu publik
│   ├── UserLayout.tsx                  // Kerangka arsitektur tata letak sidebar bagi pengguna terautentikasi sistem
│   ├── ModeratorLayout.tsx             // Kerangka arsitektur sidebar dashboard pemrosesan penyelesaian tiket aduan
│   └── AdminLayout.tsx                 // Kerangka arsitektur kaku bertema industrial luxury dengan fixed sidebar 250px
│
├── routes/
│   └── AppRouter.tsx                   // Sentralisasi konfigurasi pemetaan rute React Router v6 untuk 29 halaman utama
│
├── types/
│   └── index.ts                        // Penampung tunggal definisi tipe data core global shared typescript interfaces
│
├── index.css                           // File konfigurasi penimpaan variabel warna inti Tailwind v4 (Obsidian Black & Gold Hex)
└── main.tsx                            // Titik masuk (entry point) eksekusi utama aplikasi frontend bundler Vite
---

> [!IMPORTANT]
> **4 hal yang membutuhkan penyesuaian backend sebelum frontend bisa selesai:**
> 1. **PAGE 24** — Buat endpoint `GET /admin/stats/overview` dan `GET /admin/stats/points-summary`
> 2. **PAGE 29** — Buat route `GET /admin/comments/{comment}/history` di admin prefix
> 3. **Semua audit** — Migration: tambah kolom `reason VARCHAR(255) NULLABLE` ke `comment_edit_history`
> 4. **Modal Lockdown** — Buat endpoint `POST /admin/system/toggle-registration`
