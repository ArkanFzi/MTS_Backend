# 🔌 Dokumentasi Integrasi & Gaps API MauTanyaSuhu

Dokumen ini menjabarkan status API saat ini, API apa saja yang belum diimplementasikan (Gaps), dan panduan lengkap bagaimana Frontend harus mengintegrasikan masing-masing API ke setiap halaman (29 Pages).

---

## 🔴 BAGIAN 1: API GAPS (API YANG MASIH KURANG)

Berikut adalah daftar API yang dibutuhkan oleh desain frontend (terutama halaman Admin) namun **belum tersedia** di backend saat ini:

| Priority | Endpoint (Metode & URL) | Kegunaan & Integrasi Frontend | Status Frontend |
|----------|-----------------------|--------------------------------|-----------------|
| 🔴 **HIGH** | `GET /api/admin/stats/overview` | Diperlukan oleh **PAGE 24 (Admin Dashboard)** untuk merender chart *Post Creation* dan *User Registration* harian. Harus me-return data array tanggal dan jumlah. | ⛔ **Blocker** untuk Chart |
| 🔴 **HIGH** | `GET /api/admin/stats/points-summary` | Diperlukan oleh **PAGE 24 (Admin Dashboard)** untuk menghitung total sirkulasi poin dari tabel `points_log` (earned, deducted, net). | ⛔ **Blocker** untuk Ledger |
| 🔴 **HIGH** | `POST /api/admin/system/toggle-registration` | Diperlukan oleh Modal Lockdown di **PAGE 24**. Digunakan untuk menonaktifkan pembuatan akun baru. Harus menerima `authorization_key`. | ⛔ **Blocker** untuk Modal |
| 🟡 **MED** | `GET /api/admin/comments/{comment}/history` | Diperlukan oleh **PAGE 29 (Audit Timeline)** agar Admin bisa melihat riwayat edit komentar. Saat ini hanya ada untuk role Moderator. | ⚠️ Gunakan route Moderator sementara |
| 🟢 **LOW** | `GET /api/admin/posts` (query `sort=view_count`) | Diperlukan oleh **PAGE 24 (Admin Dashboard)** Kuadran 3 untuk menampilkan 5 post paling populer. | ⚠️ Perlu penambahan fungsi `sort` di query parameter |

*Catatan Tambahan Database*: Tabel `comment_edit_history` saat ini **tidak memiliki** kolom `reason`. Jika frontend PAGE 29 (Audit Timeline) mengharuskan ditampilkannya alasan pengeditan komentar, maka backend harus membuat migration baru untuk menambahkan kolom `reason varchar(255) nullable` pada tabel tersebut.

---

## 🟢 BAGIAN 2: DAFTAR LENGKAP API (API REFERENCE)

### 🔓 PUBLIC API (Tidak butuh Auth)
- `POST /api/auth/register` — Registrasi user baru
- `POST /api/auth/login` — Login user (mengembalikan Sanctum token)
- `GET /api/posts` — Mengambil daftar post (feed) dengan paginasi
- `GET /api/posts/{post}` — Mengambil detail spesifik post
- `GET /api/explore/search?q=` — Mencari post berdasarkan keyword
- `GET /api/explore/tags` — Mengambil daftar lengkap tags
- `GET /api/explore/tag/{slug}` — Mengambil posts berdasarkan tag tertentu
- `GET /api/explore/category/{slug}` — Mengambil posts berdasarkan kategori
- `GET /api/explore/trending` — Mengambil daftar post populer/trending
- `GET /api/explore/leaderboard` — Mengambil top 10 user berdasarkan reputasi

