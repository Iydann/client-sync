<?php

namespace Database\Seeders;

use App\Models\UserContribution;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserContributionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $year = now()->year;
        $developers = User::role('developer')->get();

        // Create sample contributions for each developer
        // Rina Wijaya (ID 3)
        for ($i = 0; $i < 45; $i++) {
            UserContribution::create([
                'user_id' => 3,
                'type' => match (rand(1, 3)) {
                    1 => 'create_task',
                    2 => 'update_task',
                    default => 'delete_task'
                },
                'value' => 1,
                'year' => $year,
                'created_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        // Bambang Irawan (ID 4)
        for ($i = 0; $i < 32; $i++) {
            UserContribution::create([
                'user_id' => 4,
                'type' => match (rand(1, 3)) {
                    1 => 'create_task',
                    2 => 'update_task',
                    default => 'delete_task'
                },
                'value' => 1,
                'year' => $year,
                'created_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        // Siti Rahayu (ID 5)
        for ($i = 0; $i < 28; $i++) {
            UserContribution::create([
                'user_id' => 5,
                'type' => match (rand(1, 3)) {
                    1 => 'create_task',
                    2 => 'update_task',
                    default => 'delete_task'
                },
                'value' => 1,
                'year' => $year,
                'created_at' => now()->subDays(rand(0, 30)),
            ]);
        }
    }
}
