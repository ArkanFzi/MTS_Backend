<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Auth\User;
use App\Models\Auth\Role;

class UserTestSeeder extends Seeder
{
    public function run(): void
    {
        // User Biasa
        $user = User::firstOrCreate(
            ['email' => 'user@email.com'],
            [
                'username'      => 'userbiasa',
                'password_hash' => bcrypt('password123'),
                'level'         => 1,
                'is_banned'     => false,
            ]
        );
        $user->roles()->sync([Role::where('name', 'user')->first()->id]);

        // Moderator
        $mod = User::firstOrCreate(
            ['email' => 'moderator@email.com'],
            [
                'username'      => 'testmoderator',
                'password_hash' => bcrypt('password123'),
                'level'         => 5,
                'is_banned'     => false,
            ]
        );
        $mod->roles()->sync([Role::where('name', 'moderator')->first()->id]);

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@email.com'],
            [
                'username'      => 'testadmin',
                'password_hash' => bcrypt('password123'),
                'level'         => 10,
                'is_banned'     => false,
            ]
        );
        $admin->roles()->sync([Role::where('name', 'admin')->first()->id]);

        $this->command->info('   3 User Test berhasil dibuat:');
        $this->command->info('   • user@email.com / password123');
        $this->command->info('   • moderator@email.com / password123');
        $this->command->info('   • admin@email.com / password123');
    }
}