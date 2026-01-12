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
        $adminPermissions = Permission::where('name', 'not like', '%Role%')
            ->pluck('name');
        
        if ($adminPermissions->isNotEmpty()) {
            $adminRole->syncPermissions($adminPermissions);
        }

        // Client - can only view their own data (basic view permissions)
        $clientPermissions = [
            'View:Client',
            'View:Project', 
            'View:Milestone',
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
