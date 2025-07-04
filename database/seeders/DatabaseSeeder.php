<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\AircraftType;
use App\Models\Airline;
use App\Models\Status;
use App\Models\Material;
use App\Models\Project;
use App\Models\VerticalSurface;
use App\Models\Cover;
use App\Models\Panel;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
        // Aircraft Types
        $aircrafts = ['A350', 'A787', 'B777', 'A330', 'B737'];
        foreach ($aircrafts as $type) {
            AircraftType::create(['name' => $type]);
        }

        // Airlines
        $airlines = [
            ['name' => 'Emirates', 'region' => 'Middle East', 'account_executive' => 'Alice'],
            ['name' => 'Air France', 'region' => 'Europe', 'account_executive' => 'Bob'],
            ['name' => 'Lufthansa', 'region' => 'Europe', 'account_executive' => 'Carol'],
            ['name' => 'Delta', 'region' => 'North America', 'account_executive' => 'Dave'],
            ['name' => 'Singapore Airlines', 'region' => 'Asia', 'account_executive' => 'Eve'],
            ['name' => 'United', 'region' => 'North America', 'account_executive' => 'Frank'],
        ];
        foreach ($airlines as $row) {
            Airline::create($row);
        }

        // Statuses
        $statuses = [
            ['name' => 'Commercial - Not started', 'description' => 'Commercial phase not started'],
            ['name' => 'Commercial - Negotiation', 'description' => 'In commercial negotiation'],
            ['name' => 'Commercial - Agreed', 'description' => 'Commercial terms agreed'],
            ['name' => 'Commercial - Signed', 'description' => 'Commercial contract signed'],
            ['name' => 'Design - Not Started', 'description' => 'Design phase not started'],
            ['name' => 'Design - In Progress', 'description' => 'Design in progress'],
            ['name' => 'Design - Completed', 'description' => 'Design completed'],
            ['name' => 'Certification - Not started', 'description' => 'Certification not started'],
            ['name' => 'Certification - Testing', 'description' => 'In certification testing'],
            ['name' => 'Certification - Certified', 'description' => 'Certification completed'],
        ];
        foreach ($statuses as $row) {
            Status::create($row);
        }

        // Materials
        $carNames = ['Tesla', 'Audi', 'BMW', 'Chevy', 'Dacia', 'Fiat', 'Golf', 'Honda', 'Jaguar', 'Kia', 'Lexus', 'Mazda', 'Nissan', 'Opel', 'Porsche', 'Renault', 'Saab', 'Toyota', 'Volvo', 'Zoe'];
        foreach ($carNames as $name) {
            Material::create([
                'name' => $name . ' Material',
                'description' => 'Material description for ' . $name,
            ]);
        }

        // Projects
        $airlineIds = Airline::pluck('id')->all();
        $aircraftTypeIds = AircraftType::pluck('id')->all();
        $designStatusIds = Status::where('name', 'like', 'Design%')->pluck('id')->all();
        $commercialStatusIds = Status::where('name', 'like', 'Commercial%')->pluck('id')->all();

        for ($i = 1; $i <= 8; $i++) {
            $project = Project::create([
                'name' => 'Project ' . Str::random(4),
                'airline_id' => Arr::random($airlineIds),
                'aircraft_type_id' => Arr::random($aircraftTypeIds),
                'number_of_aircraft' => rand(30, 50),
                'design_status_id' => Arr::random($designStatusIds),
                'commercial_status_id' => Arr::random($commercialStatusIds),
                'comment' => 'High-level comment for project',
            ]);

            $materialIds = Material::pluck('id')->shuffle();

            // Vertical Surfaces, Covers, Panels
            foreach (['vertical_surfaces', 'covers', 'panels'] as $surfaceType) {
                for ($j = 0; $j < rand(1, 2); $j++) {
                    $cabinClass = Arr::random(['first', 'business', 'premium_economy', 'economy']);
                    $probability = Arr::random([0.5, 0.7, 0.8, 0.9, 1]);
                    $potential = Arr::random([1000, 1500, 2000, 2500]);
                    $comment = ucfirst($surfaceType) . ' note ' . Str::random(3);

                    $certStatusIds = Status::where('name', 'like', 'Certification%')->pluck('id')->all();

                    $surfaceData = [
                        'project_id' => $project->id,
                        'cabin_class' => $cabinClass,
                        'probability' => $probability,
                        'opportunity_status' => Arr::random(['open','pending','won','lost']),
                        'certification_status_id' => Arr::random($certStatusIds),
                        'potential' => $potential,
                        'phy_path' => '/fake/path/' . Str::random(6) . '.pdf',
                        'comments' => $comment,
                    ];

                    if ($surfaceType == 'vertical_surfaces') {
                        $surface = VerticalSurface::create($surfaceData);
                        $surface->materials()->attach(Material::inRandomOrder()->take(rand(1, 3))->pluck('id')->all());
                    }
                    if ($surfaceType == 'covers') {
                        $surface = Cover::create($surfaceData);
                        $surface->materials()->attach(Material::inRandomOrder()->take(rand(1, 3))->pluck('id')->all());
                    }
                    if ($surfaceType == 'panels') {
                        $surface = Panel::create($surfaceData);
                        $surface->materials()->attach(Material::inRandomOrder()->take(rand(1, 3))->pluck('id')->all());
                    }
                }
            }
        }

        $this->call([
        StatusSeeder::class,
        SubcontractorSeeder::class,
        UserSeeder::class,
        // Add others here if needed
    ]);
    }
}
