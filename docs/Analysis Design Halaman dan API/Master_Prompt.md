# 🧠 MASTER PROMPT — MauTanyaSuhu (Full Application Design & Development Brief)

---

## CONTEXT & PROJECT IDENTITY

You are building **MauTanyaSuhu** — a full-stack community Q&A web application similar to Stack Overflow. The platform allows users to post questions, answer, vote, comment, follow each other, earn reputation points, unlock badges, and be managed by moderators and administrators.

**Application Type**: Stack Overflow-style Q&A Platform
**Primary Language**: Indonesian (Bahasa Indonesia) UI labels, with English variable/column names
**Target Viewport**: Desktop-first (1440px–1920px widescreen), with mobile responsive fallback

---

## TECH STACK

| Layer | Technology |
|-------|------------|
| **Backend** | Laravel (PHP) — Modular Architecture (`Modules/` folder per feature) |
| **Frontend** | React + TypeScript + Vite (`src/` folder structure) |
| **Database** | PostgreSQL — All primary keys are UUID (`uuid_generate_v4()`) |
| **Authentication** | Laravel Sanctum (Bearer Token) |
| **Styling** | Vanilla CSS (no Tailwind) |
| **Routing (FE)** | React Router v6 |
| **State Management** | Context API or Zustand (per feature) |

---

## GLOBAL DESIGN SYSTEM

Apply these design tokens **consistently across all pages**:

```
COLORS:
  --bg-primary:       #0B0B0C   /* Obsidian black — main canvas */
  --bg-secondary:     #161618   /* Dark slate-gray — card/box containers */
  --border-subtle:    1px solid #2A2A2C  /* Steel container rule */
  --accent-gold:      #D4AF37   /* Burnished gold — positive data, CTAs */
  --accent-red:       #E53E3E   /* Warning red — bans, errors, critical alerts */
  --accent-blue:      #3B82F6   /* Steel blue — secondary data, charts */
  --text-primary:     #F0F0F0   /* Near-white */
  --text-secondary:   #9CA3AF   /* Muted gray */
  --text-mono:        'Fira Code', monospace  /* For UUIDs, code, diff views */

TYPOGRAPHY:
  Font Family: 'Inter', 'Outfit', or 'Roboto' — import from Google Fonts
  Heading: Bold 700, tracked tight
  Body: Regular 400
  Mono: Fira Code — for UUIDs, timestamps, diff comparisons

SPACING: 8px base grid (8, 16, 24, 32, 48, 64)
BORDER RADIUS: 6px for cards, 4px for inputs, 999px for pill badges
TRANSITIONS: 200ms ease for hover states, 300ms for drawer/modal open
```

---

## DATABASE SCHEMA (PostgreSQL — Complete Reference)

> All IDs are UUID. Use this schema as the single source of truth for all column names.

