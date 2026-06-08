<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Content\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Programming', 'description' => 'Diskusi seputar pemrograman'],
            ['name' => 'Database', 'description' => 'Diskusi seputar database'],
            ['name' => 'DevOps', 'description' => 'Diskusi seputar DevOps'],
            ['name' => 'Mobile Development', 'description' => 'Diskusi seputar mobile dev'],
            ['name' => 'UI/UX Design', 'description' => 'Diskusi seputar desain'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                array_merge($category, ['slug' => Str::slug($category['name'])])
            );
        }

        $this->command->info('Categories berhasil dibuat.');
    }
}