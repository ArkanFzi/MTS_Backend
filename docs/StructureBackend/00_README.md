# Backend Architecture Documentation

## Dokumentasi Struktur Backend - MTS Forum

Dokumentasi lengkap mengenai arsitektur backend, struktur modul, dan mapping **31 fitur** advanced yang terintegrasi dalam sistem forum berbasis Stack Overflow style.

**Versi:** 2.0 (Terverifikasi dari Codebase Aktual)
**Tanggal Update:** 10 Juni 2026

---

## Daftar File Dokumentasi

1. **[00_README.md](00_README.md)** (File ini) — Pengenalan dan navigasi
2. **[01_FEATURES_OVERVIEW.md](01_FEATURES_OVERVIEW.md)** — Breakdown 31 fitur dengan detail endpoint, model, dan file
3. **[02_MODULE_STRUCTURE.md](02_MODULE_STRUCTURE.md)** — Struktur folder, naming conventions, layer explanation
4. **[03_DATABASE_SCHEMA.md](03_DATABASE_SCHEMA.md)** — 20 tabel aplikasi + 7 tabel infrastruktur dengan kolom aktual
5. **[04_FEATURE_MAPPING.md](04_FEATURE_MAPPING.md)** — Mapping fitur ke file, route, model, dan middleware
6. **[05_ARCHITECTURE_PATTERNS.md](05_ARCHITECTURE_PATTERNS.md)** — Design patterns dengan contoh kode aktual

---

## Ringkasan Cepat

### Statistik Proyek

| Metrik | Nilai |
|--------|-------|
| **Total Fitur** | 31 (F1–F3, F31, F4–F30) |
| **Total Folder Modul** | 31 folder feature |
| **Domain Modul** | 5 (Auth, Common, Admin, Moderator, User) |
| **Model Eloquent** | 20 (dalam 6 grup) |
| **Migration** | 27 file |
| **Architecture** | Clean Architecture + DDD + Feature-Based |
| **Auth** | Sanctum SPA Cookie (bukan token) |
| **Database** | PostgreSQL, UUID primary keys |

### Breakdown Per Modul

| Modul | Fitur | F-Prefix | Deskripsi |
|-------|-------|----------|-----------|
| **Auth** | 4 | F1, F2, F3, F31 | Register, Login, Logout, Forgot Password |
| **Common** | 4 | F4–F7 | Search, Filter Tag/Category, Trending |
| **Admin** | 5 | F8–F12 | Role, User Mgmt, Category/Badge/Tag Master |
| **Moderator** | 3 | F13–F15 | Report Queue, Action Log, User Ban |
| **User** | 15 | F16–F30 | Post, Comment, Vote, Like, Bookmark, Follow, Notification, Gamification, Profile, Badge, Report |
| **TOTAL** | **31** | | |

---

## Struktur Folder Backend

```
MTS_backend/
├── Modules/                              # Domain-driven modules (F-prefix)
│   ├── Auth/                             # F1, F2, F3, F31
│   ├── Common/                           # F4, F5, F6, F7
│   ├── Admin/                            # F8, F9, F10, F11, F12
│   ├── Moderator/                        # F13, F14, F15
│   └── User/                             # F16–F30
├── app/
│   ├── Http/Middleware/                  # RoleMiddleware
│   └── Models/                           # 20 models (6 groups)
│       ├── Auth/                         # User, Role, UserRole
│       ├── Content/                      # Post, Comment, Category, Tag, PostTag
│       ├── Gamification/                 # Badge, PointsLog, UserBadge
│       ├── History/                      # PostEditHistory, CommentEditHistory
│       ├── Interaction/                  # Vote, Like, Bookmark, Follow
│       └── Moderation/                   # Notification, Report, ModerationLog
├── bootstrap/app.php                     # Middleware config (Sanctum, CSRF, Role alias)
├── database/
│   ├── migrations/                       # 27 migrations
│   └── seeders/                          # 10 seeders
├── routes/api.php                        # All API routes (166 lines)
└── tests/                                # PHPUnit tests
```

---

## Quick Start

1. Mulai dengan [01_FEATURES_OVERVIEW.md](01_FEATURES_OVERVIEW.md) untuk daftar 31 fitur
2. Lanjut ke [02_MODULE_STRUCTURE.md](02_MODULE_STRUCTURE.md) untuk memahami organisasi kode
3. Referensi [03_DATABASE_SCHEMA.md](03_DATABASE_SCHEMA.md) untuk skema database
4. Gunakan [04_FEATURE_MAPPING.md](04_FEATURE_MAPPING.md) saat development/debugging
5. Pelajari [05_ARCHITECTURE_PATTERNS.md](05_ARCHITECTURE_PATTERNS.md) untuk best practices

---

## Key Concepts

- **Feature-Based Modularity:** Setiap folder `F{number}_{Name}` = satu fitur terisolasi
- **Clean Architecture:** Controller → Service → Repository → Model (4 layer separation)
- **Sanctum SPA Auth:** Cookie-based session, bukan Bearer token
- **UUID Primary Keys:** Semua tabel pakai UUID, bukan auto-increment
- **Role-Based Access:** `role:admin` dan `role:moderator,admin` custom middleware
- **Polymorphic Relations:** Vote, Like, Report menggunakan `target_id` + `target_type`
- **Gamification:** Poin, badge, dan level otomatis via cross-feature service

---

**Lanjut ke [01_FEATURES_OVERVIEW.md](01_FEATURES_OVERVIEW.md) untuk melihat daftar lengkap 31 fitur →**
