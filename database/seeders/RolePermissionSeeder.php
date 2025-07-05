<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Project permissions
            'view_projects',
            'create_projects',
            'edit_projects',
            'delete_projects',
            
            // Opportunity permissions
            'view_opportunities',
            'create_opportunities',
            'edit_opportunities',
            'delete_opportunities',
            
            // Subcontractor permissions
            'view_subcontractors',
            'create_subcontractors',
            'edit_subcontractors',
            'delete_subcontractors',
            
            // Contact permissions
            'view_contacts',
            'create_contacts',
            'edit_contacts',
            'delete_contacts',
            
            // Team permissions
            'view_teams',
            'create_teams',
            'edit_teams',
            'delete_teams',
            
            // Airline permissions
            'view_airlines',
            'create_airlines',
            'edit_airlines',
            'delete_airlines',
            
            // User management permissions
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            
            // Advanced permissions
            'manage_roles',
            'view_audit_logs',
            'export_data',
            'import_data',
            'manage_system_settings',
            'view_financial_data',
            'bulk_operations',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin role - all permissions
        $adminRole = Role::firstOrCreate(['name' => User::ROLE_ADMIN]);
        $adminRole->syncPermissions(Permission::all());

        // Project Manager role - can manage projects and opportunities
        $projectManagerRole = Role::firstOrCreate(['name' => User::ROLE_PROJECT_MANAGER]);
        $projectManagerRole->syncPermissions([
            'view_projects', 'create_projects', 'edit_projects', 'delete_projects',
            'view_opportunities', 'create_opportunities', 'edit_opportunities', 'delete_opportunities',
            'view_subcontractors', 'create_subcontractors', 'edit_subcontractors',
            'view_contacts', 'create_contacts', 'edit_contacts',
            'view_teams', 'create_teams', 'edit_teams', 'delete_teams',
            'view_airlines', 'create_airlines', 'edit_airlines',
            'export_data', 'import_data', 'bulk_operations',
        ]);

        // Viewer role - read-only access
        $viewerRole = Role::firstOrCreate(['name' => User::ROLE_VIEWER]);
        $viewerRole->syncPermissions([
            'view_projects',
            'view_opportunities',
            'view_subcontractors',
            'view_contacts',
            'view_teams',
            'view_airlines',
        ]);

        // Assign admin role to first user if exists
        $firstUser = User::first();
        if ($firstUser) {
            $firstUser->assignRole(User::ROLE_ADMIN);
        }
    }
}