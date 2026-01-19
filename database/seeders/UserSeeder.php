<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles (should be created by RolePermissionSeeder first)
        $superAdminRole = Role::where('name', 'super_admin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $clientRole = Role::where('name', 'client')->first();
        $developerRole = Role::where('name', 'developer')->first();

        // Super Admin User
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('password'),
        ]);
        $superAdmin->assignRole($superAdminRole);

        // Admin User
        $admin = User::create([
            'name' => 'Admin CV Deka',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole($adminRole);


        // Developer User
        $developer = User::create([
            'name' => 'Rina Wijaya',
            'email' => 'rina@gmail.com',
            'password' => Hash::make('password'),
        ]);
        $developer->assignRole($developerRole);

        // Client Users
        $client1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => Hash::make('password'),
        ]);
        $client1->assignRole($clientRole);

        $client2 = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@example.com',
            'password' => Hash::make('password'),
        ]);
        $client2->assignRole($clientRole);

        $client3 = User::create([
            'name' => 'Ahmad Fadli',
            'email' => 'ahmad@example.com',
            'password' => Hash::make('password'),
        ]);
        $client3->assignRole($clientRole);

        $client4 = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi@example.com',
            'password' => Hash::make('password'),
        ]);
        $client4->assignRole($clientRole);
    }
}
