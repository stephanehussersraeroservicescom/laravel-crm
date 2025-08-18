<?php

namespace Database\Seeders;

use App\Models\Quote;
use App\Models\QuoteLine;
use App\Models\Customer;
use App\Models\Airline;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $customers = Customer::all();
        $airlines = Airline::all();
        $productClasses = DB::table('product_classes')->get();

        if ($users->isEmpty() || $productClasses->isEmpty()) {
            $this->command->warn('Missing required data. Please ensure users and product classes are seeded first.');
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

        // Color codes for different product families
        $ultraleatherColors = [
            '3901' => 'Navy Blue',
            '3902' => 'Dark Gray',
            '3903' => 'Light Gray',
            '3904' => 'Charcoal',
            '3905' => 'Black',
            '3910' => 'Beige',
            '3915' => 'Cream',
            '3920' => 'Brown',
            '3925' => 'Burgundy',
            '3930' => 'Forest Green'
        ];
        
        $ultrasuedeColors = [
            '0001' => 'White',
            '0002' => 'Off White',
            '0003' => 'Pearl',
            '0010' => 'Light Gray',
            '0020' => 'Medium Gray',
            '0030' => 'Charcoal',
            '0040' => 'Black',
            '0050' => 'Navy',
            '0060' => 'Royal Blue',
            '0070' => 'Burgundy'
        ];

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
                'lead_time_weeks' => rand(12, 16) . ' weeks',
                'comments' => rand(0, 1) ? 'Special handling required for this order.' : '',
                'is_subcontractor' => $customer->is_subcontractor,
                'status' => collect(['draft', 'sent', 'accepted'])->random(),
            ]);

            // Add 2-5 quote lines per quote
            $lineCount = rand(2, 5);
            for ($j = 0; $j < $lineCount; $j++) {
                $productClass = $productClasses->random();
                
                // Determine colors based on product family
                $colors = $ultraleatherColors;
                if (str_starts_with($productClass->root_code, 'US')) {
                    $colors = $ultrasuedeColors;
                }
                
                $colorCode = array_rand($colors);
                $colorName = $colors[$colorCode];
                
                // Create part number
                $partNumber = $productClass->root_code . '-' . $colorCode;
                
                // Calculate pricing
                $basePrice = $productClass->standard_price;
                $quantity = rand(10, 500);
                
                // Apply quantity discount for large orders
                $finalPrice = $basePrice;
                if ($quantity > 100) {
                    $finalPrice = $basePrice * 0.95; // 5% discount
                }
                if ($quantity > 300) {
                    $finalPrice = $basePrice * 0.90; // 10% discount
                }
                
                // Convert to cents for storage
                $finalPriceInCents = (int)($finalPrice * 100);

                QuoteLine::create([
                    'quote_id' => $quote->id,
                    'product_class_id' => $productClass->id,
                    'part_number' => $partNumber,
                    'root_code' => $productClass->root_code,
                    'series_code' => null,
                    'color_code' => $colorCode,
                    'treatment_suffix' => null,
                    'is_exotic' => $productClass->is_bio || $productClass->has_ink_resist,
                    'base_part_number' => $partNumber,
                    'description' => $productClass->product_name . ' - ' . $colorName,
                    'quantity' => $quantity,
                    'unit' => 'LY',
                    'standard_price' => (int)($basePrice * 100),
                    'final_price' => $finalPriceInCents,
                    'pricing_source' => $quantity > 100 ? 'contract' : 'standard',
                    'moq' => $productClass->moq,
                    'lead_time' => $productClass->lead_time_weeks . ' weeks',
                    'notes' => $quantity > 100 ? 'Volume discount applied' : '',
                    'sort_order' => $j,
                ]);
            }
        }

        $this->command->info('Created 15 quotes with quote lines successfully.');
    }
}