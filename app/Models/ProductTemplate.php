<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_number',
        'standard_description',
        'standard_unit',
        'standard_price',
        'category',
        'is_active',
    ];

    protected $casts = [
        'standard_price' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getStandardPriceFormattedAttribute()
    {
        return '$' . number_format($this->standard_price / 100, 2);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('part_number', 'like', "%{$search}%")
              ->orWhere('standard_description', 'like', "%{$search}%");
        });
    }
}