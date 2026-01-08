<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::create([
            'user_id' => 2, // Budi
            'company_name' => 'PT Maju Bersama',
            'phone' => '081234567890',
            'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
        ]);

        Client::create([
            'user_id' => 3, // Siti
            'company_name' => 'CV Sejahtera Jaya',
            'phone' => '082345678901',
            'address' => 'Jl. Gatot Subroto No. 45, Bandung',
        ]);

        Client::create([
            'user_id' => 4, // Ahmad
            'company_name' => 'UD Berkah Mandiri',
            'phone' => '083456789012',
            'address' => 'Jl. Ahmad Yani No. 78, Surabaya',
        ]);
    }
}
