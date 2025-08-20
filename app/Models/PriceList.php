<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    use HasFactory;

    protected $fillable = [
        'list_type',
        'root_code',
        'price_ly',
        'moq_ly',
        'effective_date',
        'expiry_date',
        'is_active',
        'imported_from',
    ];

    protected $casts = [
        'price_ly' => 'decimal:2',
        'moq_ly' => 'integer',
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function productRoot()
    {
        return $this->belongsTo(ProductClass::class, 'root_code', 'root_code');
    }

    public function getPriceFormattedAttribute()
    {
        return '$' . number_format($this->price_ly, 2);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        $today = now()->toDateString();
        return $query->where('effective_date', '<=', $today)
                     ->where(function ($q) use ($today) {
                         $q->whereNull('expiry_date')
                           ->orWhere('expiry_date', '>=', $today);
                     });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('list_type', $type);
    }

    public function isCurrentlyValid()
    {
        $today = now();
        return $this->effective_date <= $today && 
               ($this->expiry_date === null || $this->expiry_date >= $today);
    }
}