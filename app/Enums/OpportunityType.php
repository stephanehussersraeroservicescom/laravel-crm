<?php

namespace App\Enums;

enum OpportunityType: string
{
    case VERTICAL = 'vertical';
    case PANELS = 'panels';
    case COVERS = 'covers';
    case OTHERS = 'others';
    
    public function label(): string
    {
        return match($this) {
            self::VERTICAL => 'Vertical',
            self::PANELS => 'Panels',
            self::COVERS => 'Covers',
            self::OTHERS => 'Others',
        };
    }
}