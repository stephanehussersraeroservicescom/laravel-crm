<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'airline_id', 'aircraft_type_id', 'number_of_aircraft', 'design_status_id', 'commercial_status_id', 'comment'];

    public function verticalSurfaces() { return $this->hasMany(VerticalSurface::class); }
    public function covers() { return $this->hasMany(Cover::class); }
    public function panels() { return $this->hasMany(Panel::class); }
    public function designStatus() { return $this->belongsTo(Status::class, 'design_status_id'); }
    public function commercialStatus() { return $this->belongsTo(Status::class, 'commercial_status_id'); }
    public function airline(){ return $this->belongsTo(\App\Models\Airline::class);}
    public function aircraftType(){ return $this->belongsTo(\App\Models\AircraftType::class);}
    
    // Project-specific subcontractor relationships
    public function subcontractorTeams()
    {
        return $this->hasMany(ProjectSubcontractor::class);
    }
    
    public function subcontractors()
    {
        return $this->belongsToMany(Subcontractor::class, 'project_subcontractor', 'project_id', 'main_subcontractor_id')
                   ->withPivot('supporting_subcontractor_id', 'role', 'notes')
                   ->withTimestamps();
    }
}
