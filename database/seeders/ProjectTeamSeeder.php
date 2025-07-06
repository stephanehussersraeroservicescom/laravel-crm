<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Subcontractor;
use App\Models\ProjectSubcontractorTeam;
use App\Models\Opportunity;

class ProjectTeamSeeder extends Seeder
{
    public function run(): void
    {
        // Only seed if no teams exist
        if (ProjectSubcontractorTeam::count() > 0) {
            return;
        }

        $projects = Project::all();
        $subcontractors = Subcontractor::all();

        if ($projects->count() == 0 || $subcontractors->count() == 0) {
            return;
        }

        // First, create opportunities for all projects (since they were created without opportunities)
        $this->createOpportunitiesForProjects($projects);

        // Refresh projects to load the new opportunities
        $projects = Project::with(['opportunities'])->get();

        // Then create teams for those opportunities
        $this->createTeamsForOpportunities($projects, $subcontractors);
    }

    private function createOpportunitiesForProjects($projects)
    {
        $cabinClasses = ['first_class', 'business_class', 'premium_economy', 'economy'];
        $opportunityTypes = ['vertical', 'panels', 'covers', 'others'];

        foreach ($projects as $index => $project) {
            // Create 2-4 random opportunities per project
            $opportunityCount = rand(2, 4);
            
            for ($i = 0; $i < $opportunityCount; $i++) {
                $type = $opportunityTypes[array_rand($opportunityTypes)];
                $cabinClass = $cabinClasses[array_rand($cabinClasses)];
                
                $opportunityData = [
                    'project_id' => $project->id,
                    'type' => $type,
                    'cabin_class' => $cabinClass,
                    'status' => 'active',
                    'probability' => rand(20, 80), // 20% to 80% as integer
                    'potential_value' => rand(50000, 2000000), // Random value between 50K and 2M
                ];
                
                // Add meaningful names for 'others' type opportunities
                if ($type === 'others') {
                    $customNames = [
                        'Entertainment System',
                        'Lighting Package', 
                        'Storage Solutions',
                        'Power Outlets',
                        'WiFi Installation'
                    ];
                    $opportunityData['name'] = $customNames[array_rand($customNames)];
                    $opportunityData['description'] = $opportunityData['name'] . ' for ' . $cabinClass . ' cabin';
                }
                
                $opportunity = Opportunity::create($opportunityData);
            }
        }
    }

    private function createTeamsForOpportunities($projects, $subcontractors)
    {
        $subIndex = 0;
        $roles = ['Design', 'Manufacturing', 'Commercial', 'Certification'];

        foreach ($projects as $project) {
            // Create teams for all opportunities
            foreach ($project->opportunities as $opportunity) {
                $team = ProjectSubcontractorTeam::create([
                    'opportunity_id' => $opportunity->id, // Direct foreign key relationship
                    'main_subcontractor_id' => $subcontractors[$subIndex % $subcontractors->count()]->id,
                    'role' => $roles[$subIndex % count($roles)],
                    'notes' => $this->getTeamNotes($opportunity, $project)
                ]);

                // Add 1-2 supporting subcontractors
                $supportCount = rand(1, 2);
                if ($subcontractors->count() > 1) {
                    $supporters = collect($subcontractors)
                        ->where('id', '!=', $team->main_subcontractor_id)
                        ->random(min($supportCount, $subcontractors->count() - 1));
                    $team->supportingSubcontractors()->attach($supporters->pluck('id'));
                }
                
                $subIndex++;
            }
        }
    }

    private function getTeamNotes($opportunity, $project)
    {
        $typeValue = $opportunity->type->value ?? $opportunity->type;
        $typeLabel = ucwords(str_replace('_', ' ', $typeValue));
        
        $cabinClassValue = $opportunity->cabin_class->value ?? $opportunity->cabin_class;
        
        if ($typeValue === 'others') {
            return "{$opportunity->name} team for {$cabinClassValue} cabin in {$project->name}";
        }
        
        return "{$typeLabel} team for {$cabinClassValue} cabin in {$project->name}";
    }
}
