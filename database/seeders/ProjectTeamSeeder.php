<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Subcontractor;
use App\Models\ProjectSubcontractorTeam;

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

        // Create teams for each project
        foreach ($projects as $index => $project) {
            // Main design team
            $team1 = ProjectSubcontractorTeam::create([
                'project_id' => $project->id,
                'main_subcontractor_id' => $subcontractors[$index % $subcontractors->count()]->id,
                'role' => 'Design',
                'notes' => 'Lead design contractor for ' . $project->name
            ]);

            // Add supporting subcontractors
            $supporters = $subcontractors->skip(($index + 1) % $subcontractors->count())->take(2);
            $team1->supportingSubcontractors()->attach($supporters->pluck('id'));

            // Manufacturing team
            if ($subcontractors->count() > 3) {
                $team2 = ProjectSubcontractorTeam::create([
                    'project_id' => $project->id,
                    'main_subcontractor_id' => $subcontractors[($index + 2) % $subcontractors->count()]->id,
                    'role' => 'Manufacturing',
                    'notes' => 'Manufacturing lead for ' . $project->name
                ]);

                // Add one supporter for manufacturing
                $supporter = $subcontractors[($index + 3) % $subcontractors->count()];
                $team2->supportingSubcontractors()->attach($supporter->id);
            }

            // Commercial team for first two projects
            if ($index < 2 && $subcontractors->count() > 4) {
                ProjectSubcontractorTeam::create([
                    'project_id' => $project->id,
                    'main_subcontractor_id' => $subcontractors[($index + 4) % $subcontractors->count()]->id,
                    'role' => 'Commercial',
                    'notes' => 'Commercial lead for ' . $project->name
                ]);
            }
        }
    }
}
