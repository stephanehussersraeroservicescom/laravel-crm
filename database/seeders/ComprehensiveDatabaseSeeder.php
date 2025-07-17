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
        $this->seedBaselineAircraftSeatConfigurations();
        $this->seedRolesAndPermissions();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
    
    private function truncateTables(): void
    {
        $tables = [
            'model_has_permissions',
            'model_has_roles',
            'role_has_permissions',
            'permissions',
            'roles',
            'audit_logs',
            'actions',
            'attachments',
            'aircraft_seat_configurations',
            'project_team_supporters',
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
            ['name' => 'Default', 'region' => 'North America', 'account_executive_id' => 1], // For baseline configurations
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
            
            // Forecasting fields for project level
            $linefitRetrofit = ['linefit', 'retrofit'][array_rand(['linefit', 'retrofit'])];
            $projectLifecycleDuration = rand(2, 8);
            $expectedStartYear = rand(2024, 2026);
            $expectedCloseYear = $expectedStartYear + ($projectLifecycleDuration - 1);
            
            // Generate realistic distribution pattern based on duration
            $distributionPatterns = [
                2 => [0.3, 0.7],
                3 => [0.2, 0.6, 0.2],
                4 => [0.15, 0.25, 0.35, 0.25],
                5 => [0.1, 0.2, 0.4, 0.2, 0.1],
                6 => [0.08, 0.15, 0.25, 0.27, 0.15, 0.1],
                7 => [0.07, 0.12, 0.18, 0.26, 0.18, 0.12, 0.07],
                8 => [0.06, 0.1, 0.15, 0.19, 0.25, 0.15, 0.1, 0.06],
            ];
            $distributionPattern = $distributionPatterns[$projectLifecycleDuration] ?? [0.2, 0.6, 0.2];
            
            $projects[] = [
                'name' => "{$airline->name} - {$aircraft->name}",
                'airline_id' => $airline->id,
                'aircraft_type_id' => $aircraft->id,
                'design_status_id' => $designStatus->id,
                'commercial_status_id' => $commercialStatus->id,
                'owner_id' => $users->random()->id, // Assign random user as project owner
                'comment' => "Interior design project for {$airline->name} {$aircraft->name} fleet - Project {$i} notes and requirements",
                'number_of_aircraft' => rand(5, 50),
                'linefit_retrofit' => $linefitRetrofit,
                'project_lifecycle_duration' => $projectLifecycleDuration,
                'distribution_pattern' => json_encode($distributionPattern),
                'expected_start_year' => $expectedStartYear,
                'expected_close_year' => $expectedCloseYear,
                'created_by' => $users->random()->id,
                'updated_by' => $users->random()->id,
                'deleted_by' => null,
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
                    'consent_given_at' => Carbon::now()->subDays(rand(1, 365)),
                    'consent_withdrawn_at' => null,
                    'data_processing_notes' => 'GDPR compliant contact - consent given for business communications',
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
        $projects = DB::table('projects')
            ->join('airlines', 'projects.airline_id', '=', 'airlines.id')
            ->join('aircraft_types', 'projects.aircraft_type_id', '=', 'aircraft_types.id')
            ->select('projects.*', 'airlines.name as airline_name', 'aircraft_types.name as aircraft_name')
            ->get();
        $users = DB::table('users')->get();
        $types = ['vertical', 'panels', 'covers', 'others'];
        $cabinClasses = ['first_class', 'business_class', 'premium_economy', 'economy'];
        $statuses = ['active', 'inactive', 'pending', 'completed'];
        
        for ($i = 1; $i <= 53; $i++) {
            $project = $projects->random();
            $user = $users->random();
            
            $cabinClass = $cabinClasses[array_rand($cabinClasses)];
            $type = $types[array_rand($types)];
            $probability = rand(10, 95);
            
            // Aircraft seat configuration fields
            $pricePerLinearYard = rand(15000, 20000) / 100; // 150-200 range
            $linearYardsPerSeat = rand(15, 30) / 10; // 1.5-3.0 range
            $seatsInOpportunity = $this->calculateSeatsInOpportunity($project, $cabinClass, $type);
            
            // Calculate potential value: per-aircraft value * number of aircraft
            $perAircraftValue = $seatsInOpportunity * $pricePerLinearYard * $linearYardsPerSeat;
            $totalPotentialValue = $perAircraftValue * $project->number_of_aircraft;
            
            // Calculate revenue distribution using project forecasting data and total potential value
            $expectedTotalValue = $totalPotentialValue * ($probability / 100);
            $revenueDistribution = [];
            $years = range($project->expected_start_year, $project->expected_close_year);
            $distributionPattern = json_decode($project->distribution_pattern, true);
            foreach ($years as $index => $year) {
                $percentage = $distributionPattern[$index] ?? 0;
                $revenueDistribution[$year] = round($expectedTotalValue * $percentage, 2);
            }
            
            // Generate volume by year based on project aircraft distribution
            $volumeByYear = [];
            $totalDistributed = 0;
            $yearCount = count($years);
            
            foreach ($years as $index => $year) {
                $percentage = $distributionPattern[$index] ?? 0;
                
                // For the last year, use remaining aircraft to ensure total matches
                if ($index === $yearCount - 1) {
                    $aircraftForYear = $project->number_of_aircraft - $totalDistributed;
                } else {
                    $aircraftForYear = round($project->number_of_aircraft * $percentage);
                    $totalDistributed += $aircraftForYear;
                }
                
                $volumeByYear[$year] = max(0, $aircraftForYear); // Ensure non-negative
            }
            
            // Format cabin class for display (replace underscores with spaces and capitalize)
            $cabinClassDisplay = str_replace('_', ' ', ucwords($cabinClass, '_'));
            
            $opportunities[] = [
                'name' => "{$project->airline_name} - {$project->aircraft_name} - " . ucfirst($type) . " - {$cabinClassDisplay}",
                'project_id' => $project->id,
                'type' => $type,
                'revenue_distribution' => json_encode($revenueDistribution),
                'volume_by_year' => json_encode($volumeByYear),
                'forecasting_notes' => "Forecasting data for {$project->airline_name} {$project->linefit_retrofit} {$type} opportunity spanning {$project->project_lifecycle_duration} years with expected revenue of $" . number_format(array_sum($revenueDistribution)),
                'cabin_class' => $cabinClass,
                'status' => $statuses[array_rand($statuses)],
                'probability' => $probability,
                'potential_value' => $totalPotentialValue,
                'price_per_linear_yard' => $pricePerLinearYard,
                'linear_yards_per_seat' => $linearYardsPerSeat,
                'seats_in_opportunity' => $seatsInOpportunity,
                'aircraft_seat_config_id' => null, // Will be populated later with AI
                'comments' => "Business opportunity for {$project->airline_name} {$cabinClassDisplay} cabin {$type} - {$project->linefit_retrofit} implementation",
                'description' => "Comprehensive {$type} solution for {$project->airline_name} {$project->aircraft_name} {$cabinClassDisplay} cabin. This {$project->linefit_retrofit} project covers {$project->project_lifecycle_duration} years with {$probability}% probability of success.",
                'created_by' => $user->id,
                'assigned_to' => $users->random()->id,
                'updated_by' => $users->random()->id,
                'deleted_by' => null,
                'created_at' => Carbon::now()->subDays(rand(1, 180)),
                'updated_at' => Carbon::now()->subDays(rand(1, 30)),
            ];
        }
        
        DB::table('opportunities')->insert($opportunities);
    }
    
    private function calculateSeatsInOpportunity($project, $cabinClass, $type): int
    {
        // Base seat counts by aircraft type and cabin class
        $baseSeatCounts = [
            'B737-800' => [
                'first_class' => 12,
                'business_class' => 20,
                'premium_economy' => 30,
                'economy' => 150,
            ],
            'B787-9' => [
                'first_class' => 8,
                'business_class' => 28,
                'premium_economy' => 35,
                'economy' => 180,
            ],
            'A350' => [
                'first_class' => 12,
                'business_class' => 42,
                'premium_economy' => 24,
                'economy' => 200,
            ],
            'B777' => [
                'first_class' => 14,
                'business_class' => 52,
                'premium_economy' => 40,
                'economy' => 220,
            ],
            'A380' => [
                'first_class' => 14,
                'business_class' => 76,
                'premium_economy' => 44,
                'economy' => 350,
            ],
        ];
        
        // Get base seat count for this aircraft/cabin combination
        $aircraftName = $project->aircraft_name ?? 'B737-800';
        $baseSeats = $baseSeatCounts[$aircraftName][$cabinClass] ?? 100;
        
        // Opportunity type affects the percentage of seats involved
        $typeMultipliers = [
            'vertical' => 1.0,     // All seats (vertical surfaces)
            'panels' => 0.3,      // ~30% of seats (specific panels)
            'covers' => 0.5,      // ~50% of seats (seat covers)
            'others' => 0.2,      // ~20% of seats (other components)
        ];
        
        $multiplier = $typeMultipliers[$type] ?? 0.5;
        $seatsInOpportunity = round($baseSeats * $multiplier);
        
        // Ensure minimum of 1 seat
        return max(1, $seatsInOpportunity);
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
    
    private function seedBaselineAircraftSeatConfigurations(): void
    {
        // Get the Default airline ID
        $defaultAirline = DB::table('airlines')->where('name', 'Default')->first();
        if (!$defaultAirline) {
            return;
        }
        
        // Official Boeing and Airbus baseline seat configurations
        $baselineConfigs = [
            // Boeing B737-800 - Single class high density: 189, Two class typical: 162
            'B737-800' => [
                'first_class' => 0,
                'business_class' => 16,  // Typical 2-class config
                'premium_economy' => 0,
                'economy' => 146,
            ],
            // Boeing B787-9 - Typical two class: 296, Three class: 290
            'B787-9' => [
                'first_class' => 0,
                'business_class' => 30,  // Typical 3-class config
                'premium_economy' => 21,
                'economy' => 239,
            ],
            // Boeing B777-300ER - Typical two class: 396, Three class: 365
            'B777-300ER' => [
                'first_class' => 8,      // Typical 3-class config
                'business_class' => 52,
                'premium_economy' => 24,
                'economy' => 281,
            ],
            // Airbus A330-900 - Typical two class: 287, Three class: 260-300
            'A330-900' => [
                'first_class' => 0,
                'business_class' => 28,  // Typical 3-class config
                'premium_economy' => 21,
                'economy' => 238,
            ],
            // Airbus A350-900 - Typical three class: 300-350
            'A350-900' => [
                'first_class' => 0,
                'business_class' => 42,  // Typical 3-class config
                'premium_economy' => 24,
                'economy' => 259,
            ],
        ];
        
        $aircraftTypes = DB::table('aircraft_types')->get();
        $cabinClasses = ['first_class', 'business_class', 'premium_economy', 'economy'];
        
        foreach ($aircraftTypes as $aircraft) {
            if (isset($baselineConfigs[$aircraft->name])) {
                $config = $baselineConfigs[$aircraft->name];
                
                foreach ($cabinClasses as $cabinClass) {
                    if ($config[$cabinClass] > 0) {
                        DB::table('aircraft_seat_configurations')->insert([
                            'airline_id' => $defaultAirline->id,
                            'aircraft_type_id' => $aircraft->id,
                            'cabin_class' => $cabinClass,
                            'total_seats' => $config[$cabinClass],
                            'seat_map_data' => json_encode([
                                'layout' => $this->getBaselineLayout($cabinClass),
                                'pitch' => $this->getBaselinePitch($cabinClass),
                                'width' => $this->getBaselineWidth($cabinClass),
                                'manufacturer_baseline' => true,
                            ]),
                            'data_source' => 'manufacturer_baseline',
                            'confidence_score' => 1.0, // 100% confidence for manufacturer data
                            'last_verified_at' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
    
    private function getBaselineLayout($cabinClass)
    {
        return [
            'first_class' => '1-2-1',
            'business_class' => '2-2-2',
            'premium_economy' => '2-4-2',
            'economy' => '3-3-3',
        ][$cabinClass] ?? '3-3-3';
    }
    
    private function getBaselinePitch($cabinClass)
    {
        return [
            'first_class' => '80 inches',
            'business_class' => '60 inches',
            'premium_economy' => '38 inches',
            'economy' => '31 inches',
        ][$cabinClass] ?? '31 inches';
    }
    
    private function getBaselineWidth($cabinClass)
    {
        return [
            'first_class' => '22 inches',
            'business_class' => '20 inches',
            'premium_economy' => '19 inches',
            'economy' => '17 inches',
        ][$cabinClass] ?? '17 inches';
    }
    
    private function seedRolesAndPermissions(): void
    {
        // Create permissions
        $permissions = [
            'view_projects',
            'create_projects',
            'edit_projects',
            'delete_projects',
            'view_opportunities',
            'create_opportunities',
            'edit_opportunities',
            'delete_opportunities',
            'view_contacts',
            'create_contacts',
            'edit_contacts',
            'delete_contacts',
            'view_subcontractors',
            'create_subcontractors',
            'edit_subcontractors',
            'delete_subcontractors',
            'view_airlines',
            'create_airlines',
            'edit_airlines',
            'delete_airlines',
            'view_reports',
            'admin_access',
        ];
        
        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
                'name' => $permission,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Create roles
        $roles = [
            'admin' => $permissions, // Admin has all permissions
            'manager' => [
                'view_projects', 'create_projects', 'edit_projects',
                'view_opportunities', 'create_opportunities', 'edit_opportunities',
                'view_contacts', 'create_contacts', 'edit_contacts',
                'view_subcontractors', 'create_subcontractors', 'edit_subcontractors',
                'view_airlines', 'create_airlines', 'edit_airlines',
                'view_reports',
            ],
            'sales' => [
                'view_projects', 'create_projects', 'edit_projects',
                'view_opportunities', 'create_opportunities', 'edit_opportunities',
                'view_contacts', 'create_contacts', 'edit_contacts',
                'view_subcontractors', 'view_airlines',
            ],
            'read_only' => [
                'view_projects', 'view_opportunities', 'view_contacts',
                'view_subcontractors', 'view_airlines',
            ],
        ];
        
        foreach ($roles as $roleName => $rolePermissions) {
            // Create role
            $roleId = DB::table('roles')->insertGetId([
                'name' => $roleName,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Assign permissions to role
            foreach ($rolePermissions as $permission) {
                $permissionId = DB::table('permissions')->where('name', $permission)->first()->id;
                DB::table('role_has_permissions')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }
        
        // Assign roles to users
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $roleId = null;
            switch ($user->role) {
                case 'managers':
                    $roleId = DB::table('roles')->where('name', 'manager')->first()->id;
                    break;
                case 'sales':
                    $roleId = DB::table('roles')->where('name', 'sales')->first()->id;
                    break;
                case 'database manager':
                    $roleId = DB::table('roles')->where('name', 'admin')->first()->id;
                    break;
                default:
                    $roleId = DB::table('roles')->where('name', 'read_only')->first()->id;
                    break;
            }
            
            if ($roleId) {
                DB::table('model_has_roles')->insert([
                    'role_id' => $roleId,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $user->id,
                ]);
            }
        }
    }
}