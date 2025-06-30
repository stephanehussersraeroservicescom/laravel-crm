<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Airline;
use App\Models\Project;
use App\Models\Subcontractor;
use App\Models\ProjectSubcontractorTeam;

class ProjectSubcontractorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample airlines
        $airFrance = Airline::firstOrCreate([
            'name' => 'Air France'
        ], [
            'region' => 'Europe',
            'account_executive' => 'Jean Dupont'
        ]);

        $lufthansa = Airline::firstOrCreate([
            'name' => 'Lufthansa'
        ], [
            'region' => 'Europe',
            'account_executive' => 'Hans Mueller'
        ]);

        // Create sample projects
        $project1 = Project::firstOrCreate([
            'name' => 'Air France A320 Vertical Surface'
        ], [
            'airline_id' => $airFrance->id,
            'number_of_aircraft' => 50,
            'comment' => 'Vertical surface refurbishment project'
        ]);

        $project2 = Project::firstOrCreate([
            'name' => 'Lufthansa A330 Interior'
        ], [
            'airline_id' => $lufthansa->id,
            'number_of_aircraft' => 25,
            'comment' => 'Complete interior overhaul'
        ]);

        // Create sample subcontractors
        $sub11 = Subcontractor::firstOrCreate([
            'name' => 'Subcontractor 11'
        ], [
            'comment' => 'Primary contractor for vertical surfaces'
        ]);

        $sub14 = Subcontractor::firstOrCreate([
            'name' => 'Subcontractor 14'
        ], [
            'comment' => 'Supporting contractor for materials'
        ]);

        $sub31 = Subcontractor::firstOrCreate([
            'name' => 'Subcontractor 31'
        ], [
            'comment' => 'Installation specialist'
        ]);

        $sub22 = Subcontractor::firstOrCreate([
            'name' => 'Subcontractor 22'
        ], [
            'comment' => 'Interior design specialist'
        ]);

        // Create project teams with multiple supporting subcontractors
        $team1 = ProjectSubcontractorTeam::firstOrCreate([
            'project_id' => $project1->id,
            'main_subcontractor_id' => $sub11->id,
            'role' => 'Manufacturing',
        ], [
            'notes' => 'Sub 11 leads manufacturing with multiple supporting contractors for Air France vertical surface'
        ]);
        // Add multiple supporters to team 1
        $team1->supportingSubcontractors()->syncWithoutDetaching([$sub14->id, $sub31->id]);

        $team2 = ProjectSubcontractorTeam::firstOrCreate([
            'project_id' => $project2->id,
            'main_subcontractor_id' => $sub22->id,
            'role' => 'Design',
        ], [
            'notes' => 'Sub 22 handles design with installation and material support for Lufthansa interior'
        ]);
        // Add supporters to team 2
        $team2->supportingSubcontractors()->syncWithoutDetaching([$sub31->id, $sub14->id]);

        $team3 = ProjectSubcontractorTeam::firstOrCreate([
            'project_id' => $project1->id,
            'main_subcontractor_id' => $sub31->id,
            'role' => 'Certification',
        ], [
            'notes' => 'Sub 31 leads certification process with documentation support'
        ]);
        // Add supporter to team 3
        $team3->supportingSubcontractors()->syncWithoutDetaching([$sub14->id]);
    }
}
