<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Airline;
use App\Models\AircraftType;
use App\Models\Status;
use App\Models\Material;
use App\Models\Subcontractor;
use App\Models\Contact;
use App\Models\Project;
use App\Models\Opportunity;

class BasicDataSeeder extends Seeder
{
    public function run(): void
    {
        echo "🌱 Starting basic data seeding...\n";

        // Seed Airlines
        $airlines = [
            ['name' => 'Non-Disclosed Airline', 'region' => 'North America'],
            ['name' => 'Delta Air Lines', 'region' => 'North America', 'account_executive' => 'John Smith'],
            ['name' => 'American Airlines', 'region' => 'North America', 'account_executive' => 'Jane Doe'],
            ['name' => 'United Airlines', 'region' => 'North America', 'account_executive' => 'Mike Johnson'],
            ['name' => 'Lufthansa', 'region' => 'Europe', 'account_executive' => 'Hans Mueller'],
            ['name' => 'British Airways', 'region' => 'Europe', 'account_executive' => 'Sarah Williams'],
            ['name' => 'Emirates', 'region' => 'Middle East', 'account_executive' => 'Ahmed Al-Rashid'],
            ['name' => 'Singapore Airlines', 'region' => 'Asia', 'account_executive' => 'Li Wei'],
        ];

        foreach ($airlines as $airline) {
            Airline::firstOrCreate(['name' => $airline['name']], $airline);
        }

        // Seed Aircraft Types
        $aircraftTypes = [
            ['name' => 'Boeing 737', 'manufacturer' => 'Boeing'],
            ['name' => 'Boeing 777', 'manufacturer' => 'Boeing'],
            ['name' => 'Boeing 787', 'manufacturer' => 'Boeing'],
            ['name' => 'Airbus A320', 'manufacturer' => 'Airbus'],
            ['name' => 'Airbus A330', 'manufacturer' => 'Airbus'],
            ['name' => 'Airbus A350', 'manufacturer' => 'Airbus'],
            ['name' => 'Airbus A380', 'manufacturer' => 'Airbus'],
        ];

        foreach ($aircraftTypes as $type) {
            AircraftType::firstOrCreate(['name' => $type['name']], $type);
        }

        // Seed Statuses
        $statuses = [
            ['status' => 'Planning', 'type' => 'design'],
            ['status' => 'In Design', 'type' => 'design'],
            ['status' => 'Design Review', 'type' => 'design'],
            ['status' => 'Design Approved', 'type' => 'design'],
            ['status' => 'Prototype', 'type' => 'design'],
            ['status' => 'Testing', 'type' => 'design'],
            ['status' => 'Production Ready', 'type' => 'design'],
            ['status' => 'Proposal', 'type' => 'commercial'],
            ['status' => 'Negotiation', 'type' => 'commercial'],
            ['status' => 'Contract Signed', 'type' => 'commercial'],
            ['status' => 'In Production', 'type' => 'commercial'],
            ['status' => 'Delivered', 'type' => 'commercial'],
            ['status' => 'Active', 'type' => 'certification'],
            ['status' => 'Under Review', 'type' => 'certification'],
            ['status' => 'Certified', 'type' => 'certification'],
        ];

        foreach ($statuses as $status) {
            Status::firstOrCreate(['status' => $status['status'], 'type' => $status['type']], $status);
        }

        // Seed Materials
        $materials = [
            ['part_number' => 'LTH-PREM-001', 'color' => 'Black', 'comment' => 'Premium leather for first class'],
            ['part_number' => 'FAB-BUS-001', 'color' => 'Navy Blue', 'comment' => 'Business class fabric'],
            ['part_number' => 'FAB-ECO-001', 'color' => 'Gray', 'comment' => 'Economy class fabric'],
            ['part_number' => 'ALU-FRM-001', 'color' => 'Silver', 'comment' => 'Aluminum frame structure'],
            ['part_number' => 'CFB-001', 'color' => 'Black', 'comment' => 'Carbon fiber composite'],
            ['part_number' => 'FOAM-001', 'color' => 'White', 'comment' => 'Foam cushioning material'],
        ];

        foreach ($materials as $material) {
            Material::firstOrCreate(['part_number' => $material['part_number']], $material);
        }

        // Seed Subcontractors
        $subcontractors = [
            ['name' => 'Premium Interiors Inc', 'comment' => 'Manufacturer specializing in first class seating'],
            ['name' => 'AeroDesign Solutions', 'comment' => 'Design company specializing in interior design'],
            ['name' => 'SkyComfort Systems', 'comment' => 'Manufacturer specializing in business class seating'],
            ['name' => 'Cabin Innovations Ltd', 'comment' => 'Engineering services for aircraft interiors'],
            ['name' => 'FlightCraft Manufacturing', 'comment' => 'Manufacturer specializing in economy seating'],
        ];

        foreach ($subcontractors as $sub) {
            Subcontractor::firstOrCreate(['name' => $sub['name']], $sub);
        }

        // Seed Contacts for each subcontractor
        $subcontractorModels = Subcontractor::all();
        foreach ($subcontractorModels as $subcontractor) {
            Contact::create([
                'subcontractor_id' => $subcontractor->id,
                'name' => 'John Manager',
                'email' => strtolower(str_replace(' ', '.', $subcontractor->name)) . '@example.com',
                'role' => 'Project Manager',
                'phone' => '+1-555-' . rand(1000, 9999),
                'consent_given_at' => now(),
                'marketing_consent' => true,
            ]);

            Contact::create([
                'subcontractor_id' => $subcontractor->id,
                'name' => 'Sarah Engineer',
                'email' => 'sarah.' . strtolower(str_replace(' ', '.', $subcontractor->name)) . '@example.com',
                'role' => 'Lead Engineer',
                'phone' => '+1-555-' . rand(1000, 9999),
                'consent_given_at' => now(),
                'marketing_consent' => false,
            ]);
        }

        // Create sample projects (including confidential ones)
        $confidentialAirline = Airline::where('name', 'Non-Disclosed Airline')->first();
        $deltaAirline = Airline::where('name', 'Delta Air Lines')->first();

        $designStatus = Status::where('type', 'design')->first();
        $commercialStatus = Status::where('type', 'commercial')->first();
        $boeing777 = AircraftType::where('name', 'Boeing 777')->first();
        $airbusA350 = AircraftType::where('name', 'Airbus A350')->first();

        // Regular disclosed project
        $project1 = Project::create([
            'name' => 'Delta Premium Economy Refresh',
            'airline_id' => $deltaAirline->id,
            'aircraft_type_id' => $boeing777->id,
            'number_of_aircraft' => 50,
            'design_status_id' => $designStatus->id,
            'commercial_status_id' => $commercialStatus->id,
            'owner' => 'Project Team Alpha',
            'comment' => 'Premium economy cabin refresh for international routes',
            'airline_disclosed' => true,
        ]);

        // Confidential project
        $project2 = Project::create([
            'name' => 'Major European Carrier Business Class',
            'airline_id' => $confidentialAirline->id,
            'aircraft_type_id' => $airbusA350->id,
            'number_of_aircraft' => 25,
            'design_status_id' => $designStatus->id,
            'commercial_status_id' => $commercialStatus->id,
            'owner' => 'Project Team Beta',
            'comment' => 'Confidential business class upgrade project',
            'airline_disclosed' => false,
            'airline_code_placeholder' => 'MAJOR-EU-001',
            'confidentiality_notes' => 'Large European carrier, NDA expires December 2025',
        ]);

        // Create opportunities for each project
        $this->createOpportunities($project1);
        $this->createOpportunities($project2);

        echo "✅ Basic data seeded successfully!\n";
        echo "📊 Created:\n";
        echo "   - " . Airline::count() . " Airlines (including confidential)\n";
        echo "   - " . AircraftType::count() . " Aircraft Types\n";
        echo "   - " . Status::count() . " Statuses\n";
        echo "   - " . Material::count() . " Materials\n";
        echo "   - " . Subcontractor::count() . " Subcontractors\n";
        echo "   - " . Contact::count() . " Contacts\n";
        echo "   - " . Project::count() . " Projects (1 disclosed, 1 confidential)\n";
        echo "   - " . Opportunity::count() . " Opportunities\n";
    }

    private function createOpportunities($project)
    {
        $certificationStatus = Status::where('type', 'certification')->first();
        
        $opportunityTypes = ['vertical', 'panels', 'covers'];
        $cabinClasses = ['first_class', 'business_class', 'premium_economy', 'economy'];
        
        foreach ($opportunityTypes as $type) {
            foreach ($cabinClasses as $cabinClass) {
                Opportunity::create([
                    'project_id' => $project->id,
                    'type' => $type,
                    'cabin_class' => $cabinClass,
                    'probability' => rand(25, 90),
                    'potential_value' => rand(100000, 1000000),
                    'status' => 'active',
                    'certification_status_id' => $certificationStatus->id,
                    'name' => ucfirst($type) . ' - ' . str_replace('_', ' ', ucwords($cabinClass, '_')),
                    'description' => "Interior work for {$type} surfaces in {$cabinClass} cabin",
                    'created_by' => 1, // Assuming first user
                ]);
            }
        }
    }
}