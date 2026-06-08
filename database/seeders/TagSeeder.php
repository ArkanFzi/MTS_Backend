<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Content\Tag;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Laravel', 'color' => '#FF2D20'],
            ['name' => 'PHP', 'color' => '#777BB4'],
            ['name' => 'JavaScript', 'color' => '#F7DF1E'],
            ['name' => 'Vue.js', 'color' => '#42B883'],
            ['name' => 'React', 'color' => '#61DAFB'],
            ['name' => 'MySQL', 'color' => '#4479A1'],
            ['name' => 'PostgreSQL', 'color' => '#336791'],
            ['name' => 'Docker', 'color' => '#2496ED'],
            ['name' => 'Git', 'color' => '#F05032'],
            ['name' => 'REST API', 'color' => '#009688'],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['slug' => Str::slug($tag['name'])],
                array_merge($tag, ['slug' => Str::slug($tag['name']), 'usage_count' => 0])
            );
        }

        $this->command->info('Tags berhasil dibuat.');
    }
}