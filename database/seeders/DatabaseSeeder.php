<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class, // Run first to create roles and permissions
            UserSeeder::class,
            ClientSeeder::class,
            ProjectSeeder::class,
            ProjectMemberSeeder::class,
            MilestoneSeeder::class,
            TaskSeeder::class,
            UserContributionSeeder::class,
            InvoiceSeeder::class,
        ]);
    }
}