```sql
-- USERS
users (id uuid PK, username varchar(100), email varchar(255), password_hash varchar(255),
       avatar_url varchar(500) nullable, bio text nullable,
       reputation_points int default 0, level int default 1,
       is_banned boolean default false, created_at, updated_at)

-- ROLES & PERMISSIONS
roles (id uuid PK, name varchar(50) unique, permissions json nullable, created_at)
user_roles (id uuid PK, user_id uuid FK→users, role_id uuid FK→roles, assigned_at)

-- TAGS & CATEGORIES
tags (id uuid PK, name varchar(50) unique, slug varchar(60) unique,
      color varchar(7) nullable, usage_count int default 0, created_at)
categories (id uuid PK, name varchar(100), slug varchar(120) unique,
            description text nullable, parent_id uuid nullable FK→categories self-ref, created_at)

-- BADGES
badges (id uuid PK, name varchar(100) unique, description text nullable,
        icon_url varchar(500) nullable, tier varchar(20) nullable,
        condition_type varchar(50) nullable, condition_value int nullable, created_at)
user_badges (id uuid PK, user_id uuid FK→users, badge_id uuid FK→badges, awarded_at)

-- POSTS
posts (id uuid PK, user_id uuid FK→users, category_id uuid FK→categories,
       title varchar(300), body text, status varchar(20) default 'draft',
       view_count int default 0, vote_score int default 0,
       is_answered boolean default false,
       accepted_answer_id uuid nullable, created_at, updated_at)
post_tags (post_id uuid FK→posts, tag_id uuid FK→tags)  -- pivot

-- COMMENTS
comments (id uuid PK, post_id uuid FK→posts, user_id uuid FK→users,
          parent_id uuid nullable FK→comments self-ref, body text,
          created_at, updated_at)

-- EDIT HISTORY
post_edit_history (id uuid PK, post_id uuid FK→posts, edited_by uuid FK→users,
                   body_before text nullable, body_after text nullable,
                   reason varchar(255) nullable, edited_at timestamp)
comment_edit_history (id uuid PK, comment_id uuid FK→comments, edited_by uuid FK→users,
                      body_before text nullable, body_after text nullable,
                      edited_at timestamp)
-- ⚠️ NOTE: comment_edit_history does NOT have a 'reason' column currently

-- INTERACTIONS
votes (id uuid PK, user_id uuid FK→users, votable_id uuid, votable_type varchar, value int)
likes (id uuid PK, user_id uuid FK→users, likeable_id uuid, likeable_type varchar)
bookmarks (id uuid PK, user_id uuid FK→users, post_id uuid FK→posts)
follows (id uuid PK, follower_id uuid FK→users, following_id uuid FK→users)

-- GAMIFICATION
points_log (id uuid PK, user_id uuid FK→users, points int, action_type varchar(50),
            reference_id uuid nullable, description varchar(255) nullable, created_at)

-- NOTIFICATIONS
notifications (id uuid PK, user_id uuid FK→users, actor_id uuid nullable FK→users,
               type varchar(50), reference_id uuid nullable,
               reference_type varchar(20) nullable, is_read boolean default false, created_at)

-- REPORTS & MODERATION
reports (id uuid PK, reporter_id uuid FK→users, target_id uuid, target_type varchar(20),
         reason varchar(100), description text nullable, status varchar(20) default 'pending',
         resolved_by uuid nullable FK→users, created_at, resolved_at nullable)
moderation_logs (id uuid PK, moderator_id uuid FK→users, target_user_id uuid nullable FK→users,
                 action_type varchar(50), reason varchar(255), notes text nullable, created_at)
```

---

## BACKEND API ROUTES (Complete Reference)

```
PUBLIC (no auth required):
  POST   /api/auth/register
  POST   /api/auth/login
  GET    /api/posts                         — list all posts
  GET    /api/posts/{post}                  — post detail
  GET    /api/explore/search?q=             — search posts
  GET    /api/explore/tags                  — all tags list
  GET    /api/explore/tag/{slug}            — filter by tag
  GET    /api/explore/category/{slug}       — filter by category
  GET    /api/explore/trending              — trending posts
  GET    /api/explore/leaderboard           — user leaderboard

PROTECTED (requires Sanctum Bearer Token):
  POST   /api/auth/logout
  GET    /api/settings/profile
  PUT    /api/settings/profile
  PUT    /api/settings/password
  GET    /api/me/posts
  GET    /api/me/badges
  POST   /api/posts
  PUT    /api/posts/{post}
  DELETE /api/posts/{post}
  GET    /api/posts/{post}/comments
  POST   /api/posts/{post}/comments
  PUT    /api/posts/{post}/comments/{comment}
  POST   /api/posts/{post}/comments/{comment}/replies
  PUT    /api/posts/{post}/comments/{comment}/replies/{reply}
  POST   /api/posts/{post}/comments/{comment}/accept
  POST   /api/tags                          — user can suggest tags
  GET    /api/notifications
  PATCH  /api/notifications/mark-all-read
  PATCH  /api/notifications/{id}/read
  POST   /api/users/{id}/follow
  GET    /api/users/{id}/followers
  GET    /api/users/{id}/following
  POST   /api/likes/toggle
  POST   /api/bookmarks/toggle
  GET    /api/bookmarks
  POST   /api/votes

MODERATOR (role: moderator OR admin):
  GET/POST/PUT/DELETE /api/moderator/categories
  GET/POST/PUT/DELETE /api/moderator/tags
  GET/POST/PUT/DELETE /api/moderator/badges  (no show)
  GET    /api/moderator/reports
  GET    /api/moderator/reports/{id}
  PUT    /api/moderator/reports/{id}
  GET    /api/moderator/logs
  GET    /api/moderator/posts/{post}/history
  GET    /api/moderator/comments/{comment}/history
  GET    /api/moderator/bans
  POST   /api/moderator/bans/{id}/ban
  POST   /api/moderator/bans/{id}/unban

ADMIN (role: admin only):
  GET/POST/PUT/DELETE /api/admin/roles
  GET/POST/PUT/DELETE /api/admin/categories
  GET/POST/PUT/DELETE /api/admin/tags  (no show)
  GET/POST/PUT/DELETE /api/admin/badges  (no show)
  GET    /api/admin/users
  GET    /api/admin/users/{id}
  PUT    /api/admin/users/{id}/role
  PUT    /api/admin/users/{id}/profile
  PUT    /api/admin/users/{id}/reset-password
  GET    /api/admin/posts/{post}/history
  GET    /api/admin/bans
  POST   /api/admin/bans/{id}/ban
  POST   /api/admin/bans/{id}/unban

ENDPOINTS TO BE CREATED (not yet in backend):
  GET    /api/admin/stats/overview          — chart data: posts + registrations per day
  GET    /api/admin/stats/points-summary    — SUM aggregate from points_log
  GET    /api/admin/comments/{comment}/history  — admin-level comment history
  POST   /api/admin/system/toggle-registration  — emergency lockdown
```

