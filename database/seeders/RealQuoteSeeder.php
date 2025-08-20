<?php

namespace Database\Seeders;

use App\Models\Quote;
use App\Models\QuoteLine;
use App\Models\Customer;
use App\Models\Airline;
use App\Models\Subcontractor;
use App\Models\ExternalCustomer;
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
        $airlines = Airline::all();
        $subcontractors = Subcontractor::all();
        $externalCustomers = ExternalCustomer::all();
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
        
        // Create 15 sample quotes with mixed customer types
        for ($i = 1; $i <= 15; $i++) {
            $assignedUser = $users->random();
            
            // Randomly select customer type (40% airline, 35% subcontractor, 25% external)
            $customerType = rand(1, 100);
            
            if ($customerType <= 40 && $airlines->isNotEmpty()) {
                // Airline customer
                $customer = $airlines->random();
                $customerType = 'App\\Models\\Airline';
                $customerName = $customer->name;
                $paymentTerms = 'Net 30';
            } elseif ($customerType <= 75 && $subcontractors->isNotEmpty()) {
                // Subcontractor customer
                $customer = $subcontractors->random();
                $customerType = 'App\\Models\\Subcontractor';
                $customerName = $customer->name;
                $paymentTerms = 'Net 30';
            } else {
                // External customer
                $customer = $externalCustomers->isNotEmpty() ? $externalCustomers->random() : null;
                if (!$customer) {
                    // Create a quick external customer if none exist
                    $customer = ExternalCustomer::create([
                        'name' => 'One-off Customer ' . $i,
                        'contact_name' => 'Contact Person',
                        'payment_terms' => 'Pro Forma',
                    ]);
                }
                $customerType = 'App\\Models\\ExternalCustomer';
                $customerName = $customer->name;
                $paymentTerms = $customer->payment_terms;
            }
            
            $quote = Quote::create([
                'user_id' => $assignedUser->id,
                'customer_type' => $customerType,
                'customer_id' => $customer->id,
                'customer_name' => $customerName,
                'quote_number' => 'Q-2025-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'salesperson_code' => $assignedUser->salesperson_code,
                'date_entry' => Carbon::now()->subDays(rand(1, 30)),
                'date_valid' => Carbon::now()->addDays(rand(30, 90)),
                'shipping_terms' => 'Ex Works Dallas Texas',
                'payment_terms' => $paymentTerms,
                'lead_time_weeks' => rand(6, 16) . '-' . rand(8, 18) . ' weeks',
                'introduction_text' => 'Thank you for your interest in our premium aircraft interior materials.',
                'terms_text' => 'Standard terms and conditions apply. All materials meet FAR 25.853 requirements.',
                'footer_text' => 'We appreciate your business and look forward to working with you.',
                'comments' => 'Please contact us for any custom requirements.',
                'status' => ['draft', 'sent', 'accepted'][rand(0, 2)],
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
        // Create sample external customers if none exist
        if (ExternalCustomer::count() == 0) {
            $externalCustomers = [
                ['name' => 'Premier Aircraft Interiors', 'contact_name' => 'John Smith', 
                 'email' => 'john@premierair.com', 'phone' => '+1-555-0101', 'payment_terms' => 'Net 30'],
                ['name' => 'Luxury Cabin Systems', 'contact_name' => 'Mike Wilson', 
                 'email' => 'mike@luxurycabin.com', 'phone' => '+1-555-0103', 'payment_terms' => 'Pro Forma'],
                ['name' => 'Executive Jets International', 'contact_name' => 'David Brown', 
                 'email' => 'david@execjets.com', 'phone' => '+1-555-0105', 'payment_terms' => 'Pro Forma'],
            ];
            
            foreach ($externalCustomers as $customer) {
                ExternalCustomer::create($customer);
            }
        }
        
        // Create sample subcontractors if none exist
        if (Subcontractor::count() == 0) {
            $subcontractors = [
                ['name' => 'Global Aviation Solutions', 'comment' => 'Large international supplier'],
                ['name' => 'Advanced Interior Materials', 'comment' => 'Specialized materials provider'],
                ['name' => 'Precision Aircraft Parts', 'comment' => 'High-precision components'],
                ['name' => 'Quality Cabin Systems', 'comment' => 'Interior system integrator'],
            ];
            
            foreach ($subcontractors as $subcontractor) {
                Subcontractor::create($subcontractor);
            }
        }
        
        // Create sample airlines if none exist
        if (Airline::count() == 0) {
            $airlines = [
                ['name' => 'American Airlines', 'region' => 'North America'],
                ['name' => 'Delta Air Lines', 'region' => 'North America'],
                ['name' => 'United Airlines', 'region' => 'North America'],
                ['name' => 'Southwest Airlines', 'region' => 'North America'],
                ['name' => 'Emirates', 'region' => 'Middle East'],
                ['name' => 'Lufthansa', 'region' => 'Europe'],
                ['name' => 'Air France', 'region' => 'Europe'],
                ['name' => 'British Airways', 'region' => 'Europe'],
            ];
            
            foreach ($airlines as $airline) {
                Airline::create($airline);
            }
        }
        
        // Create test users if none exist
        if (User::count() == 0) {
            $users = [
                ['name' => 'Stephane', 'email' => 'stephane@example.com', 'password' => bcrypt('password'), 'salesperson_code' => 'SFH'],
                ['name' => 'Dominic', 'email' => 'dominic@example.com', 'password' => bcrypt('password'), 'salesperson_code' => 'DD'],
                ['name' => 'Jason', 'email' => 'jason@example.com', 'password' => bcrypt('password'), 'salesperson_code' => 'JE'],
            ];
            
            foreach ($users as $user) {
                User::create($user);
            }
        }
    }
}