<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productClasses = [
            // Ultraleather Products
            ['root_code' => 'ULFR', 'product_name' => 'Ultraleather', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 96.25],
            ['root_code' => 'ULBOLFR', 'product_name' => 'Bolero', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 104.75],
            ['root_code' => 'ULDIHPFR', 'product_name' => 'Brisa Distressed', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 104.75],
            ['root_code' => 'ULBRHPFR', 'product_name' => 'Brisa Original', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 104.75],
            ['root_code' => 'ULCSFR', 'product_name' => 'Coast', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 110.75],
            ['root_code' => 'ULCOFR', 'product_name' => 'Contour', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 104.75],
            ['root_code' => 'ULDWFR', 'product_name' => 'Dwell', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 104.75],
            ['root_code' => 'ULFSFR', 'product_name' => 'Fusion', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 104.75],
            ['root_code' => 'ULFSSTFR', 'product_name' => 'Fusion Stretch', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 116.00],
            ['root_code' => 'ULLNFR', 'product_name' => 'Linen', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 104.75],
            ['root_code' => 'ULMVFR', 'product_name' => 'Matte Vincenza', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 104.75],
            ['root_code' => 'ULOSFR', 'product_name' => 'Ostrich', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 104.75],
            ['root_code' => 'ULPRLFR', 'product_name' => 'Pearlized', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 104.75],
            ['root_code' => 'ULPROFR', 'product_name' => 'Pro', 'moq' => 5, 'has_ink_resist' => true, 'is_bio' => false, 'standard_price' => 104.75],
            ['root_code' => 'ULPROMFR', 'product_name' => 'Promessa', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 104.75],
            ['root_code' => '492', 'product_name' => 'Promessa AV', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 94.75],
            ['root_code' => 'ULPUFR', 'product_name' => 'Pumice', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 110.75],
            ['root_code' => 'ULRAFR', 'product_name' => 'Raffia', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 104.75],
            ['root_code' => 'ULRPROFR', 'product_name' => 'Reef Pro', 'moq' => 5, 'has_ink_resist' => true, 'is_bio' => false, 'standard_price' => 110.75],
            ['root_code' => 'ULTOFR', 'product_name' => 'Tottori', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 104.75],
            ['root_code' => 'ULUTFR', 'product_name' => 'Ultratech', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 104.75],
            ['root_code' => 'ULVNFR', 'product_name' => 'Vienna', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 104.75],
            ['root_code' => 'ULVBIOFR', 'product_name' => 'Volar Bio', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => true, 'standard_price' => 168.25],
            ['root_code' => 'ULWIFR', 'product_name' => 'Wired', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 104.75],
            
            // Woven Products
            ['root_code' => 'GPFR', 'product_name' => 'Grospoint', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 96.00],
            ['root_code' => 'GVFR', 'product_name' => 'Geneve', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 102.25],
            ['root_code' => 'BRLFR', 'product_name' => 'Brussels', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 102.25],
            ['root_code' => 'WOVVYFR', 'product_name' => 'Valley', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 107.00],
            
            // Ultrasuede Products
            ['root_code' => 'USFRC', 'product_name' => 'Ultrasuede', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 99.00],
            ['root_code' => 'USGXFRC', 'product_name' => 'US Embossed Collection: Galaxy', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 123.75],
            ['root_code' => 'USBKFRC', 'product_name' => 'US Embossed Collection: Bark', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 123.75],
            ['root_code' => 'USLUFRC', 'product_name' => 'US Embossed Collection: Lunar', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 123.75],
            ['root_code' => 'USMLFRC', 'product_name' => 'US Melange', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 157.25],
            ['root_code' => 'USTWFRC', 'product_name' => 'US Twill', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 157.25],
            
            // Custom Ultrasuede Products
            ['root_code' => 'USEMFRC', 'product_name' => 'US Embossed', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 10000.00],
            ['root_code' => 'USDPFRC', 'product_name' => 'US Dye Print', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 10000.00],
            ['root_code' => 'USRPFRC', 'product_name' => 'US Resin Print', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 10000.00],
            ['root_code' => 'USPSFRC', 'product_name' => 'US Pinsonic', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 10000.00],
            ['root_code' => 'USLEFRC', 'product_name' => 'US Laser Etched', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 10000.00],
            ['root_code' => 'USDSFRC', 'product_name' => 'US Dye Sub Print', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 10000.00],
            
            // TapiSuede Products
            ['root_code' => 'TSFRC', 'product_name' => 'TapiSuede', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 99.00],
            ['root_code' => 'TSFLFRC', 'product_name' => 'TapiSuede Flannels', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 107.50],
            ['root_code' => 'TSSTFRC', 'product_name' => 'TapiSuede 2-Way Stretch', 'moq' => 5, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 96.25],
            
            // 9-Series Products (with variations)
            ['root_code' => 'ULFRB9', 'product_name' => '9-Series', 'moq' => 500, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 164.95, 'roll_length' => '30 LY'],
            ['root_code' => 'ULFRB9', 'product_name' => '9-Series with Ink Resist', 'moq' => 500, 'has_ink_resist' => true, 'is_bio' => false, 'standard_price' => 175.95, 'roll_length' => '30 LY'],
            ['root_code' => 'ULFRB9', 'product_name' => '9-Series Bio', 'moq' => 500, 'has_ink_resist' => false, 'is_bio' => true, 'standard_price' => 207.00, 'roll_length' => '30 LY'],
            ['root_code' => 'ULFRB9', 'product_name' => '9-Series Bio with Ink Resist', 'moq' => 500, 'has_ink_resist' => true, 'is_bio' => true, 'standard_price' => 207.00, 'roll_length' => '30 LY'],
            
            // BHC Products
            ['root_code' => 'BHC', 'product_name' => 'BHC', 'moq' => 66, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 153.95, 'roll_length' => '30 LY'],
            ['root_code' => 'BHC-SS', 'product_name' => 'BHC-SS', 'moq' => 99, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 160.50, 'roll_length' => '30 LY'],
            ['root_code' => 'BHC-SSFL', 'product_name' => 'BHC-SSFL', 'moq' => 99, 'has_ink_resist' => false, 'is_bio' => false, 'standard_price' => 160.50, 'roll_length' => '30 LY'],
        ];
        
        // Common description for all products
        $standardDescription = "Passes: 12 & 60 Second Vertical Flammability: FAR25.853, Appendix F, Part I (ii) and (i) Roll Width: 54\"/137cm Average Roll Length: 33 LY";
        $heatReleaseDescription = "Passes: Heat Release and Smoke Density: FAR25.853, Appendix F, Part IV and Part V, as well as 12 and 60 Second Vertical Flammability: FAR25.853, Appendix F, Part I (ii) and (i). Roll Width: 54\" Average Roll Length: 30 LY";
        
        foreach ($productClasses as $product) {
            // Determine description based on product
            $description = $standardDescription;
            if (in_array($product['root_code'], ['ULFRB9', 'BHC', 'BHC-SS', 'BHC-SSFL'])) {
                $description = $heatReleaseDescription;
            }
            
            // Extract roll_length if specified, otherwise use default
            $rollLength = $product['roll_length'] ?? '33 LY';
            unset($product['roll_length']);
            
            DB::table('product_classes')->insert([
                'root_code' => $product['root_code'],
                'product_name' => $product['product_name'],
                'description' => $description,
                'moq' => $product['moq'],
                'has_ink_resist' => $product['has_ink_resist'],
                'is_bio' => $product['is_bio'],
                'standard_price' => $product['standard_price'],
                'roll_width' => '54"',
                'roll_length' => $rollLength,
                'lead_time_weeks' => 12,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}