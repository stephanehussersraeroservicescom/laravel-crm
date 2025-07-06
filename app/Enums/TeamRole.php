<?php

namespace App\Enums;

enum TeamRole: string
{
    case COMMERCIAL = 'Commercial';
    case PROJECT_MANAGEMENT = 'Project Management';
    case DESIGN = 'Design';
    case CERTIFICATION = 'Certification';
    case MANUFACTURING = 'Manufacturing';
    case SUBCONTRACTOR = 'Subcontractor';

    public function label(): string
    {
        return $this->value;
    }
}