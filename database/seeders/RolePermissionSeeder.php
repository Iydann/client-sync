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
        
        // Admin - has full access to all resources except Role management
        $adminPermissions = Permission::where('name', 'not like', '%role%')
            ->where('name', 'not like', '%shield%')
            ->pluck('name');
        
        if ($adminPermissions->isNotEmpty()) {
            $adminRole->syncPermissions($adminPermissions);
        }

        // Client - can only view their own projects, milestones, and invoices
        // ViewAny is needed for sidebar navigation, but Policy will filter to show only their data
        $clientPermissions = [
            'ViewAny:Project',
            'View:Project', 
            'ViewAny:Milestone',
            'View:Milestone',
            'ViewAny:Invoice',
            'View:Invoice',
        ];
        
        $existingClientPermissions = Permission::whereIn('name', $clientPermissions)->pluck('name');
        if ($existingClientPermissions->isNotEmpty()) {
            $clientRole->syncPermissions($existingClientPermissions);
        }

        // Developer - can manage projects, milestones, and invoices
        $developerPermissions = [
            // Project permissions
            'ViewAny:Project',
            'View:Project',
            'Create:Project',
            'Update:Project',
            'Delete:Project',
            'Restore:Project',
            
            // Milestone permissions
            'ViewAny:Milestone',
            'View:Milestone',
            'Create:Milestone',
            'Update:Milestone',
            'Delete:Milestone',
            'Restore:Milestone',
            
            // Invoice permissions
            'ViewAny:Invoice',
            'View:Invoice',
            'Create:Invoice',
            'Update:Invoice',
            'Delete:Invoice',
            'Restore:Invoice',
            
            // Can view clients (read-only)
            'ViewAny:Client',
            'View:Client',
        ];
        
        $existingDeveloperPermissions = Permission::whereIn('name', $developerPermissions)->pluck('name');
        if ($existingDeveloperPermissions->isNotEmpty()) {
            $developerRole->syncPermissions($existingDeveloperPermissions);
        }

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Super Admin role has ' . $superAdminRole->permissions->count() . ' permissions');
        $this->command->info('Admin role has ' . $adminRole->permissions->count() . ' permissions');
        $this->command->info('Developer role has ' . $developerRole->permissions->count() . ' permissions');
        $this->command->info('Client role has ' . $clientRole->permissions->count() . ' permissions');
    }
}
