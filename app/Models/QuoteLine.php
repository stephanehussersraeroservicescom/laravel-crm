<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'part_number',
        'root_code',
        'series_code',
        'color_code',
        'treatment_suffix',
        'is_exotic',
        'description',
        'quantity',
        'unit',
        'standard_price',
        'contract_price',
        'final_price',
        'pricing_source',
        'pricing_reference',
        'moq',
        'moq_waived',
        'moq_waiver_reason',
        'base_part_number',
        'lead_time',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'standard_price' => 'integer',
        'contract_price' => 'integer',
        'final_price' => 'integer',
        'moq' => 'integer',
        'sort_order' => 'integer',
        'is_exotic' => 'boolean',
        'moq_waived' => 'boolean',
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function getStandardPriceFormattedAttribute()
    {
        return '$' . number_format($this->standard_price / 100, 2);
    }

    public function getContractPriceFormattedAttribute()
    {
        return $this->contract_price ? '$' . number_format($this->contract_price / 100, 2) : null;
    }

    public function getFinalPriceFormattedAttribute()
    {
        return '$' . number_format($this->final_price / 100, 2);
    }

    public function getLineTotalAttribute()
    {
        return $this->quantity * $this->final_price;
    }

    public function getLineTotalFormattedAttribute()
    {
        return '$' . number_format($this->line_total / 100, 2);
    }

    public function hasContractPrice()
    {
        return $this->contract_price !== null && $this->contract_price !== $this->standard_price;
    }

    public function productRoot()
    {
        // Since we can have multiple products with same root_code, just get the first one
        // This maintains backward compatibility
        return $this->belongsTo(ProductRoot::class, 'root_code', 'root_code');
    }

    public function isMoqMet()
    {
        return $this->moq_waived || $this->quantity >= $this->moq;
    }

    public function getPricingSourceDisplayAttribute()
    {
        $sources = [
            'contract' => 'Contract Price',
            'fr_list' => 'FR Price List',
            'commercial_list' => 'Commercial Price List',
            'nf_list' => 'NF Price List',
            'manual' => 'Manual Entry',
            'stock' => 'Stock Price'
        ];

        return $sources[$this->pricing_source] ?? 'Unknown';
    }

    public function getFullDescriptionAttribute()
    {
        if ($this->description) {
            return $this->description;
        }

        // Generate description using the import service
        $importService = new \App\Services\SimplifiedImportService();
        return $importService->generateDescription($this->part_number);
    }
}