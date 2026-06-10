<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            BadgeSeeder::class,
            UserTestSeeder::class,
            PostSeeder::class,
            CommentSeeder::class,
            InteractionSeeder::class,
            ModerationSeeder::class,
            GamificationSeeder::class,
        ]);
    }
}