---

## COMPLETE PAGE MAP (29 Pages)

---

### 🔐 AUTH GROUP

**PAGE 1 — Register** | Route: `/register`
- Endpoint: `POST /api/auth/register`
- Fields: `username`, `email`, `password`, `password_confirmation`
- UI: Full-page dark form, validation inline, gold CTA button, link to login

**PAGE 2 — Login** | Route: `/login`
- Endpoint: `POST /api/auth/login`
- Fields: `email`, `password`
- UI: Dark centered form, remember me checkbox, link to register, error toast

---

### 🌐 PUBLIC GROUP

**PAGE 3 — Home / Feed** | Route: `/`
- Endpoint: `GET /api/posts`
- Tables: `posts`, `users`, `categories`, `post_tags`, `tags`
- UI: Left sidebar (categories nav), center post list cards, right trending sidebar
- Each card shows: `title`, body excerpt, `vote_score`, `view_count`, `is_answered` badge, author username, tags (with color chips), category name, `created_at`

**PAGE 4 — Detail Post / Q&A Thread** | Route: `/posts/:id`
- Endpoints: `GET /api/posts/{post}`, `GET /api/posts/{post}/comments`
- Tables: `posts`, `comments`, `votes`, `likes`, `bookmarks`, `users`
- UI: Full post body with vote buttons (up/down), accept answer button (owner only), comment section with nested replies, bookmark toggle, report button
- Interactions: Vote `POST /api/votes`, Like `POST /api/likes/toggle`, Bookmark `POST /api/bookmarks/toggle`, Accept `POST /api/posts/{post}/comments/{comment}/accept`

**PAGE 5 — Search** | Route: `/search?q={keyword}`
- Endpoint: `GET /api/explore/search?q=`
- Tables: `posts`, `tags`, `categories`
- UI: Search bar (persistent), result cards with keyword highlight, filter chips

**PAGE 6 — Filter by Tag** | Route: `/tags/:slug`
- Endpoint: `GET /api/explore/tag/{slug}`
- Tables: `tags`, `post_tags`, `posts`
- UI: Tag header (name, `tags.color` swatch, `usage_count`), paginated post list

**PAGE 7 — Filter by Category** | Route: `/categories/:slug`
- Endpoint: `GET /api/explore/category/{slug}`
- Tables: `categories`, `posts`
- UI: Category title, breadcrumb (parent → child via `parent_id`), post list

**PAGE 8 — Trending & Popular** | Route: `/trending`
- Endpoint: `GET /api/explore/trending`
- Tables: `posts`, `votes`
- UI: Ranked post list, "🔥 Hot" badge, `view_count`, `vote_score` prominently shown

