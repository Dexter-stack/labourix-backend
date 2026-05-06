<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@labourix.com'],
            [
                'name'              => 'Super Admin',
                'password'          => Hash::make('Admin@1234'),
                'role'              => UserRole::SuperAdmin,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin account ready — email: admin@labourix.com | password: Admin@1234');
    }
}
