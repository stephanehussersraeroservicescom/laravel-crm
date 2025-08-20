<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExternalCustomer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'contact_name',
        'email',
        'phone',
        'address',
        'payment_terms',
        'notes',
        'is_regular'
    ];

    protected $casts = [
        'is_regular' => 'boolean',
    ];

    /**
     * Get all quotes for this external customer
     */
    public function quotes()
    {
        return $this->morphMany(Quote::class, 'customer');
    }

    /**
     * Scope to search customers by name, email, or phone
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('contact_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    /**
     * Get display name for dropdowns
     */
    public function getDisplayNameAttribute()
    {
        $name = $this->name;
        if ($this->contact_name) {
            $name .= ' (' . $this->contact_name . ')';
        }
        return $name;
    }

    /**
     * Check if customer should be promoted to regular
     */
    public function checkForPromotion()
    {
        // Promote to regular if they have 3+ quotes
        if (!$this->is_regular && $this->quotes()->count() >= 3) {
            $this->update(['is_regular' => true]);
        }
    }
}