**PAGE 9 — Leaderboard** | Route: `/leaderboard`
- Endpoint: `GET /api/explore/leaderboard?limit=10`
- Tables: `users` ordered by `reputation_points DESC`
- UI: Ranking table with rank number, avatar, `username`, `reputation_points`, `level` badge

**PAGE 10 — All Tags** | Route: `/tags`
- Endpoint: `GET /api/explore/tags`
- Tables: `tags`
- UI: Grid of tag chips — each chip shows `tags.color` as background, `tags.name`, `tags.usage_count`

---

### 👤 USER GROUP (Login Required)

**PAGE 11 — Create Post** | Route: `/posts/create`
- Endpoint: `POST /api/posts`
- Tables: `posts`, `post_tags`, `tags`
- Fields: `title`, `body` (rich text editor), `category_id` (dropdown), `tag_ids[]` (multi-select), `status` (draft/publish)
- UI: Two-column — editor left, preview right; tag selector with color chips

**PAGE 12 — Edit Post** | Route: `/posts/:id/edit`
- Endpoint: `PUT /api/posts/{post}`
- Tables: `posts`, `post_edit_history`
- Fields: Same as create + `reason` field (mandatory, saved to `post_edit_history.reason`)
- Note: Each save auto-records `body_before`, `body_after`, `edited_by` in `post_edit_history`

**PAGE 13 — My Posts** | Route: `/me/posts`
- Endpoint: `GET /api/me/posts`
- Tables: `posts` filtered by `user_id = current user`
- UI: Table/list of own posts, status badge (draft/published/answered), inline edit/delete actions

**PAGE 14 — Bookmarks** | Route: `/bookmarks`
- Endpoint: `GET /api/bookmarks`
- Tables: `bookmarks`, `posts`
- UI: List of bookmarked posts, remove bookmark button per item

**PAGE 15 — Notifications** | Route: `/notifications`
- Endpoints: `GET /api/notifications`, `PATCH /api/notifications/mark-all-read`, `PATCH /api/notifications/{id}/read`
- Tables: `notifications` (`user_id`, `actor_id`, `type`, `reference_id`, `is_read`, `created_at`)
- UI: Grouped notification feed, unread count badge, "Mark all read" button, `is_read` visual indicator

**PAGE 16 — Profile Settings** | Route: `/settings/profile`
- Endpoints: `GET /api/settings/profile`, `PUT /api/settings/profile`, `PUT /api/settings/password`
- Tables: `users` (`username`, `email`, `avatar_url`, `bio`)
- UI: Tab 1 — Edit profile form (username, bio, avatar upload); Tab 2 — Change password (old_password, new_password, confirm)

**PAGE 17 — Public User Profile** | Route: `/users/:id`
- Endpoints: `GET /api/users/{id}/followers`, `GET /api/users/{id}/following`, `POST /api/users/{id}/follow`
- Tables: `users`, `follows`, `user_badges`, `badges`
- UI: Profile header (avatar, username, bio, `reputation_points`, `level`), follower/following count, follow/unfollow button, badge showcase grid, post history tab

**PAGE 18 — My Badges** | Route: `/me/badges`
- Endpoint: `GET /api/me/badges`
- Tables: `user_badges`, `badges` (`name`, `description`, `icon_url`, `tier`, `condition_type`, `condition_value`)
- UI: Badge cards grid — each card: `icon_url` image, `name`, tier pill (bronze/silver/gold/platinum), `description`, unlock `condition_value`

---

### 🛡️ MODERATOR GROUP (Role: moderator OR admin)

**PAGE 19 — Report Queue** | Route: `/moderator/reports`
- Endpoints: `GET /api/moderator/reports`, `PUT /api/moderator/reports/{id}`
- Tables: `reports` (`reporter_id`, `target_id`, `target_type`, `reason`, `description`, `status`, `resolved_by`, `resolved_at`)
- UI: Paginated report table, filter by `status` (pending/resolved/rejected), detail modal, Resolve/Reject action buttons

**PAGE 20 — Moderation Action Log** | Route: `/moderator/logs`
- Endpoint: `GET /api/moderator/logs`
- Tables: `moderation_logs` (`moderator_id`, `target_user_id`, `action_type`, `reason`, `notes`, `created_at`)
- UI: Chronological log table, filter by `action_type` (ban/unban/warning), moderator UUID linkable to user profile

