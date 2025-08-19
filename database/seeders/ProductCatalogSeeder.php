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
        DB::table('product_classes')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Your actual product catalog
        $products = [
            // Standard FR Products
            ['ULFR', 'Ultraleather', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 96.25],
            ['ULBOLFR', 'Bolero', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 104.75],
            ['ULDIHPFR', 'Brisa Distressed', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 104.75],
            ['ULBRHPFR', 'Brisa Original', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 104.75],
            ['ULCSFR', 'Coast', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 110.75],
            ['ULCOFR', 'Contour', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 104.75],
            ['ULDWFR', 'Dwell', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 104.75],
            ['ULFSFR', 'Fusion', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 104.75],
            ['ULFSSTFR', 'Fusion Stretch', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 116.00],
            ['ULLNFR', 'Linen', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 104.75],
            ['ULMVFR', 'Matte Vincenza', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 104.75],
            ['ULOSFR', 'Ostrich', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 104.75],
            ['ULPRLFR', 'Pearlized', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 104.75],
            ['ULPROFR', 'Pro', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, true, false, 104.75],
            ['ULPROMFR', 'Promessa', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 104.75],
            ['492', 'Promessa AV', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 94.75],
            ['ULPUFR', 'Pumice', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 110.75],
            ['ULRAFR', 'Raffia', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 104.75],
            ['ULRPROFR', 'Reef Pro', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, true, false, 110.75],
            ['ULTOFR', 'Tottori', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 104.75],
            ['ULUTFR', 'Ultratech', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 104.75],
            ['ULVNFR', 'Vienna', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 104.75],
            ['ULVBIOFR', 'Volar Bio', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, true, 168.25],
            ['ULWIFR', 'Wired', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 104.75],
            
            // Woven Products
            ['GPFR', 'Grospoint', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 96.00],
            ['GVFR', 'Geneve', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 102.25],
            ['BRLFR', 'Brussels', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 102.25],
            ['WOVVYFR', 'Valley', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 107.00],
            
            // Ultrasuede Products
            ['USFRC', 'Ultrasuede', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 99.00],
            ['USGXFRC', 'US Embossed Collection: Galaxy', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 123.75],
            ['USBKFRC', 'US Embossed Collection: Bark', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 123.75],
            ['USLUFRC', 'US Embossed Collection: Lunar', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 123.75],
            ['USMLFRC', 'US Melange', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 157.25],
            ['USTWFRC', 'US Twill', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 157.25],
            ['USEMFRC', 'US Embossed', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 10000.00],
            ['USDPFRC', 'US Dye Print', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 10000.00],
            ['USRPFRC', 'US Resin Print', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 10000.00],
            ['USPSFRC', 'US Pinsonic', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 10000.00],
            ['USLEFRC', 'US Laser Etched', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 10000.00],
            ['USDSFRC', 'US Dye Sub Print', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 10000.00],
            
            // TapiSuede Products
            ['TSFRC', 'TapiSuede', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 99.00],
            ['TSFLFRC', 'TapiSuede Flannels', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 107.50],
            ['TSSTFRC', 'TapiSuede 2-Way Stretch', 'Passes: 12 & 60 Second Vertical Flammability: FAR25.853', 5, false, false, 96.25],
            
            // ULFRB9 - Multiple products with same root code (PRAGMATIC APPROACH)
            ['ULFRB9', '9-Series', 'Passes: Heat Release and Smoke Density: FAR25.853', 500, false, false, 164.95],
            ['ULFRB9', '9-Series with Ink Resist', 'Passes: Heat Release and Smoke Density: FAR25.853', 500, true, false, 175.95],
            ['ULFRB9', '9-Series Bio', 'Passes: Heat Release and Smoke Density: FAR25.853', 500, false, true, 207.00],
            ['ULFRB9', '9-Series Bio with Ink Resist', 'Passes: Heat Release and Smoke Density: FAR25.853', 500, true, true, 207.00],
            
            // BHC Products
            ['BHC', 'BHC', 'Passes: Heat Release and Smoke Density: FAR25.853', 66, false, false, 153.95],
            ['BHC-SS', 'BHC-SS', 'Passes: Heat Release and Smoke Density: FAR25.853', 99, false, false, 160.50],
            ['BHC-SSFL', 'BHC-SSFL', 'Passes: Heat Release and Smoke Density: FAR25.853', 99, false, false, 160.50],
        ];
        
        foreach ($products as [$root_code, $name, $description, $moq, $ink_resist, $bio, $price]) {
            DB::table('product_classes')->insert([
                'root_code' => $root_code,
                'root_name' => $name,
                'part_number_prefix' => $root_code,
                'description' => $description,
                'moq_ly' => $moq,
                'uom' => 'LY', // All products are sold by Linear Yard
                'lead_time_weeks' => $moq >= 500 ? '12-16' : ($moq >= 66 ? '8-12' : '6-8'),
                'has_ink_resist' => $ink_resist,
                'is_bio' => $bio,
                'price' => $price,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        
        $this->command->info('Seeded ' . count($products) . ' products into product_classes table.');
    }
}