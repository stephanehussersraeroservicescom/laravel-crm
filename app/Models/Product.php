<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'part_number',
        'root_code',
        'color_name',
        'color_code',
        'description',
        'price',
        'moq',
        'uom',
        'lead_time_weeks',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'moq' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the product class (root) this product belongs to
     */
    public function productClass()
    {
        return $this->belongsTo(ProductClass::class, 'root_code', 'root_code');
    }

    /**
     * Get all quote lines using this product
     */
    public function quoteLines()
    {
        return $this->hasMany(QuoteLine::class, 'part_number', 'part_number');
    }

    /**
     * Get all quotes that have quoted this product (through quote lines)
     */
    public function quotes()
    {
        return $this->hasManyThrough(Quote::class, QuoteLine::class, 'part_number', 'id', 'part_number', 'quote_id');
    }

    /**
     * Get all customers who have been quoted this product
     */
    public function customersQuoted()
    {
        return $this->quotes()->with('customer')->get()->pluck('customer')->unique('id');
    }

    /**
     * Scope to search products
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('part_number', 'like', "%{$search}%")
              ->orWhere('color_name', 'like', "%{$search}%")
              ->orWhere('color_code', 'like', "%{$search}%")
              ->orWhere('root_code', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to filter active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get formatted price with currency
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Get display name for dropdowns
     */
    public function getDisplayNameAttribute()
    {
        return $this->part_number . ' - ' . $this->color_name;
    }

    /**
     * Get the number of times this product has been quoted
     */
    public function getQuoteCountAttribute()
    {
        return $this->quoteLines()->count();
    }

    /**
     * Get unique customers count for this product
     */
    public function getCustomerCountAttribute()
    {
        return $this->quotes()->distinct('customer_id')->count('customer_id');
    }
}