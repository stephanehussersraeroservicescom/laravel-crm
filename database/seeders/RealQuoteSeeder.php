<?php

namespace Database\Seeders;

use App\Models\Quote;
use App\Models\QuoteLine;
use App\Models\Customer;
use App\Models\Airline;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RealQuoteSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have necessary data
        $this->ensureBasicData();
        
        $users = User::all();
        $customers = Customer::all();
        $airlines = Airline::all();
        $products = DB::table('product_classes')->get();
        
        if ($products->isEmpty()) {
            $this->command->error('No products found. Please run ProductCatalogSeeder first.');
            return;
        }
        
        // Sample color codes for different products
        $standardColors = [
            '2558' => 'Black',
            '3901' => 'Navy Blue', 
            '4405' => 'Dark Gray',
            '5991' => 'Charcoal',
            '6701' => 'Beige',
            '7812' => 'Cream',
            '8823' => 'Light Gray',
            '9934' => 'White',
            '1245' => 'Brown',
            '3467' => 'Tan',
        ];
        
        $treatments = [
            '',      // No treatment
            '.17',   // Standard treatment
            '.BC3',  // Special coating
            '.UV',   // UV protection
            '.FR',   // Fire retardant
        ];
        
        // Create 15 sample quotes
        for ($i = 1; $i <= 15; $i++) {
            $isSubcontractor = rand(0, 1) == 1;
            $customer = $customers->random();
            $airline = $airlines->isNotEmpty() ? $airlines->random() : null;
            
            $quote = Quote::create([
                'user_id' => $users->random()->id,
                'customer_id' => $customer->id,
                'airline_id' => $airline?->id,
                'quote_number' => 'Q-2025-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'date_entry' => Carbon::now()->subDays(rand(1, 30)),
                'date_valid' => Carbon::now()->addDays(rand(30, 90)),
                'shipping_terms' => 'Ex Works Dallas Texas',
                'payment_terms' => $isSubcontractor ? 'Net 30' : 'Pro Forma',
                'lead_time_weeks' => rand(6, 16) . '-' . rand(8, 18) . ' weeks',
                'introduction_text' => 'Thank you for your interest in our premium aircraft interior materials.',
                'terms_text' => 'Standard terms and conditions apply. All materials meet FAR 25.853 requirements.',
                'footer_text' => 'We appreciate your business and look forward to working with you.',
                'comments' => 'Please contact us for any custom requirements.',
                'status' => ['draft', 'sent', 'accepted'][rand(0, 2)],
                'is_subcontractor' => $isSubcontractor,
            ]);
            
            // Add quote lines based on product type
            $this->addQuoteLines($quote, $products, $standardColors, $treatments);
        }
        
        $this->command->info('Created 15 quotes with real product data successfully.');
    }
    
    private function addQuoteLines($quote, $products, $colors, $treatments)
    {
        $lineCount = rand(2, 6);
        
        // Group products by MOQ for realistic selection
        $standardProducts = $products->filter(fn($p) => $p->moq_ly <= 5);
        $commercialProducts = $products->filter(fn($p) => $p->moq_ly >= 66);
        $premiumProducts = $products->filter(fn($p) => $p->price > 150);
        
        for ($j = 0; $j < $lineCount; $j++) {
            // Randomly select product category
            $productType = rand(1, 10);
            
            if ($productType <= 6) {
                // 60% standard products
                $product = $standardProducts->random();
            } elseif ($productType <= 8) {
                // 20% commercial products
                $product = $commercialProducts->isNotEmpty() ? 
                          $commercialProducts->random() : 
                          $standardProducts->random();
            } else {
                // 20% premium products
                $product = $premiumProducts->isNotEmpty() ? 
                          $premiumProducts->random() : 
                          $standardProducts->random();
            }
            
            $color = array_rand($colors);
            $colorName = $colors[$color];
            $treatment = $treatments[array_rand($treatments)];
            
            // Build part number based on product type
            $partNumber = $product->root_code;
            
            // Add series code for certain products
            if (in_array($product->root_code, ['ULFRB9', 'BHC', 'BHC-SS'])) {
                $partNumber .= rand(900, 999);
            } elseif (strpos($product->root_code, 'FR') !== false) {
                // FR products might have series
                if (rand(0, 1)) {
                    $partNumber .= rand(100, 999);
                }
            }
            
            $partNumber .= '-' . $color . $treatment;
            
            // Quantity based on MOQ
            $quantity = max($product->moq_ly, rand($product->moq_ly, $product->moq_ly * 10));
            
            // Apply discount for larger quantities
            $standardPrice = $product->price;
            $discount = 0;
            
            if ($quantity >= 500) {
                $discount = rand(10, 20);
            } elseif ($quantity >= 100) {
                $discount = rand(5, 15);
            } elseif ($quantity >= 50) {
                $discount = rand(0, 10);
            }
            
            $finalPrice = $standardPrice * (1 - $discount / 100);
            
            // Build description
            $description = $product->root_name;
            if ($colorName) {
                $description .= ' - ' . $colorName;
            }
            if ($treatment) {
                $description .= ' with ' . trim($treatment, '.') . ' treatment';
            }
            if ($product->has_ink_resist) {
                $description .= ' (Ink Resist)';
            }
            if ($product->is_bio) {
                $description .= ' (Bio)';
            }
            
            QuoteLine::create([
                'quote_id' => $quote->id,
                'part_number' => $partNumber,
                'root_code' => $product->root_code,
                'series_code' => strpos($partNumber, $product->root_code) !== false ? 
                               substr($partNumber, strlen($product->root_code), 3) : null,
                'color_code' => $color,
                'treatment_suffix' => $treatment,
                'is_exotic' => $product->price > 1000,
                'base_part_number' => $product->root_code,
                'description' => $description,
                'quantity' => $quantity,
                'unit' => 'LY',
                'standard_price' => $standardPrice * 100, // Store in cents
                'final_price' => $finalPrice * 100, // Store in cents
                'pricing_source' => $discount > 0 ? 'contract' : 'standard',
                'moq' => $product->moq_ly,
                'lead_time' => $product->lead_time_weeks . ' weeks',
                'notes' => $product->moq_ly >= 100 ? 
                          'Commercial grade product - minimum order ' . $product->moq_ly . ' LY' : 
                          null,
                'sort_order' => $j,
            ]);
        }
    }
    
    private function ensureBasicData()
    {
        // Create sample customers if none exist
        if (Customer::count() == 0) {
            $customers = [
                ['company_name' => 'Premier Aircraft Interiors', 'contact_name' => 'John Smith', 
                 'email' => 'john@premierair.com', 'phone' => '+1-555-0101', 'is_subcontractor' => false],
                ['company_name' => 'Global Aviation Solutions', 'contact_name' => 'Sarah Johnson', 
                 'email' => 'sarah@globalaviation.com', 'phone' => '+1-555-0102', 'is_subcontractor' => true],
                ['company_name' => 'Luxury Cabin Systems', 'contact_name' => 'Mike Wilson', 
                 'email' => 'mike@luxurycabin.com', 'phone' => '+1-555-0103', 'is_subcontractor' => false],
                ['company_name' => 'Advanced Interior Materials', 'contact_name' => 'Lisa Davis', 
                 'email' => 'lisa@advancedinterior.com', 'phone' => '+1-555-0104', 'is_subcontractor' => true],
                ['company_name' => 'Executive Jets International', 'contact_name' => 'David Brown', 
                 'email' => 'david@execjets.com', 'phone' => '+1-555-0105', 'is_subcontractor' => false],
            ];
            
            foreach ($customers as $customer) {
                Customer::create($customer);
            }
        }
        
        // Create sample airlines if none exist
        if (Airline::count() == 0) {
            $airlines = [
                ['name' => 'American Airlines', 'code' => 'AA'],
                ['name' => 'Delta Air Lines', 'code' => 'DL'],
                ['name' => 'United Airlines', 'code' => 'UA'],
                ['name' => 'Southwest Airlines', 'code' => 'WN'],
                ['name' => 'Emirates', 'code' => 'EK'],
                ['name' => 'Lufthansa', 'code' => 'LH'],
                ['name' => 'Air France', 'code' => 'AF'],
                ['name' => 'British Airways', 'code' => 'BA'],
            ];
            
            foreach ($airlines as $airline) {
                Airline::create($airline);
            }
        }
        
        // Create a test user if none exist
        if (User::count() == 0) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);
        }
    }
}