### 🔐 PROTECTED API (Butuh Bearer Token)
- `POST /api/auth/logout` — Logout user (mencabut token)
- `GET /api/settings/profile` — Mendapatkan data profil diri sendiri
- `PUT /api/settings/profile` — Memperbarui profil (bio, avatar, dll)
- `PUT /api/settings/password` — Mengganti kata sandi
- `GET /api/me/posts` — Mendapatkan daftar post milik sendiri
- `GET /api/me/badges` — Mendapatkan daftar badge/achievement sendiri
- `POST /api/posts` — Membuat post/pertanyaan baru
- `PUT /api/posts/{post}` — Mengedit post sendiri (masuk ke `post_edit_history`)
- `DELETE /api/posts/{post}` — Menghapus post sendiri
- `GET /api/posts/{post}/comments` — Mengambil komentar pada sebuah post
- `POST /api/posts/{post}/comments` — Menambahkan komentar ke post
- `PUT /api/posts/{post}/comments/{comment}` — Mengedit komentar
- `POST /api/posts/{post}/comments/{comment}/replies` — Membalas komentar (nested)
- `POST /api/posts/{post}/comments/{comment}/accept` — Menerima jawaban (hanya author post)
- `POST /api/tags` — User biasa menyarankan tag baru
- `GET /api/notifications` — Daftar notifikasi
- `PATCH /api/notifications/mark-all-read` — Menandai semua notifikasi sudah dibaca
- `PATCH /api/notifications/{id}/read` — Menandai 1 notifikasi dibaca
- `POST /api/users/{id}/follow` — Mengikuti/berhenti ikuti user
- `GET /api/users/{id}/followers` & `following` — Mendapatkan daftar pengikut/diikuti
- `POST /api/likes/toggle` — Toggle like pada post/komentar
- `POST /api/bookmarks/toggle` — Toggle simpan post
- `GET /api/bookmarks` — Daftar post yang disimpan
- `POST /api/votes` — Upvote/Downvote konten

### 🛡️ MODERATOR API (Butuh Role Moderator/Admin)
- `GET/POST/PUT/DELETE /api/moderator/categories` — CRUD Kategori
- `GET/POST/PUT/DELETE /api/moderator/tags` — CRUD Tag
- `GET /api/moderator/reports` & `PUT /{id}` — Mengelola laporan user (Resolve/Reject)
- `GET /api/moderator/logs` — Riwayat aksi moderasi
- `GET /api/moderator/posts/{post}/history` — Histori edit post
- `GET /api/moderator/comments/{comment}/history` — Histori edit komentar
- `GET /api/moderator/bans`, `POST /{id}/ban`, `POST /{id}/unban` — Ban/Unban user

### ⚙️ ADMIN API (Butuh Role Admin)
- `GET/POST/PUT/DELETE /api/admin/roles` — Manajemen peran (Roles & Permissions)
- `GET/POST/PUT/DELETE /api/admin/categories` — (Sama dengan Moderator)
- `GET/POST/PUT/DELETE /api/admin/tags` — (Sama dengan Moderator)
- `GET/POST/PUT/DELETE /api/admin/badges` — Manajemen Badge Master
- `GET /api/admin/users`, `PUT /{id}/role`, `PUT /{id}/profile` — Manajemen kontrol user
- `GET /api/admin/posts/{post}/history` — Histori edit post untuk admin
- `GET /api/admin/bans`, `POST /{id}/ban`, `POST /{id}/unban` — Ban sistem level admin

---

## 🧩 BAGIAN 3: PANDUAN INTEGRASI FRONTEND (PAGE-BY-PAGE)

Berikut adalah instruksi bagaimana Frontend Engineer harus menyambungkan API ke masing-masing halaman. Gunakan *Axios* atau *Fetch* dengan Interceptor untuk menyisipkan Bearer Token secara otomatis pada rute yang membutuhkan Auth.

### 🔐 AUTHENTICATION
* **PAGE 1 (Register)**
  - **Endpoint**: `POST /api/auth/register`
  - **Payload**: `{ username, email, password, password_confirmation }`
  - **Integrasi UI**: Tangkap 422 Unprocessable Entity untuk menampilkan error validasi inline di bawah setiap input. Jika 201 Created, arahkan ke PAGE 2 (Login).

* **PAGE 2 (Login)**
  - **Endpoint**: `POST /api/auth/login`
  - **Payload**: `{ email, password }`
  - **Integrasi UI**: Saat respon 200 OK diterima, simpan token ke LocalStorage/Cookies. Panggil Global State Manager (Context/Zustand) untuk mengeset status `isAuthenticated = true` dan simpan data user. Redirect ke `/` (PAGE 3).

### 🌐 PUBLIC / EXPLORE
* **PAGE 3 (Home / Feed)**
  - **Endpoint**: `GET /api/posts`
  - **Integrasi UI**: Gunakan React Query / SWR untuk fetch. Gunakan state `page` untuk parameter `?page=x` jika backend mendukung paginasi (Laravel pagination). Render atribut relasional: nama pembuat, nama kategori, dan loop array tag untuk badge kecil.

