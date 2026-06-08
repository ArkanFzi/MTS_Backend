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
        $user     = User::where('email', 'user@email.com')->first();
        $category = Category::first();
        $tags     = Tag::take(3)->pluck('id');

        $posts = [
            ['title' => 'Cara Install Laravel 11 dalam 3 hari',           'body' => 'Langkah-langkah install Laravel 11 di sistem operasi Windows dan Linux.'],
            ['title' => 'Perbedaan SQL dan NoSQL',           'body' => 'Penjelasan mendalam tentang perbedaan database SQL dan NoSQL beserta use case masing-masing.'],
            ['title' => 'Tips Optimasi Query Database',      'body' => 'Kumpulan tips untuk mengoptimalkan query database agar lebih cepat dan efisien.'],
            ['title' => 'Belajar REST API dengan Laravel',   'body' => 'Tutorial lengkap membuat REST API menggunakan Laravel Sanctum untuk autentikasi.'],
            ['title' => 'Docker untuk Developer Pemula',     'body' => 'Panduan penggunaan Docker bagi developer yang baru memulai belajar containerization.'],
        ];

        foreach ($posts as $postData) {
            $post = Post::firstOrCreate(
                ['title' => $postData['title']],
                [
                    'user_id'     => $user->id,
                    'category_id' => $category->id,
                    'body'        => $postData['body'],
                    'status'      => 'open',
                    'view_count'  => rand(0, 100),
                    'vote_score'  => 0,
                ]
            );

            $post->tags()->sync($tags);
        }

        $this->command->info('Posts berhasil dibuat.');
    }
}