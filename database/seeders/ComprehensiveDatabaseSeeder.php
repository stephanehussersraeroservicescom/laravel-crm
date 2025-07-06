<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ComprehensiveDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to allow seeding in any order
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data
        $this->truncateTables();
        
        // Seed data in the correct order
        $this->seedUsers();
        $this->seedAirlines();
        $this->seedAircraftTypes();
        $this->seedStatuses();
        $this->seedMaterials();
        $this->seedProjects();
        $this->seedSubcontractors();
        $this->seedContacts();
        $this->seedOpportunities();
        $this->seedProjectSubcontractorTeams();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
    
    private function truncateTables(): void
    {
        $tables = [
            'project_subcontractor_teams',
            'opportunities',
            'contacts',
            'subcontractor_subcontractor',
            'subcontractors',
            'projects',
            'materials',
            'statuses',
            'aircraft_types',
            'airlines',
            'users',
        ];
        
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
    }
    
    private function seedUsers(): void
    {
        $users = [
            [
                'name' => 'Dom',
                'email' => 'dom@jmail.con',
                'password' => Hash::make('ste67676767'),
                'role' => 'sales',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Stef',
                'email' => 'stef@jmail.con',
                'password' => Hash::make('ste67676767'),
                'role' => 'sales',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Jason',
                'email' => 'jason@jmail.con',
                'password' => Hash::make('ste67676767'),
                'role' => 'managers',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
        
        DB::table('users')->insert($users);
    }
    
    private function seedAirlines(): void
    {
        $airlines = [
            ['name' => 'American Airlines', 'region' => 'North America', 'account_executive_id' => 1],
            ['name' => 'Delta Air Lines', 'region' => 'North America', 'account_executive_id' => 1],
            ['name' => 'United Airlines', 'region' => 'North America', 'account_executive_id' => 2],
            ['name' => 'Lufthansa', 'region' => 'Europe', 'account_executive_id' => 2],
            ['name' => 'British Airways', 'region' => 'Europe', 'account_executive_id' => 1],
            ['name' => 'Air France', 'region' => 'Europe', 'account_executive_id' => 2],
            ['name' => 'Emirates', 'region' => 'Middle East', 'account_executive_id' => 1],
            ['name' => 'Qatar Airways', 'region' => 'Middle East', 'account_executive_id' => 2],
            ['name' => 'Singapore Airlines', 'region' => 'Asia', 'account_executive_id' => 1],
        ];
        
        foreach ($airlines as $airline) {
            DB::table('airlines')->insert(array_merge($airline, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }
    }
    
    private function seedAircraftTypes(): void
    {
        $aircraft = [
            ['name' => 'A350-900', 'manufacturer' => 'Airbus'],
            ['name' => 'B787-9', 'manufacturer' => 'Boeing'],
            ['name' => 'B777-300ER', 'manufacturer' => 'Boeing'],
            ['name' => 'A330-900', 'manufacturer' => 'Airbus'],
            ['name' => 'B737-800', 'manufacturer' => 'Boeing'],
        ];
        
        foreach ($aircraft as $type) {
            DB::table('aircraft_types')->insert(array_merge($type, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }
    }
    
    private function seedStatuses(): void
    {
        $statuses = [
            ['status' => 'Concept', 'type' => 'design'],
            ['status' => 'Design', 'type' => 'design'],
            ['status' => 'Engineering', 'type' => 'design'],
            ['status' => 'Testing', 'type' => 'design'],
            ['status' => 'Approved', 'type' => 'design'],
            ['status' => 'Proposal', 'type' => 'commercial'],
            ['status' => 'Negotiation', 'type' => 'commercial'],
            ['status' => 'Contract', 'type' => 'commercial'],
            ['status' => 'Delivery', 'type' => 'commercial'],
            ['status' => 'Closed', 'type' => 'commercial'],
            ['status' => 'Pending', 'type' => 'certification'],
            ['status' => 'In Progress', 'type' => 'certification'],
            ['status' => 'Certified', 'type' => 'certification'],
        ];
        
        foreach ($statuses as $status) {
            DB::table('statuses')->insert(array_merge($status, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }
    }
    
    private function seedMaterials(): void
    {
        $materials = [
            ['part_number' => 'ALU-001', 'color' => 'Silver', 'comment' => 'Aluminum Panel for cabin interiors'],
            ['part_number' => 'CF-002', 'color' => 'Black', 'comment' => 'Carbon Fiber Sheet for lightweight components'],
            ['part_number' => 'COMP-003', 'color' => 'White', 'comment' => 'Composite Panel for sidewalls'],
            ['part_number' => 'TI-004', 'color' => 'Gray', 'comment' => 'Titanium Bracket for structural mounting'],
            ['part_number' => 'ST-005', 'color' => 'Silver', 'comment' => 'Steel Fastener for panel assembly'],
        ];
        
        foreach ($materials as $material) {
            DB::table('materials')->insert(array_merge($material, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }
    }
    
    private function seedProjects(): void
    {
        $projects = [];
        $airlines = DB::table('airlines')->get();
        $aircraftTypes = DB::table('aircraft_types')->get();
        $designStatuses = DB::table('statuses')->where('type', 'design')->get();
        $commercialStatuses = DB::table('statuses')->where('type', 'commercial')->get();
        $users = DB::table('users')->get();
        
        for ($i = 1; $i <= 30; $i++) {
            $airline = $airlines->random();
            $aircraft = $aircraftTypes->random();
            $designStatus = $designStatuses->random();
            $commercialStatus = $commercialStatuses->random();
            
            $projects[] = [
                'name' => "Project {$i} - {$airline->name} {$aircraft->name}",
                'airline_id' => $airline->id,
                'aircraft_type_id' => $aircraft->id,
                'design_status_id' => $designStatus->id,
                'commercial_status_id' => $commercialStatus->id,
                'owner' => $users->random()->name, // Assign random user as project owner
                'comment' => "Interior design project for {$airline->name} {$aircraft->name} fleet - Project {$i} notes and requirements",
                'number_of_aircraft' => rand(5, 50),
                'created_by' => rand(1, 3),
                'created_at' => Carbon::now()->subDays(rand(1, 365)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ];
        }
        
        DB::table('projects')->insert($projects);
    }
    
    private function seedSubcontractors(): void
    {
        $subcontractors = [
            ['name' => 'Aerospace Interiors Ltd', 'comment' => 'Specializes in Interior Design'],
            ['name' => 'Cabin Systems Inc', 'comment' => 'Specializes in Cabin Systems'],
            ['name' => 'Premium Seating Co', 'comment' => 'Specializes in Seating'],
            ['name' => 'Galley Solutions', 'comment' => 'Specializes in Galley Equipment'],
            ['name' => 'Lighting Dynamics', 'comment' => 'Specializes in Lighting Systems'],
            ['name' => 'Composite Materials Corp', 'comment' => 'Specializes in Composite Manufacturing'],
            ['name' => 'Fastener Systems Ltd', 'comment' => 'Specializes in Hardware'],
            ['name' => 'Testing Services Inc', 'comment' => 'Specializes in Quality Assurance'],
            ['name' => 'Certification Experts', 'comment' => 'Specializes in Certification'],
            ['name' => 'Engineering Consultants', 'comment' => 'Specializes in Engineering'],
            ['name' => 'Installation Services', 'comment' => 'Specializes in Installation'],
            ['name' => 'Maintenance Solutions', 'comment' => 'Specializes in Maintenance'],
        ];
        
        foreach ($subcontractors as $subcontractor) {
            DB::table('subcontractors')->insert(array_merge($subcontractor, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }
    }
    
    private function seedContacts(): void
    {
        $contacts = [];
        $subcontractors = DB::table('subcontractors')->get();
        $roles = ['engineering', 'program_management', 'design', 'certification'];
        
        $contactNames = [
            'John Smith', 'Jane Doe', 'Michael Johnson', 'Sarah Wilson', 'David Brown',
            'Emily Davis', 'Robert Miller', 'Jessica Garcia', 'William Martinez', 'Ashley Rodriguez',
            'Christopher Lee', 'Amanda Taylor', 'Matthew Anderson', 'Jennifer Thomas', 'Daniel Jackson',
            'Nicole White', 'James Harris', 'Stephanie Martin', 'Joshua Thompson', 'Melissa Garcia',
            'Andrew Wilson', 'Rebecca Moore', 'Ryan Taylor', 'Laura Anderson', 'Kevin Thomas',
            'Lisa Jackson', 'Brian White', 'Karen Harris', 'Justin Martin', 'Amy Thompson',
            'Derek Wilson', 'Michelle Davis', 'Eric Johnson'
        ];
        
        $contactIndex = 0;
        foreach ($subcontractors as $subcontractor) {
            $contactsPerSubcontractor = rand(2, 4);
            
            for ($i = 0; $i < $contactsPerSubcontractor; $i++) {
                if ($contactIndex >= count($contactNames)) break;
                
                $name = $contactNames[$contactIndex];
                $firstName = explode(' ', $name)[0];
                $lastName = explode(' ', $name)[1];
                
                $contacts[] = [
                    'name' => $name,
                    'email' => strtolower($firstName) . '.' . strtolower($lastName) . '@' . strtolower(str_replace(' ', '', $subcontractor->name)) . '.com',
                    'phone' => '+1-' . rand(100, 999) . '-' . rand(100, 999) . '-' . rand(1000, 9999),
                    'role' => $roles[array_rand($roles)],
                    'subcontractor_id' => $subcontractor->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                
                $contactIndex++;
            }
        }
        
        DB::table('contacts')->insert($contacts);
    }
    
    private function seedOpportunities(): void
    {
        $opportunities = [];
        $projects = DB::table('projects')->get();
        $users = DB::table('users')->get();
        $types = ['vertical', 'panels', 'covers', 'others'];
        $cabinClasses = ['first_class', 'business_class', 'premium_economy', 'economy'];
        $statuses = ['active', 'inactive', 'pending', 'completed'];
        
        for ($i = 1; $i <= 53; $i++) {
            $project = $projects->random();
            $user = $users->random();
            
            $cabinClass = $cabinClasses[array_rand($cabinClasses)];
            $type = $types[array_rand($types)];
            
            $opportunities[] = [
                'name' => "Opportunity {$i} - {$type}",
                'project_id' => $project->id,
                'type' => $type,
                'cabin_class' => $cabinClass,
                'status' => $statuses[array_rand($statuses)],
                'probability' => rand(10, 95),
                'potential_value' => rand(50000, 2000000),
                'comments' => "Business opportunity for {$cabinClass} cabin enhancement - {$type} implementation",
                'description' => "Detailed description for {$type} opportunity in {$cabinClass} class",
                'owner' => $users->random()->name, // Assign random user as opportunity owner
                'created_by' => $user->id,
                'assigned_to' => $users->random()->id,
                'updated_by' => $users->random()->id,
                'created_at' => Carbon::now()->subDays(rand(1, 180)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ];
        }
        
        DB::table('opportunities')->insert($opportunities);
    }
    
    private function seedProjectSubcontractorTeams(): void
    {
        $teams = [];
        $opportunities = DB::table('opportunities')->get();
        $projects = DB::table('projects')->get();
        $subcontractors = DB::table('subcontractors')->get();
        $roles = ['Commercial', 'Project Management', 'Design', 'Certification', 'Manufacturing', 'Subcontractor'];
        
        foreach ($opportunities as $opportunity) {
            $project = $projects->where('id', $opportunity->project_id)->first();
            $teamsPerOpportunity = rand(1, 2);
            
            for ($i = 0; $i < $teamsPerOpportunity; $i++) {
                $mainSubcontractor = $subcontractors->random();
                
                $teams[] = [
                    'opportunity_id' => $opportunity->id,
                    'main_subcontractor_id' => $mainSubcontractor->id,
                    'role' => $roles[array_rand($roles)],
                    'notes' => "Team assignment for opportunity {$opportunity->name}",
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }
        
        DB::table('project_subcontractor_teams')->insert($teams);
    }
}