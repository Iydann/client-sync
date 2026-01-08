<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin CV Deka',
            'email' => 'admin@cvdeka.com',
            'password' => Hash::make('password'),
        ]);

        // Client Users
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@example.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Ahmad Fadli',
            'email' => 'ahmad@example.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Rina Wijaya',
            'email' => 'rina@gmail.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => 'admin',
        ]);
    }
}
