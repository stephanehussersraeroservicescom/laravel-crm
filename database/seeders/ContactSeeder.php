<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\Subcontractor;
use App\Enums\ContactRole;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only seed if contacts table is empty
        if (Contact::count() > 0) {
            $this->command->info('Contacts already exist, skipping ContactSeeder');
            return;
        }

        $subcontractors = Subcontractor::all();
        
        if ($subcontractors->isEmpty()) {
            $this->command->warn('No subcontractors found. Please run SubcontractorSeeder first.');
            return;
        }

        $contactData = [
            // Zodiac Aerospace contacts
            [
                'name' => 'Jean-Pierre Martin',
                'email' => 'jp.martin@zodiac-aerospace.com',
                'role' => ContactRole::PROGRAM_MANAGEMENT->value,
                'phone' => '+33 1 55 61 63 00',
                'comment' => 'Primary contact for commercial negotiations'
            ],
            [
                'name' => 'Sophie Dubois',
                'email' => 'sophie.dubois@zodiac-aerospace.com',
                'role' => ContactRole::PROGRAM_MANAGEMENT->value,
                'phone' => '+33 1 55 61 63 15',
                'comment' => 'Technical project coordination'
            ],
            
            // Safran Cabin contacts
            [
                'name' => 'Michael Anderson',
                'email' => 'michael.anderson@safran-cabin.com',
                'role' => ContactRole::PROGRAM_MANAGEMENT->value,
                'phone' => '+1 425 717 0800',
                'comment' => 'US market specialist'
            ],
            [
                'name' => 'Elena Rodriguez',
                'email' => 'elena.rodriguez@safran-cabin.com',
                'role' => ContactRole::ENGINEERING->value,
                'phone' => '+1 425 717 0825',
                'comment' => 'Cabin systems integration expert'
            ],
            
            // Collins Aerospace contacts
            [
                'name' => 'David Chen',
                'email' => 'david.chen@collins.com',
                'role' => ContactRole::PROGRAM_MANAGEMENT->value,
                'phone' => '+1 319 295 5000',
                'comment' => 'Key account management for premium airlines'
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@collins.com',
                'role' => ContactRole::DESIGN->value,
                'phone' => '+1 319 295 5050',
                'comment' => 'Interior design and certification specialist'
            ],
            
            // Jamco Corporation contacts
            [
                'name' => 'Takeshi Yamamoto',
                'email' => 'takeshi.yamamoto@jamco.co.jp',
                'role' => ContactRole::PROGRAM_MANAGEMENT->value,
                'phone' => '+81 42 593 7100',
                'comment' => 'Asian market development'
            ],
            [
                'name' => 'Lisa Thompson',
                'email' => 'lisa.thompson@jamco.co.jp',
                'role' => ContactRole::PROGRAM_MANAGEMENT->value,
                'phone' => '+81 42 593 7120',
                'comment' => 'Program delivery and quality assurance'
            ],
            
            // Diehl Aviation contacts
            [
                'name' => 'Klaus Weber',
                'email' => 'klaus.weber@diehl.com',
                'role' => ContactRole::PROGRAM_MANAGEMENT->value,
                'phone' => '+49 911 618 2500',
                'comment' => 'Cabin systems and lighting solutions'
            ],
            [
                'name' => 'Anna Mueller',
                'email' => 'anna.mueller@diehl.com',
                'role' => ContactRole::ENGINEERING->value,
                'phone' => '+49 911 618 2515',
                'comment' => 'Technical support and maintenance'
            ],
        ];

        $contactIndex = 0;
        foreach ($subcontractors as $index => $subcontractor) {
            // Create 2 contacts per subcontractor (if we have enough contact data)
            for ($i = 0; $i < 2 && $contactIndex < count($contactData); $i++) {
                $contact = $contactData[$contactIndex];
                Contact::create([
                    'subcontractor_id' => $subcontractor->id,
                    'name' => $contact['name'],
                    'email' => $contact['email'],
                    'role' => $contact['role'],
                    'phone' => $contact['phone'],
                    'comment' => $contact['comment'],
                ]);
                $contactIndex++;
            }

            // If we run out of predefined contacts, create generic ones
            if ($contactIndex >= count($contactData)) {
                $contactNumber = $i + 1;
                Contact::create([
                    'subcontractor_id' => $subcontractor->id,
                    'name' => "Contact {$contactNumber}",
                    'email' => "contact{$contactNumber}@" . strtolower(str_replace(' ', '', $subcontractor->name)) . ".com",
                    'role' => $contactNumber == 1 ? ContactRole::PROGRAM_MANAGEMENT->value : ContactRole::ENGINEERING->value,
                    'phone' => '+1 555 ' . str_pad($subcontractor->id * 100 + $contactNumber, 4, '0', STR_PAD_LEFT),
                    'comment' => "Generic contact for {$subcontractor->name}",
                ]);
            }
        }

        $this->command->info('Contact seeding completed. Created ' . Contact::count() . ' contacts.');
    }
}
