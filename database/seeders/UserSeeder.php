<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin',
            'no_hp' => '081234567890',
            'email' => 'superadmin@superadmin.com',
            'password' => Hash::make('12345678'), // Password default
            'role' => 'superadmin',
            'image' => null,
        ]);

        // Owner Bengkel
        User::create([
            'name' => 'Owner Bengkel',
            'no_hp' => '081234567891',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin_bengkel',
            'image' => null,
        ]);

        // Pelanggan
        User::create([
            'name' => 'Pelanggan',
            'no_hp' => '081234567892',
            'email' => 'user@user.com',
            'password' => Hash::make('12345678'),
            'role' => 'user',
            'image' => null,
        ]);
    }
}
