<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data (handle foreign key constraints)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('products')->truncate();
        DB::table('product_classes')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Product Classes Data
        $productClasses = [
            // Standard FR Products
            ['ULFR', 'Ultraleather', 'ULFR', 5, 'LY', '6-8', false, false, 96.25, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULBOLFR', 'Bolero', 'ULBOLFR', 5, 'LY', '6-8', false, false, 104.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULDIHPFR', 'Brisa Distressed', 'ULDIHPFR', 5, 'LY', '6-8', false, false, 104.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULBRHPFR', 'Brisa Original', 'ULBRHPFR', 5, 'LY', '6-8', false, false, 104.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULCSFR', 'Coast', 'ULCSFR', 5, 'LY', '6-8', false, false, 110.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULCOFR', 'Contour', 'ULCOFR', 5, 'LY', '6-8', false, false, 104.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULDWFR', 'Dwell', 'ULDWFR', 5, 'LY', '6-8', false, false, 104.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULFSFR', 'Fusion', 'ULFSFR', 5, 'LY', '6-8', false, false, 104.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULFSSTFR', 'Fusion Stretch', 'ULFSSTFR', 5, 'LY', '6-8', false, false, 116.00, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULLNFR', 'Linen', 'ULLNFR', 5, 'LY', '6-8', false, false, 104.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULMVFR', 'Matte Vincenza', 'ULMVFR', 5, 'LY', '6-8', false, false, 104.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULOSFR', 'Ostrich', 'ULOSFR', 5, 'LY', '6-8', false, false, 104.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULPRLFR', 'Pearlized', 'ULPRLFR', 5, 'LY', '6-8', false, false, 104.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULPROFR', 'Pro', 'ULPROFR', 5, 'LY', '6-8', true, false, 104.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULPROMFR', 'Promessa', 'ULPROMFR', 5, 'LY', '6-8', false, false, 104.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['492', 'Promessa AV', '492', 5, 'LY', '6-8', false, false, 94.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULPUFR', 'Pumice', 'ULPUFR', 5, 'LY', '6-8', false, false, 110.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULRAFR', 'Raffia', 'ULRAFR', 5, 'LY', '6-8', false, false, 104.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULRPROFR', 'Reef Pro', 'ULRPROFR', 5, 'LY', '6-8', true, false, 110.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULTOFR', 'Tottori', 'ULTOFR', 5, 'LY', '6-8', false, false, 104.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULUTFR', 'Ultratech', 'ULUTFR', 5, 'LY', '6-8', false, false, 104.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULVNFR', 'Vienna', 'ULVNFR', 5, 'LY', '6-8', false, false, 104.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULVBIOFR', 'Volar Bio', 'ULVBIOFR', 5, 'LY', '6-8', false, true, 168.25, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['ULWIFR', 'Wired', 'ULWIFR', 5, 'LY', '6-8', false, false, 104.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            
            // Woven Products
            ['GPFR', 'Grospoint', 'GPFR', 5, 'LY', '6-8', false, false, 96.00, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['GVFR', 'Geneve', 'GVFR', 5, 'LY', '6-8', false, false, 102.25, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['BRLFR', 'Brussels', 'BRLFR', 5, 'LY', '6-8', false, false, 102.25, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['WOVVYFR', 'Valley', 'WOVVYFR', 5, 'LY', '6-8', false, false, 107.00, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            
            // Ultrasuede Products
            ['USFRC', 'Ultrasuede', 'USFRC', 5, 'LY', '6-8', false, false, 99.00, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['USGXFRC', 'US Embossed Collection: Galaxy', 'USGXFRC', 5, 'LY', '6-8', false, false, 123.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['USBKFRC', 'US Embossed Collection: Bark', 'USBKFRC', 5, 'LY', '6-8', false, false, 123.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['USLUFRC', 'US Embossed Collection: Lunar', 'USLUFRC', 5, 'LY', '6-8', false, false, 123.75, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['USMLFRC', 'US Melange', 'USMLFRC', 5, 'LY', '6-8', false, false, 157.25, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['USTWFRC', 'US Twill', 'USTWFRC', 5, 'LY', '6-8', false, false, 157.25, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['USEMFRC', 'US Embossed', 'USEMFRC', 5, 'LY', '6-8', false, false, 10000.00, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['USDPFRC', 'US Dye Print', 'USDPFRC', 5, 'LY', '6-8', false, false, 10000.00, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['USRPFRC', 'US Resin Print', 'USRPFRC', 5, 'LY', '6-8', false, false, 10000.00, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['USPSFRC', 'US Pinsonic', 'USPSFRC', 5, 'LY', '6-8', false, false, 10000.00, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['USLEFRC', 'US Laser Etched', 'USLEFRC', 5, 'LY', '6-8', false, false, 10000.00, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['USDSFRC', 'US Dye Sub Print', 'USDSFRC', 5, 'LY', '6-8', false, false, 10000.00, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            
            // TapiSuede Products
            ['TSFRC', 'TapiSuede', 'TSFRC', 5, 'LY', '6-8', false, false, 99.00, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['TSFLFRC', 'TapiSuede Flannels', 'TSFLFRC', 5, 'LY', '6-8', false, false, 107.50, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            ['TSSTFRC', 'TapiSuede 2-Way Stretch', 'TSSTFRC', 5, 'LY', '6-8', false, false, 96.25, 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853'],
            
            // ULFRB9 - Multiple variants with same root code
            ['ULFRB9', '9-Series', 'ULFRB9', 500, 'LY', '12-16', false, false, 164.95, 'Passes: Heat Release and Smoke Density: FAR25.853'],
            ['ULFRB9', '9-Series with Ink Resist', 'ULFRB9', 500, 'LY', '12-16', true, false, 175.95, 'Passes: Heat Release and Smoke Density: FAR25.853'],
            ['ULFRB9', '9-Series Bio', 'ULFRB9', 500, 'LY', '12-16', false, true, 207.00, 'Passes: Heat Release and Smoke Density: FAR25.853'],
            ['ULFRB9', '9-Series Bio with Ink Resist', 'ULFRB9', 500, 'LY', '12-16', true, true, 207.00, 'Passes: Heat Release and Smoke Density: FAR25.853'],
            
            // BHC Products
            ['BHC', 'BHC', 'BHC', 66, 'LY', '8-12', false, false, 153.95, 'Passes: Heat Release and Smoke Density: FAR25.853'],
            ['BHC-SS', 'BHC-SS', 'BHC-SS', 99, 'LY', '8-12', false, false, 160.50, 'Passes: Heat Release and Smoke Density: FAR25.853'],
            ['BHC-SSFL', 'BHC-SSFL', 'BHC-SSFL', 99, 'LY', '8-12', false, false, 160.50, 'Passes: Heat Release and Smoke Density: FAR25.853'],
        ];
        
        // Insert Product Classes
        foreach ($productClasses as [$root_code, $root_name, $part_number_prefix, $moq_ly, $uom, $lead_time_weeks, $has_ink_resist, $is_bio, $price, $description]) {
            DB::table('product_classes')->insert([
                'root_code' => $root_code,
                'root_name' => $root_name,
                'part_number_prefix' => $part_number_prefix,
                'moq_ly' => $moq_ly,
                'uom' => $uom,
                'lead_time_weeks' => $lead_time_weeks,
                'has_ink_resist' => $has_ink_resist,
                'is_bio' => $is_bio,
                'price' => $price,
                'description' => $description,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        
        // Individual Products Data
        $products = [
            ['ULFRB926-7029', 'ULFRB9', 'light grey', '', '', 130.95, 1, 'LY', '', true],
            ['BHC-SS-3216', 'BHC-SS', 'purple', '', '', 160.50, 99, 'LY', '8-12', true],
            ['BHC-6352', 'BHC', 'Unknown', '', '', 153.95, 500, 'LY', '12-16', true],
        ];
        
        // Insert Products
        foreach ($products as [$part_number, $root_code, $color_name, $color_code, $description, $price, $moq, $uom, $lead_time_weeks, $is_active]) {
            DB::table('products')->insert([
                'part_number' => $part_number,
                'root_code' => $root_code,
                'color_name' => $color_name,
                'color_code' => $color_code,
                'description' => $description,
                'price' => $price,
                'moq' => $moq,
                'uom' => $uom,
                'lead_time_weeks' => $lead_time_weeks,
                'is_active' => $is_active,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        
        $this->command->info('Seeded ' . count($productClasses) . ' product classes and ' . count($products) . ' individual products.');
    }
}