<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductRoot extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_classes';

    protected $fillable = [
        'root_code',
        'root_name',
        'part_number_prefix',
        'description',
        'has_ink_resist',
        'is_bio',
        'moq_ly',
        'lead_time_weeks',
        'price',
    ];

    protected $casts = [
        'has_ink_resist' => 'boolean',
        'is_bio' => 'boolean',
        'moq_ly' => 'integer',
    ];

    public function priceLists()
    {
        return $this->hasMany(PriceList::class, 'root_code', 'root_code');
    }

    public function stockedProducts()
    {
        return $this->hasMany(StockedProduct::class, 'root_code', 'root_code');
    }

    public function activePriceList($listType = 'standard')
    {
        return $this->priceLists()
            ->where('list_type', $listType)
            ->where('is_active', true)
            ->current()
            ->orderBy('effective_date', 'desc')
            ->first();
    }

    public function scopeActive($query)
    {
        return $query; // All non-soft-deleted records are considered active
    }

    
    /**
     * Get the prefix to use when building part numbers
     * Falls back to root_code if no prefix is set
     */
    public function getPartNumberPrefixAttribute($value)
    {
        return $value ?: $this->root_code;
    }
}