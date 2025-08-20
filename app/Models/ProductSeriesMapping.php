<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSeriesMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'series_code',
        'root_code',
        'series_name',
        'has_ink_resist',
        'is_bio',
        'base_series',
    ];

    protected $casts = [
        'has_ink_resist' => 'boolean',
        'is_bio' => 'boolean',
    ];

    public function productRoot()
    {
        return $this->belongsTo(ProductClass::class, 'root_code', 'root_code');
    }

    public function getFullDescriptionAttribute()
    {
        $desc = $this->series_name ?: '';
        
        if ($this->has_ink_resist) {
            $desc .= ' (Ink Resist)';
        }
        
        if ($this->is_bio) {
            $desc .= ' (Bio)';
        }
        
        return $desc;
    }

    public function scopeForRootAndSeries($query, $rootCode, $seriesCode)
    {
        return $query->where('root_code', $rootCode)
                     ->where('series_code', $seriesCode);
    }
}