<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Airline;
use App\Models\Project;
use App\Models\Subcontractor;
use App\Models\Opportunity;
use App\Models\Action;

class OpportunitySeeder extends Seeder
{
    public function run(): void
    {
        // Create a few basic users if none exist
        if (User::count() === 0) {
            User::create(['name' => 'John Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password')]);
            User::create(['name' => 'Jane Manager', 'email' => 'manager@example.com', 'password' => bcrypt('password')]);
            User::create(['name' => 'Bob Engineer', 'email' => 'engineer@example.com', 'password' => bcrypt('password')]);
        }
        
        // Get existing data or create minimal data
        $users = User::all();
        $airlines = Airline::all();
        $projects = Project::all();
        $subcontractors = Subcontractor::all();
        
        // Create sample opportunities with the new structure
        $opportunities = [
            [
                'type' => 'panels',
                'cabin_class' => 'economy',
                'probability' => 85,
                'potential_value' => 750000.00,
                'status' => 'active',
                'name' => 'Economy Seat Back Panels',
                'description' => 'Lightweight seat back panel system for economy class',
                'comments' => 'High probability win - existing relationship',
                'created_by' => $users->first()?->id,
                'assigned_to' => $users->skip(1)->first()?->id,
            ],
            [
                'type' => 'covers',
                'cabin_class' => 'business_class',
                'probability' => 60,
                'potential_value' => 450000.00,
                'status' => 'active',
                'name' => 'Business Class Armrest Covers',
                'description' => 'Premium leather armrest covers for business class',
                'comments' => 'Competing with two other suppliers',
                'created_by' => $users->skip(1)->first()?->id,
                'assigned_to' => $users->skip(2)->first()?->id,
            ],
            [
                'type' => 'vertical',
                'cabin_class' => 'first_class',
                'probability' => 90,
                'potential_value' => 1200000.00,
                'status' => 'active',
                'name' => 'First Class Privacy Walls',
                'description' => 'Advanced privacy wall system for first class suites',
                'comments' => 'Preferred supplier status',
                'created_by' => $users->skip(2)->first()?->id,
                'assigned_to' => $users->first()?->id,
            ],
            [
                'type' => 'panels',
                'cabin_class' => 'premium_economy',
                'probability' => 75,
                'potential_value' => 580000.00,
                'status' => 'active',
                'description' => 'Premium economy interior panels with integrated lighting',
                'comments' => 'Strong technical proposal submitted',
                'created_by' => $users->first()?->id,
                'assigned_to' => $users->last()?->id,
            ],
            [
                'type' => 'others',
                'cabin_class' => 'first_class',
                'probability' => 95,
                'potential_value' => 2500000.00,
                'status' => 'active',
                'name' => 'Luxury Suite Components',
                'description' => 'Complete first class suite interior components',
                'comments' => 'Exclusive partnership opportunity',
                'created_by' => $users->last()?->id,
                'assigned_to' => $users->skip(1)->first()?->id,
            ],
            [
                'type' => 'covers',
                'cabin_class' => 'business_class',
                'probability' => 70,
                'potential_value' => 680000.00,
                'status' => 'active',
                'description' => 'Business class seat covers with smart fabric technology',
                'comments' => 'Innovative material proposal under review',
                'created_by' => $users->skip(1)->first()?->id,
                'assigned_to' => $users->skip(2)->first()?->id,
            ],
            [
                'type' => 'panels',
                'cabin_class' => 'economy',
                'probability' => 55,
                'potential_value' => 320000.00,
                'status' => 'active',
                'description' => 'Economy class sidewall panels with improved acoustics',
                'comments' => 'Technical discussions ongoing',
                'created_by' => $users->skip(2)->first()?->id,
                'assigned_to' => $users->first()?->id,
            ],
            [
                'type' => 'vertical',
                'cabin_class' => 'premium_economy',
                'probability' => 40,
                'potential_value' => 290000.00,
                'status' => 'draft',
                'description' => 'Premium economy divider walls',
                'comments' => 'Early stage discussions',
                'created_by' => $users->first()?->id,
                'assigned_to' => $users->last()?->id,
            ],
        ];
        
        foreach ($opportunities as $oppData) {
            $opportunity = Opportunity::create($oppData);
            
            // Attach to first available project if any exist
            if ($projects->count() > 0) {
                $opportunity->projects()->attach($projects->random()->id);
            }
            
            // Assign subcontractors if any exist
            if ($subcontractors->count() > 0) {
                $leadSubcontractor = $subcontractors->random();
                $opportunity->subcontractors()->attach($leadSubcontractor->id, [
                    'role' => 'lead',
                    'notes' => 'Primary contractor for this opportunity'
                ]);
                
                // Add supporting subcontractors
                $supportingCount = min(2, $subcontractors->count() - 1);
                if ($supportingCount > 0) {
                    $supportingSubcontractors = $subcontractors->except($leadSubcontractor->id)->random($supportingCount);
                    foreach ($supportingSubcontractors as $supporter) {
                        $opportunity->subcontractors()->attach($supporter->id, [
                            'role' => 'supporting',
                            'notes' => 'Supporting contractor role'
                        ]);
                    }
                }
            }
            
            // Create sample actions for some opportunities
            if (rand(1, 3) === 1) {
                Action::create([
                    'actionable_type' => Opportunity::class,
                    'actionable_id' => $opportunity->id,
                    'title' => 'Follow up on technical proposal',
                    'description' => 'Schedule meeting to discuss technical specifications and requirements',
                    'type' => 'meeting',
                    'priority' => 'high',
                    'status' => 'pending',
                    'assigned_to' => $users->random()?->id,
                    'created_by' => $users->random()?->id,
                    'due_date' => now()->addDays(rand(3, 14))
                ]);
            }
            
            if (rand(1, 4) === 1) {
                Action::create([
                    'actionable_type' => Opportunity::class,
                    'actionable_id' => $opportunity->id,
                    'title' => 'Submit cost proposal',
                    'description' => 'Prepare and submit detailed cost breakdown and pricing proposal',
                    'type' => 'task',
                    'priority' => 'medium',
                    'status' => 'pending',
                    'assigned_to' => $users->random()?->id,
                    'created_by' => $users->random()?->id,
                    'due_date' => now()->addDays(rand(7, 21))
                ]);
            }
        }
        
        $this->command->info('Opportunity seeding completed successfully!');
        $this->command->info('Created:');
        $this->command->info('- ' . Opportunity::count() . ' opportunities');
        $this->command->info('- ' . Action::count() . ' actions');
        $this->command->info('- Opportunity-subcontractor relationships established');
    }
}