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
        // Step 1: Create 50 subcontractors
        for ($i = 1; $i <= 50; $i++) {
            $sub = Subcontractor::create([
                'name' => 'Subcontractor ' . $i,
            ]);

            // Step 2: Add 1–3 contacts per subcontractor
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

        // Step 3: Many-to-many links
        $subs = Subcontractor::all();
        foreach ($subs as $sub) {
            // Each sub is linked to 1–3 random “main”s (but not itself)
            $parentIds = $subs->where('id', '!=', $sub->id)
                              ->random(rand(1, 3))
                              ->pluck('id')
                              ->toArray();
            $sub->parents()->syncWithoutDetaching($parentIds);
        }
    }
}

