<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core data first
            RolePermissionSeeder::class,  // Roles and permissions
            UserSeeder::class,           // Admin users
            BasicDataSeeder::class,      // Airlines, aircraft types, statuses
            
            // Business data
            SubcontractorSeeder::class,  // Subcontractors
            ContactSeeder::class,        // Contacts for subcontractors
            ComprehensiveSeeder::class,  // Projects and opportunities
            OpportunitySeeder::class,    // Additional opportunities if needed
            ProjectTeamSeeder::class,    // Project teams and assignments
        ]);
    }
}
