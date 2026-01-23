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
        Project::create([
            'client_id' => 1,
            'title' => 'Website Company Profile',
            'description' => 'Pembuatan website company profile dengan fitur blog dan portfolio.',
            'progress' => 50,
            'contract_date' => now()->addDays(60),
            'contract_number' => 'MB-001',
            'contract_value' => 30000000,
            'status' => 'in_progress',
            'start_date' => now(),
            'deadline' => now()->addDays(30),
            'payment_progress' => 50,
        ]);

        Project::create([
            'client_id' => 1,
            'title' => 'Sistem Inventory Management',
            'description' => 'Aplikasi web untuk mengelola stok barang dan laporan inventory.',
            'status' => 'pending',
            'contract_date' => now()->addDays(120),
            'contract_number' => 'SJ-012',
            'contract_value' => 25000000,
            'start_date' => now(),
            'deadline' => now()->addDays(60),
            'payment_progress' => 0,
        ]);

        Project::create([
            'client_id' => 2,
            'title' => 'E-Commerce Platform',
            'description' => 'Platform e-commerce dengan payment gateway dan sistem kurir.',
            'progress' => 66,
            'status' => 'in_progress',
            'contract_date' => now()->addDays(180),
            'contract_number' => 'BM-023',
            'contract_value' => 50000000,
            'start_date' => now()->subDays(30),
            'deadline' => now()->addDays(90),
            'payment_progress' => 60,
        ]);

        Project::create([
            'client_id' => 3,
            'title' => 'Mobile App Delivery',
            'description' => 'Aplikasi mobile untuk layanan delivery makanan.',
            'progress' => 100,
            'status' => 'completed',
            'contract_date' => now()->addMonths(3),
            'contract_number' => 'SA-034',
            'contract_value' => 35000000,
            'start_date' => now()->subMonths(2),
            'deadline' => now()->subDays(10),
            'payment_progress' => 100,
        ]);

        Project::create([
            'client_id' => 4,
            'title' => 'Dashboard Analytics',
            'description' => 'Dashboard untuk monitoring dan analisis data bisnis.',
            'status' => 'pending',
            'contract_date' => now()->addDays(90),
            'contract_number' => 'DA-045',
            'contract_value' => 20000000,
            'start_date' => now()->subDays(20),
            'deadline' => now()->addDays(45),
            'payment_progress' => 0,
        ]);
    }
}
