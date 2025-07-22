<?php

namespace App\Services;

class ProductParserService
{
    /**
     * Parse a part number into its components
     * Example: ULFRB924-5991.BC3 => [root: ULFRB, series: 924, color: 5991, treatment: .BC3]
     * Note: The root here is the pattern root, not necessarily the database root
     * For example, ULFRB924 might belong to root ULFRB900
     */
    public function parsePartNumber(string $partNumber): array
    {
        $result = [
            'full_part_number' => $partNumber,
            'root_code' => null,
            'series_code' => null,
            'color_code' => null,
            'treatment_suffix' => null,
            'base_part_number' => null,
            'is_exotic' => false,
            'parse_success' => false
        ];

        try {
            // First, check for treatment suffix (anything after a dot)
            $parts = explode('.', $partNumber);
            $basePartNumber = $parts[0];
            $treatment = isset($parts[1]) ? '.' . $parts[1] : null;
            
            $result['treatment_suffix'] = $treatment;
            $result['base_part_number'] = $basePartNumber;

            // Try to parse the base part number
            // Pattern 1: ROOT + SERIES + "-" + COLOR (e.g., ULFRB924-5991)
            if (preg_match('/^([A-Z]+)(\d{3,4})-(.+)$/', $basePartNumber, $matches)) {
                $result['root_code'] = $matches[1];
                $result['series_code'] = $matches[2];
                $result['color_code'] = $matches[3];
                $result['parse_success'] = true;
            }
            // Pattern 2: ROOT + "-" + COLOR (no series) (e.g., TSNF-1234)
            elseif (preg_match('/^([A-Z]+)-(.+)$/', $basePartNumber, $matches)) {
                $result['root_code'] = $matches[1];
                $result['series_code'] = null;
                $result['color_code'] = $matches[2];
                $result['parse_success'] = true;
            }
            // Pattern 3: Try to extract any recognizable root at the beginning
            elseif (preg_match('/^([A-Z]+)(.*)$/', $basePartNumber, $matches)) {
                $potentialRoot = $matches[1];
                $remainder = $matches[2];
                
                // Check if this is a known root (would need to check against database)
                $result['root_code'] = $potentialRoot;
                
                // Try to extract series if it starts with digits
                if (preg_match('/^(\d{3,4})(.*)$/', $remainder, $subMatches)) {
                    $result['series_code'] = $subMatches[1];
                    $remainder = $subMatches[2];
                }
                
                // Everything else becomes the color code
                if (strlen($remainder) > 0) {
                    // Remove leading dash if present
                    $result['color_code'] = ltrim($remainder, '-');
                }
                
                $result['parse_success'] = !empty($result['color_code']);
            }

            // If we couldn't parse it, mark as exotic
            if (!$result['parse_success']) {
                $result['is_exotic'] = true;
                $result['full_part_number'] = $partNumber;
            }

        } catch (\Exception $e) {
            // If any error occurs, mark as exotic
            $result['is_exotic'] = true;
            $result['parse_success'] = false;
        }

        return $result;
    }

    /**
     * Build a part number from components
     */
    public function buildPartNumber(string $rootCode, ?string $seriesCode, string $colorCode, ?string $treatmentSuffix = null): string
    {
        $partNumber = $rootCode;
        
        if ($seriesCode) {
            $partNumber .= $seriesCode;
        }
        
        $partNumber .= '-' . $colorCode;
        
        if ($treatmentSuffix) {
            // Ensure treatment suffix starts with a dot
            if (!str_starts_with($treatmentSuffix, '.')) {
                $treatmentSuffix = '.' . $treatmentSuffix;
            }
            $partNumber .= $treatmentSuffix;
        }
        
        return $partNumber;
    }

    /**
     * Validate if a part number follows standard format
     */
    public function isStandardFormat(string $partNumber): bool
    {
        $parsed = $this->parsePartNumber($partNumber);
        return $parsed['parse_success'] && !$parsed['is_exotic'];
    }

    /**
     * Get all variations of a base part number with different treatments
     */
    public function getPartNumberVariations(string $basePartNumber, array $treatmentSuffixes): array
    {
        $variations = [$basePartNumber]; // Include base without treatment
        
        foreach ($treatmentSuffixes as $suffix) {
            if (!str_starts_with($suffix, '.')) {
                $suffix = '.' . $suffix;
            }
            $variations[] = $basePartNumber . $suffix;
        }
        
        return $variations;
    }

    /**
     * Check if two part numbers share the same base (for MOQ calculation)
     */
    public function haveSameBase(string $partNumber1, string $partNumber2): bool
    {
        $parsed1 = $this->parsePartNumber($partNumber1);
        $parsed2 = $this->parsePartNumber($partNumber2);
        
        // If either is exotic, they can't share a base
        if ($parsed1['is_exotic'] || $parsed2['is_exotic']) {
            return false;
        }
        
        return $parsed1['base_part_number'] === $parsed2['base_part_number'];
    }
    
    /**
     * Find the database root for a given part number
     * This handles cases where ULFRB924 belongs to ULFRB900 root
     */
    public function findDatabaseRoot(string $partNumber): ?string
    {
        $parsed = $this->parsePartNumber($partNumber);
        
        if ($parsed['is_exotic'] || !$parsed['root_code']) {
            return null;
        }
        
        // Special handling for known patterns
        if ($parsed['root_code'] === 'ULFRB' && $parsed['series_code']) {
            // ULFRB924, ULFRB926, ULFRB974 all belong to ULFRB900
            if (in_array($parsed['series_code'], ['924', '926', '974', '924IR', '926IR'])) {
                return 'ULFRB900';
            }
        }
        
        // For most cases, the parsed root is the database root
        return $parsed['root_code'];
    }
}