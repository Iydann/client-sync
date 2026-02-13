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
        
        // Admin - has full access except roles/permissions management (shield)
        $restrictedAdminPermissions = Permission::query()
            ->where('name', 'like', '%:Role')
            ->orWhere('name', 'like', '%:Permission')
            ->pluck('name');

        $adminPermissions = Permission::query()
            ->whereNotIn('name', $restrictedAdminPermissions)
            ->pluck('name');

        if ($adminPermissions->isNotEmpty()) {
            $adminRole->syncPermissions($adminPermissions);
        }

        $pagePermissionsAll = [
            'View:Dashboard',
        ];

        $pagePermissionsForClient = [
            'View:ProjectTimeline',
        ];

        $pagePermissionsForStaff = [
            'View:ProjectTimeline',
            'View:UserContributions',
        ];

        $widgetPermissionsForStaff = [
            'View:StatsOverview',
            'View:ContractValuePerYearChart',
            'View:UpcomingProjectDeadlines',
        ];

        $widgetPermissionsForClient = [
            'View:StatsOverview',
            'View:UpcomingProjectDeadlines',
        ];

        $existingPagePermissions = Permission::whereIn('name', $pagePermissionsAll)->pluck('name');
        $existingPagePermissionsForClient = Permission::whereIn('name', $pagePermissionsForClient)->pluck('name');
        $existingPagePermissionsForStaff = Permission::whereIn('name', $pagePermissionsForStaff)->pluck('name');
        $existingWidgetPermissionsForStaff = Permission::whereIn('name', $widgetPermissionsForStaff)->pluck('name');
        $existingWidgetPermissionsForClient = Permission::whereIn('name', $widgetPermissionsForClient)->pluck('name');

        // Client - can only view their own projects, milestones, and invoices
        // ViewAny is needed for sidebar navigation, but Policy will filter to show only their data
        $clientPermissions = [
            'ViewAny:Project',
            'View:Project', 
            'ViewAny:Milestone',
            'View:Milestone',
            'ViewAny:Invoice',
            'View:Invoice',
            'ViewAny:ProjectDiscussion',
            'View:ProjectDiscussion',
            'Create:ProjectDiscussion',
            'ViewAny:Task',
            'View:Task',
        ];
        
        $existingClientPermissions = Permission::whereIn('name', $clientPermissions)->pluck('name');
        if ($existingClientPermissions->isNotEmpty()) {
            $clientRole->syncPermissions($existingClientPermissions
                ->merge($existingPagePermissions)
                ->merge($existingPagePermissionsForClient)
                ->merge($existingWidgetPermissionsForClient));
        }

        // Developer - view-only for projects and milestones
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

            // Discussion permissions
            'ViewAny:ProjectDiscussion',
            'View:ProjectDiscussion',
            'Create:ProjectDiscussion',
        ];
        
        $existingDeveloperPermissions = Permission::whereIn('name', $developerPermissions)->pluck('name');
        if ($existingDeveloperPermissions->isNotEmpty()) {
            $developerRole->syncPermissions($existingDeveloperPermissions
                ->merge($existingPagePermissions)
                ->merge($existingPagePermissionsForStaff)
                ->merge($existingWidgetPermissionsForStaff));
                
        }

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Super Admin role has ' . $superAdminRole->permissions->count() . ' permissions');
        $this->command->info('Admin role has ' . $adminRole->permissions->count() . ' permissions');
        $this->command->info('Developer role has ' . $developerRole->permissions->count() . ' permissions');
        $this->command->info('Client role has ' . $clientRole->permissions->count() . ' permissions');
    }
}