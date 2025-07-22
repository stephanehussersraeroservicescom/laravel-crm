<?php

namespace App\Services;

use App\Models\ProductRoot;
use App\Models\ProductSeriesMapping;
use App\Models\PriceList;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SimplifiedProductImportService
{
    /**
     * Import everything from a single master CSV file
     */
    public function importMasterProducts(string $filePath): array
    {
        $results = [
            'roots_created' => 0,
            'series_created' => 0,
            'prices_created' => 0,
            'errors' => [],
            'skipped' => 0
        ];

        try {
            $file = fopen($filePath, 'r');
            $headers = fgetcsv($file); // Skip header row

            DB::beginTransaction();

            $processedRoots = [];
            $processedSeries = [];

            while (($row = fgetcsv($file)) !== false) {
                if (empty($row[0])) continue; // Skip empty rows

                $data = array_combine($headers, $row);
                
                try {
                    $rootCode = $data['root_code'];
                    $seriesCode = $data['series_code'] ?? null;

                    // 1. Create/Update Product Root (only once per root)
                    if (!in_array($rootCode, $processedRoots)) {
                        ProductRoot::updateOrCreate(
                            ['root_code' => $rootCode],
                            [
                                'root_name' => $data['root_name'],
                                'category' => $data['category'],
                                'description' => $data['root_description'],
                                'is_active' => $this->parseBool($data['is_active'] ?? 'TRUE')
                            ]
                        );
                        $processedRoots[] = $rootCode;
                        $results['roots_created']++;
                    }

                    // 2. Create/Update Series Mapping (if series exists)
                    if (!empty($seriesCode)) {
                        $seriesKey = $rootCode . '-' . $seriesCode;
                        if (!in_array($seriesKey, $processedSeries)) {
                            ProductSeriesMapping::updateOrCreate(
                                [
                                    'root_code' => $rootCode,
                                    'series_code' => $seriesCode
                                ],
                                [
                                    'description' => $data['series_description'] ?? '',
                                    'has_ink_resist' => $this->parseBool($data['has_ink_resist'] ?? 'FALSE'),
                                    'is_bio' => $this->parseBool($data['is_bio'] ?? 'FALSE'),
                                    'base_series' => $seriesCode // Use same series as base
                                ]
                            );
                            $processedSeries[] = $seriesKey;
                            $results['series_created']++;
                        }
                    }

                    // 3. Create Price List Entry
                    if (!empty($data['price_ly']) && !empty($data['moq_ly'])) {
                        // Determine price list type based on category
                        $listType = $data['category'] === 'commercial' ? 'Commercial' : 'FR';
                        
                        // Create unique identifier for this price entry
                        $priceKey = $listType . '-' . $rootCode . '-' . ($seriesCode ?? 'base');
                        
                        PriceList::updateOrCreate(
                            [
                                'list_type' => $listType,
                                'root_code' => $rootCode,
                                'effective_date' => '2025-01-01'
                            ],
                            [
                                'price_ly' => (float)$data['price_ly'],
                                'moq_ly' => (int)$data['moq_ly'],
                                'expiry_date' => null,
                                'is_active' => true,
                                'imported_from' => 'master_products_import'
                            ]
                        );
                        $results['prices_created']++;
                    }

                } catch (\Exception $e) {
                    $results['errors'][] = "Row " . ($results['roots_created'] + $results['series_created'] + 1) . ": " . $e->getMessage();
                    Log::error('Master product import error', [
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
     * Generate complete product description from a part number
     */
    public function generateCompleteDescription(string $partNumber): string
    {
        $parser = new ProductParserService();
        $parsed = $parser->parsePartNumber($partNumber);

        if ($parsed['is_exotic']) {
            return "Special Product: {$partNumber}";
        }

        $description = '';

        // Get root description (the main product description)
        if ($parsed['root_code']) {
            $root = ProductRoot::where('root_code', $parsed['root_code'])->first();
            if ($root) {
                $description = $root->description; // Use the full root description
            }
        }

        // Add series variation if exists
        if ($parsed['series_code'] && $parsed['root_code']) {
            $series = ProductSeriesMapping::forRootAndSeries($parsed['root_code'], $parsed['series_code'])->first();
            if ($series && $series->description) {
                $description .= ' (' . $series->description . ')';
            }
        }

        // Add color specification
        if ($parsed['color_code']) {
            $description .= ' - Color: ' . $parsed['color_code'];
        }

        // Add treatment specification
        if ($parsed['treatment_suffix']) {
            $treatment = ltrim($parsed['treatment_suffix'], '.');
            $description .= ' - Treatment: ' . $treatment;
        }

        return $description ?: $partNumber;
    }

    /**
     * Parse boolean values from CSV
     */
    private function parseBool($value): bool
    {
        $value = strtoupper(trim($value));
        return in_array($value, ['TRUE', '1', 'YES', 'Y']);
    }
}