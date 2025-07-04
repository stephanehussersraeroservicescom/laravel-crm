<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProgramStatus;

class ProgramStatusSeeder extends Seeder
{
    public function run()
    {
        $statuses = [
            'Market Rumor',
            'Announced',
            'Contacted',
            'Initial Discussion',
            'RFQ Issued',
            'Quoting',
            'Negotiation',
            'Preferred Vendor',
            'Purchase Order Issued',
            'In Delivery',
            'Project Running',
            'Completed',
            'Lost',
        ];

        foreach ($statuses as $order => $name) {
            ProgramStatus::updateOrCreate(
                ['name' => $name],
                ['order' => $order]
            );
        }
    }
}
