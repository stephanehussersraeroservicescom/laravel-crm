<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subcontractor;
use App\Models\Contact;
use Illuminate\Support\Str;

class SubcontractorSeeder extends Seeder
{
    public function run(): void
    {
        // Create 100 subcontractors, some with a parent
        for ($i = 1; $i <= 100; $i++) {
            $parentId = ($i > 10 && rand(0, 1)) ? rand(1, 10) : null; // some nested
            $sub = Subcontractor::create([
                'name' => 'Subcontractor ' . $i,
                'parent_id' => $parentId,
            ]);

            // Add 1–3 contacts per subcontractor
            for ($j = 1; $j <= rand(1, 3); $j++) {
                Contact::create([
                    'subcontractor_id' => $sub->id,
                    'name' => 'Contact ' . Str::random(5),
                    'email' => 'contact' . rand(1000,9999) . '@example.com',
                    'role' => ['Sales', 'Manager', 'QA'][array_rand(['Sales', 'Manager', 'QA'])],
                    'phone' => '+33 1 45 ' . rand(100000, 999999),
                ]);
            }
        }
    }
}
