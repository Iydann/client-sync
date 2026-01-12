<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Project 1 - PT Maju Bersama
        Project::create([
            'client_id' => 1,
            'title' => 'Website Company Profile',
            'description' => 'Pembuatan website company profile dengan fitur blog dan portfolio.',
            'progress' => 50,
            'status' => 'in_progress',
            'deadline' => now()->addDays(30),
        ]);

        Project::create([
            'client_id' => 1,
            'title' => 'Sistem Inventory Management',
            'description' => 'Aplikasi web untuk mengelola stok barang dan laporan inventory.',
            'status' => 'pending',
            'deadline' => now()->addDays(60),
        ]);

        // Project 2 - CV Sejahtera Jaya
        Project::create([
            'client_id' => 2,
            'title' => 'E-Commerce Platform',
            'description' => 'Platform e-commerce dengan payment gateway dan sistem kurir.',
            'progress' => 66,
            'status' => 'in_progress',
            'deadline' => now()->addDays(90),
        ]);

        // Project 3 - UD Berkah Mandiri
        Project::create([
            'client_id' => 3,
            'title' => 'Mobile App Delivery',
            'description' => 'Aplikasi mobile untuk layanan delivery makanan.',
            'progress' => 100,
            'status' => 'completed',
            'deadline' => now()->subDays(10),
        ]);

        Project::create([
            'client_id' => 3,
            'title' => 'Dashboard Analytics',
            'description' => 'Dashboard untuk monitoring dan analisis data bisnis.',
            'status' => 'pending',
            'deadline' => now()->addDays(45),
        ]);
    }
}
