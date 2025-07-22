<?php

namespace Database\Seeders;

use App\Models\Quote;
use App\Models\QuoteLine;
use App\Models\Customer;
use App\Models\Airline;
use App\Models\ProductRoot;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $customers = Customer::all();
        $airlines = Airline::all();
        $productRoots = ProductRoot::all();

        if ($users->isEmpty() || $productRoots->isEmpty()) {
            $this->command->warn('Missing required data. Please ensure users and product roots are seeded first.');
            return;
        }
        
        // Create sample customers if none exist
        if ($customers->isEmpty()) {
            $sampleCustomers = [
                ['company_name' => 'Aerospace Solutions Inc', 'contact_name' => 'John Smith', 'email' => 'john@aerospace.com', 'phone' => '+1-555-0101', 'is_subcontractor' => false],
                ['company_name' => 'Delta Interiors', 'contact_name' => 'Sarah Johnson', 'email' => 'sarah@deltainteriors.com', 'phone' => '+1-555-0102', 'is_subcontractor' => true],
                ['company_name' => 'Premium Cabin Systems', 'contact_name' => 'Mike Wilson', 'email' => 'mike@premiumcabin.com', 'phone' => '+1-555-0103', 'is_subcontractor' => false],
                ['company_name' => 'Advanced Materials Corp', 'contact_name' => 'Lisa Davis', 'email' => 'lisa@advancedmaterials.com', 'phone' => '+1-555-0104', 'is_subcontractor' => true],
                ['company_name' => 'Airline Interiors Ltd', 'contact_name' => 'David Brown', 'email' => 'david@airlineinteriors.com', 'phone' => '+1-555-0105', 'is_subcontractor' => false],
            ];
            
            foreach ($sampleCustomers as $customerData) {
                Customer::create($customerData);
            }
            
            $customers = Customer::all();
        }

        // Create 15 sample quotes
        for ($i = 1; $i <= 15; $i++) {
            $customer = $customers->random();
            $airline = $airlines->random();
            
            $quote = Quote::create([
                'user_id' => $users->random()->id,
                'customer_id' => $customer->id,
                'airline_id' => rand(0, 1) ? $airline->id : null, // 50% chance of having airline
                'date_entry' => now()->subDays(rand(0, 90))->format('Y-m-d'),
                'date_valid' => now()->addDays(rand(30, 60))->format('Y-m-d'),
                'shipping_terms' => collect(['Ex Works Dallas Texas', 'FOB Dallas', 'CIF Destination', 'DDP'])->random(),
                'payment_terms' => collect(['Pro Forma', 'Net 30', 'Net 60', '50% Down, Balance on Delivery'])->random(),
                'lead_time_weeks' => rand(2, 12) . '-' . rand(12, 20) . ' weeks',
                'comments' => rand(0, 1) ? 'Special handling required for this order.' : '',
                'is_subcontractor' => $customer->is_subcontractor,
            ]);

            // Add 2-5 quote lines per quote
            $lineCount = rand(2, 5);
            for ($j = 0; $j < $lineCount; $j++) {
                $root = $productRoots->random();
                $series = collect(['A', 'B', 'C', 'D', 'E'])->random();
                $color = collect(['01', '02', '03', '04', '05', '10', '15', '20'])->random();
                $treatment = collect(['', 'NF', 'FR'])->random();
                
                $partNumber = $root->part_number_prefix . $series . '-' . $color;
                if ($treatment) {
                    $partNumber .= $treatment;
                }

                $standardPrice = rand(5000, 50000); // $50-500 in cents
                $finalPrice = $standardPrice;
                $pricingSource = 'standard';

                // 20% chance of contract pricing
                if (rand(1, 5) == 1) {
                    $finalPrice = $standardPrice * (0.8 + (rand(0, 20) / 100)); // 80-100% of standard
                    $pricingSource = 'contract';
                }

                QuoteLine::create([
                    'quote_id' => $quote->id,
                    'part_number' => $partNumber,
                    'root_code' => $root->root_code,
                    'series_code' => $series,
                    'color_code' => $color,
                    'treatment_suffix' => $treatment,
                    'is_exotic' => rand(0, 1),
                    'base_part_number' => $root->part_number_prefix . $series . '-' . $color,
                    'description' => $root->description . ($treatment ? " ({$treatment} treated)" : ''),
                    'quantity' => rand(1, 10),
                    'unit' => collect(['LY', 'UNIT'])->random(),
                    'standard_price' => $standardPrice,
                    'final_price' => (int)$finalPrice,
                    'pricing_source' => $pricingSource,
                    'moq' => rand(1, 5),
                    'lead_time' => rand(4, 12) . '-' . rand(12, 16) . ' weeks',
                    'notes' => $pricingSource === 'contract' ? 'Contract pricing applied' : '',
                    'sort_order' => $j,
                ]);
            }
        }

        $this->command->info('Created 15 quotes with quote lines successfully.');
    }
}