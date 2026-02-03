<?php

namespace Database\Seeders;

use App\Models\Milestone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MilestoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Project 1 - Website Company Profile
        Milestone::create(['project_id' => 1, 'name' => 'Desain UI/UX', 'is_completed' => true,  'order' => 1]);
        Milestone::create(['project_id' => 1, 'name' => 'Development Frontend', 'is_completed' => true,  'order' => 2]);
        Milestone::create(['project_id' => 1, 'name' => 'Development Backend', 'is_completed' => false, 'order' => 3]);
        Milestone::create(['project_id' => 1, 'name' => 'Testing & Deployment', 'is_completed' => false, 'order' => 4]);

        // Project 3 - E-Commerce Platform
        Milestone::create(['project_id' => 3, 'name' => 'Requirement Analysis',       'is_completed' => true,  'order' => 1]);
        Milestone::create(['project_id' => 3, 'name' => 'Database Design',            'is_completed' => true,  'order' => 2]);
        Milestone::create(['project_id' => 3, 'name' => 'Payment Gateway Integration','is_completed' => false, 'order' => 3]);

        // Project 4 - Mobile App Delivery (completed)
        Milestone::create(['project_id' => 4, 'name' => 'Design & Prototype', 'is_completed' => true, 'order' => 1]);
        Milestone::create(['project_id' => 4, 'name' => 'Development',        'is_completed' => true, 'order' => 2]);
        Milestone::create(['project_id' => 4, 'name' => 'Testing',            'is_completed' => true, 'order' => 3]);
        Milestone::create(['project_id' => 4, 'name' => 'Launch',             'is_completed' => true, 'order' => 4]);

        // Project 6 - Client Portal Revamp (current)
        Milestone::create(['project_id' => 6, 'name' => 'Discovery & Planning', 'is_completed' => true,  'order' => 1]);
        Milestone::create(['project_id' => 6, 'name' => 'UI Redesign',          'is_completed' => false, 'order' => 2]);
        Milestone::create(['project_id' => 6, 'name' => 'Reporting Module',     'is_completed' => false, 'order' => 3]);
    }
}
