<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'company_code',
        'contact_name',
        'email',
        'phone',
        'address',
        'billing_address',
        'shipping_address',
        'tax_id',
        'payment_terms',
        'is_subcontractor',
        'has_blanket_po',
        'credit_limit',
        'account_manager',
        'notes',
    ];

    protected $casts = [
        'is_subcontractor' => 'boolean',
        'has_blanket_po' => 'boolean',
        'credit_limit' => 'decimal:2',
    ];

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function contractPrices()
    {
        return $this->hasMany(ContractPrice::class, 'customer_identifier', 'company_name');
    }

    public function getDisplayNameAttribute()
    {
        return $this->company_name . ' - ' . $this->contact_name;
    }

    public function getShippingAddressDisplayAttribute()
    {
        return $this->shipping_address ?: $this->address;
    }

    public function getBillingAddressDisplayAttribute()
    {
        return $this->billing_address ?: $this->address;
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('company_name', 'like', "%{$search}%")
              ->orWhere('contact_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('company_code', 'like', "%{$search}%");
        });
    }

    public function scopeActive($query)
    {
        return $query; // All customers are active by default
    }

    public function scopeSubcontractors($query)
    {
        return $query->where('is_subcontractor', true);
    }
}