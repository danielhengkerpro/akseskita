<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed akun admin (verifikator) & akun user contoh.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin AksesKita',
                'password' => Hash::make('admin123'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        User::updateOrCreate(
            ['email' => 'relawan@akseskita.id'],
            [
                'name' => 'Relawan AksesKita',
                'password' => Hash::make('password'),
                'role' => User::ROLE_USER,
            ]
        );

        User::updateOrCreate(
            ['email' => 'warga@akseskita.id'],
            [
                'name' => 'Warga Peduli',
                'password' => Hash::make('password'),
                'role' => User::ROLE_USER,
            ]
        );

        $this->command?->info("Admin: admin@gmail.com / admin123 (id: {$admin->id})");
    }
}
