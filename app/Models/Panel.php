<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Panel extends Model
{
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'panel_material');
    }

    public function certificationStatus()
    {
        return $this->belongsTo(Status::class, 'certification_status_id');
    }
    public function subcontractor()
    {
        return $this->belongsTo(Subcontractor::class);
    }
}