* **PAGE 4 (Detail Post & Q&A)**
  - **Endpoint 1 (Data Post)**: `GET /api/posts/{id}`
  - **Endpoint 2 (Komentar)**: `GET /api/posts/{id}/comments`
  - **Integrasi UI**: Jalankan `Promise.all` untuk memuat post & komentar. Render body post (gunakan library markdown/HTML parser jika data dirender dari rich text). 
  - **Aksi Interaktif**: 
    - Saat tombol upvote/downvote diklik, cegah default, optimistically update UI score, lalu tembak `POST /api/votes` dengan payload `{ votable_id: id, votable_type: 'post', value: 1 atau -1 }`.
    - Saat "Bookmark" diklik, tembak `POST /api/bookmarks/toggle` dengan `{ post_id: id }`.

* **PAGE 5 (Search)**
  - **Endpoint**: `GET /api/explore/search?q={keyword}`
  - **Integrasi UI**: Gunakan teknik *debounce* pada input search (misal 500ms) agar tidak men-spam API saat user mengetik. Map hasilnya ke komponen Card.

* **PAGE 6, 7 & 10 (Explore Tags & Categories)**
  - **Endpoints**: `GET /api/explore/tag/{slug}`, `GET /api/explore/category/{slug}`, `GET /api/explore/tags`
  - **Integrasi UI**: Untuk halaman filter, ambil `slug` dari URL (React Router `useParams`). Panggil API dan tampilkan judul filter (cth: "Posts in tag: Laravel") di atas hasil.

* **PAGE 8 (Trending) & PAGE 9 (Leaderboard)**
  - **Endpoints**: `GET /api/explore/trending` & `GET /api/explore/leaderboard?limit=10`
  - **Integrasi UI**: Secara UI ini adalah data read-only. Untuk leaderboard, sorot styling khusus (contoh: warna emas/perak/perunggu) untuk index array 0, 1, dan 2.

### 👤 USER (Membutuhkan Bearer Token)
* **PAGE 11 (Create Post) & PAGE 12 (Edit Post)**
  - **Endpoint Create**: `POST /api/posts`
  - **Endpoint Edit**: `PUT /api/posts/{id}`
  - **Integrasi UI**: 
    - Frontend perlu memanggil `GET /api/explore/categories` dan `GET /api/explore/tags` saat komponen dimount untuk mengisi Dropdown/Select options.
    - Untuk *Edit*, payload HARUS ditambahkan `{ reason: "..." }` karena wajib masuk ke tabel history. Jika user menekan submit, tunjukkan overlay loading untuk mencegah double submit.

* **PAGE 13 (My Posts) & PAGE 14 (Bookmarks)**
  - **Endpoints**: `GET /api/me/posts`, `GET /api/bookmarks`
  - **Integrasi UI**: Tampilkan list dengan layout yang lebih ringkas dari feed publik. Pada list "My Posts", tampilkan tombol *Edit* (mengarah ke PAGE 12) dan *Delete* (memanggil `DELETE /api/posts/{id}` dengan konfirmasi alert).

* **PAGE 15 (Notifications)**
  - **Endpoint**: `GET /api/notifications`
  - **Integrasi UI**: Fetch setiap user masuk atau via polling ringan/WebSocket (jika ada). Render beda styling (background lebih gelap/terang) jika flag `is_read` bernilai `false`. Tembak `PATCH /api/notifications/mark-all-read` saat header tombol "Mark all read" ditekan.

* **PAGE 16 (Profile Settings)**
  - **Endpoints**: `GET /api/settings/profile`, `PUT /api/settings/profile`, `PUT /api/settings/password`
  - **Integrasi UI**: Gunakan dua tab terpisah. Saat profil di-update (`PUT`), jangan lupa meng-update Global State agar avatar di Navigation Bar ikut berubah tanpa perlu refresh halaman. Payload untuk avatar bisa mensyaratkan `FormData` jika mendukung file upload, pastikan header `Content-Type: multipart/form-data`.

* **PAGE 17 (Public Profile)**
  - **Endpoint**: `GET /api/users/{id}/followers`, dll.
  - **Integrasi UI**: Tampilkan stat (total follower/following). Tombol Follow memanggil `POST /api/users/{id}/follow`. Update text tombol menjadi "Unfollow" jika toggle mengembalikan status di-unfollow.