**PAGE 21 — Ban Management** | Route: `/moderator/bans`
- Endpoints: `GET /api/moderator/bans`, `POST /api/moderator/bans/{id}/ban`, `POST /api/moderator/bans/{id}/unban`
- Tables: `users` (`is_banned`), `moderation_logs`
- UI: Banned users list, unban button per row, ban form with `reason` text input (writes to `moderation_logs`)

**PAGE 22 — Moderator Tag & Category CRUD** | Route: `/moderator/tags`
- Endpoints: `GET/POST/PUT/DELETE /api/moderator/tags`, `GET/POST/PUT/DELETE /api/moderator/categories`
- Tables: `tags`, `categories`
- UI: (Share component with Admin PAGE 25 — same interface, different role route)

**PAGE 23 — Edit History Viewer** | Route: `/moderator/posts/:id/history`
- Endpoints: `GET /api/moderator/posts/{post}/history`, `GET /api/moderator/comments/{comment}/history`
- Tables: `post_edit_history`, `comment_edit_history`
- UI: Timeline of edits, dual-column BEFORE/AFTER diff view in Fira Code monospace, `edited_at` timestamp, `edited_by` UUID

---

### ⚙️ ADMIN GROUP (Role: admin only) — DARK INDUSTRIAL LUXURY DESIGN

> These 6 admin pages share a unified design system: strict 2-column layout with a fixed 250px left sidebar and flex right panel. Theme is dark industrial luxury.

**SHARED ADMIN LAYOUT:**
- Fixed Left Sidebar (250px): Dark `#161618` background, vertical nav links with vector icons
  - "System Overview" → `/admin/dashboard`
  - "Tag & Category Master" → `/admin/tags`
  - "User Directory Control" → `/admin/users`
  - "Content Audit Timeline" → `/admin/audit`
  - "Role Management" → `/admin/roles`
  - "Badge Master" → `/admin/badges`
  - Active link: gold `#D4AF37` left border indicator
- Right Panel: `flex` width, `#0B0B0C` background, 24px padding

---

**PAGE 24 — Admin Dashboard** | Route: `/admin/dashboard`
*(This is the "PAGE 7" from the original design prompt)*

Right panel uses a **4-Quadrant Bento Grid** layout:

> QUADRANT 1 (Top Left — Analytics Chart):
> - Dual-line time-series chart (use Recharts or Chart.js)
> - Line 1 (gold): Post creation count per day — from `posts` grouped by `created_at`
> - Line 2 (steel-blue): New user registrations per day — from `users` grouped by `created_at`
> - X-axis: dates | Y-axis: integer count
> - Requires endpoint: `GET /api/admin/stats/overview` returning `{ posts_over_time: [{date, count}], registrations_over_time: [{date, count}] }`
> - ⚠️ This endpoint does NOT exist yet — must be created in backend

> QUADRANT 2 (Top Right — Points Ledger):
> - Aggregate metrics from `points_log` table:
>   - Total earned: SUM of positive `points_log.points` — displayed in gold
>   - Total deducted: SUM of negative `points_log.points` — displayed in red
>   - Net balance: overall SUM
>   - Breakdown mini-table: group by `points_log.action_type` with sum per type
> - Requires endpoint: `GET /api/admin/stats/points-summary`
> - ⚠️ This endpoint does NOT exist yet — must be created in backend

> QUADRANT 3 (Bottom Left — Top Engaging Posts):
> - Compact table: TOP 5 rows from `posts` ordered by `view_count DESC` then `vote_score DESC`
> - Columns: Post UUID (first 8 chars, monospace), Title (truncated 40 chars), view_count (gold), vote_score (signed), status badge
> - Requires: `GET /api/admin/posts?sort=view_count&limit=5`

> QUADRANT 4 (Bottom Right — Role Shortcuts):
> - 4 action cards navigating to role/user management:
>   - "Manage Roles" → `/admin/roles` (CRUD for `roles` table)
>   - "Assign User Roles" → `/admin/users` (maps `user_roles`: user_id, role_id, assigned_at)
>   - "Banned Users" → `/admin/users?is_banned=true`
>   - "Moderation Logs" → `/moderator/logs`

