# Backend Architecture Documentation

## 📚 Dokumentasi Struktur Backend - Forum Advanced Features

Dokumentasi lengkap mengenai arsitektur backend, struktur modul, dan mapping 29 fitur advanced yang terintegrasi dalam sistem forum berbasis Stack Overflow style.

---

## 📋 Daftar File Dokumentasi

Folder ini berisi dokumentasi terstruktur:

1. **[00_README.md](00_README.md)** (File ini)
   - Pengenalan dan navigasi dokumentasi

2. **[01_FEATURES_OVERVIEW.md](01_FEATURES_OVERVIEW.md)**
   - Breakdown lengkap ke-29 fitur fungsional
   - Kategori per modul
   - Deskripsi detail setiap fitur

3. **[02_MODULE_STRUCTURE.md](02_MODULE_STRUCTURE.md)**
   - Struktur folder dan organisasi modul
   - Penjelasan DDD & Feature-Based Modularity
   - Folder hierarchy dan naming conventions

4. **[03_DATABASE_SCHEMA.md](03_DATABASE_SCHEMA.md)**
   - Entity Relationship Diagram (ERD) textual
   - Tabel-tabel utama dan relasi
   - Field descriptions

5. **[04_FEATURE_MAPPING.md](04_FEATURE_MAPPING.md)**
   - Mapping fitur ke file/folder
   - Controllers, Services, Repositories yang terlibat
   - Dependencies antar modul

6. **[05_ARCHITECTURE_PATTERNS.md](05_ARCHITECTURE_PATTERNS.md)**
   - Design patterns yang digunakan
   - Clean Architecture principles
   - Dependency Injection & Service Container

---

## 🎯 Ringkasan Cepat

### Statistik Proyek
- **Total Fitur Fungsional**: 29
- **Total Folder Modul**: 13
- **Modul Utama**: 5 (Auth, Common, Admin, Moderator, User)
- **Architecture Style**: DDD + Feature-Based Modularity

### Breakdown Per Modul

| Modul | Folder | Fitur | Deskripsi |
|-------|--------|-------|-----------|
| **Auth** | 1 | 2 | Authentikasi & Autorisasi |
| **Common** | 1 | 4 | Fitur publik (Search, Filter, Trending) |
| **Admin** | 4 | 5 | Manajemen master data & roles |
| **Moderator** | 2 | 3 | Moderasi konten & user sanctions |
| **User** | 5 | 15 | Core forum features (Post, Comment, Interaction, Notification, Gamification) |
| **TOTAL** | **13** | **29** | |

---

## 🚀 Quick Start

Untuk memahami struktur backend secara menyeluruh:

1. Mulai dengan [01_FEATURES_OVERVIEW.md](01_FEATURES_OVERVIEW.md) untuk melihat daftar lengkap fitur
2. Lanjut ke [02_MODULE_STRUCTURE.md](02_MODULE_STRUCTURE.md) untuk memahami organisasi kode
3. Referensi [03_DATABASE_SCHEMA.md](03_DATABASE_SCHEMA.md) untuk skema database
4. Gunakan [04_FEATURE_MAPPING.md](04_FEATURE_MAPPING.md) saat development/debugging

---

## 📁 Struktur Folder Backend

```
backend_roleuser/
├── Modules/                    # Domain-driven modules
│   ├── Auth/                   # Authentication
│   ├── Common/
│   │   └── Explore/            # Public search & filter
│   ├── Admin/                  # Admin management
│   │   ├── Badge/
│   │   ├── Category/
│   │   ├── RoleManagement/
│   │   └── Tag/
│   ├── Moderator/              # Content moderation
│   │   ├── Report/
│   │   └── UserSanction/
│   └── User/                   # Core forum features
│       ├── Comment/
│       ├── Gamification/
│       ├── Interaction/
│       ├── Notification/
│       └── Post/
├── app/                        # Laravel app folder
├── config/                     # Configuration files
├── database/                   # Migrations & seeders
├── docs/
│   ├── StructureBackend/       # Backend documentation (Anda di sini)
│   └── ...
└── ...
```

---

## 🔑 Key Concepts

### Domain-Driven Design (DDD)
Setiap modul merepresentasikan bounded context yang independen dengan tanggung jawab spesifik.

### Feature-Based Modularity
Satu folder modul dapat membungkus **multiple features** yang berkaitan dengan domain yang sama.

### Example
Folder `User/Post` bukan hanya 1 fitur, tetapi membungkus:
- Fitur 15: Buat Postingan
- Fitur 16: Edit Postingan
- Fitur 17: Hapus Postingan
- Fitur 18: Mark as Accepted Answer
- Fitur 19: Edit History

---

## 📖 Konvensi Naming

### Controllers
- Pattern: `{Resource}Controller`
- Example: `PostController`, `CommentController`
- Location: `Modules/{Domain}/{Feature}/Controllers/`

### Services
- Pattern: `{Resource}Service`
- Example: `PostService`, `CommentService`
- Location: `Modules/{Domain}/{Feature}/Services/`

### Repositories
- Pattern: `{Resource}Repository`
- Example: `PostRepository`, `CommentRepository`
- Location: `Modules/{Domain}/{Feature}/Repositories/`

### Requests (Form Validation)
- Pattern: `{Action}{Resource}Request`
- Example: `StorePostRequest`, `UpdatePostRequest`
- Location: `Modules/{Domain}/{Feature}/Requests/`

---

## 🔗 Useful Links

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sanctum (API Authentication)](https://laravel.com/docs/sanctum)
- [DDD Best Practices](https://en.wikipedia.org/wiki/Domain-driven_design)
- [Clean Architecture](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)

---

## ✏️ Last Updated
- **Date**: Juni 2026
- **Version**: 1.0
- **Status**: Active Development

---

**Lanjut ke [01_FEATURES_OVERVIEW.md](01_FEATURES_OVERVIEW.md) untuk melihat daftar lengkap 29 fitur →**
