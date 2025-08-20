<?php

namespace App\Services;

use App\Models\ProductClass;
use App\Models\ProductSeriesMapping;
use App\Models\PriceList;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductImportService
{
    /**
     * Import product roots from CSV
     */
    public function importProductClasss(string $filePath): array
    {
        $results = [
            'success' => 0,
            'errors' => [],
            'skipped' => 0
        ];

        try {
            $file = fopen($filePath, 'r');
            $headers = fgetcsv($file); // Skip header row

            DB::beginTransaction();

            while (($row = fgetcsv($file)) !== false) {
                if (empty($row[0])) continue; // Skip empty rows

                $data = array_combine($headers, $row);
                
                try {
                    ProductClass::updateOrCreate(
                        ['root_code' => $data['root_code']],
                        [
                            'root_name' => $data['root_name'],
                            'category' => $data['category'],
                            'description' => $data['description'],
                            'is_active' => $this->parseBool($data['is_active'] ?? 'TRUE')
                        ]
                    );

                    // Also create/update the default price list entry
                    if (!empty($data['price_ly']) && !empty($data['moq_ly'])) {
                        $listType = $data['category'] === 'commercial' ? 'Commercial' : 'FR';
                        
                        PriceList::updateOrCreate(
                            [
                                'list_type' => $listType,
                                'root_code' => $data['root_code'],
                                'effective_date' => '2025-01-01'
                            ],
                            [
                                'price_ly' => (float)$data['price_ly'],
                                'moq_ly' => (int)$data['moq_ly'],
                                'expiry_date' => null,
                                'is_active' => true,
                                'imported_from' => 'product_roots_import'
                            ]
                        );
                    }

                    $results['success']++;
                } catch (\Exception $e) {
                    $results['errors'][] = "Row {$results['success']}: " . $e->getMessage();
                    Log::error('Product root import error', [
                        'row' => $data,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();
            fclose($file);

        } catch (\Exception $e) {
            DB::rollBack();
            $results['errors'][] = 'File processing error: ' . $e->getMessage();
        }

        return $results;
    }

    /**
     * Import product series mappings from CSV
     */
    public function importProductSeries(string $filePath): array
    {
        $results = [
            'success' => 0,
            'errors' => [],
            'skipped' => 0
        ];

        try {
            $file = fopen($filePath, 'r');
            $headers = fgetcsv($file); // Skip header row

            DB::beginTransaction();

            while (($row = fgetcsv($file)) !== false) {
                if (empty($row[0]) || empty($row[1])) continue; // Skip rows without root_code or series_code

                $data = array_combine($headers, $row);
                
                try {
                    ProductSeriesMapping::updateOrCreate(
                        [
                            'root_code' => $data['root_code'],
                            'series_code' => $data['series_code']
                        ],
                        [
                            'description' => $data['description'] ?? '',
                            'has_ink_resist' => $this->parseBool($data['has_ink_resist'] ?? 'FALSE'),
                            'is_bio' => $this->parseBool($data['is_bio'] ?? 'FALSE'),
                            'base_series' => $data['base_series'] ?? $data['series_code']
                        ]
                    );

                    $results['success']++;
                } catch (\Exception $e) {
                    $results['errors'][] = "Row {$results['success']}: " . $e->getMessage();
                    Log::error('Product series import error', [
                        'row' => $data,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();
            fclose($file);

        } catch (\Exception $e) {
            DB::rollBack();
            $results['errors'][] = 'File processing error: ' . $e->getMessage();
        }

        return $results;
    }

    /**
     * Import price lists from CSV
     */
    public function importPriceLists(string $filePath): array
    {
        $results = [
            'success' => 0,
            'errors' => [],
            'skipped' => 0
        ];

        try {
            $file = fopen($filePath, 'r');
            $headers = fgetcsv($file); // Skip header row

            DB::beginTransaction();

            while (($row = fgetcsv($file)) !== false) {
                if (empty($row[0]) || empty($row[1])) continue; // Skip rows without list_type or root_code

                $data = array_combine($headers, $row);
                
                try {
                    PriceList::create([
                        'list_type' => $data['list_type'],
                        'root_code' => $data['root_code'],
                        'price_ly' => (float)$data['price_ly'],
                        'moq_ly' => (int)$data['moq_ly'],
                        'effective_date' => $data['effective_date'],
                        'expiry_date' => !empty($data['expiry_date']) ? $data['expiry_date'] : null,
                        'is_active' => true,
                        'imported_from' => basename($filePath)
                    ]);

                    $results['success']++;
                } catch (\Exception $e) {
                    $results['errors'][] = "Row {$results['success']}: " . $e->getMessage();
                    Log::error('Price list import error', [
                        'row' => $data,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();
            fclose($file);

        } catch (\Exception $e) {
            DB::rollBack();
            $results['errors'][] = 'File processing error: ' . $e->getMessage();
        }

        return $results;
    }

    /**
     * Parse boolean values from CSV
     */
    private function parseBool($value): bool
    {
        $value = strtoupper(trim($value));
        return in_array($value, ['TRUE', '1', 'YES', 'Y']);
    }

    /**
     * Get auto-generated description for a part number
     */
    public function generateDescription(string $partNumber): string
    {
        $parser = new ProductParserService();
        $parsed = $parser->parsePartNumber($partNumber);

        if ($parsed['is_exotic']) {
            return "Special Product: {$partNumber}";
        }

        $description = '';

        // Get root description
        if ($parsed['root_code']) {
            $root = ProductClass::where('root_code', $parsed['root_code'])->first();
            if ($root) {
                $description = $root->root_name;
            }
        }

        // Add series description
        if ($parsed['series_code'] && $parsed['root_code']) {
            $series = ProductSeriesMapping::forRootAndSeries($parsed['root_code'], $parsed['series_code'])->first();
            if ($series && $series->description) {
                $description .= ' - ' . $series->description;
            }
        }

        // Add color info
        if ($parsed['color_code']) {
            $description .= ' - Color: ' . $parsed['color_code'];
        }

        // Add treatment info
        if ($parsed['treatment_suffix']) {
            $description .= ' - Treatment: ' . ltrim($parsed['treatment_suffix'], '.');
        }

        return $description ?: $partNumber;
    }

    /**
     * Get pricing information for a part number
     */
    public function getPricingInfo(string $partNumber): array
    {
        $parser = new ProductParserService();
        $parsed = $parser->parsePartNumber($partNumber);

        $result = [
            'price_ly' => null,
            'moq_ly' => null,
            'lead_time' => null,
            'pricing_source' => 'manual'
        ];

        if ($parsed['is_exotic'] || !$parsed['root_code']) {
            return $result;
        }

        // Get root information
        $root = ProductClass::where('root_code', $parsed['root_code'])->first();
        if (!$root) {
            return $result;
        }

        // Get price list (priority: FR > Commercial > NF)
        $priceList = $root->activePriceList('FR') 
            ?: $root->activePriceList('Commercial')
            ?: $root->activePriceList('NF');

        if ($priceList) {
            $result['price_ly'] = $priceList->price_ly;
            $result['moq_ly'] = $priceList->moq_ly;
            $result['pricing_source'] = strtolower($priceList->list_type) . '_list';
        }

        return $result;
    }
}