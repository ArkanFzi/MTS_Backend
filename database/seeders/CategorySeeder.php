<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Content\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // ─── Parent categories ───
        $parents = [
            ['name' => 'Programming', 'description' => 'Diskusi seputar pemrograman umum'],
            ['name' => 'Database', 'description' => 'Diskusi seputar database dan penyimpanan data'],
            ['name' => 'DevOps', 'description' => 'Diskusi seputar infrastruktur dan deployment'],
            ['name' => 'Mobile Development', 'description' => 'Diskusi seputar mobile development'],
            ['name' => 'UI/UX Design', 'description' => 'Diskusi seputar desain antarmuka dan pengalaman pengguna'],
        ];

        $parentIds = [];
        foreach ($parents as $category) {
            $cat = Category::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                array_merge($category, [
                    'slug' => Str::slug($category['name']),
                    'created_at' => now()->subDays(rand(60, 120)),
                ])
            );
            $parentIds[$category['name']] = $cat->id;
        }

        // ─── Child categories (sub-categories) ───
        $children = [
            ['name' => 'Laravel', 'description' => 'Framework PHP Laravel', 'parent' => 'Programming'],
            ['name' => 'JavaScript', 'description' => 'Bahasa pemrograman JavaScript', 'parent' => 'Programming'],
            ['name' => 'Python', 'description' => 'Bahasa pemrograman Python', 'parent' => 'Programming'],
            ['name' => 'PostgreSQL', 'description' => 'Database relasional PostgreSQL', 'parent' => 'Database'],
            ['name' => 'MySQL', 'description' => 'Database relasional MySQL', 'parent' => 'Database'],
            ['name' => 'MongoDB', 'description' => 'Database NoSQL MongoDB', 'parent' => 'Database'],
            ['name' => 'Docker', 'description' => 'Containerization dengan Docker', 'parent' => 'DevOps'],
            ['name' => 'CI/CD', 'description' => 'Continuous Integration & Deployment', 'parent' => 'DevOps'],
            ['name' => 'Flutter', 'description' => 'Framework mobile Flutter', 'parent' => 'Mobile Development'],
            ['name' => 'React Native', 'description' => 'Framework mobile React Native', 'parent' => 'Mobile Development'],
        ];

        foreach ($children as $child) {
            $parentId = $parentIds[$child['parent']] ?? null;
            Category::firstOrCreate(
                ['slug' => Str::slug($child['name'])],
                [
                    'name' => $child['name'],
                    'slug' => Str::slug($child['name']),
                    'description' => $child['description'],
                    'parent_id' => $parentId,
                    'created_at' => now()->subDays(rand(30, 90)),
                ]
            );
        }

        $this->command->info('✅ 15 Categories created (5 parents + 10 children).');
    }
}
