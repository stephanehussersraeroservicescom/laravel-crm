<?php

namespace App\Enums;

enum CabinClass: string
{
    case FIRST_CLASS = 'first_class';
    case BUSINESS_CLASS = 'business_class';
    case PREMIUM_ECONOMY = 'premium_economy';
    case ECONOMY = 'economy';
    
    public function label(): string
    {
        return match($this) {
            self::FIRST_CLASS => 'First Class',
            self::BUSINESS_CLASS => 'Business Class',
            self::PREMIUM_ECONOMY => 'Premium Economy',
            self::ECONOMY => 'Economy',
        };
    }
}