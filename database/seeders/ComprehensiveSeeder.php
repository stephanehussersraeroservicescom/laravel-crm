<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AircraftType;
use App\Models\Airline;
use App\Models\Status;
use App\Models\Material;
use App\Models\Project;
use App\Models\Subcontractor;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ComprehensiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only seed if tables are empty
        if (User::count() == 0) {
            $this->seedUsers();
        }
        
        if (AircraftType::count() == 0) {
            $this->seedAircraftTypes();
        }
        
        if (Airline::count() == 0) {
            $this->seedAirlines();
        }
        
        if (Status::count() == 0) {
            $this->seedStatuses();
        }
        
        if (Material::count() == 0) {
            $this->seedMaterials();
        }
        
        if (Subcontractor::count() == 0) {
            $this->seedSubcontractors();
        }
        
        if (Project::count() == 0) {
            $this->seedProjects();
        }
    }
    
    private function seedUsers()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'managers',
        ]);
        
        User::create([
            'name' => 'Database Manager',
            'email' => 'dbmanager@example.com',
            'password' => Hash::make('password'),
            'role' => 'database manager',
        ]);
        
        User::create([
            'name' => 'Sales User',
            'email' => 'sales@example.com',
            'password' => Hash::make('password'),
            'role' => 'sales',
        ]);
    }
    
    private function seedAircraftTypes()
    {
        $types = ['A350', 'A380', 'A330', 'B777', 'B787', 'B737', 'A320'];
        foreach ($types as $type) {
            AircraftType::create(['name' => $type]);
        }
    }
    
    private function seedAirlines()
    {
        $airlines = [
            ['name' => 'Emirates', 'region' => 'Middle East', 'account_executive' => 'Alice Johnson'],
            ['name' => 'Air France', 'region' => 'Europe', 'account_executive' => 'Bob Smith'],
            ['name' => 'Lufthansa', 'region' => 'Europe', 'account_executive' => 'Carol Wilson'],
            ['name' => 'Delta', 'region' => 'North America', 'account_executive' => 'David Brown'],
            ['name' => 'Singapore Airlines', 'region' => 'Asia', 'account_executive' => 'Eve Davis'],
            ['name' => 'United', 'region' => 'North America', 'account_executive' => 'Frank Miller'],
            ['name' => 'British Airways', 'region' => 'Europe', 'account_executive' => 'Grace Taylor'],
            ['name' => 'Qatar Airways', 'region' => 'Middle East', 'account_executive' => 'Henry Anderson'],
        ];
        
        foreach ($airlines as $airline) {
            Airline::create($airline);
        }
    }
    
    private function seedStatuses()
    {
        $statuses = [
            // Commercial statuses
            ['type' => 'commercial', 'status' => 'Not Started'],
            ['type' => 'commercial', 'status' => 'Negotiation'],
            ['type' => 'commercial', 'status' => 'Proposal Sent'],
            ['type' => 'commercial', 'status' => 'Under Review'],
            ['type' => 'commercial', 'status' => 'Agreed'],
            ['type' => 'commercial', 'status' => 'Signed'],
            
            // Design statuses
            ['type' => 'design', 'status' => 'Not Started'],
            ['type' => 'design', 'status' => 'Concept'],
            ['type' => 'design', 'status' => 'In Progress'],
            ['type' => 'design', 'status' => 'Review'],
            ['type' => 'design', 'status' => 'Approved'],
            ['type' => 'design', 'status' => 'Completed'],
            
            // Certification statuses
            ['type' => 'certification', 'status' => 'Not Started'],
            ['type' => 'certification', 'status' => 'Documentation'],
            ['type' => 'certification', 'status' => 'Testing'],
            ['type' => 'certification', 'status' => 'Review'],
            ['type' => 'certification', 'status' => 'Certified'],
        ];
        
        foreach ($statuses as $status) {
            Status::create($status);
        }
    }
    
    private function seedMaterials()
    {
        $materials = [
            ['part_number' => 'MAT-001', 'color' => 'Navy Blue', 'comment' => 'Premium leather for first class'],
            ['part_number' => 'MAT-002', 'color' => 'Charcoal Grey', 'comment' => 'Business class fabric'],
            ['part_number' => 'MAT-003', 'color' => 'Cream White', 'comment' => 'Premium economy material'],
            ['part_number' => 'MAT-004', 'color' => 'Burgundy', 'comment' => 'Accent material for premium cabins'],
            ['part_number' => 'MAT-005', 'color' => 'Forest Green', 'comment' => 'Eco-friendly sustainable material'],
            ['part_number' => 'MAT-006', 'color' => 'Silver', 'comment' => 'Metallic accent material'],
            ['part_number' => 'MAT-007', 'color' => 'Black', 'comment' => 'Standard economy class material'],
            ['part_number' => 'MAT-008', 'color' => 'Royal Blue', 'comment' => 'Brand color material'],
        ];
        
        foreach ($materials as $material) {
            Material::create($material);
        }
    }
    
    private function seedSubcontractors()
    {
        $subcontractors = [
            ['name' => 'Zodiac Aerospace', 'comment' => 'Leading cabin interior manufacturer'],
            ['name' => 'Safran Cabin', 'comment' => 'Seat and cabin systems specialist'],
            ['name' => 'Collins Aerospace', 'comment' => 'Interior systems and components'],
            ['name' => 'Jamco Corporation', 'comment' => 'Premium cabin interiors'],
            ['name' => 'Diehl Aviation', 'comment' => 'Cabin lighting and management systems'],
            ['name' => 'Recaro Aircraft Seating', 'comment' => 'Aircraft seating solutions'],
            ['name' => 'Thompson Aero Seating', 'comment' => 'Business and first class seating'],
            ['name' => 'Geven', 'comment' => 'Economy and premium economy seating'],
            ['name' => 'Acro Aircraft Seating', 'comment' => 'Lightweight seating solutions'],
            ['name' => 'Haeco Cabin Solutions', 'comment' => 'Cabin modification and interiors'],
        ];
        
        foreach ($subcontractors as $subcontractor) {
            Subcontractor::create($subcontractor);
        }
    }
    
    private function seedProjects()
    {
        $airlineIds = Airline::pluck('id')->toArray();
        $aircraftIds = AircraftType::pluck('id')->toArray();
        $designStatusIds = Status::where('type', 'design')->pluck('id')->toArray();
        $commercialStatusIds = Status::where('type', 'commercial')->pluck('id')->toArray();
        
        $projects = [
            [
                'name' => 'Emirates A350 Premium Interior',
                'airline_id' => $airlineIds[0] ?? 1,
                'aircraft_type_id' => $aircraftIds[0] ?? 1,
                'number_of_aircraft' => 50,
                'design_status_id' => $designStatusIds[2] ?? 1,
                'commercial_status_id' => $commercialStatusIds[4] ?? 1,
                'comment' => 'Premium interior design for Emirates A350 fleet with first class suites'
            ],
            [
                'name' => 'Lufthansa B777 Business Retrofit',
                'airline_id' => $airlineIds[2] ?? 2,
                'aircraft_type_id' => $aircraftIds[3] ?? 2,
                'number_of_aircraft' => 25,
                'design_status_id' => $designStatusIds[1] ?? 1,
                'commercial_status_id' => $commercialStatusIds[3] ?? 1,
                'comment' => 'Business class retrofit for Lufthansa long-haul fleet'
            ],
            [
                'name' => 'Singapore Airlines A380 First Class',
                'airline_id' => $airlineIds[4] ?? 3,
                'aircraft_type_id' => $aircraftIds[1] ?? 1,
                'number_of_aircraft' => 12,
                'design_status_id' => $designStatusIds[0] ?? 1,
                'commercial_status_id' => $commercialStatusIds[1] ?? 1,
                'comment' => 'Ultra-premium first class suites for A380 fleet'
            ],
            [
                'name' => 'Delta B737 Economy Plus',
                'airline_id' => $airlineIds[3] ?? 4,
                'aircraft_type_id' => $aircraftIds[5] ?? 3,
                'number_of_aircraft' => 100,
                'design_status_id' => $designStatusIds[3] ?? 2,
                'commercial_status_id' => $commercialStatusIds[2] ?? 2,
                'comment' => 'Economy Plus cabin upgrade for domestic fleet'
            ],
        ];
        
        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}
