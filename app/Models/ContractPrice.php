<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_identifier',
        'part_number',
        'root_code',
        'airline_id',
        'contract_price',
        'valid_from',
        'valid_to',
        'notes',
    ];

    protected $casts = [
        'contract_price' => 'integer',
        'valid_from' => 'date',
        'valid_to' => 'date',
    ];

    public function airline()
    {
        return $this->belongsTo(Airline::class);
    }

    public function getContractPriceFormattedAttribute()
    {
        return '$' . number_format($this->contract_price / 100, 2);
    }

    public function scopeActive($query)
    {
        $today = now()->toDateString();
        return $query->where(function ($q) use ($today) {
            $q->whereNull('valid_from')->orWhere('valid_from', '<=', $today);
        })->where(function ($q) use ($today) {
            $q->whereNull('valid_to')->orWhere('valid_to', '>=', $today);
        });
    }

    public static function findBestPrice($customerIdentifier, $rootCode = null, $partNumber = null, $airlineId = null)
    {
        $query = self::active()->orderBy('valid_from', 'desc');

        // Priority order for contract pricing:
        // 1. Customer + specific part number (highest priority)
        // 2. Airline + specific part number
        // 3. Customer + product root (class)
        // 4. Airline + product root (class)
        // 5. Customer-wide contract
        // 6. Airline-wide contract

        // 1. Customer + specific part number
        if ($customerIdentifier && $partNumber) {
            $customerPartMatch = (clone $query)
                ->where('customer_identifier', $customerIdentifier)
                ->where('part_number', $partNumber)
                ->first();
            
            if ($customerPartMatch) return $customerPartMatch;
        }

        // 2. Airline + specific part number
        if ($airlineId && $partNumber) {
            $airlinePartMatch = (clone $query)
                ->where('airline_id', $airlineId)
                ->where('part_number', $partNumber)
                ->first();
            
            if ($airlinePartMatch) return $airlinePartMatch;
        }

        // 3. Customer + product root (class)
        if ($customerIdentifier && $rootCode) {
            $customerRootMatch = (clone $query)
                ->where('customer_identifier', $customerIdentifier)
                ->where('root_code', $rootCode)
                ->whereNull('part_number')
                ->first();
            
            if ($customerRootMatch) return $customerRootMatch;
        }

        // 4. Airline + product root (class)
        if ($airlineId && $rootCode) {
            $airlineRootMatch = (clone $query)
                ->where('airline_id', $airlineId)
                ->where('root_code', $rootCode)
                ->whereNull('part_number')
                ->first();
            
            if ($airlineRootMatch) return $airlineRootMatch;
        }

        // 5. Customer-wide contract
        if ($customerIdentifier) {
            $customerWideMatch = (clone $query)
                ->where('customer_identifier', $customerIdentifier)
                ->whereNull('root_code')
                ->whereNull('part_number')
                ->first();
            
            if ($customerWideMatch) return $customerWideMatch;
        }

        // 6. Airline-wide contract
        if ($airlineId) {
            $airlineWideMatch = (clone $query)
                ->where('airline_id', $airlineId)
                ->whereNull('root_code')
                ->whereNull('part_number')
                ->first();
            
            if ($airlineWideMatch) return $airlineWideMatch;
        }

        return null;
    }

    // Helper methods for contract type identification
    public function getContractTypeAttribute()
    {
        if ($this->part_number) {
            return $this->customer_identifier ? 'Customer Part' : 'Airline Part';
        } elseif ($this->root_code) {
            return $this->customer_identifier ? 'Customer Root' : 'Airline Root';
        } else {
            return $this->customer_identifier ? 'Customer Wide' : 'Airline Wide';
        }
    }

    public function getContractScopeAttribute()
    {
        if ($this->part_number) {
            return 'Specific Part: ' . $this->part_number;
        } elseif ($this->root_code) {
            return 'Product Class: ' . $this->root_code;
        } else {
            return 'All Products';
        }
    }

    public function getPartyNameAttribute()
    {
        if ($this->customer_identifier) {
            return $this->customer_identifier;
        } elseif ($this->airline_id && $this->airline) {
            return $this->airline->name;
        }
        return 'Unknown';
    }

    public function productRoot()
    {
        return $this->belongsTo(ProductClass::class, 'root_code', 'root_code');
    }

    // Validation rules for contract pricing
    public static function validationRules()
    {
        return [
            'contract_number' => 'required|string|max:255',
            'contract_price' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_to' => 'nullable|date|after:valid_from',
            'priority' => 'required|integer|min:1|max:100',
            'customer_identifier' => 'required_without:airline_id|nullable|string|max:255',
            'airline_id' => 'required_without:customer_identifier|nullable|exists:airlines,id',
            'part_number' => 'nullable|string|max:255',
            'root_code' => 'nullable|exists:product_roots,root_code',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    // Check if this contract conflicts with existing ones
    public function hasConflicts()
    {
        $conflictQuery = self::active()
            ->where('id', '!=', $this->id ?? 0);

        // Check for same customer/airline + same scope
        if ($this->customer_identifier) {
            $conflictQuery->where('customer_identifier', $this->customer_identifier);
        } elseif ($this->airline_id) {
            $conflictQuery->where('airline_id', $this->airline_id);
        }

        // Same scope (part number or root code)
        if ($this->part_number) {
            $conflictQuery->where('part_number', $this->part_number);
        } elseif ($this->root_code) {
            $conflictQuery->where('root_code', $this->root_code)->whereNull('part_number');
        } else {
            $conflictQuery->whereNull('root_code')->whereNull('part_number');
        }

        return $conflictQuery->exists();
    }
}