<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'New',
            'In Progress',
            'Under Review',
            'Approved',
            'On Hold',
            'Cancelled',
            'Completed',
            'Pending',
            'Active',
            'Inactive',
            'Draft',
            'Published',
            'Archived'
        ];

        foreach ($statuses as $status) {
            Status::firstOrCreate(['status' => $status]);
        }
    }
}
