<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Auth\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'user', 'permissions' => json_encode(['basic_access'])],
            ['name' => 'moderator', 'permissions' => json_encode(['moderate_content', 'ban_user'])],
            ['name' => 'admin', 'permissions' => json_encode(['full_access'])],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }

        $this->command->info('Roles berhasil dibuat: user, moderator, admin');
    }
}