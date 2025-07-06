<?php

namespace App\Enums;

enum ContactRole: string
{
    case ENGINEERING = 'engineering';
    case PROGRAM_MANAGEMENT = 'program_management';
    case DESIGN = 'design';
    case CERTIFICATION = 'certification';

    public function label(): string
    {
        return match($this) {
            self::ENGINEERING => 'Engineering',
            self::PROGRAM_MANAGEMENT => 'Program Management',
            self::DESIGN => 'Design',
            self::CERTIFICATION => 'Certification',
        };
    }
}