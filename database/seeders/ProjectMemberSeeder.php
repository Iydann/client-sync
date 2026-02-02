<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Project 1 - Website Company Profile
        // Team: Rina (3), Bambang (4)
        Project::find(1)->members()->sync([3, 4]);

        // Project 3 - E-Commerce Platform
        // Team: Bambang (4), Siti (5)
        Project::find(3)->members()->sync([4, 5]);

        // Project 4 - Mobile App Delivery
        // Team: Rina (3), Siti (5)
        Project::find(4)->members()->sync([3, 5]);
    }
}

