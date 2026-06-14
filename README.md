# MauTanyaSuhu (MTS) - Core API & Backend Services

Repositori ini berisi *source code* backend untuk **MauTanyaSuhu (MTS)**, sebuah platform forum diskusi berbasis komunitas. Dibangun menggunakan **Laravel**, layanan ini bertanggung jawab atas seluruh logika bisnis, pemrosesan data, keamanan, dan penyediaan RESTful API untuk dikonsumsi oleh *client-side* (Web dan Desktop).

## 🛠 Tim Pengembang Core API
* **Alvian** - *Lead Backend Developer*
  Bertanggung jawab penuh atas arsitektur database, pembuatan endpoint API, optimasi *query*, serta keamanan sistem autentikasi dan otorisasi.

## 🚀 Rincian Fitur & Logika Sistem

Aplikasi ini dibagi menjadi beberapa domain bisnis utama:

### 1. Autentikasi & Keamanan (Domain: Auth)
Sistem ini menggunakan token-based authentication untuk menjaga keamanan sesi pengguna.
* **Registrasi & Login (F1, F2):** Endpoint untuk mendaftarkan pengguna baru dengan enkripsi password standar industri, serta penerbitan *access token* untuk sesi login.
* **Logout Terpusat (F3):** Pencabutan token aktif untuk memastikan akun aman saat pengguna keluar dari perangkat.
* **Sistem Pemulihan Akun (F31):** Logika pengiriman email *reset link* beserta verifikasi token *one-time use* untuk pembaharuan password yang aman.

### 2. Eksplorasi & Distribusi Konten (Domain: Common/Explore)
* **Mesin Pencari Dinamis (F4):** Endpoint pencarian dengan algoritma pencocokan kata kunci terhadap judul dan isi diskusi.
* **Kategorisasi & Tagging (F5, F6):** Relasi *Many-to-Many* yang memungkinkan pemfilteran *thread* secara spesifik berdasarkan tag dan kategori yang tersedia di database master.
* **Algoritma Trending (F7):** Kalkulasi otomatis yang mengumpulkan *thread* terpopuler berdasarkan rasio *upvote*, jumlah komentar, dan usia *post* untuk disajikan di sidebar *client*.

### 3. Interaksi & Gamifikasi Pengguna (Domain: User)
* **Manajemen Thread (F16):** Operasi CRUD (Create, Read, Update, Delete) lengkap dengan sanitasi input dari *Rich Text Editor* untuk mencegah serangan XSS.
* **Komentar Bersarang / Nested Replies (F17, F20):** Struktur tabel *self-referencing* yang mendukung *query* rekursif untuk memuat komentar dan balasan bertingkat.
* **Sistem Penilaian Cerdas (F22, F23):** Endpoint *Vote* (Upvote/Downvote) dan *Like* yang dilengkapi dengan proteksi *double-voting* untuk menjaga integritas reputasi konten.
* **Pencapaian & Reputasi (F27, F29):** Logika *trigger* otomatis yang menambahkan poin reputasi pengguna setiap kali mereka mendapatkan *upvote* atau jawaban terbaik (Accepted Answer), serta pemberian *Badge* (Bronze, Silver, Gold).
* **Notifikasi Real-time (F26):** Perekaman aktivitas *trigger* (seperti balasan baru atau *like*) yang dikirimkan ke tabel notifikasi pengguna.

### 4. Moderasi & Panel Admin (Domain: Moderator & Admin)
* **Antrean Laporan & Sanksi (F13, F15):** Penampungan keluhan pengguna (Spam, Harassment) yang dapat ditinjau oleh Moderator, dilengkapi dengan fitur *Banning* (pemblokiran akses API berdasarkan ID Pengguna).
* **Master Data Management (F9, F10, F11, F12):** Endpoint eksklusif Admin untuk mengatur hierarki peran pengguna (Role), serta CRUD untuk Master Kategori, Tag, dan Badge.

## ⚙️ Persyaratan Sistem & Instalasi
1. Pastikan **PHP >= 8.x**, **Composer**, dan **MySQL/PostgreSQL** terinstal.
2. Clone repositori: `git clone <repo-url>`
3. Instalasi dependensi: `composer install`
4. Konfigurasi environment: Salin `.env.example` ke `.env` dan sesuaikan kredensial database.
5. Jalankan migrasi dan *seeder*: `php artisan migrate --seed` (untuk mengisi Master Data).
6. Jalankan server lokal: `php artisan serve`