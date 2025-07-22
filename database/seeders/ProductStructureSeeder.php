<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductRoot;
use App\Models\ProductSeriesMapping;
use App\Models\PriceList;
use App\Models\StockedProduct;

class ProductStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create product roots
        $roots = [
            ['ULFR', 'UltraLeather Standard', 'standard'],
            ['ULFRB', 'UltraLeather Commercial', 'commercial'],
            ['USNF', 'UltraSuede Non-Flame', 'standard'],
            ['TSNF', 'TapSuede Non-Flame', 'standard'],
            ['BHCSS', 'Block Hill Commercial', 'commercial'],
            ['BHCSSFL', 'Block Hill Commercial Flame', 'commercial'],
        ];

        foreach ($roots as [$code, $name, $category]) {
            ProductRoot::updateOrCreate(
                ['root_code' => $code],
                [
                    'root_name' => $name,
                    'category' => $category,
                    'moq_ly' => 500, // Default MOQ
                    'lead_time_weeks' => '8-12', // Default lead time
                    'is_active' => true,
                ]
            );
        }

        // Create series mappings
        $seriesMappings = [
            ['ULFR', '914', '9-series standard'],
            ['ULFR', '924', '9-series with ink resist'],
            ['ULFR', '926', '9-series bio treatment'],
            ['ULFR', '971', '70-series premium'],
            ['ULFR', '981', '80-series heavy duty'],
            ['ULFRB', '914', '9-series commercial standard'],
            ['ULFRB', '924', '9-series commercial with ink resist'],
            ['ULFRB', '926', '9-series commercial bio'],
            ['BHCSS', '100', 'Standard commercial series'],
            ['BHCSS', '200', 'Premium commercial series'],
        ];

        foreach ($seriesMappings as [$root, $series, $desc]) {
            ProductSeriesMapping::updateOrCreate(
                ['root_code' => $root, 'series_code' => $series],
                [
                    'series_name' => $desc,
                    'has_ink_resist' => str_contains($desc, 'ink resist'),
                    'is_bio' => str_contains($desc, 'bio'),
                ]
            );
        }

        // Create price lists
        $priceLists = [
            // FR Price Lists
            ['FR', 'ULFR', 45.00, 500, '2025-01-01'],
            ['FR', 'ULFRB', 52.00, 500, '2025-01-01'],
            ['FR', 'USNF', 38.00, 300, '2025-01-01'],
            ['FR', 'TSNF', 35.00, 200, '2025-01-01'],
            
            // Commercial Price Lists
            ['Commercial', 'ULFRB', 48.00, 500, '2025-01-01'],
            ['Commercial', 'BHCSS', 25.00, 99, '2025-01-01'],
            ['Commercial', 'BHCSSFL', 28.00, 99, '2025-01-01'],
            
            // NF Price Lists (for mock-ups)
            ['NF', 'USNF', 32.00, 50, '2025-01-01'],
            ['NF', 'TSNF', 30.00, 50, '2025-01-01'],
        ];

        foreach ($priceLists as [$type, $root, $price, $moq, $date]) {
            PriceList::create([
                'list_type' => $type,
                'root_code' => $root,
                'price_ly' => $price,
                'moq_ly' => $moq,
                'effective_date' => $date,
                'expiry_date' => null,
                'is_active' => true,
            ]);
        }

        // Create some stocked products
        $stockedProducts = [
            ['ULFR924-2558.BC3', 'ULFR', '924', '2558', '.BC3', 150, 50],
            ['ULFR924-2558.17', 'ULFR', '924', '2558', '.17', 75, 50],
            ['ULFRB924-5991.BC3', 'ULFRB', '924', '5991', '.BC3', 200, 100],
            ['BHCSS100-1234', 'BHCSS', '100', '1234', null, 500, 25],
            ['TSNF-3344.16', 'TSNF', null, '3344', '.16', 80, 25],
        ];

        foreach ($stockedProducts as [$partNumber, $root, $series, $color, $treatment, $stock, $customMoq]) {
            StockedProduct::create([
                'full_part_number' => $partNumber,
                'root_code' => $root,
                'series_code' => $series,
                'color_code' => $color,
                'treatment_suffix' => $treatment,
                'stock_quantity' => $stock,
                'custom_moq_ly' => $customMoq,
                'location' => 'Warehouse A',
                'is_exotic' => false,
            ]);
        }

        // Add some exotic products
        StockedProduct::create([
            'full_part_number' => 'SPECIAL-FABRIC-001',
            'root_code' => null,
            'series_code' => null,
            'color_code' => null,
            'treatment_suffix' => null,
            'stock_quantity' => 10,
            'custom_moq_ly' => 1,
            'location' => 'Special Storage',
            'is_exotic' => true,
            'notes' => 'Custom fabric for special project',
        ]);
    }
}