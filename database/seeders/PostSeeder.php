<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Content\Post;
use App\Models\Content\Category;
use App\Models\Content\Tag;
use App\Models\Auth\User;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // Use only parent categories for posts (child categories are for filtering)
        $categories = Category::whereNull('parent_id')->get();
        $tags = Tag::all();
        $users = User::where('is_banned', false)->take(6)->get();

        if ($users->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('⚠️ PostSeeder: No users or categories found. Skipping.');
            return;
        }

        $posts = [
            // User 1 (userbiasa) - 2 posts
            [
                'user_idx' => 0, 'cat_idx' => 0,
                'title' => 'Cara Install Laravel 11 dalam 3 Hari',
                'body' => "Langkah-langkah install Laravel 11:\n\n1. Install Composer\n2. Jalankan `composer create-project laravel/laravel myapp`\n3. Setup .env file\n4. Jalankan `php artisan migrate`\n5. Done!\n\nBerikut penjelasan detailnya...",
                'tags' => [0, 1, 9], 'views' => 340, 'votes' => 28, 'days_ago' => 3,
            ],
            [
                'user_idx' => 0, 'cat_idx' => 1,
                'title' => 'Perbedaan SQL dan NoSQL yang Wajib Diketahui',
                'body' => "Banyak developer pemula bingung antara SQL dan NoSQL.\n\n**SQL** (Relational):\n- Struktur tetap (schema)\n- ACID compliance\n- Cocok untuk transaksi kompleks\n\n**NoSQL** (Non-relational):\n- Schema fleksibel\n- Horizontal scaling\n- Cocok untuk data tidak terstruktur",
                'tags' => [5, 6], 'views' => 215, 'votes' => 15, 'days_ago' => 6,
            ],

            // User 2 (moderator_suhu) - 2 posts
            [
                'user_idx' => 1, 'cat_idx' => 0,
                'title' => 'Belajar REST API dengan Laravel Sanctum',
                'body' => "Tutorial lengkap membuat REST API menggunakan Laravel Sanctum untuk autentikasi.\n\nKita akan membahas:\n- Setup Sanctum\n- SPA Authentication\n- Token-based auth\n- Middleware protection\n\nIkuti langkah-langkah berikut...",
                'tags' => [0, 9], 'views' => 450, 'votes' => 42, 'days_ago' => 2,
            ],
            [
                'user_idx' => 1, 'cat_idx' => 2,
                'title' => 'Docker untuk Developer Pemula: Panduan Lengkap',
                'body' => "Panduan penggunaan Docker bagi developer yang baru memulai.\n\nApa itu Docker?\nDocker adalah platform containerization yang memungkinkan Anda menjalankan aplikasi dalam isolated container.\n\nKeuntungan:\n- Konsistensi environment\n- Mudah deploy\n- Resource efficient",
                'tags' => [7, 8], 'views' => 180, 'votes' => 22, 'days_ago' => 5,
            ],

            // User 3 (budi_dev) - 2 posts
            [
                'user_idx' => 2, 'cat_idx' => 0,
                'title' => 'Tips Optimasi Query Database di Laravel',
                'body' => "Kumpulan tips untuk mengoptimalkan query database:\n\n1. Gunakan `EXPLAIN` untuk analisis query\n2. Index kolom yang sering di-query\n3. Hindari N+1 query problem (gunakan eager loading)\n4. Gunakan `select()` untuk ambil kolom yang diperlukan saja\n5. Cache query yang sering dipanggil",
                'tags' => [0, 5, 6], 'views' => 290, 'votes' => 35, 'days_ago' => 4,
            ],
            [
                'user_idx' => 2, 'cat_idx' => 3,
                'title' => 'Flutter vs React Native 2024: Mana yang Lebih Baik?',
                'body' => "Perbandingan framework mobile development terpopuler.\n\n**Flutter:**\n- Dart language\n- Widget-based UI\n- Performance lebih baik\n\n**React Native:**\n- JavaScript\n- Native components\n- Ekosistem lebih besar\n\nKeduanya punya kelebihan masing-masing.",
                'tags' => [2, 4], 'views' => 520, 'votes' => 48, 'days_ago' => 1,
            ],

            // User 4 (siti_coder) - 1 post
            [
                'user_idx' => 3, 'cat_idx' => 0,
                'title' => 'State Management di React: useState vs useReducer vs Redux',
                'body' => "Panduan memilih state management di React.\n\n**useState** - Simple local state\n**useReducer** - Complex local state\n**Redux** - Global app state\n**Zustand** - Lightweight global state\n\nKapan pakai yang mana? Tergantung kompleksitas aplikasi.",
                'tags' => [2, 4], 'views' => 380, 'votes' => 30, 'days_ago' => 7,
            ],

            // User 5 (andi_js) - 1 post
            [
                'user_idx' => 4, 'cat_idx' => 4,
                'title' => 'Prinsip UI/UX yang Wajib Diketahui Developer',
                'body' => "Prinsip desain UI/UX untuk developer:\n\n1. Konsistensi visual\n2. Hierarki informasi\n3. White space yang cukup\n4. Feedback untuk setiap aksi\n5. Accessibility (a11y)\n\nJangan hanya fokus pada code, tapi juga pengalaman pengguna.",
                'tags' => [], 'views' => 150, 'votes' => 12, 'days_ago' => 9,
            ],

            // User 6 (dewi_react) - 2 posts
            [
                'user_idx' => 5, 'cat_idx' => 0,
                'title' => 'Membuat Authentication dengan JWT di Laravel',
                'body' => "Tutorial implementasi JWT di Laravel.\n\nJWT (JSON Web Token) adalah standar untuk autentikasi stateless.\n\nLangkah:\n1. Install tymon/jwt-auth\n2. Konfigurasi guard\n3. Buat controller auth\n4. Protect routes dengan middleware",
                'tags' => [0, 1, 9], 'views' => 410, 'votes' => 38, 'days_ago' => 0,
            ],
            [
                'user_idx' => 5, 'cat_idx' => 1,
                'title' => 'PostgreSQL vs MySQL: Kapan Pakai yang Mana?',
                'body' => "Kapan harus pakai PostgreSQL dan kapan MySQL.\n\n**PostgreSQL:**\n- Fitur advanced (JSONB, Full-text search)\n- Standard SQL compliance\n- Cocok untuk data kompleks\n\n**MySQL:**\n- Lebih cepat untuk read-heavy\n- Ekosistem hosting lebih luas\n- Lebih mudah dipelajari",
                'tags' => [5, 6], 'views' => 320, 'votes' => 25, 'days_ago' => 8,
            ],
        ];

        foreach ($posts as $data) {
            $user = $users[$data['user_idx']] ?? $users[0];
            $category = $categories[$data['cat_idx']] ?? $categories[0];

            $post = Post::firstOrCreate(
                ['title' => $data['title']],
                [
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'body' => $data['body'],
                    'status' => 'published',
                    'view_count' => $data['views'],
                    'vote_score' => $data['votes'],
                    'is_answered' => false,
                    'created_at' => now()->subDays($data['days_ago']),
                    'updated_at' => now()->subDays($data['days_ago']),
                ]
            );

            if (!empty($data['tags'])) {
                $tagIds = collect($data['tags'])
                    ->filter(fn($idx) => isset($tags[$idx]))
                    ->map(fn($idx) => $tags[$idx]->id)
                    ->toArray();
                $post->tags()->sync($tagIds);
            }
        }

        // ─── Update tag usage_count based on actual post_tags ───
        foreach ($tags as $tag) {
            $count = $tag->posts()->count();
            $tag->update(['usage_count' => $count]);
        }

        $this->command->info('✅ 11 Posts created (status=published) across 6 users, 5 categories.');
    }
}