* **PAGE 18 (My Badges)**
  - **Endpoint**: `GET /api/me/badges`
  - **Integrasi UI**: Map array badges. Badge memiliki `tier` (bronze, silver, gold) - di Frontend, gunakan dictionary untuk memberikan class warna CSS sesuai tier.

### 🛡️ MODERATOR & ADMIN
*(Halaman ini diproteksi oleh Private Route di Frontend, cek role user terlebih dahulu sebelum merender komponen)*

* **PAGE 19 (Report Queue)**
  - **Endpoints**: `GET /api/moderator/reports`, `PUT /api/moderator/reports/{id}`
  - **Integrasi UI**: Sediakan tombol *Resolve* dan *Reject*. Saat diklik, panggil PUT update dengan payload status. Gunakan toast notification ("Laporan berhasil diselesaikan") dan buang row tersebut dari UI list atau refresh list API.

* **PAGE 21 (Ban Management)**
  - **Endpoint**: `POST /api/moderator/bans/{id}/ban`
  - **Integrasi UI**: Frontend **Wajib** memunculkan modal meminta "Alasan Penangguhan / Reason" karena data ini akan dilempar ke field `reason` yang disimpan backend di tabel `moderation_logs`.

* **PAGE 22 & 25 (Tag/Category CRUD)**
  - **Endpoints**: `GET/POST/PUT/DELETE /api/admin/tags`
  - **Integrasi UI**: Form *Add Tag* harus memiliki Color Picker yang mereturn Hex string (ex: `#ff0000`). Payload POST harus menyertakan color tersebut. Pada list categories, tabel UI cukup menggunakan rekursi ringan jika ada relasi `parent_id`.

* **PAGE 24 (Admin Dashboard)**
  - **Integrasi UI**: Ini adalah dashboard utama (4 Quadrant Bento Layout).
  - Quadrant 1 (Chart): ⚠️ **Menunggu API Gap diselesaikan**. Jika sudah ada `GET /admin/stats/overview`, mapping array data ke library *Recharts* (Sumbu X: tanggal, Sumbu Y: Jumlah).
  - Quadrant 2 (Ledger): ⚠️ **Menunggu API Gap**. Tampilkan total poin.
  - Quadrant 3 (Top Posts): Panggil `GET /api/posts` tetapi frontend harus menyisipkan parameter manual `?sort=view_count&limit=5` (Perlu backend adjust support ini).

* **PAGE 26 (User Directory Control)**
  - **Endpoint Utama**: `GET /api/admin/users`
  - **Integrasi UI**: Ini adalah data-table berat. Pasang dropdown untuk memfilter (men-trigger re-fetch ke `GET /api/admin/users?is_banned=true` atau `?role=admin`). Baris tabel bisa diklik untuk membuka panel akordeon untuk Edit Reputasi/Role. Tombol save pada akordeon memanggil `PUT /api/admin/users/{id}/profile`.

* **PAGE 27 & 28 (Role & Badge Master)**
  - **Endpoints**: `/api/admin/roles`, `/api/admin/badges`
  - **Integrasi UI**: Form standar CRUD. Khusus untuk Role, sediakan JSON editor komponen minimalis (atau checklist array besar) karena `permissions` disimpan sebagai JSON di database.

* **PAGE 29 (Audit Timeline)**
  - **Endpoints**: `GET /api/admin/posts/{post_id}/history`
  - **Integrasi UI**: Gunakan 2 tab (Post / Comment). Render diff view (Before vs After) menggunakan layout berdampingan (flexbox, width 50%-50%). Pastikan font di set monospace (`Fira Code`).
  - Untuk komentar, ⚠️ **Gunakan Endpoint Moderator** jika Endpoint Admin belum dibuat backend (`GET /api/moderator/comments/{comment}/history`).

---
> **📌 Tips Implementasi untuk Tim Frontend:**
> 1. Gunakan library seperti **Axios** dan pasang interceptor global: Jika request mengembalikan `401 Unauthorized` atau `403 Forbidden`, arahkan user keluar (logout / buang token) ke halaman Login.
> 2. Buat custom React Hook (misal `useAuth()`) untuk dengan mudah mengecek role: `const { isAdmin } = useAuth()`.
> 3. Jika API yang ditandai 🔴 **HIGH GAPS** belum tersedia, gunakan mock data JSON sementara (Static Fallback) di frontend agar pengerjaan UI layout Dashboard PAGE 24 tidak terblokir.
