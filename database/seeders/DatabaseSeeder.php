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
            UserSeeder::class,           // Always create stef and dominic users
            ComprehensiveSeeder::class,  // Create other sample data
            ContactSeeder::class,        // Create contacts for subcontractors
            ProjectTeamSeeder::class,    // Create project teams
        ]);
    }
}