EMBEDDED MODAL — Emergency Registration Lockdown:
> - Trigger: red "🔴 LOCKDOWN" button in dashboard header
> - Floating modal with `#E53E3E` thick border stroke, dim translucent overlay
> - Title: "⚠ SYSTEM LOCKDOWN CONFIRMATION"
> - Body: "This action will toggle the registration lock. All new account creation will be suspended."
> - Authorization key input (password type, required)
> - Buttons: "Cancel" (gray) | "Confirm Lockdown" (red)
> - Submits: `POST /api/admin/system/toggle-registration` with `{ authorization_key }`
> - ⚠️ This endpoint does NOT exist yet — must be created in backend

---

**PAGE 25 — Tag & Category CRUD** | Route: `/admin/tags`
*(This is the "PAGE 8" from the original design prompt)*

SECTION A — TAGS TABLE (maps to `tags` schema):
> Alternating-row dark data table with columns:
> - "Tag ID": `tags.id` UUID, first 8 chars, Fira Code monospace, gold tint
> - "Name": `tags.name` (max 50 chars)
> - "Slug": `tags.slug` (max 60 chars), steel-gray monospace
> - "Color": filled circle (12px diameter) using `tags.color` as CSS fill + hex string label
> - "Usage Count": `tags.usage_count` integer — gold tint if > 100
> - "Actions": Edit icon (pencil, gold hover) + Delete icon (trash, red hover)
> - Data source: `GET /api/admin/tags`

SECTION B — CATEGORIES TABLE (maps to `categories` schema):
> Second data table below tags section with columns:
> - "Category ID": `categories.id` UUID, 8 chars truncated
> - "Name": `categories.name` (max 100 chars)
> - "Slug": `categories.slug` (max 120 chars)
> - "Description": `categories.description`, truncated 60 chars, full on hover tooltip
> - "Parent": `categories.parent_id` → show parent name or "— Root —" if null
> - "Actions": Edit + Delete
> - ⚠️ IMPORTANT: `categories` does NOT have `color` or `usage_count` columns — do not render them
> - Data source: `GET /api/admin/categories`

EMBEDDED DRAWER — Add New Tag (slides from right):
> - Trigger: "+ Add Tag" button in section header
> - Smooth right-side drawer (300ms slide-in)
> - Input: "Tag Name" → `tags.name` (max 50 chars, required)
> - Input: "Slug" → `tags.slug` (auto-generated from name, editable, max 60 chars)
> - Color Picker: 16-color swatch grid → populates `tags.color` as hex string (e.g. `#D4AF37`), with preview circle
> - Save button: `POST /api/admin/tags` with `{ name, slug, color }`
> - Close (X) button

---

**PAGE 26 — User Directory Control** | Route: `/admin/users`
*(This is the "PAGE 9" from the original design prompt)*

TOP TOOLBAR:
> - Dropdown "Account Status": All / Active (`is_banned=false`) / Banned (`is_banned=true`)
> - Dropdown "Role": All Roles / Admin / Moderator / User
> - Search input: filter by `username` or `email`
> - All map to query params: `GET /api/admin/users?is_banned=&role=&search=`

MASTER USER TABLE (maps to `users` schema):
> Dense data table with columns:
> - "User ID": `users.id` UUID, first 8 chars, Fira Code, gold
> - "Username": `users.username` (max 100 chars)
> - "Email": `users.email` (max 255 chars), smaller font
> - "Reputation": `users.reputation_points` integer — gold if > 1000
> - "Level": `users.level` integer — pill-shaped badge
> - "Status": `users.is_banned` → red "BANNED" pill or green "ACTIVE" pill

EXPANDABLE ROW — Account Sanction Control Panel:
> Clicking any user row expands it vertically to reveal an inline sub-form:
> - "Adjust Reputation": numeric +/- input → `users.reputation_points`
>   → writes via `PUT /api/admin/users/{id}/profile` with `{ reputation_points }`
> - "Is Banned": boolean checkbox → `users.is_banned`
>   → ban: `POST /api/admin/bans/{id}/ban`
>   → unban: `POST /api/admin/bans/{id}/unban`
> - "Sanction Reason": required textarea → logs to `moderation_logs`:
>   - `moderation_logs.moderator_id` = current admin UUID
>   - `moderation_logs.target_user_id` = selected user UUID
>   - `moderation_logs.action_type` = 'ban' | 'unban' | 'warning'
>   - `moderation_logs.reason` = textarea value (max 255 chars)
>   - `moderation_logs.notes` = optional extended notes (nullable)
> - Action buttons: "Apply Ban" (red) | "Apply Unban" (gold) | "Send Warning" (gray) | "Cancel"

