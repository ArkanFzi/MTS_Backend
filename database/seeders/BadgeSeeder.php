<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gamification\Badge;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            // Post count
            ['name' => 'Newcomer',      'description' => 'Membuat post pertama',        'icon_url' => 'https://cdn-icons-png.flaticon.com/512/1/1.png', 'tier' => 'bronze',   'condition_type' => 'post_count',        'condition_value' => 1],
            ['name' => 'Active Member', 'description' => 'Membuat 10 post',             'icon_url' => 'https://cdn-icons-png.flaticon.com/512/1/1.png', 'tier' => 'silver',   'condition_type' => 'post_count',        'condition_value' => 10],
            ['name' => 'Veteran',       'description' => 'Membuat 50 post',             'icon_url' => 'https://cdn-icons-png.flaticon.com/512/1/1.png', 'tier' => 'gold',     'condition_type' => 'post_count',        'condition_value' => 50],

            // Reputation points
            ['name' => 'Rising Star',   'description' => 'Mencapai 100 poin',           'icon_url' => 'https://cdn-icons-png.flaticon.com/512/1/1.png', 'tier' => 'silver',   'condition_type' => 'reputation_points', 'condition_value' => 100],
            ['name' => 'Elite',         'description' => 'Mencapai 500 poin',           'icon_url' => 'https://cdn-icons-png.flaticon.com/512/1/1.png', 'tier' => 'gold',     'condition_type' => 'reputation_points', 'condition_value' => 500],
            ['name' => 'Legend',        'description' => 'Mencapai 1000 poin',          'icon_url' => 'https://cdn-icons-png.flaticon.com/512/1/1.png', 'tier' => 'platinum', 'condition_type' => 'reputation_points', 'condition_value' => 1000],

            // Answer accepted
            ['name' => 'Helper',        'description' => 'Jawaban pertama di-accept',   'icon_url' => 'https://cdn-icons-png.flaticon.com/512/1/1.png', 'tier' => 'bronze',   'condition_type' => 'answer_accepted',   'condition_value' => 1],
            ['name' => 'Expert',        'description' => '10 jawaban di-accept',        'icon_url' => 'https://cdn-icons-png.flaticon.com/512/1/1.png', 'tier' => 'gold',     'condition_type' => 'answer_accepted',   'condition_value' => 10],

            // Upvote received
            ['name' => 'Popular',       'description' => 'Mendapat 10 upvote',          'icon_url' => 'https://cdn-icons-png.flaticon.com/512/1/1.png', 'tier' => 'silver',   'condition_type' => 'upvote_received',   'condition_value' => 10],
            ['name' => 'Influential',   'description' => 'Mendapat 50 upvote',          'icon_url' => 'https://cdn-icons-png.flaticon.com/512/1/1.png', 'tier' => 'gold',     'condition_type' => 'upvote_received',   'condition_value' => 50],
        ];

        foreach ($badges as $badge) {
            Badge::firstOrCreate(['name' => $badge['name']], $badge);
        }

        $this->command->info('Badges berhasil dibuat.');
    }
}