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
            'user_id' => 4, // Budi
            'client_name' => 'Maju Bersama',
            'client_type' => 'organization',
            'phone' => '081234567890',
            'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
        ]);

        Client::create([
            'user_id' => 5, // Siti
            'client_name' => 'Sejahtera Jaya',
            'client_type' => 'organization',
            'phone' => '082345678901',
            'address' => 'Jl. Gatot Subroto No. 45, Bandung',
        ]);

        Client::create([
            'user_id' => 6, // Ahmad
            'client_name' => 'Berkah Mandiri',
            'client_type' => 'organization',
            'phone' => '083456789012',
            'address' => 'Jl. Ahmad Yani No. 78, Surabaya',
        ]);

        Client::create([
            'user_id' => 7, // Dewi
            'client_name' => 'Sentosa Abadi',
            'client_type' => 'individual',
            'phone' => '084567890123',
            'address' => 'Jl. Diponegoro No. 90, Yogyakarta',
        ]);
    }
}