---

**PAGE 27 — Role Management** | Route: `/admin/roles`

> CRUD interface for the `roles` table:
> - Table columns: Role UUID, Name (`roles.name`), Permissions (preview of `roles.permissions` JSON), Created At
> - Inline JSON editor for `roles.permissions` (collapsible)
> - Below table: User-Role assignment sub-section (maps `user_roles`: user_id, role_id, assigned_at)
> - Endpoints: `GET/POST/PUT/DELETE /api/admin/roles`

---

**PAGE 28 — Badge Master** | Route: `/admin/badges`

> CRUD interface for the `badges` table:
> - Table columns: Badge UUID, `icon_url` (thumbnail image), `name`, `tier` (pill badge), `condition_type`, `condition_value`, Description
> - Add/Edit form: `name`, `description`, `icon_url` (file upload or URL input), `tier` dropdown (bronze/silver/gold/platinum), `condition_type` text, `condition_value` number
> - Endpoints: `GET/POST/PUT/DELETE /api/admin/badges`

---

**PAGE 29 — Content Audit Timeline** | Route: `/admin/audit`
*(This is the "PAGE 10" from the original design prompt)*

TOP TOOLBAR:
> - Toggle Tab: "Post Edits" | "Comment Edits"
> - Date range picker (from/to) filtering by `edited_at`

VERTICAL TIMELINE STREAM:

FOR POST EDITS (source: `post_edit_history` table):
> Each timeline card renders:
> - Timestamp: `post_edit_history.edited_at` in format `YYYY-MM-DD HH:mm:ss.SSS`, Fira Code, steel-gray
> - Target: "Post ID: {`post_edit_history.post_id`}" — UUID in gold monospace
> - Editor: "Edited By: {`post_edit_history.edited_by`}" — UUID linking to `users.id`
> - Reason: "Reason: {`post_edit_history.reason`}" — italic gray. If null → "— No reason provided —"
> - Diff Box: Dual-column comparison in Fira Code monospace:
>   - LEFT panel "BEFORE" (red-tinted `#1A0C0C` bg): `post_edit_history.body_before`
>   - RIGHT panel "AFTER" (green-tinted `#0C1A0C` bg): `post_edit_history.body_after`
>   - Max 8 lines height, internal scrollbar
> - Data source: `GET /api/admin/posts/{post_id}/history`

FOR COMMENT EDITS (source: `comment_edit_history` table):
> Each timeline card renders:
> - Timestamp: `comment_edit_history.edited_at` same format
> - Target: "Comment ID: {`comment_edit_history.comment_id`}" — UUID gold
> - Editor: "Edited By: {`comment_edit_history.edited_by`}" — UUID
> - ⚠️ DO NOT render a "Reason" field for comment edits — `comment_edit_history` does NOT have a `reason` column in the current schema
> - Diff Box: same dual-column BEFORE/AFTER layout
> - Data source: `GET /api/moderator/comments/{comment_id}/history`
> - ⚠️ Admin-specific comment history route does NOT exist yet. Use moderator route or create: `GET /api/admin/comments/{comment}/history`

---

## RECOMMENDED FRONTEND FOLDER STRUCTURE

