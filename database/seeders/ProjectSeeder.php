<?php

namespace Database\Seeders;

use App\Models\Project;
use Carbon\Carbon;
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
            'contract_date' => Carbon::create(2023, 2, 15),
            'contract_number' => 'MB-001',
            'contract_value' => 30000000,
            'ppn_amount' => 2907489,
            'pph_amount' => 660793,
            'include_tax' => true,
            'status' => 'in_progress',
            'start_date' => Carbon::create(2023, 2, 1),
            'deadline' => Carbon::create(2023, 3, 15),
            'payment_progress' => 50,
        ]);

        Project::create([
            'client_id' => 1,
            'title' => 'Sistem Inventory Management',
            'description' => 'Aplikasi web untuk mengelola stok barang dan laporan inventory.',
            'status' => 'pending',
            'contract_date' => Carbon::create(2024, 4, 10),
            'contract_number' => 'SJ-012',
            'contract_value' => 25000000,
            'ppn_amount' => 2422907,
            'pph_amount' => 550661,
            'include_tax' => true,
            'start_date' => Carbon::create(2024, 4, 1),
            'deadline' => Carbon::create(2024, 6, 1),
            'payment_progress' => 0,
        ]);

        Project::create([
            'client_id' => 2,
            'title' => 'E-Commerce Platform',
            'description' => 'Platform e-commerce dengan payment gateway dan sistem kurir.',
            'progress' => 66,
            'status' => 'in_progress',
            'contract_date' => Carbon::create(2025, 7, 20),
            'contract_number' => 'BM-023',
            'contract_value' => 50000000,
            'ppn_amount' => 4845815,
            'pph_amount' => 1101322,
            'include_tax' => true,
            'start_date' => Carbon::create(2025, 6, 15),
            'deadline' => Carbon::create(2025, 10, 15),
            'payment_progress' => 60,
        ]);

        Project::create([
            'client_id' => 3,
            'title' => 'Mobile App Delivery',
            'description' => 'Aplikasi mobile untuk layanan delivery makanan.',
            'progress' => 100,
            'status' => 'completed',
            'contract_date' => Carbon::create(2026, 1, 12),
            'contract_number' => 'SA-034',
            'contract_value' => 35000000,
            'ppn_amount' => 3392070,
            'pph_amount' => 770925,
            'include_tax' => true,
            'start_date' => Carbon::create(2025, 11, 10),
            'deadline' => Carbon::create(2026, 1, 2),
            'payment_progress' => 100,
        ]);

        Project::create([
            'client_id' => 4,
            'title' => 'Dashboard Analytics',
            'description' => 'Dashboard untuk monitoring dan analisis data bisnis.',
            'status' => 'pending',
            'contract_date' => Carbon::create(2023, 9, 5),
            'contract_number' => 'DA-045',
            'contract_value' => 20000000,
            'ppn_amount' => 1938326,
            'pph_amount' => 440529,
            'include_tax' => true,
            'start_date' => Carbon::create(2023, 8, 20),
            'deadline' => Carbon::create(2023, 10, 20),
            'payment_progress' => 0,
        ]);

        Project::create([
            'client_id' => 2,
            'title' => 'Client Portal Revamp',
            'description' => 'Pembaruan portal client dengan UI modern dan fitur reporting.',
            'progress' => 20,
            'status' => 'in_progress',
            'contract_date' => Carbon::create(2026, 2, 1),
            'contract_number' => 'CP-2026-001',
            'contract_value' => 42000000,
            'ppn_amount' => 4070485,
            'pph_amount' => 925110,
            'include_tax' => true,
            'start_date' => Carbon::create(2026, 1, 20),
            'deadline' => Carbon::create(2026, 4, 20),
            'payment_progress' => 10,
        ]);
    }
}
