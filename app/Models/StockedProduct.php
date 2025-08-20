<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockedProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_part_number',
        'root_code',
        'is_exotic',
        'notes',
    ];

    protected $casts = [
        'is_exotic' => 'boolean',
    ];

    public function productRoot()
    {
        return $this->belongsTo(ProductClass::class, 'root_code', 'root_code');
    }

    public function getBasePartNumberAttribute()
    {
        if ($this->is_exotic) {
            return $this->full_part_number;
        }
        
        // Remove treatment suffix to get base part number
        $parts = explode('.', $this->full_part_number);
        return $parts[0];
    }

    public function getEffectiveMoqAttribute()
    {
        // All stocked products have 5 LY MOQ
        return 5;
    }

    public function scopeByPartNumber($query, $partNumber)
    {
        return $query->where('full_part_number', $partNumber);
    }

    public function scopeExotic($query)
    {
        return $query->where('is_exotic', true);
    }

    public function scopeStandard($query)
    {
        return $query->where('is_exotic', false);
    }
}