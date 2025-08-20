<?php

namespace App\Services;

use App\Models\ProductClass;
use App\Models\ProductSeriesMapping;
use App\Models\PriceList;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SimplifiedImportService
{
    /**
     * Import product roots from CSV
     */
    public function importProductClasss(string $filePath): array
    {
        $results = [
            'success' => 0,
            'errors' => [],
            'updated' => 0
        ];

        try {
            $file = fopen($filePath, 'r');
            $headers = fgetcsv($file); // Skip header row

            DB::beginTransaction();

            while (($row = fgetcsv($file)) !== false) {
                if (empty($row[0])) continue; // Skip empty rows

                $data = array_combine($headers, $row);
                
                try {
                    $root = ProductClass::updateOrCreate(
                        ['root_code' => $data['root_code']],
                        [
                            'root_name' => $data['root_name'],
                            'category' => $data['category'],
                            'description' => $data['description'],
                            'moq_ly' => (int)($data['moq_ly'] ?? 1),
                            'lead_time_weeks' => $data['lead_time_weeks'] ?? null,
                            'is_active' => $this->parseBool($data['is_active'] ?? 'TRUE')
                        ]
                    );

                    if ($root->wasRecentlyCreated) {
                        $results['success']++;
                    } else {
                        $results['updated']++;
                    }

                } catch (\Exception $e) {
                    $results['errors'][] = "Row {$data['root_code']}: " . $e->getMessage();
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
     * Import product series from CSV
     */
    public function importProductSeries(string $filePath): array
    {
        $results = [
            'success' => 0,
            'errors' => [],
            'updated' => 0
        ];

        try {
            $file = fopen($filePath, 'r');
            $headers = fgetcsv($file); // Skip header row

            DB::beginTransaction();

            while (($row = fgetcsv($file)) !== false) {
                if (empty($row[0]) || empty($row[1])) continue; // Skip rows without root_code or series_code

                $data = array_combine($headers, $row);
                
                try {
                    $series = ProductSeriesMapping::updateOrCreate(
                        [
                            'root_code' => $data['root_code'],
                            'series_code' => $data['series_code']
                        ],
                        [
                            'series_name' => $data['series_name'] ?? '',
                            'has_ink_resist' => $this->parseBool($data['has_ink_resist'] ?? 'FALSE'),
                            'is_bio' => $this->parseBool($data['is_bio'] ?? 'FALSE'),
                            'base_series' => $data['base_series'] ?? $data['series_code']
                        ]
                    );

                    if ($series->wasRecentlyCreated) {
                        $results['success']++;
                    } else {
                        $results['updated']++;
                    }

                } catch (\Exception $e) {
                    $results['errors'][] = "Row {$data['root_code']}-{$data['series_code']}: " . $e->getMessage();
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
                    $priceList = PriceList::create([
                        'list_type' => $data['list_type'],
                        'root_code' => $data['root_code'],
                        'price_ly' => (float)$data['price_ly'],
                        'moq_ly' => 1, // MOQ comes from root, not price list
                        'effective_date' => $data['effective_date'],
                        'expiry_date' => !empty($data['expiry_date']) ? $data['expiry_date'] : null,
                        'is_active' => $this->parseBool($data['is_active'] ?? 'TRUE'),
                        'imported_from' => basename($filePath)
                    ]);

                    $results['success']++;

                } catch (\Exception $e) {
                    $results['errors'][] = "Row {$data['list_type']}-{$data['root_code']}: " . $e->getMessage();
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
     * Generate complete description for a part number
     */
    public function generateDescription(string $partNumber): string
    {
        $parser = new ProductParserService();
        $parsed = $parser->parsePartNumber($partNumber);

        if ($parsed['is_exotic']) {
            return "Special Product: {$partNumber}";
        }

        $description = '';

        // Get root description (this is the main product description)
        if ($parsed['root_code']) {
            $root = ProductClass::where('root_code', $parsed['root_code'])->first();
            if ($root) {
                $description = $root->description; // Use full root description
            }
        }

        // Add series variation if exists
        if ($parsed['series_code'] && $parsed['root_code']) {
            $series = ProductSeriesMapping::forRootAndSeries($parsed['root_code'], $parsed['series_code'])->first();
            if ($series && $series->series_name) {
                $description .= ' (' . $series->series_name . ')';
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
     * Get pricing information for a part number based on root
     */
    public function getPricingInfo(string $partNumber, string $customerIdentifier = null, int $airlineId = null): array
    {
        $parser = new ProductParserService();
        $parsed = $parser->parsePartNumber($partNumber);

        $result = [
            'price_ly' => null,
            'moq_ly' => null,
            'lead_time' => null,
            'pricing_source' => 'manual',
            'contract_price' => null
        ];

        if ($parsed['is_exotic'] || !$parsed['root_code']) {
            return $result;
        }

        // Get root information for MOQ and lead time
        $root = ProductClass::where('root_code', $parsed['root_code'])->first();
        if (!$root) {
            return $result;
        }

        // Set MOQ and lead time from root
        $result['moq_ly'] = $root->moq_ly;
        $result['lead_time'] = $root->lead_time_weeks;

        // 1. Check for contract pricing first
        if ($customerIdentifier || $airlineId) {
            $contractPrice = \App\Models\ContractPrice::findBestPrice(
                $customerIdentifier, 
                $parsed['root_code'], 
                $airlineId
            );
            
            if ($contractPrice) {
                $result['price_ly'] = $contractPrice->contract_price / 100; // Convert from cents
                $result['pricing_source'] = 'contract';
                $result['contract_price'] = $contractPrice;
                return $result;
            }
        }

        // 2. Get standard price list (priority: FR > Commercial > NF)
        $priceList = $root->activePriceList('FR') 
            ?: $root->activePriceList('Commercial')
            ?: $root->activePriceList('NF');

        if ($priceList) {
            $result['price_ly'] = $priceList->price_ly;
            $result['pricing_source'] = strtolower($priceList->list_type) . '_list';
        }

        return $result;
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