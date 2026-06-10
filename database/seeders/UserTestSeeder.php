<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Auth\User;
use App\Models\Auth\Role;
use Illuminate\Support\Facades\Hash;

class UserTestSeeder extends Seeder
{
    public function run(): void
    {
        $userRole = Role::where('name', 'user')->first();
        $modRole = Role::where('name', 'moderator')->first();
        $adminRole = Role::where('name', 'admin')->first();

        // ─── 3 Main test accounts (user, moderator, admin) ───
        $mainUsers = [
            [
                'email' => 'user@email.com',
                'username' => 'userbiasa',
                'level' => 1,
                'reputation_points' => 25,
                'bio' => 'User biasa yang suka bertanya dan belajar.',
                'avatar_url' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=userbiasa',
                'role' => $userRole,
            ],
            [
                'email' => 'moderator@email.com',
                'username' => 'moderator_suhu',
                'level' => 5,
                'reputation_points' => 250,
                'bio' => 'Moderator forum, siap membantu menjaga kualitas diskusi.',
                'avatar_url' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=modsuhu',
                'role' => $modRole,
            ],
            [
                'email' => 'admin@email.com',
                'username' => 'admin_master',
                'level' => 10,
                'reputation_points' => 1000,
                'bio' => 'Administrator sistem Mau Tanya Suhu.',
                'avatar_url' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=adminmaster',
                'role' => $adminRole,
            ],
        ];

        foreach ($mainUsers as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'username' => $data['username'],
                    'password_hash' => Hash::make('password123'),
                    'level' => $data['level'],
                    'reputation_points' => $data['reputation_points'],
                    'bio' => $data['bio'],
                    'avatar_url' => $data['avatar_url'],
                    'is_banned' => false,
                    'created_at' => now()->subDays(rand(30, 90)),
                ]
            );
            $user->roles()->syncWithoutDetaching([$data['role']->id]);
        }

        // ─── 7 Additional users for leaderboard, follows, interactions ───
        $extraUsers = [
            ['email' => 'budi@email.com', 'username' => 'budi_dev', 'level' => 3, 'points' => 150, 'bio' => 'Full-stack developer suka Laravel.', 'days_ago' => 5],
            ['email' => 'siti@email.com', 'username' => 'siti_coder', 'level' => 4, 'points' => 200, 'bio' => 'Backend engineer, PHP enthusiast.', 'days_ago' => 3],
            ['email' => 'andi@email.com', 'username' => 'andi_js', 'level' => 2, 'points' => 80, 'bio' => 'Frontend dev, React & Vue.', 'days_ago' => 10],
            ['email' => 'dewi@email.com', 'username' => 'dewi_react', 'level' => 6, 'points' => 350, 'bio' => 'Senior developer, mentor di forum.', 'days_ago' => 2],
            ['email' => 'rizki@email.com', 'username' => 'rizki_php', 'level' => 3, 'points' => 120, 'bio' => 'Mahasiswa IT, suka ngoding.', 'days_ago' => 8],
            ['email' => 'putri@email.com', 'username' => 'putri_vue', 'level' => 5, 'points' => 280, 'bio' => 'UI/UX designer + developer.', 'days_ago' => 1],
            ['email' => 'banned_user@email.com', 'username' => 'user_nakal', 'level' => 1, 'points' => 0, 'bio' => 'Akun ini telah diblokir.', 'days_ago' => 60, 'banned' => true],
        ];

        foreach ($extraUsers as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'username' => $data['username'],
                    'password_hash' => Hash::make('password123'),
                    'level' => $data['level'],
                    'reputation_points' => $data['points'],
                    'bio' => $data['bio'],
                    'avatar_url' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . $data['username'],
                    'is_banned' => $data['banned'] ?? false,
                    'created_at' => now()->subDays($data['days_ago']),
                ]
            );
            $user->roles()->syncWithoutDetaching([$userRole->id]);
        }

        $this->command->info('✅ 10 Users created (3 main + 7 extra):');
        $this->command->info('   • user@email.com / password123 (User)');
        $this->command->info('   • moderator@email.com / password123 (Moderator)');
        $this->command->info('   • admin@email.com / password123 (Admin)');
        $this->command->info('   • +7 additional users for leaderboard, follows, interactions');
    }
}
