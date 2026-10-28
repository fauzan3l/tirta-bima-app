<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Jalankan seeder database.
     */
    public function run(): void
    {
        // 1. Akun Admin Utama
        // 2. Akun Pelatih (Coach)
        User::create([
            'name' => 'Coach Bima',
            'email' => 'coach@tirta.com',
            'password' => Hash::make('password123'),
            'role' => 'pelatih',
            'phone' => '081234567891',
            'terms_accepted_at' => now(),
        ]);

        // 3. Akun Murid / Atlet Renang
        User::create([
            'name' => 'Dimas Anggara',
            'email' => 'dimas@tirta.com',
            'password' => Hash::make('password123'),
            'role' => 'murid',
            'phone' => '081234567892',
            'terms_accepted_at' => now(),
        ]);
    }
}