```
src/
├── pages/
│   ├── Auth/
│   │   ├── RegisterPage.tsx          (PAGE 1)
│   │   └── LoginPage.tsx             (PAGE 2)
│   ├── Public/
│   │   ├── HomePage.tsx              (PAGE 3)
│   │   ├── PostDetailPage.tsx        (PAGE 4)
│   │   ├── SearchPage.tsx            (PAGE 5)
│   │   ├── TagFilterPage.tsx         (PAGE 6)
│   │   ├── CategoryFilterPage.tsx    (PAGE 7)
│   │   ├── TrendingPage.tsx          (PAGE 8)
│   │   ├── LeaderboardPage.tsx       (PAGE 9)
│   │   └── TagsListPage.tsx          (PAGE 10)
│   ├── User/
│   │   ├── CreatePostPage.tsx        (PAGE 11)
│   │   ├── EditPostPage.tsx          (PAGE 12)
│   │   ├── MyPostsPage.tsx           (PAGE 13)
│   │   ├── BookmarksPage.tsx         (PAGE 14)
│   │   ├── NotificationsPage.tsx     (PAGE 15)
│   │   ├── ProfileSettingsPage.tsx   (PAGE 16)
│   │   ├── PublicProfilePage.tsx     (PAGE 17)
│   │   └── MyBadgesPage.tsx          (PAGE 18)
│   ├── Moderator/
│   │   ├── ReportQueuePage.tsx       (PAGE 19)
│   │   ├── ActionLogPage.tsx         (PAGE 20)
│   │   ├── BanManagementPage.tsx     (PAGE 21)
│   │   ├── ModTagCategoryPage.tsx    (PAGE 22)
│   │   └── EditHistoryPage.tsx       (PAGE 23)
│   └── Admin/
│       ├── AdminDashboardPage.tsx    (PAGE 24)
│       ├── TagCategoryPage.tsx       (PAGE 25)
│       ├── UserDirectoryPage.tsx     (PAGE 26)
│       ├── RoleManagementPage.tsx    (PAGE 27)
│       ├── BadgeMasterPage.tsx       (PAGE 28)
│       └── AuditTimelinePage.tsx     (PAGE 29)
├── features/                         (API calls, hooks, components per feature)
│   ├── Admin/
│   │   ├── F7_RoleAndPermission/     (api/, components/, types/)
│   │   ├── F8_UserManagement/
│   │   ├── F9_CategoryMaster/
│   │   ├── F10_TagMaster/
│   │   └── F11_BadgeMaster/
│   ├── Auth/
│   ├── Common/
│   ├── Moderator/
│   └── User/
├── layouts/
│   ├── PublicLayout.tsx              (top navbar, public footer)
│   ├── UserLayout.tsx                (navbar + user sidebar)
│   ├── ModeratorLayout.tsx           (moderator sidebar)
│   └── AdminLayout.tsx              (fixed 250px admin sidebar)
└── routes/
    └── AppRouter.tsx                 (React Router v6, role-based guards)
```

---

## BACKEND GAPS — MUST CREATE BEFORE BUILDING THESE PAGES

| Priority | Endpoint | For Page | Action Required |
|----------|----------|----------|-----------------|
| 🔴 HIGH | `GET /api/admin/stats/overview` | PAGE 24 | Create new controller + route in Admin module |
| 🔴 HIGH | `GET /api/admin/stats/points-summary` | PAGE 24 | Aggregate query on `points_log` |
| 🔴 HIGH | `POST /api/admin/system/toggle-registration` | PAGE 24 Modal | New system-level endpoint |
| 🟡 MED | `GET /api/admin/comments/{comment}/history` | PAGE 29 | Add to admin route group |
| 🟡 MED | Migration: add `reason varchar(255) nullable` to `comment_edit_history` | PAGE 29 | New migration file |
| 🟢 LOW | `GET /api/admin/posts?sort=view_count&limit=5` | PAGE 24 Q3 | Add query param support to existing endpoint |

---

## QUICK REFERENCE — COLUMN ACCURACY CHECKLIST

> Use this when designing UI — these common mistakes must be avoided:

| ❌ Wrong Assumption | ✅ Actual Schema |
|--------------------|-----------------|
| `categories` has `color` column | ❌ No — only `tags` has `color` |
| `categories` has `usage_count` | ❌ No — only `tags` has `usage_count` |
| `comment_edit_history` has `reason` | ❌ No — only `post_edit_history` has `reason` |
| `notifications` has `data` JSON column | ❌ No — has `type`, `reference_id`, `reference_type` |
| `moderation_logs` has `target_post_id` | ❌ No — only `target_user_id` |
| Points tracked in `users.points` | ❌ No — tracked in separate `points_log` table |
