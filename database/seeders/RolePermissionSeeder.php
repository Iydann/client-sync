<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $clientRole = Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);
        $developerRole = Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);

        // Super Admin - has all permissions
        $allPermissions = Permission::all();
        if ($allPermissions->isNotEmpty()) {
            $superAdminRole->syncPermissions($allPermissions);
        }
        
        // Admin - has full access to all permissions
        $adminPermissions = Permission::all()->pluck('name');
        
        if ($adminPermissions->isNotEmpty()) {
            $adminRole->syncPermissions($adminPermissions);
        }

        $pagePermissionsAll = [
            'View:Dashboard',
        ];

        $widgetPermissions = [
            'View:StatsOverview',
            'View:ContractValuePerYearChart',
            'View:UpcomingProjectDeadlines',
        ];

        $existingPagePermissions = Permission::whereIn('name', $pagePermissionsAll)->pluck('name');
        $existingWidgetPermissions = Permission::whereIn('name', $widgetPermissions)->pluck('name');

        // Client - can only view their own projects, milestones, invoices, and requests
        // ViewAny is needed for sidebar navigation, but Policy will filter to show only their data
        $clientPermissions = [
            'ViewAny:Project',
            'View:Project', 
            'ViewAny:Milestone',
            'View:Milestone',
            'ViewAny:Invoice',
            'View:Invoice',
            'ViewAny:ProjectRequest',
            'View:ProjectRequest',
            'Create:ProjectRequest',
            'ViewAny:ProjectRequestMessage',
            'View:ProjectRequestMessage',
            'Create:ProjectRequestMessage',
            'ViewAny:Task',
            'View:Task',
        ];
        
        $existingClientPermissions = Permission::whereIn('name', $clientPermissions)->pluck('name');
        if ($existingClientPermissions->isNotEmpty()) {
            $clientRole->syncPermissions($existingClientPermissions
                ->merge($existingPagePermissions)
                ->merge($existingWidgetPermissions));
        }

        // Developer - view-only for projects and milestones, can reply to requests
        $developerPermissions = [
            // Project permissions
            'ViewAny:Project',
            'View:Project',
            
            // Milestone permissions
            'ViewAny:Milestone',
            'View:Milestone',

            // Task permissions
            'ViewAny:Task',
            'View:Task',
            'Create:Task',
            'Update:Task',
            'Delete:Task',
            
            // Can view clients (read-only)
            'ViewAny:Client',
            'View:Client',

            // Request permissions (read + reply only)
            'ViewAny:ProjectRequest',
            'View:ProjectRequest',
            'ViewAny:ProjectRequestMessage',
            'View:ProjectRequestMessage',
            'Create:ProjectRequestMessage',
        ];
        
        $existingDeveloperPermissions = Permission::whereIn('name', $developerPermissions)->pluck('name');
        if ($existingDeveloperPermissions->isNotEmpty()) {
            $developerRole->syncPermissions($existingDeveloperPermissions
                ->merge($existingPagePermissions)
                ->merge($existingWidgetPermissions));
        }

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Super Admin role has ' . $superAdminRole->permissions->count() . ' permissions');
        $this->command->info('Admin role has ' . $adminRole->permissions->count() . ' permissions');
        $this->command->info('Developer role has ' . $developerRole->permissions->count() . ' permissions');
        $this->command->info('Client role has ' . $clientRole->permissions->count() . ' permissions');
    }
}